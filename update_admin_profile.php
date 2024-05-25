<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_POST["admin_username"]) && isset($_POST["admin_email"]) && isset($_POST["current_password"]) && isset($_POST["new_password"]) && isset($_POST["confirm_new_password"])) {
        
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
        
        // Retrieve values from the form
        $admin_username = $conn->real_escape_string($_POST["admin_username"]);
        $admin_email = $conn->real_escape_string($_POST["admin_email"]);
        $current_password = $conn->real_escape_string($_POST["current_password"]);
        $new_password = $conn->real_escape_string($_POST["new_password"]);
        $confirm_new_password = $conn->real_escape_string($_POST["confirm_new_password"]);
        
        $sql_select_admin = "SELECT * FROM admins WHERE admin_username = '$admin_username'";
        $result = $conn->query($sql_select_admin);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            if ($current_password == $row["admin_password"]) {
                if ($new_password == $confirm_new_password) {
                    $sql_update = "UPDATE admins SET admin_email = '$admin_email', admin_password = '$new_password' WHERE admin_username = '$admin_username'";
                    if ($conn->query($sql_update) === TRUE) {
                        header("Location: homepage.php?success");
                        exit();
                    } else {
                        header("Location: homepage.php?error=update_failed");
                        exit();
                    }
                } else {
                    header("Location: homepage.php?error=password_mismatch");
                    exit();
                }
            } else {
                header("Location: homepage.php?error=incorrect_password");
                exit();
            }
        } else {
            header("Location: homepage.php?error=admin_not_found");
            exit();
        }
        $conn->close();
        
    } else {
        header("Location: homepage.php?error=missing_fields");
        exit();
    }
} else {
    header("Location: homepage.php");
    exit();
}
?>
