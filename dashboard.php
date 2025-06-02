<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect jika tidak ada sesi
    exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "gawe_yuk";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Fetch latest 4 job listings
$sql = "SELECT * FROM lowongan ORDER BY created_at DESC LIMIT 4";
$result = $conn->query($sql);
$latest_jobs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $latest_jobs[] = $row;
    }
}

// Fetch user profile for recommendations
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT skills, status FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_profile = $user_result->fetch_assoc();
$user_stmt->close();

$rekomendasi = [];
if ($user_profile) {
    $skills = $user_profile['skills'];
    $status = $user_profile['status'];
    $where = [];
    $params = [];
    $types = '';
    if (!empty($skills)) {
        $skills_arr = array_map('trim', explode(',', $skills));
        foreach ($skills_arr as $skill) {
            $where[] = "(LOWER(l.deskripsi) LIKE ? OR LOWER(l.jenis_pekerjaan) LIKE ? OR LOWER(l.nama_perusahaan) LIKE ?)";
            $params[] = '%' . strtolower($skill) . '%';
            $params[] = '%' . strtolower($skill) . '%';
            $params[] = '%' . strtolower($skill) . '%';
            $types .= 'sss';
        }
    }
    if (!empty($status)) {
        $where[] = "LOWER(l.jenis_pekerjaan) LIKE ?";
        $params[] = '%' . strtolower($status) . '%';
        $types .= 's';
    }
    $where_sql = $where ? ('WHERE ' . implode(' OR ', $where)) : '';
    $sql_rekom = "SELECT l.* FROM lowongan l $where_sql ORDER BY l.created_at DESC LIMIT 4";
    $stmt_rekom = $conn->prepare($sql_rekom);
    if ($params) {
        $stmt_rekom->bind_param($types, ...$params);
    }
    $stmt_rekom->execute();
    $result_rekom = $stmt_rekom->get_result();
    while ($row = $result_rekom->fetch_assoc()) {
        $rekomendasi[] = $row;
    }
    $stmt_rekom->close();
}
$conn->close();
?>

