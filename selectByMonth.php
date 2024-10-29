<?php
require_once 'database.php'; 
require_once 'saveImage.php';

$db = new Database("localhost", "root", "", "my_database");

//add styling for a social media feed
echo "<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    body {
        background-color: #f0f2f5;
        color: #1c1e21;
        line-height: 1.5;
        padding: 20px;
    }

    .feed-container {
        max-width: 680px;
        margin: 0 auto;
    }

    .user {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px; /* Increased space between users */
        overflow: hidden;
        border: 1px solid #e4e6eb; /* Added subtle border */
    }

    .user-header {
        display: flex;
        align-items: center;
        padding: 16px;
        background-color: #ffffff;
        border-bottom: 1px solid #e4e6eb;
    }

    .profile-img {
        width: 48px; /* Slightly larger profile image */
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 12px;
        border: 2px solid #e4e6eb; /* Added border to profile image */
    }

    .user-name {
        font-size: 16px;
        font-weight: 600;
        color: #050505;
        text-decoration: none;
    }

    .user-birthdate {
        font-size: 12px; /* Reduced font size */
        color: #65676b; /* Color for consistency */
        margin-top: 4px;
    }

    .posts {
        padding: 0;
    }

    .post {
        padding: 20px;
        border-bottom: 1px solid #e4e6eb;
        background-color: #ffffff;
    }

    .post:last-child {
        border-bottom: none;
    }

    .post-title {
        font-size: 18px;
        font-weight: 600;
        color: #050505;
        margin-bottom: 12px;
    }

    .post-body {
        font-size: 15px;
        color: #050505;
        margin-bottom: 12px;
        line-height: 1.6;
    }

    .post-date {
        font-size: 13px;
        color: #65676b;
        display: block;
    }

    .month-select {
        margin-bottom: 20px;
    }
</style>";

//A box to select a desired month to display
echo "<div class='feed-container'>";
echo "<form method='POST' class='month-select'>";
echo "<label for='month'>בחר חודש:</label>";
echo "<select name='month' id='month'>";
for ($i = 1; $i <= 12; $i++) {
    $monthName = date("F", mktime(0, 0, 0, $i, 1)); 
    echo "<option value='$i'>$monthName</option>";
}
echo "</select>";
echo "<button type='submit'>הצג פוסט אחרון</button>";
echo "</form>";

try {
    //sql query for getting the data
    if (isset($_POST['month'])){
        $target_month = (int)$_POST['month'];  
        $sql = "
            SELECT users.id AS user_id, users.name, users.birthdate, posts.title, posts.body, posts.date
            FROM users
            JOIN posts ON users.id = posts.userId
            WHERE MONTH(users.birthdate) = ?
            ORDER BY posts.date DESC
            LIMIT 1
        ";

        $stmt = $db->getConnection()->prepare($sql);
        $stmt->bindParam(1, $target_month, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            //print the table
            echo "<div class='user'>";
            echo "<div class='user-header'>";
            $imageUrl = 'https://cdn2.vectorstock.com/i/1000x1000/23/81/default-avatar-profile-icon-vector-18942381.jpg';
            $imageFileName = 'image.jpg';
            $img = saveImageFromUrl($imageUrl, $imageFileName); 
            echo "<img src='" . htmlspecialchars($img ?? 'default.jpg') . "' alt='" . htmlspecialchars($result['name']) . "' class='profile-img'>";
            echo "<span class='user-name'>" . htmlspecialchars($result['name']) . "</span>";
            echo "</div>";

            if (!empty($result['birthdate'])) {
                echo "<span class='user-birthdate'>תאריך לידה: " . htmlspecialchars(date('d/m/Y', strtotime($result['birthdate']))) . "</span>";
            }

            echo "<div class='posts'>";
            echo "<div class='post'>";
            echo "<div class='post-title'>" . htmlspecialchars($result['title']) . "</div>";
            echo "<div class='post-body'>" . htmlspecialchars($result['body']) . "</div>";
            echo "<span class='post-date'>" . htmlspecialchars($result['date']) . "</span>";
            echo "</div>";

            echo "</div></div>"; 
        } else {
            echo "<p>לא נמצאו פוסטים לחודש הזה.</p>";
        }

        echo "</div>"; // סיום div של feed-container


    } else{
        echo "<p> אנא בחר חודש כדי להציג פוסטים </p>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
