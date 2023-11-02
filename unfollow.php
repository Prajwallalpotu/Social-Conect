<?php
session_start();
include("php/config.php");

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["userId"])) {
    if (isset($_SESSION['id'])) {
        $followerId = $_SESSION['id']; // The currently logged-in user's ID
        $followingId = $_GET["userId"]; // The ID of the user being unfollowed

        // Delete the relationship from the user_following table
        $deleteQuery = "DELETE FROM user_following WHERE follower_id = $followerId AND following_id = $followingId";

        if ($con->query($deleteQuery)) {
            // Successfully removed the relationship
            echo "Unfollowed successfully.";
        } else {
            // Error removing the relationship
            echo "Error unfollowing the user.";
        }
    } else {
        // User not logged in
        echo "You must be logged in to unfollow users.";
    }
} else {
    // Invalid request
    echo "Invalid request.";
}

$con->close();
?>
