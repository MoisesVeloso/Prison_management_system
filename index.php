<?php
// Start the session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

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

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_username = ? AND admin_password = ?");
    $stmt->bind_param("ss", $username, $password);

    // Execute the query
    $stmt->execute();

    // Store the result
    $result = $stmt->get_result();

    // Check if there is a matching row in the database
    if ($result->num_rows == 1) {
        // Authentication successful
        $_SESSION["username"] = $username; // Set the username in the session variable
        header("Location: homepage.php"); // Redirect to homepage
        exit();
    // ...
    } else {
        // Authentication failed
        $_SESSION["error_message"] = "Invalid username or password. Please try again.";
        header("Location: index.php"); // Redirect back to the login page
        exit();
    }
    // ...


    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('img/background.jpg'); /* Path to your background image */
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            filter: brightness(1); /* Darken the background (adjust the value for desired darkness) */
            backdrop-filter: blur(10px); /* Apply blur effect to the background */
        }

        .login-form {
            max-width: 350px;
            width: 100%;
            padding: 15px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background-color: white;
            padding: 2.5em;
            border-radius: 25px;
            transition: .4s ease-in-out;
            box-shadow: rgba(0, 0, 0, 0.4) 1px 2px 2px;
            }

        .form:hover {
            transform: translateX(-0.5em) translateY(-0.5em);
            border: 1px solid #171717;
            box-shadow: 10px 10px 0px #666666;
            }

        .heading {
            color: black;
            padding-bottom: 1em;
            text-align: center;
            font-weight: bold;
            }

        .input {
            border-radius: 5px;
            border: 1px solid whitesmoke;
            background-color: whitesmoke;
            outline: none;
            padding: 0.7em;
            transition: .4s ease-in-out;
            }

        .input:hover {
            box-shadow: 6px 6px 0px #969696,
                        -3px -3px 10px #ffffff;
            }

        .input:focus {
            background: #ffffff;
            box-shadow: inset 2px 5px 10px rgba(0,0,0,0.3);
            }

        .form .btn {
            margin-top: 2em;
            align-self: center;
            padding: 0.7em;
            padding-left: 1em;
            padding-right: 1em;
            border-radius: 10px;
            border: none;
            color: black;
            transition: .4s ease-in-out;
            box-shadow: rgba(0, 0, 0, 0.4) 1px 1px 1px;
            }

        .form .btn:hover {
            box-shadow: 6px 6px 0px #969696,
                        -3px -3px 10px #ffffff;
            transform: translateX(-0.5em) translateY(-0.5em);
            }

        .form .btn:active {
            transition: .2s;
            transform: translateX(0em) translateY(0em);
            box-shadow: none;
            }
    </style>
</head>

<body>
    <div class="login-form">
        <h2 class="text-center mb-4">Login</h2>
        <form action="index.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="alert alert-danger" id="error-message" style="display: none;"></div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
    </div>


    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
    // Check if there's an error message in the session and display it
    var errorMessage = "<?php echo isset($_SESSION['error_message']) ? $_SESSION['error_message'] : ''; ?>";
    if (errorMessage) {
        document.getElementById('error-message').innerText = errorMessage;
        document.getElementById('error-message').style.display = 'block';
    }
</script>

</html>




