<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
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

$id = $_POST['editId'];
$lastName = $_POST['editLastName'];
$firstName = $_POST['editFirstName'];
$middleName = $_POST['editMiddleName'];
$age = $_POST['editAge'];
$gender = $_POST['editGender'];
$case = $_POST['editCase'];
$dateAdmitted = $_POST['editDateAdmitted'];
$dateRelease = $_POST['editDateRelease'];

$sql = "UPDATE admitted SET 
        con_lname='$lastName', 
        con_fname='$firstName', 
        con_mname='$middleName', 
        con_age='$age', 
        con_gender='$gender', 
        con_case='$case', 
        con_date_admitted='$dateAdmitted', 
        con_date_rel='$dateRelease'
        WHERE con_id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: homepage.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
?>
