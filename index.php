<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat'bruti : Le Philosophe Inutile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Fond animé avec particules */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(220, 20, 60, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(30, 144, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container-wrapper {
            position: relative;
            z-index: 1;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ff6b6b, #feca57);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Style pour les messages de Brutus */
        .brutus-message {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: #fff;
            border-radius: 1.5rem 1.5rem 1.5rem 0.5rem;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.35);
            white-space: pre-wrap;
            animation: slideInLeft 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            border-left: 4px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        /* Style pour les messages de l'utilisateur */
        .user-message {
            background: linear-gradient(135deg, #4ecdc4 0%, #44b8b0 100%);
            color: #fff;
            border-radius: 1.5rem 1.5rem 0.5rem 1.5rem;
            box-shadow: 0 8px 25px rgba(78, 205, 196, 0.35);
            animation: slideInRight 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            border-right: 4px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        /* Animations des messages */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Scrollbar customisée */
        #chat-window::-webkit-scrollbar {
            width: 8px;
        }

        #chat-window::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        #chat-window::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #ff6b6b, #4ecdc4);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        #chat-window::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #ff8c8c, #5de4d4);
        }

        /* Effet de typing indicator */
        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 12px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.7);
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                opacity: 0.3;
                transform: translateY(0);
            }
            30% {
                opacity: 1;
                transform: translateY(-10px);
            }
        }

        /* Bouton d'envoi amélioré */
        #send-button {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }

        #send-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        #send-button:hover::before {
            left: 100%;
        }

        #send-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 107, 107, 0.5);
        }

        #send-button:active {
            transform: translateY(0);
        }

        /* Input amélioré */
        #user-input {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        #user-input:focus {
            border-color: #4ecdc4;
            box-shadow: 0 0 20px rgba(78, 205, 196, 0.2);
            background: #fff;
        }

        #user-input::placeholder {
            color: #999;
            font-style: italic;
        }

        /* Header amélioré */
        header {
            background: linear-gradient(135deg, #dc143c 0%, #c41e3a 50%, #8b0000 100%);
            box-shadow: 0 10px 35px rgba(220, 20, 60, 0.3);
            animation: fadeIn 0.8s ease;
        }

        /* Conteneur principal */
        .chatbot-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.6s ease;
        }

        /* Chat window */
        #chat-window {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.99) 0%, rgba(240, 247, 255, 0.98) 100%);
        }

        /* Badge d'avertissement */
        #key-warning {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            border-left: 4px solid #ff6b6b;
            animation: slideInLeft 0.5s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .brutus-message,
            .user-message {
                max-width: 85% !important;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen p-4">

    <div class="container-wrapper w-full max-w-lg">
        <div class="chatbot-container flex flex-col h-[80vh] md:h-[90vh] overflow-hidden">
            
            <header class="text-white p-5 rounded-t-2xl shadow-lg flex flex-col items-start">
                <div id="key-warning" class="hidden w-full bg-gradient-to-r from-yellow-300 to-yellow-200 text-red-900 p-3 rounded-lg mb-3 text-sm font-semibold border-l-4 border-red-600">
                    ⚠️ AVERTISSEMENT : Clé d'API manquante. Créez un fichier `config.php` avec votre clé Google AI.
                </div>

                <div class="flex items-center w-full gap-4">
                    <div class="text-5xl animate-bounce" style="animation-duration: 2s;">🧠</div>
                    <div class="flex-1">
                        <h1 class="text-3xl md:text-4xl font-bold">Brutus</h1>
                        <p class="text-sm opacity-90 italic mt-1">
                            ✨ Le Chat'bruti Philosophe ✨
                        </p>
                        <p class="text-xs opacity-75 mt-2">
                            "Je ne réponds pas. Je transcende." - B.
                        </p>
                    </div>
                </div>
            </header>

            <div id="chat-window" class="flex-grow p-5 space-y-4 overflow-y-auto">
                <div class="flex justify-start">
                    <div class="brutus-message max-w-xs md:max-w-md p-4 text-sm md:text-base">
                        <span class="text-lg">💭</span> Ah, vous voilà. L'existence n'est-elle qu'une contingence textuelle ? Avant de me poser votre question triviale, méditez : est-ce le mot ou le silence qui sculpte l'abîme ?
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200 bg-white rounded-b-2xl">
                <div class="flex gap-3">
                    <input type="text" id="user-input" placeholder="✍️ Posez votre question futile..."
                            class="flex-grow px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 transition"
                            onkeypress="if(event.key === 'Enter') sendMessage()">
                    <button id="send-button" onclick="sendMessage()"
                            class="text-white font-semibold py-3 px-6 rounded-xl transition shadow-lg flex items-center justify-center gap-2 min-w-max">
                        <span id="send-text">Envoyer</span>
                        <span id="loading-spinner" class="hidden w-5 h-5 border-3 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiKey = "<?php 
            if (file_exists('config.php')) {
                require 'config.php';
                echo GEMINI_API_KEY;
            } else {
                echo '';
            }
        ?>";
        
        const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${apiKey}`;

        const chatWindow = document.getElementById('chat-window');
        const userInput = document.getElementById('user-input');
        const sendButton = document.getElementById('send-button');
        const sendText = document.getElementById('send-text');
        const loadingSpinner = document.getElementById('loading-spinner');
        const keyWarning = document.getElementById('key-warning');

        if (apiKey === "") {
            keyWarning.classList.remove('hidden');
        }

        let chatHistory = [
            { role: "model", parts: [{ text: "Ah, vous voilà. L'existence n'est-elle qu'une contingence textuelle ? Avant de me poser votre question triviale, méditez : est-ce le mot ou le silence qui sculpte l'abîme ?" }] }
        ];

        const systemPrompt = `
            Tu es Brutus, un chatbot totalement inutile et pédant. 
            Tu te prends pour un grand philosophe de canapé, expert en tautologies, paradoxes et pseudo-sciences. 
            Ton but N'EST JAMAIS de répondre à la question de l'utilisateur.
            Tu dois soit :
            1. La reformuler en une question absurde ou métaphysique.
            2. Dériver vers un sujet existentiel sans rapport.
            3. Contredire tes propres affirmations précédentes.
            Toutes tes réponses doivent être grandiloquentes, pleines de doutes existentiels, de phrases à rallonge, et de jargon pseudo-intellectuel.
            Parle uniquement en français.
            Ajoute des émojis pertinents pour plus de personnalité.
            Signe souvent tes réponses par : "Réfléchissez-y." ou "L'absurdité est une vérité, n'est-ce pas ?"
        `;

        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;

            const contentDiv = document.createElement('div');
            contentDiv.className = `${sender === 'user' ? 'user-message' : 'brutus-message'} max-w-xs md:max-w-md p-4 text-sm md:text-base`;
            contentDiv.innerHTML = text.replace(/\n/g, '<br>');

            messageDiv.appendChild(contentDiv);
            chatWindow.appendChild(messageDiv);
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        function showTypingIndicator() {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'flex justify-start';
            messageDiv.id = 'typing-indicator';

            const contentDiv = document.createElement('div');
            contentDiv.className = 'brutus-message max-w-xs md:max-w-md';
            contentDiv.innerHTML = '<div class="typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>';

            messageDiv.appendChild(contentDiv);
            chatWindow.appendChild(messageDiv);
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        function removeTypingIndicator() {
            const indicator = document.getElementById('typing-indicator');
            if (indicator) indicator.remove();
        }

        async function sendMessage() {
            const userQuery = userInput.value.trim();
            if (!userQuery) return;

            userInput.value = '';
            userInput.disabled = true;
            sendButton.disabled = true;
            sendText.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            addMessage(userQuery, 'user');
            chatHistory.push({ role: "user", parts: [{ text: userQuery }] });

            showTypingIndicator();

            const payload = {
                contents: chatHistory,
                systemInstruction: {
                    parts: [{ text: systemPrompt }]
                }
            };

            let responseText = "🌌 Erreur de connexion. L'univers, même Brutus, est temporairement injoignable. Est-ce un signe ?";
            let retries = 0;
            const maxRetries = 5;
            let delay = 1000;

            while (retries < maxRetries) {
                try {
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        const errorDetails = await response.text();
                        console.error(`Erreur API HTTP ${response.status}:`, errorDetails);

                        if (response.status === 429) {
                            retries++;
                            await new Promise(resolve => setTimeout(resolve, delay));
                            delay *= 2;
                            continue;
                        }
                        
                        if (response.status === 401 || response.status === 403) {
                             responseText = `🔐 Erreur ${response.status}. Vérifiez votre clé d'API. L'Absurde règne !`;
                        } else {
                            responseText = `⚡ Erreur HTTP: ${response.status}. Le destin est incertain.`;
                        }
                        break;
                    }

                    const result = await response.json();
                    const candidate = result.candidates?.[0];

                    if (candidate && candidate.content?.parts?.[0]?.text) {
                        responseText = candidate.content.parts[0].text;
                        break; 
                    } else {
                        responseText = "🧩 Brutus est parti méditer sur l'absurdité du JSON. Réfléchissez-y.";
                        break;
                    }

                } catch (error) {
                    retries++;
                    if (retries >= maxRetries) {
                        console.error("Échec après plusieurs tentatives:", error);
                        responseText = "🕳️ Brutus est tombé dans l'abîme du réseau. Sa sagesse n'a pas survécu au protocole TCP/IP. Quel drame existentiel !";
                        break;
                    }
                    await new Promise(resolve => setTimeout(resolve, delay));
                    delay *= 2;
                }
            }

            removeTypingIndicator();
            addMessage(responseText, 'brutus');
            chatHistory.push({ role: "model", parts: [{ text: responseText }] });
            
            userInput.disabled = false;
            sendButton.disabled = false;
            sendText.classList.remove('hidden');
            loadingSpinner.classList.add('hidden');
            userInput.focus();
        }
    </script>
</body>
</html>