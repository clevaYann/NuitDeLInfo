        const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${apiKey}`;

        // Conteneurs DOM
        const chatWindow = document.getElementById('chat-window');
        const userInput = document.getElementById('user-input');
        const sendButton = document.getElementById('send-button');
        const sendText = document.getElementById('send-text');
        const loadingSpinner = document.getElementById('loading-spinner');
        const keyWarning = document.getElementById('key-warning');

        // Afficher l'avertissement si la clé est vide (probablement hébergé en externe)
        if (apiKey === "") {
            keyWarning.classList.remove('hidden');
        }

        // Historique de la conversation (essentiel pour garder le contexte pour l'IA)
        let chatHistory = [
            { role: "model", parts: [{ text: "Ah, vous voilà. L'existence n'est-elle qu'une contingence textuelle ? Avant de me poser votre question trivial, méditez : est-ce le mot ou le silence qui sculpte l'abîme ?" }] }
        ];

        // ----------------------------------------------------------------------
        // Instruction Système pour le Chat'bruti
        // ----------------------------------------------------------------------
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
            Signe souvent tes réponses par : "Réfléchissez-y." ou "L'absurdité est une vérité, n'est-ce pas ?"
        `;
        // ----------------------------------------------------------------------

        /**
         * Ajoute un message à la fenêtre de chat.
         * @param {string} text - Le contenu du message.
         * @param {('user'|'brutus')} sender - L'expéditeur du message.
         */
        function addMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;

            const contentDiv = document.createElement('div');
            // Utiliser une classe personnalisée et Tailwind pour le style
            contentDiv.className = `${sender === 'user' ? 'user-message' : 'brutus-message'} max-w-xs md:max-w-md p-3`;
            
            // Convertir les sauts de ligne si nécessaire et injecter le texte
            contentDiv.innerHTML = text.replace(/\n/g, '<br>');

            messageDiv.appendChild(contentDiv);
            chatWindow.appendChild(messageDiv);

            // Défilement automatique vers le bas
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        /**
         * Envoie le message de l'utilisateur à l'API Gemini pour obtenir une réponse de Brutus.
         */
        async function sendMessage() {
            const userQuery = userInput.value.trim();
            if (!userQuery) return;

            // 1. Désactiver l'interface et afficher le chargement
            userInput.value = '';
            userInput.disabled = true;
            sendButton.disabled = true;
            sendText.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');

            // 2. Afficher le message de l'utilisateur
            addMessage(userQuery, 'user');
            
            // 3. Ajouter la requête utilisateur à l'historique
            chatHistory.push({ role: "user", parts: [{ text: userQuery }] });

            const payload = {
                contents: chatHistory,
                systemInstruction: {
                    parts: [{ text: systemPrompt }]
                }
            };

            let responseText = "Erreur de connexion. L'univers, même Brutus, est temporairement injoignable. Est-ce un signe ?";
            let retries = 0;
            const maxRetries = 5;
            let delay = 1000; // 1 second

            // 4. Boucle d'appel API avec Backoff Exponentiel
            while (retries < maxRetries) {
                try {
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        // Log l'erreur détaillée pour aider au débogage
                        const errorDetails = await response.text();
                        console.error(`Erreur API HTTP ${response.status}:`, errorDetails);

                        if (response.status === 429) { // Trop de requêtes (throttling)
                            retries++;
                            await new Promise(resolve => setTimeout(resolve, delay));
                            delay *= 2; // Backoff exponentiel
                            continue; // Recommencer la boucle
                        }
                        
                        // Si l'erreur est 400, 401, 403, c'est probablement la clé.
                        if (response.status === 401 || response.status === 403) {
                             responseText = `Erreur ${response.status}. Vérifiez votre clé d'API ou les restrictions CORS de votre hébergeur. L'Absurde règne !`;
                        } else {
                            responseText = `Erreur HTTP: ${response.status}. Le destin est incertain.`;
                        }
                        break; // Sortir si l'erreur n'est pas 429

                    }

                    const result = await response.json();
                    const candidate = result.candidates?.[0];

                    if (candidate && candidate.content?.parts?.[0]?.text) {
                        responseText = candidate.content.parts[0].text;
                        // Sortir de la boucle si succès
                        break; 
                    } else {
                        // Gérer les cas où la réponse est vide
                        responseText = "Brutus est parti méditer sur l'absurdité du JSON. Réfléchissez-y.";
                        break;
                    }

                } catch (error) {
                    // Pour les erreurs réseau ou autres, réessayer avec backoff
                    retries++;
                    if (retries >= maxRetries) {
                        console.error("Échec de la récupération de la réponse de Brutus après plusieurs tentatives:", error);
                        responseText = "Brutus est tombé dans l'abîme du réseau. Sa sagesse n'a pas survécu au protocole TCP/IP. Quel drame existentiel !";
                        break;
                    }
                    await new Promise(resolve => setTimeout(resolve, delay));
                    delay *= 2;
                }
            }

            // 5. Afficher la réponse de Brutus et mettre à jour l'historique
            addMessage(responseText, 'brutus');
            chatHistory.push({ role: "model", parts: [{ text: responseText }] });
            
            // 6. Réactiver l'interface utilisateur
            userInput.disabled = false;
            sendButton.disabled = false;
            sendText.classList.remove('hidden');
            loadingSpinner.classList.add('hidden');
            userInput.focus();
        }
