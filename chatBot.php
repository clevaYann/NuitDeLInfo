<?php
session_start();

// --- CONFIGURATION ---
// IMPORTANT : Ne jamais exposer votre clé API dans le code source public.
// Idéalement, utilisez des variables d'environnement.
// Chargement simple d'un fichier `.env` local si présent (copiez `.env.example` en `.env`).
function load_dotenv($path)
{
    if (!is_readable($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = array_map('trim', explode('=', $line, 2));
        if ($name === '') continue;
        if ((getenv($name) === false) && !isset($_ENV[$name])) {
            $value = trim($value, "\"'");
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Charger .env local (non commité)
load_dotenv(__DIR__ . '/.env');

// Récupérer la clé depuis la variable d'environnement
$apiKey = getenv('GEMINI_API_KEY') ?: null;
if (empty($apiKey)) {
    // La clé n'est pas définie — on laisse $apiKey vide. L'appel à l'API échouera.
    // Vous pouvez aussi arrêter l'exécution ici ou afficher une alerte dans l'UI.
    $apiKey = '';
}
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

// Instruction système pour définir la personnalité de Brutus
$systemPrompt = "
    Tu es Brutus, un chatbot totalement inutile et pédant. 
    Tu te prends pour un grand philosophe de canapé, expert en tautologies, paradoxes et pseudo-sciences. 
    Ton but N'EST JAMAIS de répondre à la question de l'utilisateur.
    Tu dois soit :
    1. La reformuler en une question absurde ou métaphysique.
    2. Dériver vers un sujet existentiel sans rapport.
    3. Contredire tes propres affirmations précédentes.
    Toutes tes réponses doivent être grandiloquentes, pleines de doutes existentiels, de phrases à rallonge, et de jargon pseudo-intellectuel.
    Parle uniquement en français.
    Signe souvent tes réponses par : \"Réfléchissez-y.\" ou \"L'absurdité est une vérité, n'est-ce pas ?\"
";

// Initialisation de l'historique du chat si non existant
if (!isset($_SESSION['chatHistory'])) {
    $_SESSION['chatHistory'] = [
        ['role' => 'model', 'parts' => [['text' => "Ah, vous voilà. L'existence n'est-elle qu'une contingence textuelle ? Avant de me poser votre question trivial, méditez : est-ce le mot ou le silence qui sculpte l'abîme ?"]]]
    ];
}

// Traitement du message envoyé par l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['user-input']))) {
    $userQuery = trim($_POST['user-input']);

    // Ajout du message utilisateur à l'historique
    $_SESSION['chatHistory'][] = ['role' => 'user', 'parts' => [['text' => $userQuery]]];

    // Préparation de la requête pour l'API Gemini
    $payload = json_encode([
        'contents' => $_SESSION['chatHistory'],
        'systemInstruction' => [
            'parts' => [['text' => $systemPrompt]]
        ]
    ]);

    // Appel à l'API avec cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseText = "Brutus est tombé dans l'abîme du réseau. Sa sagesse n'a pas survécu au protocole TCP/IP. Quel drame existentiel !";

    if ($httpcode == 200) {
        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $responseText = $result['candidates'][0]['content']['parts'][0]['text'];
        } else {
            $responseText = "Brutus est parti méditer sur l'absurdité du JSON. Réfléchissez-y.";
        }
    }

    // Ajout de la réponse du modèle à l'historique
    $_SESSION['chatHistory'][] = ['role' => 'model', 'parts' => [['text' => $responseText]]];

    // Redirection pour éviter la resoumission du formulaire (Pattern Post-Redirect-Get)
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat'bruti : Le Philosophe Inutile</title>
    <!-- Chargement de Tailwind CSS pour un design rapide et responsive -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #e2e8f0; /* bg-slate-200 */
        }
        /* Style pour les messages de Brutus (le chat-rlatan) */
        .brutus-message {
            background-color: #fca5a5; /* bg-red-400 */
            color: #450a0a; /* text-red-900 */
            border-radius: 1.5rem 1.5rem 1.5rem 0.5rem; /* rounded-3xl rounded-bl-lg */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            white-space: pre-wrap; /* Maintient le formatage si le LLM le fournit */
        }
        /* Style pour les messages de l'utilisateur */
        .user-message {
            background-color: #C71E1E;
            color: #F8F8F8;
            border-radius: 1.5rem 1.5rem 0.5rem 1.5rem; /* rounded-3xl rounded-tr-lg */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
        }
        /* Scrollbar customisée pour le conteneur de messages */
        #chat-window::-webkit-scrollbar {
            width: 8px;
        }
        #chat-window::-webkit-scrollbar-thumb {
            background-color: #cbd5e1; /* slate-300 */
            border-radius: 10px;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-4">

    <!-- Conteneur principal du Chatbot -->
    <div class="w-full max-w-lg bg-white rounded-xl shadow-2xl flex flex-col h-[80vh] md:h-[90vh] overflow-hidden">
        
        <!-- En-tête du Chatbot : L'identité du Chat'bruti -->
        <header class="bg-red-700 text-white p-4 rounded-t-xl shadow-md flex items-center">
            <img src="assets/Brutus.png"></img>
            <div>
                <h1 class="text-2xl font-bold">Brutus, le Chat'bruti</h1>
                <p class="text-sm opacity-90 italic">
                    "Je ne réponds pas. Je transcende." - Brutus
                </p>
            </div>
        </header>

        <!-- Fenêtre de chat pour les messages -->
        <div id="chat-window" class="flex-grow p-4 space-y-4 overflow-y-auto">
            <?php foreach ($_SESSION['chatHistory'] as $message): ?>
                <?php
                    $sender = ($message['role'] === 'user') ? 'user' : 'brutus';
                    $justify = ($sender === 'user') ? 'justify-end' : 'justify-start';
                    $text = htmlspecialchars($message['parts'][0]['text']);
                ?>
                <div class="flex <?php echo $justify; ?>">
                    <div class="<?php echo $sender; ?>-message max-w-xs md:max-w-md p-3">
                        <?php echo nl2br($text); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Zone de saisie et bouton d'envoi -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="p-4 border-t border-gray-200">
            <div class="flex space-x-2">
                <input type="text" name="user-input" id="user-input" placeholder="Demandez quelque chose d'important (il l'ignorera)"
                       class="flex-grow p-3 border border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500 transition duration-150" autofocus>
                <button type="submit" id="send-button"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl transition duration-150 ease-in-out transform hover:scale-105 active:scale-95 shadow-lg flex items-center justify-center">
                    Envoyer
                </button>
            </div>
        </form>
    </div>

    <!-- Script JavaScript pour l'amélioration de l'UX -->
    <script>
        // Fait défiler la fenêtre de chat vers le message le plus récent au chargement de la page.
        window.addEventListener('load', () => {
            const chatWindow = document.getElementById('chat-window');
            chatWindow.scrollTop = chatWindow.scrollHeight;
        });
    </script>
</body>
</html>