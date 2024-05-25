<?php

$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "prison_mgmt";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("SELECT con_id, con_lname, con_fname, con_mname, con_age, con_gender, con_case, con_date_admitted, con_date_rel FROM admitted WHERE con_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(array('error' => 'No data found with the provided ID.'));
    }

    $stmt->close();
} else {
    echo json_encode(array('error' => 'ID is not provided.'));
}

$conn->close();
?>