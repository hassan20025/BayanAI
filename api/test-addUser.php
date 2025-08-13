<?php
// Test file to check if addUser.php is working
header('Content-Type: application/json');

// Simulate POST data
$_POST['name'] = 'Test User 2';
$_POST['email'] = 'test2@example.com';
$_POST['department'] = 'IT';
$_POST['role'] = 'User';

// Include the addUser.php file
include 'addUser.php';
?> 