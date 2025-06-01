<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect jika tidak ada sesi
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - GaweYuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body, html {
            background: #f4f8fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #222;
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
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #fff;
            padding-top: 20px;
            transition: 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0,0,0,0.07);
        }
        .sidebar a {
            padding: 12px 20px;
            text-decoration: none;
            font-size: 1rem;
            color: #222;
            display: block;
            transition: 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar a:hover {
            background-color: #f4f8fb;
            color: #2E7D32;
            border-left: 3px solid #2E7D32;
        }
        .logout-btn {
            margin: 20px 15px;
            padding: 12px;
            text-align: center;
            font-size: 1rem;
            color: white;
            background-color: #dc3545;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .logout-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .open-sidebar-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1500;
            color: #2E7D32;
        }
        .active-sidebar {
            left: 0;
        }
        .main-content {
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .company-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px 24px;
            margin-bottom: 32px;
            display: flex;
            align-items: flex-start;
            gap: 24px;
        }
        .company-logo {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            background: #f4f8fb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #2E7D32;
        }
        .company-info {
            flex: 1;
        }
        .company-info h4 {
            margin-bottom: 4px;
            font-weight: 700;
        }
        .company-info a {
            color: #1976d2;
            text-decoration: none;
        }
        .company-info a:hover {
            text-decoration: underline;
        }
        .subscribe-btn {
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 28px;
            font-weight: 600;
            margin-top: 12px;
        }
        .info-section {
            display: flex;
            gap: 24px;
            margin-bottom: 32px;
        }
        .info-tabs {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 24px 0 24px 0;
            min-width: 240px;
            flex: 0 0 260px;
        }
        .info-tabs .nav-link {
            color: #222;
            border: none;
            border-radius: 0;
            text-align: left;
            font-weight: 500;
            padding: 12px 24px;
        }
        .info-tabs .nav-link.active {
            background: #e3f2fd;
            color: #1976d2;
        }
        .info-content {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px 24px;
            flex: 1;
        }
        .info-content h5 {
            color: #1976d2;
            font-weight: 700;
            margin-bottom: 24px;
        }
        .info-table td {
            padding: 8px 16px 8px 0;
            color: #444;
        }
        @media (max-width: 991px) {
            .info-section {
                flex-direction: column;
            }
            .info-tabs {
                min-width: 0;
                flex: 1;
                margin-bottom: 16px;
            }
        }
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
                <li class="nav-item"><a class="nav-link active" href="tentang_kami.php">Tentang Kami</a></li>
                <li class="nav-item"><a class="nav-link" href="lowongan.php">Lowongan Kerja</a></li>
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
    <!-- Visi & Misi Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius: 16px;">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-eye fa-2x text-success me-3"></i>
                            <h5 class="mb-0 fw-bold text-success">Visi</h5>
                        </div>
                        <p class="mb-0">Menjadi platform pencari kerja terdepan di Indonesia yang menghubungkan talenta terbaik dengan perusahaan impian secara mudah, cepat, dan terpercaya.</p>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-bullseye fa-2x text-primary me-3"></i>
                            <h5 class="mb-0 fw-bold text-primary">Misi</h5>
                        </div>
                        <ul class="mb-0 ps-3">
                            <li>Menyediakan informasi lowongan kerja yang akurat dan up-to-date.</li>
                            <li>Mendukung pengembangan karir dan keterampilan pencari kerja.</li>
                            <li>Menjadi jembatan antara perusahaan dan pencari kerja melalui teknologi inovatif.</li>
                            <li>Menciptakan pengalaman pengguna yang mudah, aman, dan nyaman.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Company Card -->
    <div class="company-card">
        <div class="company-logo">
            <i class="fas fa-building"></i>
        </div>
        <div class="company-info">
            <h4>GaweYuk</h4>
            <div>Gedung Maju Bersama, Jl. Teknologi No. 1, Bandar Lampung</div>
            <div><a href="https://gawe-yuk.com" target="_blank">https://gawe-yuk.com</a></div>
        </div>
        <button class="subscribe-btn ms-auto">Subscribe</button>
    </div>
    <div class="info-section">
        <div class="info-tabs nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <button class="nav-link active" id="v-pills-info-tab" data-bs-toggle="pill" data-bs-target="#v-pills-info" type="button" role="tab" aria-controls="v-pills-info" aria-selected="true">Informasi Perusahaan</button>
            <button class="nav-link" id="v-pills-about-tab" data-bs-toggle="pill" data-bs-target="#v-pills-about" type="button" role="tab" aria-controls="v-pills-about" aria-selected="false">Tentang Perusahaan</button>
            <button class="nav-link" id="v-pills-culture-tab" data-bs-toggle="pill" data-bs-target="#v-pills-culture" type="button" role="tab" aria-controls="v-pills-culture" aria-selected="false">Culture & Benefit</button>
            <button class="nav-link" id="v-pills-gallery-tab" data-bs-toggle="pill" data-bs-target="#v-pills-gallery" type="button" role="tab" aria-controls="v-pills-gallery" aria-selected="false">Foto & Video</button>
            <button class="nav-link" id="v-pills-jobs-tab" data-bs-toggle="pill" data-bs-target="#v-pills-jobs" type="button" role="tab" aria-controls="v-pills-jobs" aria-selected="false">Lowongan di GaweYuk</button>
        </div>
        <div class="tab-content info-content flex-grow-1" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-info" role="tabpanel" aria-labelledby="v-pills-info-tab">
                <h5>Informasi Perusahaan</h5>
                <table class="info-table">
                    <tr><td>Industri</td><td>Teknologi & Layanan</td></tr>
                    <tr><td>Jumlah Karyawan</td><td>50-100</td></tr>
                    <tr><td>Dress Code</td><td>Casual & Smart</td></tr>
                    <tr><td>Hari Kerja</td><td>Senin - Jumat</td></tr>
                    <tr><td>Jam Kerja</td><td>08:00 - 17:00 WIB</td></tr>
                </table>
            </div>
            <div class="tab-pane fade" id="v-pills-about" role="tabpanel" aria-labelledby="v-pills-about-tab">
                <h5>Tentang Perusahaan</h5>
                <p>GaweYuk adalah aplikasi pencari kerja berbasis web yang dibuat untuk memudahkan masyarakat mencari lowongan pekerjaan. Kami berkomitmen untuk menghubungkan pencari kerja dengan perusahaan terbaik di Indonesia melalui platform yang mudah digunakan dan informatif.</p>
            </div>
            <div class="tab-pane fade" id="v-pills-culture" role="tabpanel" aria-labelledby="v-pills-culture-tab">
                <h5>Culture & Benefit</h5>
                <ul>
                    <li>Lingkungan kerja kolaboratif dan inovatif</li>
                    <li>Kesempatan pengembangan karir</li>
                    <li>Benefit kesehatan dan asuransi</li>
                    <li>Waktu kerja fleksibel</li>
                </ul>
            </div>
            <div class="tab-pane fade" id="v-pills-gallery" role="tabpanel" aria-labelledby="v-pills-gallery-tab">
                <h5>Foto & Video Perusahaan</h5>
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80" class="img-fluid rounded shadow-sm" alt="Office 1">
                    </div>
                    <div class="col-6 col-md-4">
                        <img src="https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=400&q=80" class="img-fluid rounded shadow-sm" alt="Office 2">
                    </div>
                    <div class="col-6 col-md-4">
                        <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=400&q=80" class="img-fluid rounded shadow-sm" alt="Team">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="v-pills-jobs" role="tabpanel" aria-labelledby="v-pills-jobs-tab">
                <h5>Lowongan Kerja di GaweYuk</h5>
                <ul>
                    <li>Frontend Developer</li>
                    <li>Backend Developer</li>
                    <li>UI/UX Designer</li>
                    <li>Marketing Specialist</li>
                </ul>
            </div>
        </div>
    </div>
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

#

