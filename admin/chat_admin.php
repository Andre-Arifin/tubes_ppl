<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
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

$admin_id = $_SESSION['user_id'];
$selected_user_id = null;
if (isset($_GET['user_id'])) {
    $selected_user_id = (int)$_GET['user_id'];
}
// Get all users who have chatted with admin
$user_sql = "SELECT DISTINCT u.id, u.full_name FROM users u
    JOIN messages m ON (u.id = m.sender_id OR u.id = m.receiver_id)
    WHERE u.role != 'admin' AND (m.sender_id = $admin_id OR m.receiver_id = $admin_id)
    ORDER BY u.full_name ASC";
$user_result = $conn->query($user_sql);
$users = [];
while ($row = $user_result->fetch_assoc()) {
    $users[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat - GaweYuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f8fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-chat-container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            display: flex;
            height: 80vh;
            overflow: hidden;
        }
        .user-list {
            width: 250px;
            background: #e3f2fd;
            border-right: 1px solid #e0e0e0;
            padding: 0;
            overflow-y: auto;
        }
        .user-list .user-item {
            padding: 18px 20px;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
            font-weight: 500;
            color: #1976d2;
            background: #e3f2fd;
            transition: background 0.2s;
        }
        .user-list .user-item.active, .user-list .user-item:hover {
            background: #bbdefb;
        }
        .chat-section {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e0e0e0;
            background: #2E7D32;
            color: #fff;
            font-weight: bold;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 60px;
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
        .bubble.admin {
            background: #2E7D32;
            color: #fff;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }
        .bubble.user {
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
        @media (max-width: 900px) {
            .admin-chat-container { flex-direction: column; height: auto; }
            .user-list { width: 100%; height: 120px; display: flex; flex-direction: row; overflow-x: auto; overflow-y: hidden; }
            .user-list .user-item { border-bottom: none; border-right: 1px solid #e0e0e0; min-width: 180px; }
            .chat-section { min-height: 400px; }
        }
    </style>
</head>
<body>
<div class="admin-chat-container">
    <div class="user-list" id="user-list">
        <?php foreach ($users as $i => $user): ?>
            <div class="user-item<?php if ($i === 0) echo ' active'; ?>" data-user-id="<?php echo $user['id']; ?>">
                <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($user['full_name']); ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="chat-section">
        <div class="chat-header" id="chat-header">
            <?php echo count($users) ? '<i class="fas fa-user"></i> ' . htmlspecialchars($users[0]['full_name']) : 'Pilih User'; ?>
        </div>
        <div class="chat-messages" id="chat-messages"></div>
        <div class="chat-input">
            <form id="chatForm" class="chat-input-form" autocomplete="off">
                <input type="text" id="message" placeholder="Ketik pesan..." autocomplete="off" required />
                <button type="submit"><i class="fas fa-paper-plane"></i> Kirim</button>
            </form>
        </div>
    </div>
</div>
<script>
const adminId = <?php echo (int)$admin_id; ?>;
let currentUserId = <?php echo ($selected_user_id ? (int)$selected_user_id : (count($users) ? (int)$users[0]['id'] : 'null')); ?>;
const userList = document.getElementById('user-list');
const chatHeader = document.getElementById('chat-header');
const chatMessages = document.getElementById('chat-messages');

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
    if (!currentUserId) { chatMessages.innerHTML = '<div class="text-center text-muted mt-5">Pilih user untuk mulai chat</div>'; return; }
    fetch('../fetch_messages.php?target_id=' + currentUserId)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                chatMessages.innerHTML = '';
                data.messages.forEach(msg => {
                    const isAdmin = msg.sender_id == adminId;
                    const bubble = document.createElement('div');
                    bubble.className = 'bubble ' + (isAdmin ? 'admin' : 'user');
                    bubble.innerHTML = escapeHtml(msg.message) +
                        '<span class="time">' + formatTime(msg.sent_at) + '</span>';
                    chatMessages.appendChild(bubble);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
}
loadMessages();
setInterval(loadMessages, 2000);

userList.querySelectorAll('.user-item').forEach(item => {
    if (currentUserId && item.getAttribute('data-user-id') == currentUserId) {
        item.classList.add('active');
        chatHeader.innerHTML = '<i class="fas fa-user"></i> ' + item.textContent.trim();
    }
    item.addEventListener('click', function() {
        userList.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
        currentUserId = this.getAttribute('data-user-id');
        chatHeader.innerHTML = '<i class="fas fa-user"></i> ' + this.textContent.trim();
        loadMessages();
    });
});

document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('message');
    const msg = input.value.trim();
    if (!msg || !currentUserId) return;
    fetch('../send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'receiver_id=' + encodeURIComponent(currentUserId) + '&message=' + encodeURIComponent(msg)
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