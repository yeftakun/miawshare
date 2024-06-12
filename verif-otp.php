<?php
// Include koneksi ke database
include 'koneksi.php';

// Pesan kesalahan
$errorMsg = '';

if(isset($_GET['username'])){
    $hehe = $_GET['username'];
}else{
    $hehe = '';
}

// Ambil data dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['username']) && isset($_POST['otp'])) {
        $verifUsrname = $_POST['username'];
        $verifOtp = $_POST['otp'];

        // Query untuk mendapatkan kode OTP dari database
        $queryOtp = "SELECT otp_code FROM otp WHERE user_name='$verifUsrname' ORDER BY id DESC LIMIT 1";
        $result = mysqli_query($koneksi, $queryOtp);

        if($result) {
            $row = mysqli_fetch_assoc($result);
            if($row) {
                $otpFromDB = $row['otp_code'];

                // Validasi OTP
                if ($verifOtp == $otpFromDB) {
                    // Update status akun menjadi Aktif
                    $updateStatusQuery = "UPDATE users SET status='Aktif' WHERE user_name='$verifUsrname'";
                    mysqli_query($koneksi, $updateStatusQuery);
                    // Hapus username dan kode OTP dari tabel OTP yang sudah berhasil diverifikasi
                    $deleteOtpQuery = "DELETE FROM otp WHERE user_name='$verifUsrname'";
                    mysqli_query($koneksi, $deleteOtpQuery);
                    header("location:index.php?pesan=valsuccess");
                    exit(); // Optional: stop script execution after successful validation
                } else {
                    $errorMsg = "Kode OTP yang dimasukkan tidak valid.";
                    header("location:verif-otp.php?pesan=otpinvalid");
                }
            } else {
                $errorMsg = "Tidak ada data OTP untuk pengguna ini.";
                header("location:verif-otp.php?pesan=otpnotfound");
            }
        } else {
            $errorMsg = "Error saat mengambil data dari database.";
            header("location:verif-otp.php?pesan=dberror");
        }
    } else {
        $errorMsg = "Mohon lengkapi semua field.";
        header("location:verif-otp.php?pesan=emptyfield");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiawShare - Login</title>
    <link rel="stylesheet" href="styles/verify.css">
    <link rel="stylesheet" href="styles/alert.css">
    <link rel="icon" type="image/png" href="assets/logo/logo.png">
    <link rel="stylesheet" href="styles/hide-spin-button.css">
    <!-- Boxicons CSS -->
    <link flex href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
    <div class="login-container">
            <?php
            if(isset($_GET['pesan'])){
                if($_GET['pesan']=="otpinvalid"){
                    echo "<div class='alert'>Kode OTP yang dimasukkan tidak valid.</div>";
                }
                if($_GET['pesan']=="otpnotfound"){
                    echo "<div class='alert'>Tidak ada data OTP untuk pengguna ini.</div>";
                }
                if($_GET['pesan']=="dberror"){
                    echo "<div class='alert'>Error saat mengambil data dari database.</div>";
                }
                if($_GET['pesan']=="emptyfield"){
                    echo "<div class='alert'>Mohon lengkapi semua field.</div>";
                }
                if($_GET['pesan']=="registered"){
                    echo "<div class='done'>OTP terkirim di Telegram, berlaku 3 menit</div>";
                }
            }
            ?>
        <form class="login-form" id="loginForm" method="POST">
            <div class="logo_items flex">
                <span class="nav_image">
                    <img src="assets/logo/logo.png" alt="logo_img" />
                </span>
                <span class="logo_name">MiawShare</span>
            </div>
            <!-- <div id="successMessage" class="notification success">Registrasi berhasil!</div>
            <div id="errorMessage" class="notification error">Registrasi gagal. Silakan coba lagi.</div> -->
            <h2>Verifikasi</h2> 
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $hehe; ?>" required>
            </div>
            <div class="form-group password-group">
                <label for="otp">OTP</label>
                <input type="number" id="otp" name="otp" required>
                <!-- <button type="button" id="togglePassword" class="toggle-password">Show</button> -->
            </div>
            <button type="submit">Verifikasi</button>
            <!-- Tombol Daftar -->
            <br><p id="mendaftar">Sudah selesai? <a id="regist" href="index.php">Kembali</a></p>
        </form>
    </div>
    <!-- <script src="script/login.js"></script> -->
    <script src="script/alert-time.js"></script>
</body>
</html>