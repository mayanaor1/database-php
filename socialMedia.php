<?php
require_once 'database.php'; 
require_once 'saveImage.php';

$db = new Database("localhost", "root", "", "my_database");

//add styling for a social media feed
echo 
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
</style>";

try {
    echo "<div class='feed-container'>";
    
    $sql = "
        SELECT users.id AS user_id, users.name, posts.title, posts.body, posts.date
        FROM users
        JOIN posts ON users.id = posts.userId
        WHERE users.is_active = 1
        ORDER BY users.id, posts.date DESC
    ";

    $stmt = $db->getConnection()->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $currentUser = null;
    $img = saveImageFromUrl($imageUrl, $imageFileName);
    
    foreach ($results as $row) {
        if ($currentUser !== $row['user_id']) {
            if ($currentUser !== null) {
                echo "</div></div>"; 
            }

            echo "<div class='user'>";
            echo "<div class='user-header'>";
            echo "<img src='" . htmlspecialchars($img ?? 'default.jpg') . "' alt='" . htmlspecialchars($row['name']) . "' class='profile-img'>";
            echo "<span class='user-name'>" . htmlspecialchars($row['name']) . "</span>";
            echo "</div>";
            echo "<div class='posts'>";
            $currentUser = $row['user_id'];
        }

        echo "<div class='post'>";
        echo "<div class='post-title'>" . htmlspecialchars($row['title']) . "</div>";
        echo "<div class='post-body'>" . htmlspecialchars($row['body']) . "</div>";
        echo "<span class='post-date'>" . htmlspecialchars($row['date']) . "</span>";
        echo "</div>";
    }

    if ($currentUser !== null) {
        echo "</div></div>";
    }

    echo "</div>"; 

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>