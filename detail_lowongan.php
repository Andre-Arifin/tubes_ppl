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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$lowongan = null;
$has_applied = false;

if ($id > 0) {
    $sql = "SELECT * FROM lowongan WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $lowongan = $result->fetch_assoc();
        
        // Check if user has already applied
        $check_sql = "SELECT id FROM lamaran WHERE user_id = ? AND lowongan_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $_SESSION['user_id'], $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $has_applied = $check_result->num_rows > 0;
        $check_stmt->close();
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lowongan - GaweYuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body, html { background: #f4f8fb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #222; }
        .navbar { background-color: #fff !important; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .navbar-brand { font-size: 1.5rem; font-weight: bold; color: #2E7D32 !important; }
        .navbar-brand span { color: #222; }
        .sidebar { height: 100%; width: 250px; position: fixed; top: 0; left: -250px; background-color: #fff; padding-top: 20px; transition: 0.3s; z-index: 1000; box-shadow: 2px 0 5px rgba(0,0,0,0.07); }
        .sidebar a { padding: 12px 20px; text-decoration: none; font-size: 1rem; color: #222; display: block; transition: 0.2s; border-left: 3px solid transparent; }
        .sidebar a:hover { background-color: #f4f8fb; color: #2E7D32; border-left: 3px solid #2E7D32; }
        .logout-btn { margin: 20px 15px; padding: 12px; text-align: center; font-size: 1rem; color: white; background-color: #dc3545; border: none; border-radius: 8px; cursor: pointer; transition: 0.3s; }
        .logout-btn:hover { background-color: #c82333; transform: translateY(-2px); }
        .open-sidebar-btn { position: absolute; top: 15px; left: 15px; font-size: 1.5rem; cursor: pointer; z-index: 1500; color: #2E7D32; }
        .active-sidebar { left: 0; }
        .main-content { margin-top: 40px; margin-bottom: 40px; }
        .detail-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 32px 24px; max-width: 700px; margin: 0 auto; }
        .detail-title { font-size: 1.5rem; font-weight: bold; color: #1976d2; margin-bottom: 8px; }
        .company-name { color: #2E7D32; font-weight: 500; margin-bottom: 8px; }
        .job-meta { font-size: 1rem; color: #555; margin-bottom: 8px; }
        .job-meta i { margin-right: 6px; }
        .job-badges { margin-bottom: 12px; }
        .job-badge { background: #e3f2fd; color: #1976d2; border-radius: 12px; padding: 4px 14px; font-size: 0.85rem; margin-right: 6px; margin-bottom: 4px; display: inline-block; }
        .job-date { font-size: 0.95rem; color: #888; margin-bottom: 8px; }
        .job-desc, .job-qual { color: #444; font-size: 1rem; margin-bottom: 16px; }
        .contact-info { background: #f4f8fb; border-radius: 10px; padding: 16px; margin-bottom: 16px; }
        .contact-info i { color: #1976d2; margin-right: 8px; }
        .btn-back { background: #2E7D32; color: #fff; border: none; border-radius: 8px; padding: 8px 22px; font-weight: 600; transition: 0.2s; }
        .btn-back:hover { background: #1976d2; color: #fff; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-bars"></i>
            <span>Gawe</span>Yuk
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="tentang_kami.php">Tentang Kami</a></li>
                <li class="nav-item"><a class="nav-link active" href="lowongan.php">Lowongan Kerja</a></li>
                <li class="nav-item"><a class="nav-link" href="#">FAQ</a></li>
                <li class="nav-item"><a class="nav-link" href="tambah_lowongan.php">Posting Pekerjaan</a></li>
            </ul>
        </div>
    </div>
</nav>
<div id="mySidebar" class="sidebar">
    <h4 class="text-center mb-4"><?php echo $_SESSION['full_name']; ?></h4>
    <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
    <a href="profil.php"><i class="fas fa-user me-2"></i>Profil</a>
    <a href="tentang_kami.php"><i class="fas fa-info-circle me-2"></i>Tentang Kami</a>
    <a href="kirim_masukan.php"><i class="fas fa-comment me-2"></i>Kirim Masukan</a>
    <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
</div>
<span class="open-sidebar-btn" onclick="toggleSidebar()">☰</span>
<div class="container main-content">
    <?php if (!$lowongan): ?>
        <div class="alert alert-danger mt-5">Lowongan tidak ditemukan.</div>
    <?php else: ?>
        <div class="detail-card">
            <div class="d-flex align-items-center mb-3">
                <div class="job-logo me-3">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <div class="detail-title"><?php echo htmlspecialchars($lowongan['jenis_pekerjaan']); ?></div>
                    <div class="company-name"><?php echo htmlspecialchars($lowongan['nama_perusahaan']); ?></div>
                    <div class="job-date mb-2"><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($lowongan['created_at'])); ?></div>
                </div>
            </div>
            <div class="job-meta mb-2">
                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($lowongan['alamat']); ?></span>
                <span class="ms-3"><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($lowongan['gaji']); ?></span>
                <span class="ms-3"><i class="fas fa-user"></i> Umur: <?php echo htmlspecialchars($lowongan['umur']); ?></span>
            </div>
            <div class="job-badges mb-2">
                <span class="job-badge"><?php echo htmlspecialchars($lowongan['jenis_pekerjaan']); ?></span>
                <?php if (!empty($lowongan['tunjangan'])): ?><span class="job-badge">Tunjangan</span><?php endif; ?>
            </div>
            <div class="job-desc"><strong>Deskripsi:</strong><br><?php echo nl2br(htmlspecialchars($lowongan['deskripsi'])); ?></div>
            <div class="job-qual"><strong>Kualifikasi:</strong><br><?php echo !empty($lowongan['kualifikasi']) ? nl2br(htmlspecialchars($lowongan['kualifikasi'])) : '-'; ?></div>
            <div class="contact-info">
                <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($lowongan['email']); ?></div>
                <div><i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($lowongan['whatsapp']); ?></div>
                <?php if (!empty($lowongan['instagram'])): ?><div><i class="fab fa-instagram"></i> <?php echo htmlspecialchars($lowongan['instagram']); ?></div><?php endif; ?>
                <?php if (!empty($lowongan['facebook'])): ?><div><i class="fab fa-facebook"></i> <?php echo htmlspecialchars($lowongan['facebook']); ?></div><?php endif; ?>
                <?php if (!empty($lowongan['website'])): ?><div><i class="fas fa-globe"></i> <?php echo htmlspecialchars($lowongan['website']); ?></div><?php endif; ?>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <?php if (!$has_applied): ?>
            <div class="application-form mt-4">
                <h4 class="mb-3">Kirim Lamaran</h4>
                <form action="proses_lamaran.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="lowongan_id" value="<?php echo $lowongan['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="cv" class="form-label">Upload CV (PDF)</label>
                        <input type="file" class="form-control" id="cv" name="cv" accept=".pdf" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="surat_lamaran" class="form-label">Surat Lamaran</label>
                        <textarea class="form-control" id="surat_lamaran" name="surat_lamaran" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Kirim Lamaran</button>
                </form>
            </div>
            <?php else: ?>
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>Anda sudah mengirim lamaran untuk lowongan ini.
                </div>
            <?php endif; ?>
            
            <a href="lowongan.php" class="btn btn-back mt-3"><i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan</a>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("mySidebar");
        sidebar.classList.toggle("active-sidebar");
    }
    function logout() {
        alert("Anda telah logout!");
        window.location.href = "index.php";
    }
</script>
</body>
</html> 
