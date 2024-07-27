<?php
session_start();
include '../../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == UPLOAD_ERR_OK) {
        $zipFile = $_FILES['backup_file']['tmp_name'];
        $userId = $_SESSION['user_id'];
        $extractPath = '../../storage/posting/';
        
        // Ensure the extraction path exists and is writable
        if (!is_dir($extractPath) && !mkdir($extractPath, 0755, true)) {
            $message = 'restore-failed&error=extract-path-creation-failed';
            header("Location: backup_restore.php?pesan=$message");
            exit;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();

            // Debugging: Check extracted files
            $extractedFiles = scandir($extractPath);
            error_log('Extracted files: ' . print_r($extractedFiles, true));

            // Check if user-db.json is in the correct directory
            $jsonFile = $extractPath . 'user-db.json';
            error_log('Checking JSON file at: ' . $jsonFile);

            if (file_exists($jsonFile)) {
                $posts = json_decode(file_get_contents($jsonFile), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    foreach ($posts as $post) {
                        $postImgPath = $post['post_img_path'];
                        $postTitle = mysqli_real_escape_string($koneksi, $post['post_title']);
                        $postDescription = mysqli_real_escape_string($koneksi, $post['post_description']);
                        $postLink = mysqli_real_escape_string($koneksi, $post['post_link']);
                        $createIn = $post['create_in'];

                        $newFilePath = $extractPath . basename($postImgPath);
                        $fileInfo = pathinfo($newFilePath);
                        $baseName = $fileInfo['filename'];
                        $extension = isset($fileInfo['extension']) ? '.' . $fileInfo['extension'] : '';
                        $counter = 1;

                        while (file_exists($newFilePath)) {
                            $newFilePath = $extractPath . $baseName . '_' . $counter . $extension;
                            $postImgPath = $baseName . '_' . $counter . $extension;
                            $counter++;
                        }

                        $oldPath = $extractPath . 'image/' . basename($post['post_img_path']);
                        if (file_exists($oldPath)) {
                            rename($oldPath, $newFilePath);
                        } else {
                            error_log('Old image path not found: ' . $oldPath);
                        }

                        $query = "INSERT INTO posts (user_id, post_img_path, post_title, post_description, post_link, create_in) 
                                  VALUES ('$userId', '$postImgPath', '$postTitle', '$postDescription', '$postLink', '$createIn')";
                        mysqli_query($koneksi, $query);
                    }

                    unlink($jsonFile);
                    $message = 'restore-success';
                } else {
                    $message = 'restore-failed&error=json-decode-error';
                }
            } else {
                $message = 'restore-failed&error=json-not-found';
            }
        } else {
            $message = 'restore-failed&error=zip-open-failed';
        }
    } else {
        $error = $_FILES['backup_file']['error'];
        $message = "restore-failed&error=file-upload-failed&file-error=$error";
    }

    header("Location: ../backup_restore.php?pesan=$message");
    exit;
}
?>