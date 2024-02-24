<?php
session_start();
include("php/config.php");

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["followingId"])) {
    if (isset($_SESSION['id'])) {
        $followerId = $_SESSION['id'];
        $followingId = $_GET["followingId"];

        // Delete the relationship from the user_following table
        $deleteQuery = "DELETE FROM user_following WHERE follower_id = $followerId AND following_id = $followingId";

        if ($con->query($deleteQuery)) {
            // Successfully removed the relationship
            echo "success";
        } else {
            // Error removing the relationship
            echo "error";
        }
    } else {
        // User not logged in
        echo "not_logged_in";
    }
} else {
    // Invalid request
    echo "invalid_request";
}

$con->close();
?>
