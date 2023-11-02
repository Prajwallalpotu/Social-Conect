<?php
session_start();
include("php/config.php");

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["userId"])) {
    if (isset($_SESSION['id'])) {
        $followerId = $_SESSION['id']; // The currently logged-in user's ID
        $followingId = $_GET["userId"]; // The ID of the user being followed

        // Check if the relationship doesn't already exist to avoid duplicates
        $checkQuery = "SELECT * FROM user_following WHERE follower_id = $followerId AND following_id = $followingId";
        $checkResult = $con->query($checkQuery);

        if ($checkResult->num_rows === 0) {
            // Insert the relationship into the user_following table
            $insertQuery = "INSERT INTO user_following (follower_id, following_id) VALUES ($followerId, $followingId)";
            if ($con->query($insertQuery)) {
                // Successfully added the relationship
                echo "Followed successfully.";
            } else {
                // Error adding the relationship
                echo "Error following the user.";
            }
        } else {
            // Relationship already exists
            echo "You are already following this user.";
        }
    } else {
        // User not logged in
        echo "You must be logged in to follow users.";
    }
} else {
    // Invalid request
    echo "Invalid request.";
}

$con->close();
?>

