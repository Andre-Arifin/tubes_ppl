<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - GaweYuk</title>
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
        .faq-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }
        .faq-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .faq-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2E7D32;
            margin-bottom: 16px;
        }
        .faq-subtitle {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        .faq-section {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 32px;
            margin-bottom: 32px;
        }
        .faq-section-title {
            color: #2E7D32;
            font-size: 1.5rem;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 2px solid #2E7D32;
        }
        .faq-item {
            margin-bottom: 24px;
            border-bottom: 1px solid #eee;
            padding-bottom: 24px;
        }
        .faq-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .faq-question {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 12px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-question i {
            transition: transform 0.3s;
        }
        .faq-question.active i {
            transform: rotate(180deg);
        }
        .faq-answer {
            color: #666;
            line-height: 1.6;
            display: none;
            padding: 12px 0;
        }
        .faq-answer.show {
            display: block;
        }
        .search-box {
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 16px 24px;
            border: 2px solid #ddd;
            border-radius: 30px;
            font-size: 1rem;
            transition: 0.3s;
        }
        .search-box input:focus {
            border-color: #2E7D32;
            box-shadow: 0 0 0 0.2rem rgba(46,125,50,0.25);
            outline: none;
        }
        .search-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        .contact-section {
            text-align: center;
            margin-top: 40px;
            padding: 40px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }
        .contact-title {
            color: #2E7D32;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        .contact-text {
            color: #666;
            margin-bottom: 24px;
        }
        .contact-btn {
            background: #2E7D32;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .contact-btn:hover {
            background: #1976d2;
            color: #fff;
            transform: translateY(-2px);
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
                    <li class="nav-item"><a class="nav-link" href="tentang_kami.php">Tentang Kami</a></li>
                    <li class="nav-item"><a class="nav-link" href="lowongan.php">Lowongan Kerja</a></li>
                    <li class="nav-item"><a class="nav-link active" href="faq.php">FAQ</a></li>
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

    <div class="faq-container">
        <div class="faq-header">
            <h1 class="faq-title">Frequently Asked Questions</h1>
            <p class="faq-subtitle">Temukan jawaban untuk pertanyaan-pertanyaan umum seputar GaweYuk dan pencarian kerja</p>
        </div>

        <div class="search-box">
            <input type="text" id="faqSearch" placeholder="Cari pertanyaan...">
            <i class="fas fa-search"></i>
        </div>

        <div class="faq-section">
            <h2 class="faq-section-title"><i class="fas fa-user me-2"></i>Akun & Profil</h2>
            
            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara membuat akun di GaweYuk?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Untuk membuat akun di GaweYuk, ikuti langkah-langkah berikut:
                    1. Klik tombol "Daftar" di halaman utama
                    2. Isi formulir pendaftaran dengan data lengkap
                    3. Verifikasi email Anda
                    4. Lengkapi profil Anda dengan informasi yang diperlukan
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara mengubah foto profil?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Untuk mengubah foto profil:
                    1. Masuk ke halaman profil Anda
                    2. Klik tombol "Edit Profil"
                    3. Pilih "Ubah Foto Profil"
                    4. Upload foto yang diinginkan (format: JPG, JPEG, atau PNG)
                    5. Klik "Simpan Perubahan"
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara mengubah password?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Untuk mengubah password:
                    1. Masuk ke halaman profil
                    2. Klik "Edit Profil"
                    3. Masukkan password lama
                    4. Masukkan password baru
                    5. Konfirmasi password baru
                    6. Klik "Simpan Perubahan"
                </div>
            </div>
        </div>

        <div class="faq-section">
            <h2 class="faq-section-title"><i class="fas fa-briefcase me-2"></i>Pencarian & Lamaran Kerja</h2>
            
            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara mencari lowongan kerja?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Untuk mencari lowongan kerja:
                    1. Masuk ke halaman "Lowongan Kerja"
                    2. Gunakan filter untuk mempersempit pencarian (lokasi, jenis pekerjaan, dll)
                    3. Ketik kata kunci di kolom pencarian
                    4. Klik lowongan yang menarik untuk melihat detail
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara melamar pekerjaan?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Untuk melamar pekerjaan:
                    1. Pastikan profil Anda sudah lengkap
                    2. Buka detail lowongan yang diinginkan
                    3. Klik tombol "Lamar Sekarang"
                    4. Isi formulir lamaran
                    5. Upload CV dan dokumen pendukung
                    6. Klik "Kirim Lamaran"
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara melacak status lamaran?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Untuk melacak status lamaran:
                    1. Masuk ke dashboard Anda
                    2. Klik menu "Lamaran Saya"
                    3. Lihat status lamaran di setiap lowongan
                    4. Anda akan mendapat notifikasi jika ada perubahan status
                </div>
            </div>
        </div>

        <div class="faq-section">
            <h2 class="faq-section-title"><i class="fas fa-building me-2"></i>Perusahaan & Perekrut</h2>
            
            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara perusahaan memposting lowongan?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Untuk memposting lowongan:
                    1. Daftar sebagai perusahaan
                    2. Verifikasi akun perusahaan
                    3. Klik "Posting Pekerjaan"
                    4. Isi detail lowongan
                    5. Upload logo perusahaan
                    6. Klik "Publikasikan"
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara mengelola lamaran yang masuk?
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Untuk mengelola lamaran:
                    1. Masuk ke dashboard perusahaan
                    2. Klik "Lamaran Masuk"
                    3. Filter lamaran berdasarkan status
                    4. Klik lamaran untuk melihat detail
                    5. Update status lamaran
                    6. Kirim pesan ke pelamar
                </div>
            </div>
        </div>

        <div class="contact-section">
            <h3 class="contact-title">Masih punya pertanyaan?</h3>
            <p class="contact-text">Jika Anda tidak menemukan jawaban yang Anda cari, silakan hubungi tim dukungan kami.</p>
            <a href="kirim_masukan.php" class="contact-btn">
                <i class="fas fa-envelope me-2"></i>Hubungi Kami
            </a>
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

        // FAQ Toggle
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const icon = question.querySelector('i');
                
                question.classList.toggle('active');
                answer.classList.toggle('show');
            });
        });

        // FAQ Search
        document.getElementById('faqSearch').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                
                if (question.includes(searchText) || answer.includes(searchText)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 
