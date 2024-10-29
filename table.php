<?php

require_once 'database.php';

try {
    $db = new database("localhost", "root", "", "my_database");
    //sql query for getting the data
    $sql = "
        SELECT DATE(date) AS post_date, HOUR(date) AS post_hour, COUNT(id) AS post_count
        FROM posts 
        GROUP BY post_date, post_hour
        ORDER BY post_date, post_hour
    ";

    $stmt = $db->getConnection()->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //print the table
    if ($results) {
        echo "
        <div style='display: flex; justify-content: center;'>
            <table style='border-collapse: collapse; width: 60%; text-align: center; margin-top: 20px; font-family: Arial, sans-serif;'>
                <tr style='background-color: #1E90FF; color: white;'>
                    <th style='padding: 10px; border: 1px solid #ddd;'>תאריך</th>
                    <th style='padding: 10px; border: 1px solid #ddd;'>שעה</th>
                    <th style='padding: 10px; border: 1px solid #ddd;'>כמות פוסטים לאותה שעה</th>
                </tr>";
        
        foreach ($results as $row) {
            echo "<tr style='background-color: #f2f2f2;'>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row["post_date"]) . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row["post_hour"]) . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row["post_count"]) . "</td>";
            echo "</tr>";
        }

        echo "</table>
        </div>";
    } else {
        echo "<p style='text-align: center; font-family: Arial, sans-serif;'>לא נמצאו תוצאות</p>";
    }
} catch (PDOException $e) {
    echo "<p style='text-align: center; font-family: Arial, sans-serif; color: red;'>שגיאה בהתחברות למסד הנתונים: " . htmlspecialchars($e->getMessage()) . "</p>";
}

