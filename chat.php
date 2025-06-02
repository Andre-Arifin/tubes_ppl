<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$database = "gawe_yuk";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Get admin user
$admin_sql = "SELECT id, full_name FROM users WHERE role = 'admin' LIMIT 1";
$admin_result = $conn->query($admin_sql);
$admin = $admin_result->fetch_assoc();
$admin_id = $admin['id'];
$admin_name = $admin['full_name'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Admin - GaweYuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f8fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .chat-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            display: flex;
            flex-direction: column;
            height: 80vh;
        }
        .chat-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e0e0e0;
            background: #2E7D32;
            color: #fff;
            border-radius: 16px 16px 0 0;
            font-weight: bold;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .chat-messages {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            background: #f4f8fb;
        }
        .chat-input {
            border-top: 1px solid #e0e0e0;
            padding: 16px 24px;
            background: #fff;
            border-radius: 0 0 16px 16px;
        }
        .bubble {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 18px;
            margin-bottom: 12px;
            font-size: 1rem;
            line-height: 1.5;
            position: relative;
            word-break: break-word;
        }
        .bubble.user {
            background: #2E7D32;
            color: #fff;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }
        .bubble.admin {
            background: #e3f2fd;
            color: #1976d2;
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }
        .bubble .time {
            display: block;
            font-size: 0.8rem;
            color: #bbb;
            margin-top: 4px;
            text-align: right;
        }
        .chat-input-form {
            display: flex;
            gap: 12px;
        }
        .chat-input-form input[type="text"] {
            flex: 1;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 14px;
            font-size: 1rem;
        }
        .chat-input-form button {
            background: #2E7D32;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            transition: 0.2s;
        }
        .chat-input-form button:hover {
            background: #1976d2;
        }
    </style>
</head>
<body>
<div class="chat-container">
    <div class="chat-header">
        <i class="fas fa-user-shield"></i> Chat dengan Admin (<?php echo htmlspecialchars($admin_name); ?>)
    </div>
    <div class="chat-messages" id="chat-messages"></div>
    <div class="chat-input">
        <form id="chatForm" class="chat-input-form" autocomplete="off">
            <input type="text" id="message" placeholder="Ketik pesan..." autocomplete="off" required />
            <button type="submit"><i class="fas fa-paper-plane"></i> Kirim</button>
        </form>
    </div>
</div>
<script>
const adminId = <?php echo (int)$admin_id; ?>;
function escapeHtml(text) {
    var map = {
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
function formatTime(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
function loadMessages() {
    fetch('fetch_messages.php?target_id=' + adminId)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const messagesDiv = document.getElementById('chat-messages');
                messagesDiv.innerHTML = '';
                data.messages.forEach(msg => {
                    const isUser = msg.sender_id == <?php echo (int)$_SESSION['user_id']; ?>;
                    const bubble = document.createElement('div');
                    bubble.className = 'bubble ' + (isUser ? 'user' : 'admin');
                    bubble.innerHTML = escapeHtml(msg.message) +
                        '<span class="time">' + formatTime(msg.sent_at) + '</span>';
                    messagesDiv.appendChild(bubble);
                });
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }
        });
}
loadMessages();
setInterval(loadMessages, 2000);

document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('message');
    const msg = input.value.trim();
    if (!msg) return;
    fetch('send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'receiver_id=' + encodeURIComponent(adminId) + '&message=' + encodeURIComponent(msg)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            input.value = '';
            loadMessages();
        } else {
            alert(data.message || 'Gagal mengirim pesan');
        }
    });
});
</script>
</body>
</html> 