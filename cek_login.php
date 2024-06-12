<?php 
// mengaktifkan session pada php
session_start();

// menghubungkan php dengan koneksi database
include 'koneksi.php';

// menangkap data yang dikirim dari form login
$username = $_POST['user_name'];
$password = $_POST['password'];

$username = mysqli_real_escape_string($koneksi, $username);
$password = mysqli_real_escape_string($koneksi, $password);


// menyeleksi data user dengan username dan password yang sesuai
$login = mysqli_query($koneksi,"select * from users where user_name='$username' and password='$password'");
// menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);

// dapatkan informasi user
$data = mysqli_fetch_assoc($login);
$user_id = $data['user_id'];
// $user_name = $data['user_name']; // sudah diambil
$name = $data['name'];
$user_profile_path = $data['user_profile_path'];
$user_bio = $data['user_bio'];
$level_id = $data['level_id'];
$password = $data['password'];
$status = $data['status'];
$tele_chat_id = $data['tele_chat_id'];

// cek apakah username dan password di temukan pada database
if($cek > 0){
	
	// if($data['status'] == 'Nonaktif'){
	// 	header("location:index.php?pesan=unvalidated");
	// 	exit();
	// } else {
	// 	// cek jika user login sebagai admin
	// 	if($data['level_id']==1){

	// 		// masukan data ke dalam session
	// 		$_SESSION['user_id'] = $user_id;
	// 		// $_SESSION['user_name'] = $username; // sudah diambil
	// 		$_SESSION['name'] = $name;
	// 		$_SESSION['user_profile_path'] = $user_profile_path;
	// 		$_SESSION['user_bio'] = $user_bio;
	// 		// $_SESSION['level_id'] = $level_id; // sudah diambil
	// 		$_SESSION['password'] = $password;
	// 		$_SESSION['status'] = $status;
	// 		$_SESSION['tele_chat_id'] = $tele_chat_id;
	
	// 		// buat session login dan username
	// 		$_SESSION['user_name'] = $username;
	// 		$_SESSION['level_id'] = 1;
	// 		// alihkan ke halaman beranda admin
	// 		header("location:pages/beranda_admin.php");
	
	// 	// cek jika user login sebagai user
	// 	}else if($data['level_id']==2){

	// 		// masukan data ke dalam session
	// 		$_SESSION['user_id'] = $user_id;
	// 		// $_SESSION['user_name'] = $username; // sudah diambil
	// 		$_SESSION['name'] = $name;
	// 		$_SESSION['user_profile_path'] = $user_profile_path;
	// 		$_SESSION['user_bio'] = $user_bio;
	// 		// $_SESSION['level_id'] = $level_id; // sudah diambil
	// 		$_SESSION['password'] = $password;
	// 		$_SESSION['status'] = $status;
	// 		$_SESSION['tele_chat_id'] = $tele_chat_id;
			
	// 		// buat session login dan username
	// 		$_SESSION['user_name'] = $username;
	// 		$_SESSION['level_id'] = 2;
	// 		// alihkan ke halaman beranda user
	// 		header("location:pages/beranda_user.php");
		
	// 	}else{
	
	// 		// alihkan ke halaman login kembali
	// 		header("location:index.php?pesan=gagal");
	// 	}
	// }
	
	if($data['status'] == 'Nonaktif'){
		header("location:index.php?pesan=unvalidated");
		exit();
	} else {
		// masukan data ke dalam session
		$_SESSION['user_id'] = $user_id;
		// $_SESSION['user_name'] = $username; // sudah diambil
		$_SESSION['name'] = $name;
		$_SESSION['user_profile_path'] = $user_profile_path;
		$_SESSION['user_bio'] = $user_bio;
		// $_SESSION['level_id'] = $level_id; // sudah diambil
		$_SESSION['password'] = $password;
		$_SESSION['status'] = $status;
		$_SESSION['tele_chat_id'] = $tele_chat_id;

		// buat session login dan username
		$_SESSION['user_name'] = $username;
		$_SESSION['level_id'] = $level_id;
		// alihkan ke halaman beranda
		header("location:pages/beranda.php");
	}
}else{
	header("location:index.php?pesan=gagal");
}
?>