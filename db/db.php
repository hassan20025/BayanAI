<?php

$db = new mysqli("localhost", "root", "", "bayanai");
if ($db->connect_error) {
    die("DB Connection failed: " . $db->connect_error);
}

$mysqli = new mysqli("localhost", "root", "", "bayanai");
if ($mysqli->connect_error) {
    die("MySQLi Connection failed: " . $mysqli->connect_error);
}