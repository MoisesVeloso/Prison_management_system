<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $lastName = $_POST["last_name"];
    $firstName = $_POST["first_name"];
    $middleName = $_POST["middle_name"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $case = $_POST["case"];
    $dateAdmitted = $_POST["date_admitted"];
    $dateRelease = $_POST["date_release"];

    // Database credentials
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "prison_mgmt";

    // Create connection
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement and execute to insert data into the admitted table
    $stmt = $conn->prepare("INSERT INTO admitted (con_lname, con_fname, con_mname, con_age, con_gender, con_case, con_date_admitted, con_date_rel) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssissss", $lastName, $firstName, $middleName, $age, $gender, $case, $dateAdmitted, $dateRelease);
    $stmt->execute();

    // Close the database connection
    $stmt->close();
    $conn->close();

    // Redirect back to the homepage after adding the person
    header("Location: homepage.php");
    exit();
}
?>
