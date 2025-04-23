<?php
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle clear chat request
if (isset($_GET['clear'])) {
    unset($_SESSION['chat_history']);
    header("Location: chat.php");
    exit();
}

// Initialize chat history if not set
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ChatGPT Clone</title>
    <style>
        /* Reset & base styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F7F7F7;
            color: #333;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        /* Header styling similar to ChatGPT */
        header {
            background-color: #343541;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 20px;
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .clear-button {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #e74c3c;
            border: none;
            color: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .clear-button:hover {
            background-color: #c0392b;
        }
        /* Chat container and messages */
        .chat-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .chat-message {
            max-width: 70%;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 12px;
            line-height: 1.5;
            animation: fadeIn 0.3s ease;
            word-wrap: break-word;
        }
        .user-message {
            background-color: #DCF8C6;
            align-self: flex-end;
        }
        .ai-message {
            background-color: #fff;
            border: 1px solid #e0e0e0;
            align-self: flex-start;
        }
        /* Footer input area */
        footer {
            background-color: #fff;
            padding: 10px 20px;
            border-top: 1px solid #ddd;
            display: flex;
            align-items: center;
        }
        #message-input {
            flex: 1;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s ease;
        }
        #message-input:focus {
            border-color: #007bff;
        }
        #send-button {
            padding: 12px 20px;
            margin-left: 10px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #send-button:hover {
            background-color: #0056b3;
        }
        /* Fade in animation for messages */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <header>
        ChatGPT Clone
        <button class="clear-button" onclick="location.href='chat.php?clear=true'">Clear Chat</button>
    </header>
    <div class="chat-container" id="chat-container">
        <?php 
        // Display chat history
        foreach ($_SESSION['chat_history'] as $chat) {
            $class = ($chat['sender'] == 'user') ? 'user-message' : 'ai-message';
            echo '<div class="chat-message ' . $class . '">' . htmlspecialchars($chat['message']) . '</div>';
        }
        ?>
    </div>
    <footer>
        <input type="text" id="message-input" placeholder="Type your message here..." autocomplete="off">
        <button id="send-button">Send</button>
    </footer>
    <script>
        // JavaScript for sending messages
        document.getElementById('send-button').addEventListener('click', sendMessage);
        document.getElementById('message-input').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                sendMessage();
            }
        });
        function sendMessage(){
            var input = document.getElementById('message-input');
            var message = input.value.trim();
            if(message === "") return;
            appendMessage(message, 'user');
            input.value = "";
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "process_chat.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    appendMessage(xhr.responseText, 'ai');
                }
            };
            xhr.send("message=" + encodeURIComponent(message));
        }
        function appendMessage(message, sender){
            var chatContainer = document.getElementById('chat-container');
            var div = document.createElement('div');
            div.className = "chat-message " + (sender === 'user' ? "user-message" : "ai-message");
            div.textContent = message;
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    </script>
</body>
</html>
