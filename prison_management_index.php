<?php
$servername = "localhost"; // Change this to your database server name if it's different
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$database = "prison_mgmt";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Perform database operations here

// Close the connection
$conn->close();
?>
