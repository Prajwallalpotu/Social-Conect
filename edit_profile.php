<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
}

$id = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update basic profile information
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $newAge = $_POST['age'];
    
    $updateQuery = "UPDATE users SET Username='$newUsername', Email='$newEmail', Age=$newAge WHERE Id=$id";
    mysqli_query($con, $updateQuery);

    // Update or insert additional profile information
    $bio = $_POST['bio'];
    $link = $_POST['link'];
    $updateAdditionalQuery = "INSERT INTO user_details (Id, Bio, Link) VALUES ($id, '$bio', '$link') ON DUPLICATE KEY UPDATE Bio='$bio', Link='$link'";
    mysqli_query($con, $updateAdditionalQuery);

    // Handle file upload
    if ($_FILES['profile-pic']['name'] != "") {
        $profile_pic = $_FILES['profile-pic']['name'];
        $target = "path_to_upload_directory/" . $profile_pic;
        move_uploaded_file($_FILES['profile-pic']['tmp_name'], $target);
        
        // Update the profile image in the database
        mysqli_query($con, "UPDATE user_details SET Profile_Img='$profile_pic' WHERE Id=$id");
    }

    if ($edit_query) {
        echo "<div class='message'>
                <p>Profile Updated!</p>
            </div> <br>";
        echo "<a href='home.php'><button class='btn'>Go Home</button>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css" type="text/css">
    <title>Edit Profile</title>
</head>

<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php">SOCIALConnect</a> </p>
        </div>

        <div class="right-links">
            <!-- <a href="home.php">Home</a> -->
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box form-box">
            <header>Edit Profile</header>
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
    <?php
        $id = $_SESSION['id'];
        $query = mysqli_query($con, "SELECT * FROM users WHERE Id = $id");
        while($result = mysqli_fetch_assoc($query)){
            $res_Uname = $result["Username"];
            $res_Email = $result["Email"];
            $res_Age = $result["Age"];
        }

        $queryDetails = mysqli_query($con, "SELECT * FROM user_details WHERE user_id = $id");
        while($resultDetails = mysqli_fetch_assoc($queryDetails)){
            $res_Bio = $resultDetails["Bio"];
            $res_Link = $resultDetails["Link"];
        }
    ?>
    <div class="field input">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $res_Uname ?>" required>
    </div>
    <div class="field input">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $res_Email ?>" required>
    </div>
    <div class="field input">
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" value="<?php echo $res_Age ?>" required>
    </div>
    <div class="field input">
        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" placeholder="Tell something about yourself..."><?php echo $res_Bio; ?></textarea>
    </div>
    <div class="field input">
        <label for="profile-pic">Profile Picture:</label>
        <input type="file" id="profile-pic" name="profile-pic">
    </div>
    <div class="field input">
        <label for="link">Portfolio Link:</label>
        <input type="text" id="link" name="link" placeholder="Your portfolio link" value="<?php echo $res_Link; ?>">
    </div>
    <button type="submit" class="btn" name="submit">Update Profile</button>
</form>

        </div>
    </div>
</body>

</html>
