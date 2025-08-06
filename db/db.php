<?php
// $mysqli = new mysqli("23.95.206.237", "bayanai", "bayanaiteam", "bayanai");
$mysqli = new mysqli("localhost", "root", "", "bayanai");

if ($mysqli->connect_error) {
    die($mysqli->connect_error);
}