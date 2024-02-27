<?php

session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
}

$id = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newAge = $_POST['age'];
    $bio = $_POST['bio'];
    $link = $_POST['link'];

    // Update basic profile information
    $updateQuery = "UPDATE users SET Username='$newUsername', Email='$newEmail', Age=$newAge WHERE Id=$id";
    mysqli_query($con, $updateQuery);

    // Check if user_details record exists
    $checkQuery = "SELECT * FROM user_details WHERE user_id = $id";
    $result = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        // If record exists, update the data
        $updateAdditionalQuery = "UPDATE user_details SET Bio='$bio', Link='$link' WHERE user_id=$id";
        mysqli_query($con, $updateAdditionalQuery);
    } else {
        // If record doesn't exist, insert a new record
        $insertQuery = "INSERT INTO user_details (user_id, Bio, Link) VALUES ($id, '$bio', '$link')";
        mysqli_query($con, $insertQuery);
    }

    // Handle profile picture upload
    if ($_FILES['profile-pic']['size'] > 0) {
        $targetDir = "profile_pics/";
        $targetFile = $targetDir . basename($_FILES['profile-pic']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES['profile-pic']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
            echo "File is not an image.";
        }

        // Check if the file already exists
        if (file_exists($targetFile)) {
            $uploadOk = 0;
            echo "Sorry, file already exists.";
        }

        // Check file size
        if ($_FILES['profile-pic']['size'] > 500000) {
            $uploadOk = 0;
            echo "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" &&
            $imageFileType != "gif"
        ) {
            $uploadOk = 0;
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES['profile-pic']['tmp_name'], $targetFile)) {
                echo "The file " . htmlspecialchars(basename($_FILES['profile-pic']['name'])) . " has been uploaded.";
                // Update the profile image in the database
                $checkImageQuery = "SELECT Profile_Img FROM user_details WHERE user_id=$id";
                $result = mysqli_query($con, $checkImageQuery);
                $row = mysqli_fetch_assoc($result);

                if ($row['Profile_Img'] === NULL) {
                    // If the current value is NULL, insert the new image
                    $updateImageQuery = "UPDATE user_details SET Profile_Img='$targetFile' WHERE user_id=$id";
                } else {
                    // If the current value is not NULL, update the existing image
                    $updateImageQuery = "UPDATE user_details SET Profile_Img='$targetFile' WHERE user_id=$id";
                }

                mysqli_query($con, $updateImageQuery);
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Redirect to home page after updating profile
    header("Location: home.php");
}
?>
