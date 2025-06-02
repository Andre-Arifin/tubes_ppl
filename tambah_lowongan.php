
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
    <title>Tambah Lowongan - GaweYuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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

        .form-section {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-top: 30px;
        }

        .form-title {
            color: var(--primary-color);
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #eee;
            padding: 12px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .form-select {
            border: 2px solid #eee;
            padding: 12px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
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
            .form-section {
                padding: 20px;
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
                        <a class="nav-link" href="#">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="tambah_lowongan.php">Posting Pekerjaan</a>
                    </li>
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

    <div class="container">
        <div class="form-section">
            <h2 class="form-title">Posting Lowongan Baru</h2>
            <form action="proses_tambah_lowongan.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="nama_perusahaan">Nama Perusahaan</label>
                        <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="judul">Judul Lowongan</label>
                        <input type="text" class="form-control" id="judul" name="judul" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="jenis_pekerjaan">Jenis Pekerjaan</label>
                        <select class="form-select" id="jenis_pekerjaan" name="jenis_pekerjaan" required>
                            <option value="">Pilih Jenis Pekerjaan</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="gaji">Rentang Gaji</label>
                        <input type="text" class="form-control" id="gaji" name="gaji" placeholder="Contoh: Rp 5.000.000 - Rp 7.000.000" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="lokasi">Lokasi</label>
                        <select class="form-select" id="lokasi" name="lokasi" required>
                            <option value="">Pilih Kota</option>
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="pengalaman">Pengalaman</label>
                        <select class="form-select" id="pengalaman" name="pengalaman" required>
                            <option value="">Pilih Pengalaman</option>
                            <option value="Fresh Graduate">Fresh Graduate</option>
                            <option value="1-3 tahun">1-3 tahun</option>
                            <option value="3-5 tahun">3-5 tahun</option>
                            <option value="5+ tahun">5+ tahun</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="deskripsi">Deskripsi Pekerjaan</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="kualifikasi">Kualifikasi</label>
                    <textarea class="form-control" id="kualifikasi" name="kualifikasi" rows="5" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="email">Email Kontak</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="telepon">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="telepon" name="telepon" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="gambar">Logo Perusahaan</label>
                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="alamat">Alamat Perusahaan</label>
                    <input type="text" class="form-control" id="alamat" name="alamat" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="tunjangan">Tunjangan</label>
                    <input type="text" class="form-control" id="tunjangan" name="tunjangan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="umur">Batas Umur</label>
                    <input type="text" class="form-control" id="umur" name="umur" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="whatsapp">WhatsApp</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="instagram">Instagram</label>
                    <input type="text" class="form-control" id="instagram" name="instagram">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="facebook">Facebook</label>
                    <input type="text" class="form-control" id="facebook" name="facebook">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="website">Website</label>
                    <input type="text" class="form-control" id="website" name="website">
                </div>
            
                
                <div class="text-end">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-paper-plane me-2"></i>Posting Lowongan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Tentang GaweYuk</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Tentang kami</a></li>
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
            alert("Anda telah logout!");
            window.location.href = "index.php";
        }
    </script>
</body>
</html>
