<?php 
// $koneksi = mysqli_connect("osl.h.filess.io","first_tobaccobat","hehehe123","first_tobaccobat","3307");
$koneksi = mysqli_connect("localhost","root","","db_community","3306");
 
// Check connection
if (mysqli_connect_errno()){
	echo "Koneksi database gagal : " . mysqli_connect_error();
}
 
?>