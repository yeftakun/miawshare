<?php
session_start();
include '../koneksi.php';

// Pastikan ada kata kunci pencarian yang dikirimkan melalui URL
if(isset($_GET['search'])) {
    // Simpan kata kunci pencarian dalam variabel
    $search = $_GET['search'];
    
    // Ambil nilai order dari parameter GET
    $order = isset($_GET['order']) ? $_GET['order'] : 'newest'; // Default order is 'newest'

    // Buat query untuk mencari data gambar yang memiliki informasi terkait
    $query = "SELECT users.user_name, users.name, users.user_profile_path, posts.post_id, posts.post_img_path, posts.post_title, posts.create_in 
            FROM posts 
            JOIN users ON posts.user_id = users.user_id 
            WHERE posts.post_title LIKE '%$search%' OR 
                    posts.post_description LIKE '%$search%' OR 
                    users.user_name LIKE '%$search%' OR 
                    users.name LIKE '%$search%'";

    // Tambahkan ORDER BY berdasarkan nilai $order
    if ($order == 'newest') {
        $query .= " ORDER BY posts.create_in DESC";
    } elseif ($order == 'oldest') {
        $query .= " ORDER BY posts.create_in ASC";
    } elseif ($order == 'name_asc') {
        $query .= " ORDER BY posts.post_title ASC";
    } elseif ($order == 'name_desc') {
        $query .= " ORDER BY posts.post_title DESC";
    }
    
    // Eksekusi query
    $result = mysqli_query($koneksi, $query);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Result</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css">
    <link rel="stylesheet" type="text/css" href="../styles/alert.css">
    <link rel="icon" type="image/png" href="../assets/ico/HitoriGotou.ico">
</head>
<body>

<header>
    <div class="logo">
        <img src="../assets/ico/HitoriGotou.ico" alt="logo" width="50">
    </div>
    <div class="home-search-bar">
        <form action="search_result.php" method="GET">
            <input type="text" name="search" id="searchInput" placeholder="Judul / #tag / Nama pengunggah">
            <input type="submit" value="Search">
        </form>
    </div>
    <div class="nav-to">
        <p><a href="beranda.php">Beranda</a></p>
    </div>
    <div class="nav-to">
        <p><a href="admin_panel.php">Admin Panel</a></p>
    </div>
    <div class="profile-pic">
        <a href="profile.php?user_name=<?php echo $_SESSION['user_name']; ?>">
            <?php
            echo '<img src="../storage/profile/' . $_SESSION['user_profile_path'] . '" alt="' . $_SESSION['user_profile_path'] . '" width="50px"';
            ?>
        </a>
    </div>
    <div class="logout">
        <a href="../logout.php">LOGOUT</a>
    </div>
</header>

<!-- Form untuk memilih kriteria pencarian dan pengurutan -->
<div class="container">
    <form id="searchForm" method="GET">
        <label for="order">Order By:</label>
        <select name="order" id="order" onchange="this.form.submit()">
            <option value="newest" <?php if($order == 'newest') echo 'selected'; ?>>Newest First</option>
            <option value="oldest" <?php if($order == 'oldest') echo 'selected'; ?>>Oldest First</option>
            <option value="name_asc" <?php if($order == 'name_asc') echo 'selected'; ?>>Title (Ascending)</option>
            <option value="name_desc" <?php if($order == 'name_desc') echo 'selected'; ?>>Title (Descending)</option>
        </select>
        <input type="hidden" name="search" value="<?php echo $search; ?>">
    </form>
</div>

<div class="container">
    <h2>Search Result for "<?php echo $search; ?>"</h2>

    <?php
    if (mysqli_num_rows($result) > 0) {
        // Tampilkan hasil pencarian
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="box">
                <!-- Tampilkan gambar dari direktori "../storage/posting/" -->
                <div class="img-preview">
                    <a href="<?php echo 'view_img.php?post_id=' . $row['post_id']; ?>">
                        <img src="../storage/posting/<?php echo $row['post_img_path']; ?>" alt="<?php echo $row['post_title']; ?>">
                        <p><?php echo $row['post_title']; ?></p>
                    </a>
                </div>
                <div class="posted-by">
                    <!-- Tampilkan foto profil dari direktori "../storage/profile/" -->
                    <a href="<?php echo 'profile.php?user_name=' . $row['user_name']; ?>">
                        <img src="../storage/profile/<?php echo $row['user_profile_path']; ?>" alt="pp" width="50px">
                        <p><?php echo $row['name']; ?></p>
                    </a>
                </div>
            </div>
            <?php
        }
    } else {
        // Jika tidak ada hasil pencarian
        echo "No results found.";
    }
    ?>

</div>

</body>
</html>

<?php
    // Bebaskan hasil query
    mysqli_free_result($result);

    // Tutup koneksi ke database
    mysqli_close($koneksi);
} else {
    // Jika tidak ada kata kunci pencarian yang dikirimkan
    header("Location: beranda.php");
    exit();
}
?>
