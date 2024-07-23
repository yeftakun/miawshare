<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiawShare - Login</title>
    <meta name="description" content="Bagikan gambarmu di MiawShare!" />
    <meta property="og:title" content="MiawShare - Login" />
    <meta property="og:url" content="https://miawshare.my.id/" />
    <meta property="og:description" content="Daftar sekarang dan bagikan gambarmu!" />
    <meta property="og:image" content="https://miawshare.my.id/assets/logo/logo.png" />
    <link rel="stylesheet" href="styles/login.css">
    <link rel="stylesheet" href="styles/alert2.css">
    <link rel="icon" type="image/png" href="assets/logo/logo.png">
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>

    <div class="login-container">
        <form class="login-form" action="cek_login.php" method="post">
        <?php 
            if(isset($_GET['pesan'])){
                if($_GET['pesan']=="gagal"){
                    echo "<div class='alert'>Username dan Password tidak sesuai !</div>";
                }
            }
            if(isset($_GET['pesan'])){
                if($_GET['pesan']=="unvalidated"){
                    echo "<div class='alert'>Akun belum divalidasi oleh admin</div>";
                }
            }
            if(isset($_GET['pesan'])){
                if($_GET['pesan']=="valsuccess"){
                    echo "<div class='done'>Akun anda telah diverifikasi, silahkan login</div>";
                }
            }
            if(isset($_GET['pesan'])){
                if($_GET['pesan']=="needlogin"){
                    echo "<div class='done'>Login terlebih dahulu</div>";
                }
            }
        ?>
            <div class="logo_items flex">
                <span class="nav_image">
                    <a href="pages/beranda.php" style="color: inherit; text-decoration: none;"></a>
                    <img src="assets/logo/logo.png" alt="logo_img" />
                </span>
                <span class="logo_name"><a href="pages/beranda.php" style="color: inherit; text-decoration: none;">MiawShare</a></span>
            </div>
            <h2>Login</h2> 
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="user_name" class="form_login" placeholder="Username .." required="required">
            </div>
            <div class="form-group password-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form_login" placeholder="Password .." required>
                <button type="button" id="togglePassword" class="toggle-password">Show</button>
            </div>
            <button type="submit">Login</button>
            <!-- Tombol Daftar -->
            <br><p id="mendaftar">Tidak punya akun? <a id="regist" href="daftar.php">Daftar</a></p>
            <!-- Tombol Verifikasi -->
            <a href="verif-otp.php" id="verify">Verifikasi</a>
        </form>
    </div>
    <script src="script/login.js"></script>
    <script src="script/alert-time.js"></script>
</body>
</html>
