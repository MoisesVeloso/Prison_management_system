<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "prison_mgmt";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $visitorName = $_POST["visitorName"];
    $visitDateTime = date("Y-m-d H:i:s"); 
    $inmateId = $_POST["inmateId"];
    $purpose = $_POST["purpose"];

    $sql = "INSERT INTO visitor (visitor_name, visit_datetime, inmate_id, purpose) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssis", $visitorName, $visitDateTime, $inmateId, $purpose);

        if ($stmt->execute()) {
            header("Location: visitors.php");
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
