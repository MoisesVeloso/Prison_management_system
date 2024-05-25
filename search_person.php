<?php
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

// Get the search query from the URL query parameter
$searchQuery = "%" . $conn->real_escape_string($_GET['con_id']) . "%";

// Perform the search using prepared statement
$sql = "SELECT con_id, con_lname, con_fname, con_mname, con_age, con_gender, con_case, con_date_admitted, con_date_rel 
        FROM admitted 
        WHERE LOWER(con_lname) LIKE LOWER(?) 
        OR LOWER(con_fname) LIKE LOWER(?) 
        OR LOWER(con_mname) LIKE LOWER(?) 
        OR DATE_FORMAT(con_date_admitted, '%Y-%m-%d') = ? 
        OR DATE_FORMAT(con_date_rel, '%Y-%m-%d') = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $searchQuery, $searchQuery, $searchQuery, $_GET['q'], $_GET['q']);
$stmt->execute();
$result = $stmt->get_result();

// Output data of each row
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . $row["con_id"] . "</td>
            <td>" . $row["con_lname"] . "</td>
            <td>" . $row["con_fname"] . "</td>
            <td>" . $row["con_mname"] . "</td>
            <td>" . $row["con_age"] . "</td>
            <td>" . $row["con_gender"] . "</td>
            <td>" . $row["con_case"] . "</td>
            <td>" . $row["con_date_admitted"] . "</td>
            <td>" . $row["con_date_rel"] . "</td>
        </tr>";
}

// Close the database connection
$stmt->close();
$conn->close();
?>
