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

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id']) && isset($_POST['status'])) {
    $application_id = intval($_POST['application_id']);
    $status = $_POST['status'];
    
    $update_sql = "UPDATE lamaran SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $status, $application_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Status lamaran berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui status lamaran.";
    }
    $update_stmt->close();
    
    header("Location: manage_applications.php");
    exit();
}

// Get all applications with user and job details
$sql = "SELECT l.*, u.full_name as applicant_name, u.email as applicant_email, 
        j.jenis_pekerjaan, j.nama_perusahaan 
        FROM lamaran l 
        JOIN users u ON l.user_id = u.id 
        JOIN lowongan j ON l.lowongan_id = j.id 
        ORDER BY l.created_at DESC";
$result = $conn->query($sql);

$applications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Kelola Lamaran - Admin Panel</title>
    <link crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            background: #f4f8fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #fff !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2E7D32 !important;
        }
        .navbar-brand span {
            color: #222;
        }
        .application-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            margin-bottom: 20px;
            padding: 20px;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-accepted {
            background: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        .btn-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-right: 5px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-user-shield"></i>
            <span>Admin</span>Panel
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="manage_applications.php">Kelola Lamaran</a></li>
                <li class="nav-item"><a class="nav-link" href="chat_admin.php">Chat</a></li>
                <li class="nav-item"><a class="nav-link" href="../tambah_lowongan.php">Posting Pekerjaan</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h2 class="mb-4">Kelola Lamaran</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($applications as $app): ?>
            <div class="col-md-6">
                <div class="application-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($app['jenis_pekerjaan']); ?></h5>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($app['nama_perusahaan']); ?></p>
                        </div>
                        <span class="status-badge status-<?php echo $app['status']; ?>">
                            <?php 
                            switch($app['status']) {
                                case 'pending':
                                    echo 'Pending';
                                    break;
                                case 'diterima':
                                    echo 'Diterima';
                                    break;
                                case 'ditolak':
                                    echo 'Ditolak';
                                    break;
                            }
                            ?>
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-1"><strong>Pelamar:</strong> <?php echo htmlspecialchars($app['applicant_name']); ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($app['applicant_email']); ?></p>
                        <p class="mb-1"><strong>Tanggal Lamar:</strong> <?php echo date('d M Y', strtotime($app['created_at'])); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <a href="../uploads/cv/<?php echo htmlspecialchars($app['cv']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i>Lihat CV
                        </a>
                        <a href="chat_admin.php?user_id=<?php echo $app['user_id']; ?>" class="btn btn-sm btn-outline-success ms-2">
                            <i class="fas fa-comments me-1"></i>Chat
                        </a>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-2"><strong>Surat Lamaran:</strong></p>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($app['surat_lamaran'])); ?></p>
                    </div>
                    
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                        <div class="btn-group">
                            <button type="submit" name="status" value="pending" class="btn btn-status btn-warning">
                                <i class="fas fa-clock me-1"></i>Pending
                            </button>
                            <button type="submit" name="status" value="diterima" class="btn btn-status btn-success">
                                <i class="fas fa-check me-1"></i>Terima
                            </button>
                            <button type="submit" name="status" value="ditolak" class="btn btn-status btn-danger">
                                <i class="fas fa-times me-1"></i>Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 