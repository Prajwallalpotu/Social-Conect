<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css" type="text/css">
    <title>Home</title>
</head>
<body>
    <h2>Your Followers</h2>
    <section class="table_section">
        <div class="table_start">
            <table class="display_table" rules="rows">
                <tr class="table_heading">
                    <th>User Name</th>
                    <th>Following</th>
                </tr>
                <?php
                    session_start();
                    include("php/config.php");

                    if (!isset($_SESSION['valid'])) {
                        header("Location: index.php");
                        exit();
                    }

                    $followerId = $_SESSION['id'];

                    // Retrieve the list of followings with their usernames
                    $query = mysqli_query($con, "SELECT u.Username, uf.following_id FROM users u
                                                JOIN user_following uf ON u.Id = uf.follower_id
                                                WHERE uf.following_id = $followerId");
                    $followingList = [];
                    while ($row = mysqli_fetch_assoc($query)) {
                        $followingList[] = [
                            'id' => $row['following_id'],
                            'username' => $row['Username']
                        ];
                    }

                    // Display the list of followings in a table

                    if (empty($followingList)) {
                        echo "<p>You are not following anyone yet.</p>";
                    } else {
                        foreach ($followingList as $following) {
                            echo "<tr data-userid='{$following['id']}'>";
                            echo "<td>{$following['username']}</td>";
                            echo "<td><button class='red' onclick=\"removeFollowing({$following['id']})\">Remove Follower</button></td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </table>
        </div>
    </section>

    <script>
        async function removeFollowing(followingId) {
    try {
        const response = await fetch(`unfollow.php?followingId=${followingId}`);

        if (response.ok) {
            alert('User removed successfully.');
            const rowToRemove = document.querySelector(`tr[data-userid="${followingId}"]`);
            if (rowToRemove) {
                rowToRemove.remove();
            }
        } else {
            alert('Error removing the user. Please try again later.');
        }
    } catch (error) {
        console.error('An error occurred:', error);
    }
}
    </script>
</body>
</html>
