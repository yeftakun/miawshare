<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
} else {
    $userId = $_SESSION['user_id'];
    $query = "SELECT post_id, post_img_path, post_title, post_description, post_link, create_in FROM posts WHERE user_id = '$userId'";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        die("Query error: " . mysqli_error($koneksi));
    }

    $posts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = [
            'post_id' => $row['post_id'],
            'post_img_path' => $row['post_img_path'],
            'post_title' => $row['post_title'],
            'post_description' => $row['post_description'],
            'post_link' => $row['post_link'],
            'create_in' => $row['create_in']
        ];
    }

    $jsonFile = '../../storage/posting/' . $_SESSION['user_name'] . '-user-db.json';
    file_put_contents($jsonFile, json_encode($posts));

    $zip = new ZipArchive();
    $zipFile = '../../storage/posting/miawshare-backuppost-' . $_SESSION['user_name'] . '.zip';

    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        foreach ($posts as $post) {
            $filePath = '../../storage/posting/' . $post['post_img_path'];
            if (file_exists($filePath)) {
                $zip->addFile($filePath, 'image/' . basename($filePath));
            }
        }
        $zip->addFile($jsonFile, 'user-db.json');
        $zip->close();

        // Menghapus file JSON sementara
        unlink($jsonFile);

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename=' . basename($zipFile));
        header('Content-Length: ' . filesize($zipFile));
        readfile($zipFile);

        // Menghapus file ZIP sementara
        unlink($zipFile);
    } else {
        echo 'Failed to create zip file.';
    }
}
?>
