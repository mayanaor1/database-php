<?php

require_once 'database.php';

$db = new Database('localhost', 'root', '', 'my_database');

//test SELECT method
$users = $db->select('users');
echo "The selected users are: <br>";
foreach ($users as $user) {
    echo $user['name'] . '<br>';
}

//test INSERT method
$data = [
    'name' => 'Maya',
    'email' => 'maya@gmail.com',
    'age' => '25'
];
$userId = $db->insert('users', $data);
echo "user number " . $userId . " successfully inserted <br>";

//test UPDATE method
$data = ['name' => 'Dana'];
$count = $db->update('users', $data, 'id = 1');
echo $count . " users have been updated <br>";

//test DELETE method
$count = $db->delete('users', 'id = 21');
echo $count . " users have been deleted <br>";
