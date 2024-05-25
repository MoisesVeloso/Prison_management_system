<?php
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "prison_mgmt";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchTerm = isset($_GET['term']) ? $_GET['term'] : '';

$sql = "SELECT con_id, con_fname, con_lname 
        FROM admitted 
        WHERE con_fname LIKE ? OR con_lname LIKE ?";

$stmt = $conn->prepare($sql);
$term = "%$searchTerm%";
$stmt->bind_param("ss", $term, $term);
$stmt->execute();
$result = $stmt->get_result();

$inmates = array();
while ($row = $result->fetch_assoc()) {
    $inmates[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($inmates);
?>