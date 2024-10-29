<?php

    require_once 'database.php';
    $db = new database("localhost", "root", "", "my_database");

    //Fetche and decode JSON data from a specified URL using cURL.
    function getDataFromURL($url) {
        
        // Initialize a cURL session
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        $response = curl_exec($curl);
    
        // Check for cURL errors
        if ($response === false) {
            echo "cURL Error: " . curl_error($curl) . "\n";
            curl_close($curl);
            return []; 
        }
    
        curl_close($curl); // Close the cURL session
    
        // Decode the JSON response into an associative array
        $data = json_decode($response, true);
    
        // Check if json_decode failed
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Decode Error: " . json_last_error_msg() . "\n";
            return []; // Return an empty array on error
        }
    
        return $data; // Return the decoded data
    }
    

    //create the tables 'users' and 'posts'
    $usersColumns = [
        'id INT PRIMARY KEY',
        'name VARCHAR(100) NOT NULL',
        'email VARCHAR(100) DEFAULT NULL',   
        'is_active BINARY(1) NOT NULL DEFAULT 1',     
        'birthdate DATE DEFAULT NULL'
    ];
    $db->createTable('users', $usersColumns);

    $postsColumns = [
        'userId INT NOT NULL',                                                     
        'id INT PRIMARY KEY',                                                    
        'title VARCHAR(255) NOT NULL',                                           
        'body TEXT NOT NULL',     
        'date DATETIME DEFAULT NULL',                                                
        'is_active BINARY(1) NOT NULL DEFAULT 1',                                   
        'FOREIGN KEY (userId) REFERENCES users(id)'                                 
    ];
    $db->createTable('posts', $postsColumns);

    //get the data from the links to an array
    $users= getDataFromURL("https://jsonplaceholder.typicode.com/users");
    $posts= getDataFromURL("https://jsonplaceholder.typicode.com/posts");

    //put the data in the tables
    foreach ($users as $user) {
        if (!$db->recordExists('users', 'id = :id', [':id' => $user['id']])) {
            $db->insert('users', 
            [   
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'is_active' => 1
            ]);
        }
    }

    foreach ($posts as $post) {
        if (!$db->recordExists('posts', 'id = :id', [':id' => $post['id']])) {
            $db->insert('posts', 
            [
                'userId' => $post['userId'],
                'id' => $post['id'],
                'title' => $post['title'],
                'body' => $post['body'],
                'date' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ]);
        }
    }
