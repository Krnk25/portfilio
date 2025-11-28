<?php
// php/db.php
$host = "localhost";   // XAMPP default
$user = "root";        // XAMPP default
$pass = "";            // your MySQL password (empty if default)
$dbname = "portfolio";



$conn = new mysqli($host, $user, $pass, $dbname);


?>