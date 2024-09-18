<?php
// Include the database connection
include '../../koneksi.php';

// Start the session
session_start();

// Check if the user is allowed to update
if (!isset($_SESSION['level_id']) || $_SESSION['level_id'] != 1) {
    header('Location: ../error/deniedpage.php');
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $tokenBot = isset($_POST['tokenBot']) ? $_POST['tokenBot'] : '';
    $ownerChatId = isset($_POST['ownerChatId']) ? $_POST['ownerChatId'] : '';
    $hostMain = isset($_POST['hostMain']) ? $_POST['hostMain'] : '';
    $hostAPI = isset($_POST['hostAPI']) ? $_POST['hostAPI'] : '';
    $nsfwDetect = isset($_POST['nsfwDetect']) ? $_POST['nsfwDetect'] : '';
    $maxImageSize = isset($_POST['maxImageSize']) ? $_POST['maxImageSize'] : '';
    $maxProfileImageSize = isset($_POST['maxProfileImageSize']) ? $_POST['maxProfileImageSize'] : '';
    $limitBeranda = isset($_POST['limitBeranda']) ? $_POST['limitBeranda'] : '';

    // Prepare and bind the SQL query
    $sql = "UPDATE config SET 
                token_bot = ?, 
                owner_chat_id = ?, 
                host_main = ?, 
                host_api = ?, 
                nsfw_detect = ?, 
                img_size = ?, 
                profile_size = ?, 
                limit_beranda = ?";

    if ($stmt = $koneksi->prepare($sql)) {
        // Bind the parameters to the SQL query
        $stmt->bind_param(
            "ssssiiii",
            $tokenBot,
            $ownerChatId,
            $hostMain,
            $hostAPI,
            $nsfwDetect,
            $maxImageSize,
            $maxProfileImageSize,
            $limitBeranda
        );

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to a success page
            header('Location: ../admin_panel.php?pesan=reconfig');
        } else {
            // Handle query execution error
            echo "Error updating record: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        // Handle SQL preparation error
        echo "Error preparing statement: " . $koneksi->error;
    }

    // Close the database connection
    $koneksi->close();
}
?>
