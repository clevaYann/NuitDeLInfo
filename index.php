<?php
// index.php
// Simple redirect to the chat app entry point `chatBot.php`.
// Keeps a clickable fallback in case headers can't be sent.

$destination = 'chatBot.php';
header('Location: ' . $destination);
exit;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Redirection vers Chat'bruti</title>
</head>
<body>
    <p>Si vous n'êtes pas redirigé automatiquement, <a href="chatBot.php">cliquez ici pour ouvrir Chat'bruti</a>.</p>
</body>
</html>
