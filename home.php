<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: index.php");
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css" type="text/css">
    <title>Home</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="home.php">SOCIALConnect</a> </p>
        </div>

        <div class="right-links">

            <?php 
            
            $id = $_SESSION['id'];
            $query = mysqli_query($con,"SELECT*FROM users WHERE Id=$id");

            while($result = mysqli_fetch_assoc($query)){
                $res_Uname = $result['Username'];
                $res_Email = $result['Email'];
                $res_Age = $result['Age'];
                $res_id = $result['Id'];
            }
            
            echo "<a href='edit.php?Id=$res_id'>Change Profile</a>";
            ?>

            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>

        </div>
    </div>
    <main>

       <div class="main-box top">
          <div class="top">
            <div class="box">
                <p>Hello <b><?php echo $res_Uname ?></b>, Welcome</p>
            </div>
            <div class="box">
                <p>Your email is <b><?php echo $res_Email ?></b>.</p>
            </div>
          </div>
          <div class="bottom">
            <div class="box">
                <p>You are <b><?php echo $res_Age ?> years old</b>.</p> 
            </div>
          </div>
          <div class="bottom">
            <div class="box">
                <p>
                    Your Followings &nbsp; &nbsp; <span style="color: green; font-weight: 600;">
                        <?php
                            $id = $_SESSION['id'];
                            $query = mysqli_query($con, "SELECT COUNT(*) as count FROM user_following WHERE follower_id = $id");
                            $result = mysqli_fetch_assoc($query);
                            echo $result['count'];
                ?>
            </span>
                </p> 
            </div>
          </div>
       </div>

    </main>

    <section class="table_section">
    <div class="table_start">
        <table class="display_table" rules="cols">
            <tr class="table_heading">
                <th>User Name</th>
                <th>Follow</th>
            </tr>
            <?php
            include("php/config.php");

            $followerId = $_SESSION['id']; // The ID of the logged-in user

            $sql2 = "SELECT * FROM users";
            $result2 = $con->query($sql2);

            if ($result2->num_rows > 0) {
                while ($row = $result2->fetch_assoc()) {
                    $Uname = $row['Username'];
                    $userId = $row['Id'];

                    // Check if the user is already followed by the logged-in user
                    $checkQuery = "SELECT id FROM user_following WHERE follower_id = $followerId AND following_id = $userId";
                    $checkResult = $con->query($checkQuery);

                    if ($checkResult->num_rows > 0) {
                        // User is already followed
                        $isFollowed = 'true';
                        $buttonText = 'Unfollow';
                        $buttonColor = '#d26868'; 
                    } else {
                        // User is not followed
                        $isFollowed = 'false';
                        $buttonText = 'Follow';
                        $buttonColor = '#699053';
                    }

                    if ($Uname != $res_Uname) {
                        echo "<tr>
                              <td>$Uname</td>
                              <td><button class='follow-button' id='follow' data-userid='$userId' data-followed='$isFollowed' style='background-color: $buttonColor; border-radius: 5px; border: 0; padding: 5px 10px;'>$buttonText</button></td>
                          </tr>";
                    }
                }
            } else {
                echo "<tr><td colspan='2'>No users found!</td></tr>";
            }

            $con->close();
            ?>
        </table>
    </div>
</section>



<script>
    const followButtons = document.querySelectorAll('.follow-button');

    followButtons.forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-userid');
            const isFollowed = this.getAttribute('data-followed') === 'true';

            if (isFollowed) {
                // Unfollow
                this.innerHTML = 'Follow';
                this.setAttribute('data-followed', 'false');
                this.classList.remove('followed');
                console.log('follower_id:', userId);

                // Send AJAX request to remove the relationship from the database
                fetch(`unfollow.php?userId=${userId}`)
                    .then(response => {
                        if (response.ok) {
                            alert('You have unfollowed this user successfully.');
                        } else {
                            alert('Error unfollowing the user. Please try again later.');
                        }
                    });
            } else {
                // Follow
                this.innerHTML = 'Unfollow';
                this.setAttribute('data-followed', 'true');
                this.classList.add('followed');

                // Send AJAX request to add the relationship to the database
                    console.log('follower_id:', userId);
                fetch(`follow.php?userId=${userId}`)
                    .then(response => {
                        if (response.ok) {

                            alert('You are now following this user.');

                        } else {
                            alert('Error following the user. Please try again later.');
                        }
                    });
            }
        });
    });
</script>

</body>
</html>