<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>GaweYuk - Dashboard</title>
    <link crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #4CAF50;
            --accent-color: #81C784;
            --text-color: #333;
            --light-bg: #f8f9fa;
        }

        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: white;
            padding-top: 20px;
            transition: 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar a {
            padding: 12px 20px;
            text-decoration: none;
            font-size: 1rem;
            color: var(--text-color);
            display: block;
            transition: 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar a:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
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
            color: var(--primary-color);
        }

        .active-sidebar {
            left: 0;
        }

        .navbar {
            background-color: white !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color) !important;
        }

        .navbar-brand span {
            color: var(--text-color);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 80px 0;
            margin-top: 56px;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .search-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: -50px;
            position: relative;
            z-index: 1;
        }

        .search-input {
            border: 2px solid #eee;
            padding: 12px 20px;
            border-radius: 8px;
            width: 100%;
            transition: 0.3s;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .search-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .search-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .stats-section {
            padding: 60px 0;
            background-color: var(--light-bg);
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }

        .featured-jobs {
            padding: 60px 0;
        }

        .job-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 30px;
            transition: 0.3s;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .job-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .company-name {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 15px;
        }

        .job-details {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .job-detail {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
        }

        .job-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .job-tag {
            background-color: var(--light-bg);
            color: var(--primary-color);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .company-logos {
            padding: 40px 0;
            background-color: white;
        }

        .company-logo {
            filter: grayscale(100%);
            opacity: 0.6;
            transition: 0.3s;
        }

        .company-logo:hover {
            filter: grayscale(0%);
            opacity: 1;
        }

        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 40px 0;
            margin-top: auto;
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: 0.3s;
        }

        .footer a:hover {
            opacity: 1;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .hero-subtitle {
                font-size: 1.2rem;
            }
            .search-box {
                margin-top: 0;
            }
        }

        .gaweyuk-logo {
            letter-spacing: 0;
            word-spacing: 0;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light" style="position:relative;">
        <div class="container d-flex align-items-center justify-content-between">
            <a class="navbar-brand d-flex align-items-center" href="#" style="gap:0;">
                <span class="open-sidebar-btn" onclick="toggleSidebar()" style="cursor:pointer;font-size:1.5rem;line-height:1;">☰</span><span class="gaweyuk-logo" style="font-weight:bold;letter-spacing:0;word-spacing:0;white-space:nowrap;">Gawe<span style="color:#2E7D32;">Yuk</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tentang_kami.php">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="lowongan.php">Lowongan Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="faq.php">FAQ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="mySidebar" class="sidebar">
        <h4 class="text-center mb-4"><?php echo $_SESSION['full_name']; ?></h4>
        <a href="dashboard.php"><i class="fas fa-home me-2"></i>Dashboard</a>
        <a href="profil.php"><i class="fas fa-user me-2"></i>Profil</a>
        <a href="chat.php"><i class="fas fa-comments me-2"></i>Chat Admin</a>
        <a href="tentang_kami.php"><i class="fas fa-info-circle me-2"></i>Tentang Kami</a>
        <a href="kirim_masukan.php"><i class="fas fa-comment me-2"></i>Kirim Masukan</a>
        <button class="logout-btn" onclick="logout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
    </div>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Temukan Pekerjaan Impianmu</h1>
                        <p class="hero-subtitle">Ribuan lowongan kerja menanti untuk karirmu yang lebih baik</p>
                        <a href="lowongan.php" class="btn btn-light btn-lg">
                            Mulai Pencarian <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://storage.googleapis.com/a1aa/image/feJLORhEDbhtz0NzbMR7usjKefBWQ4GRmowZhetA5Dn9z6RgC.jpg" 
                         alt="Illustration of people working together" 
                         class="img-fluid rounded-3 shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="search-box">
            <form class="row g-3" action="lowongan.php" method="get">
                <div class="col-md-4">
                    <label for="jenis" class="form-label visually-hidden">Jenis Pekerjaan</label>
                    <select class="form-select search-input" id="jenis" name="jenis">
                        <option value="">Semua Jenis Pekerjaan</option>
                        <option value="Full-time">Full-time</option>
                        <option value="Part-time">Part-time</option>
                        <option value="Freelance">Freelance</option>
                        <option value="Internship">Internship</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="lokasi" class="form-label visually-hidden">Lokasi</label>
                    <select class="form-select search-input" id="lokasi" name="lokasi">
                        <option value="">Semua Lokasi</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Surabaya">Surabaya</option>
                        <option value="Bandung">Bandung</option>
                        <option value="Medan">Medan</option>
                        <option value="Semarang">Semarang</option>
                        <option value="Makassar">Makassar</option>
                        <option value="Palembang">Palembang</option>
                        <option value="Depok">Depok</option>
                        <option value="Tangerang">Tangerang</option>
                        <option value="Bekasi">Bekasi</option>
                        <option value="Bogor">Bogor</option>
                        <option value="Malang">Malang</option>
                        <option value="Yogyakarta">Yogyakarta</option>
                        <option value="Denpasar">Denpasar</option>
                        <option value="Batam">Batam</option>
                        <option value="Pekanbaru">Pekanbaru</option>
                        <option value="Padang">Padang</option>
                        <option value="Balikpapan">Balikpapan</option>
                        <option value="Manado">Manado</option>
                        <option value="Pontianak">Pontianak</option>
                        <option value="Banjarmasin">Banjarmasin</option>
                        <option value="Jambi">Jambi</option>
                        <option value="Mataram">Mataram</option>
                        <option value="Kupang">Kupang</option>
                        <option value="Jayapura">Jayapura</option>
                        <option value="Palu">Palu</option>
                        <option value="Ambon">Ambon</option>
                        <option value="Ternate">Ternate</option>
                        <option value="Banda Aceh">Banda Aceh</option>
                        <option value="Pangkal Pinang">Pangkal Pinang</option>
                        <option value="Tanjung Pinang">Tanjung Pinang</option>
                        <option value="Kendari">Kendari</option>
                        <option value="Gorontalo">Gorontalo</option>
                        <option value="Mamuju">Mamuju</option>
                        <option value="Sofifi">Sofifi</option>
                        <option value="Manokwari">Manokwari</option>
                        <option value="Merauke">Merauke</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="search-btn w-100" type="submit">
                        <i class="fas fa-search me-2"></i>Cari Lowongan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fas fa-briefcase stat-icon"></i>
                        <div class="stat-number">1,234</div>
                        <div class="stat-label">Lowongan Aktif</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fas fa-building stat-icon"></i>
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Perusahaan Terdaftar</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <i class="fas fa-users stat-icon"></i>
                        <div class="stat-number">10,000+</div>
                        <div class="stat-label">Pencari Kerja</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-jobs">
        <div class="container">
            <h2 class="text-center mb-5">Rekomendasi Untuk Anda</h2>
            <div class="row">
                <?php if (empty($rekomendasi)): ?>
                    <div class="col-12 text-center">
                        <p>Tidak ada rekomendasi pekerjaan yang cocok dengan profil Anda saat ini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($rekomendasi as $job): ?>
                        <div class="col-md-6">
                            <div class="job-card">
                                <h3 class="job-title"><?php echo htmlspecialchars($job['jenis_pekerjaan']); ?></h3>
                                <div class="company-name"><?php echo htmlspecialchars($job['nama_perusahaan']); ?></div>
                                <div class="job-details">
                                    <div class="job-detail">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($job['alamat']); ?></span>
                                    </div>
                                    <div class="job-detail">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span><?php echo htmlspecialchars($job['gaji']); ?></span>
                                    </div>
                                    <div class="job-detail">
                                        <i class="fas fa-user"></i>
                                        <span>Umur: <?php echo htmlspecialchars($job['umur']); ?></span>
                                    </div>
                                </div>
                                <div class="job-tags">
                                    <span class="job-tag"><?php echo htmlspecialchars($job['jenis_pekerjaan']); ?></span>
                                    <?php if (!empty($job['tunjangan'])): ?>
                                        <span class="job-tag">Tunjangan</span>
                                    <?php endif; ?>
                                </div>
                                <div class="job-desc">
                                    <?php echo htmlspecialchars(mb_strimwidth($job['deskripsi'], 0, 120, '...')); ?>
                                </div>
                                <a href="detail_lowongan.php?id=<?php echo $job['id']; ?>" class="btn btn-success mt-3">Lihat Detail</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="company-logos">
        <div class="container">
            <h3 class="text-center mb-4">Perusahaan Partner Kami</h3>
            <div class="row g-4 justify-content-center">
                <div class="col-6 col-md-3 col-lg-2">
                    <img src="https://storage.googleapis.com/a1aa/image/Ow20zUoX2n6wApOMJA9Pe6Eo8kpMnhfJIKGsrvlH4EGUWPCUA.jpg" 
                         alt="Telkom Indonesia Logo" 
                         class="img-fluid company-logo">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <img src="https://storage.googleapis.com/a1aa/image/esL5cWRmpnyrFS1wcexaw9Zw5xFJ5xjjmPdwbjjggIbrWPCUA.jpg" 
                         alt="Gojek Logo" 
                         class="img-fluid company-logo">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <img src="https://storage.googleapis.com/a1aa/image/GKgsLgaZWb5YKtnwiMK1PKEKs638dfpWfSGI0ckcKgcVWPCUA.jpg" 
                         alt="Tokopedia Logo" 
                         class="img-fluid company-logo">
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <img src="https://storage.googleapis.com/a1aa/image/FKQAkKBTq1qaD9CXN577fe4xHM2lJ7e5CR92IvOwcaIAteIQB.jpg" 
                         alt="Bukalapak Logo" 
                         class="img-fluid company-logo">
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Tentang GaweYuk</h5>
                    <ul class="list-unstyled">
                        <li><a href="tentang_kami.php">Tentang kami</a></li>
                        <li><a href="#">Kirim saran</a></li>
                        <li><a href="#">Partner kami</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Pencari kerja</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Cari pekerjaan</a></li>
                        <li><a href="#">Testimoni</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Perusahaan</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Posting lowongan</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Media Sosial</h5>
                    <ul class="list-unstyled">
                        <li><a href="#"><i class="fab fa-facebook me-2"></i>Facebook</a></li>
                        <li><a href="#"><i class="fab fa-instagram me-2"></i>Instagram</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("mySidebar");
            sidebar.classList.toggle("active-sidebar");
        }

        function logout() {
            window.location.href = "logout.php";
        }
    </script>
</body>
</html>
 
