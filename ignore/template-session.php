<?php

// KEADAAN HALAMAN PROFIL
if(isset($_SESSION['user_id'])) {
    if(isset($_SESSION['user_name']) && $_SESSION['user_name'] !== $user_name) {
        // profil user lain
    } else {
        // profil pribadi
    }
} else {
    // profil belum login
}

// KEADAAN HALAMAN BERANDA
if(isset($_SESSION['level_id'])) {
    if($_SESSION['level_id'] == 1) {
        // beranda admin
    } elseif($_SESSION['level_id'] == 2) {
        // beranda user
    }
} else {
    // beranda belum login
}

if(isset($_SESSION['level_id'])) {
    if($_SESSION['level_id'] == 1) {
        // beranda admin
    } else {
        header("location:error/deniedpage.php");
    }
} else {
    header("location:error/index.php?pesan=needlogin");
}

if(isset($_SESSION['level_id'])) {
    if($_SESSION['user_name'] == $row['user_name']) {
        // tampilan gambar yang diupload user sendiri
    } else {
        // tampilan gambar yang diupload user lain
    }
} else {
    // tampilan gambar yang diupload user lain (tapi belum login)
}

if(isset($_SESSION['level_id'])) {
    if($_SESSION['level_id'] == 1) {
        // beranda admin
        if(isset($_GET['page'])) {
            $page = $_GET['page'];
            if($page == "level") {
                // add data level
            } elseif($page == "otp") {
                // add data otp
            } elseif($page == "posts") {
                // add data posts
            } elseif($page == "users") {
                // add data users
            } else {
                header("location:../error/not_found.php");
            }
        }
    } else {
        header("location:../error/deniedpage.php");
    }
} else {
    header("location:../error/index.php?pesan=needlogin");
}

?>