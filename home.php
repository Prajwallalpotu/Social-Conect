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
    <link rel="stylesheet" href="index3.css" type="text/css">
    <title>Home</title>
    <style>
    
</style>

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
                $res_Gender = $result['Gender'];
            }
            
            ?>

            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>

        </div>
    </div>
    <main>

       <div class="main1 top1">
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
                            Your Following &nbsp; &nbsp;
                            <button onclick="window.location.href='following_list.php'" style="cursor:pointer; padding:2px 10px;">
                                <span style="color: green; font-weight: 600;">
                                    <?php
                                        $id = $_SESSION['id'];
                                        $query = mysqli_query($con, "SELECT COUNT(*) as count FROM user_following WHERE follower_id = $id");
                                        $result = mysqli_fetch_assoc($query);
                                        echo $result['count'];
                                    ?>
                                </span>
                            </button>
                        </p>
                    </div>
                </div>
                <div class="bottom">
                    <div class="box">
                    <p>
                        Your Followers &nbsp; &nbsp;
                        <button onclick="window.location.href='followers_list.php'" style="cursor:pointer; padding:2px 10px;">
                            <span style="color: green; font-weight: 600;">
                                <?php
                                    $id = $_SESSION['id'];
                                    $query = mysqli_query($con, "SELECT COUNT(*) as count FROM user_following WHERE following_id = $id");
                                    $result = mysqli_fetch_assoc($query);
                                    echo $result['count'];
                                ?>
                            </span>
                        </button>
                    </p>
                    </div>
                </div>
            </div>
            <div class="profile_description box">
    <a href='edit_profile.php'>
        <box-icon name='edit'></box-icon>
    </a>
    <?php
        $id = $_SESSION['id'];
        $query = mysqli_query($con, "SELECT * FROM user_details WHERE user_id=$id");

        if ($query) {
            $userData = mysqli_fetch_assoc($query);
            $Gender = $userData['Gender'];
            $ProfileImg = $userData['Profile_Img'];
            $Bio = $userData['Bio'];
            $Link = $userData['Link'];

            echo '<div class="profile_img">';
            if ($ProfileImg && file_exists($ProfileImg)) {
                echo '<img src="' . $ProfileImg . '">';
            } else {
                if ($Gender == "Male" || $Gender == "") {
                    echo '<img src="/mini_project/img/profile.jpeg">';
                } 
                if($Gender == "Female"){
                    echo '<img src="/mini_project/img/female_profile.png">';
                }
            }
            echo '</div>';

            echo '<div class="profile_des">';
            echo '<p>' . $Bio . '</p>';
            echo '</div>';

            if (!empty($Link)) {
                echo "<div class='links'>";
                echo "<a href='$Link'><button class='btn'>Portfolio</button></a>";
                echo "</div>";
            } else {
                echo ""; 
            }
        } else {
            echo "Error in the query: " . mysqli_error($con);
        }
    ?>
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

                $followerId = $_SESSION['id'];

                $sql = "SELECT u.Id, u.Username, uf.id AS following_id
                        FROM users u
                        LEFT JOIN user_following uf ON u.Id = uf.following_id AND uf.follower_id = $followerId
                        WHERE u.Id <> $followerId AND uf.id IS NULL";


                $result = $con->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $Uname = $row['Username'];
                        $userId = $row['Id'];
                        $isFollowed = ($row['following_id'] !== null);

                        echo "<tr>
                            <td>$Uname</td>
                            <td><button class='follow_button' data-userid='$userId' data-followed='" . ($isFollowed ? 'true' : 'false') . "' style='background-color: " . ($isFollowed ? '#d26868' : '#699053') . "; border-radius: 5px; border: 0; padding: 5px 10px; color: 'white'; cursor: 'pointer'>" . ($isFollowed ? 'Unfollow' : 'Follow') . "</button></td>
                        </tr>";
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
    const followButtons = document.querySelectorAll('.follow_button');

    followButtons.forEach(button => {
        button.addEventListener('click', function () {
            const userId = this.getAttribute('data-userid');
            const isFollowed = this.getAttribute('data-followed') === 'true';

            if (isFollowed) {
                // Unfollow
                this.innerHTML = 'Follow';
                this.setAttribute('data-followed', 'false');
                this.classList.remove('followed');

                // Send AJAX request to remove the relationship from the database
                fetch(`unfollow.php?userId=${userId}`)
                    .then(response => {
                        if (!response.ok) {
                            alert('Error unfollowing the user. Please try again later.');
                        }
                    });
            } else {
                // Follow
                this.innerHTML = 'Unfollow';
                this.setAttribute('data-followed', 'true');
                this.classList.add('followed');

                // Send AJAX request to add the relationship to the database
                fetch(`follow.php?userId=${userId}`)
                    .then(response => {
                        if (!response.ok) {
                            alert('Error following the user. Please try again later.');
                        }
                    });
            }
        });
    });
</script>
<script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

</body>
</html>