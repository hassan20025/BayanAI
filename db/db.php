<?php
// $mysqli = new mysqli("23.95.206.237", "root", "", "bayanai");
$mysqli = new mysqli("localhost", "root", "", "bayanai");

if ($mysqli->connect_error) {
    die($mysqli->connect_error);
}