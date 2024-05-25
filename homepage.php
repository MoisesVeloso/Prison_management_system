<?php
session_start();

    if (!isset($_SESSION["username"])) {
        header("Location: index.php");
        exit();
    }

    $username = $_SESSION["username"];
?>
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

// Query to retrieve admin details
$sql = "SELECT admin_lname, admin_fname, admin_mname, admin_username, admin_email FROM admins WHERE admin_id = 1"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of the admin
    $row = $result->fetch_assoc();
    $admin_last_name = $row["admin_lname"];
    $admin_first_name = $row["admin_fname"];
    $admin_middle_name = $row["admin_mname"];
    $admin_username = $row["admin_username"];
    $admin_email = $row["admin_email"];
} else {
    // Admin not found
    echo "Admin not found in the database.";
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .sidebar {
            background-color: #343a40;
            color: #ffffff;
            height: 100vh;
            width: 20%;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }

        .sidebar a {
            padding: 8px 16px;
            display: block;
            color: #ffffff;
            text-decoration: none;
            margin-bottom: 10px;
        }

        .sidebar a:hover {
            background-color: #565e64;
        }

        .content {
            margin-left: 22%;
            padding: 20px;
        }

        table {
            width: 100%;
        }

        .btn.active {
            background-color: #007bff;
            color: #fff;
        }

        .logo-img {
            width: 150px; /* Set the desired width for the logo */
        }


        .admin-profile-button{
            position: absolute;
            bottom: 50px;

        }
        .logout-button {
            position: absolute;
            bottom: 10px;
        }

        .welcome-message{
            position: absolute;
            bottom: 110px;
            left: 15px;
        }

        .modal-dialog-landscape {
            max-width: 90%;
            width: 90%;
        }

        @media (min-width: 576px) {
            .modal-dialog-landscape {
                max-width: 80%;
            }
        }

        @media (min-width: 768px) {
            .modal-dialog-landscape {
                max-width: 70%;
            }
        }

        @media (min-width: 992px) {
            .modal-dialog-landscape {
                max-width: 60%;
            }
        }

        @media (min-width: 1200px) {
            .modal-dialog-landscape {
                max-width: 50%; 
            }
        }

    </style>
    <script>
        function validateForm() {
            var nameRegex = /^[a-zA-Z\-.,\s]+$/;
            var name = document.getElementById("name").value;
            var age = document.getElementById("age").value;
            var gender = document.getElementById("gender").value; // No need to convert to uppercase
            var caseText = document.getElementById("case").value;
            var dateAdmitted = new Date(document.getElementById("date_admitted").value);
            var dateRelease = new Date(document.getElementById("date_release").value);
            var currentDate = new Date();
            var isValid = true;

            // Clear previous error messages
            document.getElementById("nameError").textContent = "";
            document.getElementById("ageError").textContent = "";
            document.getElementById("genderError").textContent = "";
            document.getElementById("caseError").textContent = "";
            document.getElementById("dateAdmittedError").textContent = "";
            document.getElementById("dateReleaseError").textContent = "";

            // Validate name
            if (!name.match(nameRegex)) {
                document.getElementById("nameError").textContent = "Invalid name format. Please use only letters, '-', '.', or ','.";
                isValid = false;
            }

            // Validate age
            if (age.length !== 2 || isNaN(parseInt(age)) || parseInt(age) < 16) {
                document.getElementById("ageError").textContent = "Age must be a valid two-digit number and 16 years or older.";
                isValid = false;
            }

            // Validate gender
            if (gender !== "M" && gender !== "F") {
                document.getElementById("genderError").textContent = "Gender must be 'M' or 'F'.";
                isValid = false;
            }

            // Validate case
            if (caseText.length === 0) {
                document.getElementById("caseError").textContent = "Case field cannot be empty.";
                isValid = false;
            }

            // Validate date admitted
            if (dateAdmitted > currentDate) {
                document.getElementById("dateAdmittedError").textContent = "Date admitted cannot be ahead of the present date.";
                isValid = false;
            }

            // Validate date release
            if (dateRelease < dateAdmitted) {
                document.getElementById("dateReleaseError").textContent = "Date of release cannot be before the date admitted.";
                isValid = false;
            }

            return isValid;
        }
        
    </script>
<script>
    function searchPerson() {
    var searchTerm = document.getElementById("searchTerm").value.toLowerCase();
    var rows = document.getElementsByTagName("tbody")[0].rows;
    var found = false;

    for (var i = 0; i < rows.length; i++) {
        var lname = rows[i].cells[1].textContent.toLowerCase();
        var fname = rows[i].cells[2].textContent.toLowerCase();
        var mname = rows[i].cells[3].textContent.toLowerCase();
        var dateAdmitted = rows[i].cells[7].textContent.toLowerCase();
        var dateReleased = rows[i].cells[8].textContent.toLowerCase();

        if (lname.includes(searchTerm) || fname.includes(searchTerm) || mname.includes(searchTerm) || dateAdmitted.includes(searchTerm) || dateReleased.includes(searchTerm)) {
            rows[i].style.display = "";
            found = true;
        } else {
            rows[i].style.display = "none";
        }
    }

    if (!found) {
        alert("No matching records found.");
    } else {
        $('.modal-backdrop').remove();
        $('#searchPersonModal').modal('hide');
    }
}
</script>

<script>
    $(document).on('click', '#deleteConfirmationModal button[data-bs-dismiss="modal"]', function() {
        $('#deleteConfirmationModal').modal('hide');
    });
</script>

<script>
    document.getElementById("adminProfileForm").addEventListener("submit", function(event) {
        event.preventDefault();

        if (validateAdminProfileForm()) {
            // If validation passes, submit the form
            this.submit(); // 'this' refers to the form element
        }
    });

    function validateAdminProfileForm() {
        var adminEmail = document.getElementById("admin_email").value;
        var currentPassword = document.getElementById("current_password").value;
        var newPassword = document.getElementById("new_password").value;
        var confirmNewPassword = document.getElementById("confirm_new_password").value;

        // Clear previous error messages
        document.getElementById("passwordError").textContent = "";

        // Password validation
        if (newPassword.length < 8) {
            document.getElementById("passwordError").textContent = "Password must be at least 8 characters long.";
            return false;
        }

        if (!/[A-Z]/.test(newPassword) || !/[a-z]/.test(newPassword) || !/[!@#$%^&*]/.test(newPassword)) {
            document.getElementById("passwordError").textContent = "Password must contain at least one uppercase letter, one lowercase letter, and one special character.";
            return false;
        }

        if (newPassword !== confirmNewPassword) {
            document.getElementById("passwordError").textContent = "New password and confirm password do not match.";
            return false;
        }

        return true;
    }
</script>

</head>

<body>
    
    <div class="sidebar">
        <a href="homepage.php"><img src="img/police-logo.png" alt="Police Logo" class="mx-auto d-block mb-4 logo-img"></a>
        <a href="add_person.php" data-bs-toggle="modal" data-bs-target="#addPersonModal">Add person</a>
        <a href="search_person.php" data-bs-toggle="modal" data-bs-target="#searchPersonModal">Search person</a>
        <a href="report.php">Report</a>
        <a class="visitors" href="visitors.php" id="visitorTab">Visitors</a>
        <div class="welcome-message">Welcome, <?php echo $username; ?>!</div>
        <a href="update_admin_profile.php" class="admin-profile-button" data-bs-toggle="modal" data-bs-target="#adminProfileModal">Admin Profile</a>
        <a href="logout.php" class="logout-button" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>

    </div>


    <!-- Add Person Modal -->
    <div class="modal fade" id="addPersonModal" tabindex="-1" aria-labelledby="addPersonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPersonModalLabel">Add Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add person form goes here -->
                    <form action="add_person.php" method="post" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="age" name="age" required>
                        <div id="ageError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                        <div id="genderError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="case" class="form-label">Case</label>
                        <input type="text" class="form-control" id="case" name="case" required>
                        <div id="caseError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="date_admitted" class="form-label">Date Admitted</label>
                        <input type="date" class="form-control" id="date_admitted" name="date_admitted" required>
                        <div id="dateAdmittedError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="date_release" class="form-label">Date of Release</label>
                        <input type="date" class="form-control" id="date_release" name="date_release" required>
                        <div id="dateReleaseError" class="text-danger"></div>
                    </div>
                    <form action="add_person.php" method="post" onsubmit="return validateForm()">
                        <!-- ...form fields... -->
                        <button type="submit" class="btn btn-primary">Add Person</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

        <!-- Search Person Modal -->
    <div class="modal fade" id="searchPersonModal" tabindex="-1" aria-labelledby="searchPersonModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchPersonModalLabel">Search Person</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="searchTerm" class="form-label">Search Term (either by Name or Date)</label>
                        <input type="text" class="form-control" id="searchTerm" required>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="searchPerson()">Search</button>
                </div>
            </div>
        </div>
    </div>

        <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Logout Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to log out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this item?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Admin Profile Modal -->
        <div class="modal fade" id="adminProfileModal" tabindex="-1" aria-labelledby="adminProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminProfileModalLabel">Admin Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="update_admin_profile.php" method="post" onsubmit="return validateAdminProfileForm()">
                        <!-- Last Name (Text Field) -->
                        <div class="mb-3">
                            <label for="admin_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="admin_last_name" name="admin_last_name" value="<?php echo htmlspecialchars($admin_last_name); ?>" readonly>
                        </div>

                        <!-- First Name (Text Field) -->
                        <div class="mb-3">
                            <label for="admin_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="admin_first_name" name="admin_first_name" value="<?php echo htmlspecialchars($admin_first_name); ?>" readonly>
                        </div>

                        <!-- Middle Name (Text Field) -->
                        <div class="mb-3">
                            <label for="admin_middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="admin_middle_name" name="admin_middle_name" value="<?php echo htmlspecialchars($admin_middle_name); ?>" readonly>
                        </div>

                        <!-- Username (Text Field) -->
                        <div class="mb-3">
                            <label for="admin_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="admin_username" name="admin_username" value="<?php echo htmlspecialchars($admin_username); ?>" readonly>
                        </div>

                        <!-- Email (Text Field) -->
                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($admin_email); ?>" required>
                        </div>

                        <!-- Current Password (Blank Text Field) -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>

                        <!-- New Password (Blank Text Field) -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>

                        <!-- Confirm New Password (Blank Text Field) -->
                        <div class="mb-3">
                            <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                        </div>

                        <!-- Update Profile Button -->
                        <button type="button" class="btn btn-primary" id="updateProfileBtn">Update Profile</button>

                        <!-- Close Button (with data-dismiss attribute to close the modal) -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to update your profile?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmUpdateBtn">Confirm Update</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById("updateProfileBtn").addEventListener("click", function() {
                $('#confirmationModal').modal('show');
            });

            document.getElementById("confirmUpdateBtn").addEventListener("click", function() {
                document.querySelector("#adminProfileModal form").submit();
            });
            function validateAdminProfileForm() {
                return true;
            }
        </script>

       <!-- Edit Person Modal -->
       <div class="modal fade" id="editPersonModal" tabindex="-1" aria-labelledby="editPersonModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPersonModalLabel">Edit Person</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="edit_person.php" method="post" id="editPersonForm">
                            <input type="hidden" id="editId" name="editId">
                            <div class="mb-3">
                                <label for="editLastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="editLastName" name="editLastName" required>
                            </div>
                            <div class="mb-3">
                                <label for="editFirstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="editFirstName" name="editFirstName" required>
                            </div>
                            <div class="mb-3">
                                <label for="editMiddleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="editMiddleName" name="editMiddleName" required>
                            </div>
                            <div class="mb-3">
                                <label for="editAge" class="form-label">Age</label>
                                <input type="number" class="form-control" id="editAge" name="editAge" required>
                            </div>
                            <div class="mb-3">
                                <label for="editGender" class="form-label">Gender</label>
                                <select class="form-select" id="editGender" name="editGender" required>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editCase" class="form-label">Case</label>
                                <input type="text" class="form-control" id="editCase" name="editCase" required>
                            </div>
                            <div class="mb-3">
                                <label for="editDateAdmitted" class="form-label">Date Admitted</label>
                                <input type="date" class="form-control" id="editDateAdmitted" name="editDateAdmitted" required>
                            </div>
                            <div class="mb-3">
                                <label for="editDateRelease" class="form-label">Date of Release</label>
                                <input type="date" class="form-control" id="editDateRelease" name="editDateRelease" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Person Modal -->
        <div class="modal fade" id="viewPersonModal" tabindex="-1" aria-labelledby="viewPersonModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-landscape">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewPersonModalLabel">View Person</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="viewPersonForm">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="viewLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="viewLastName" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="viewFirstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="viewFirstName" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="viewMiddleName" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="viewMiddleName" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="viewAge" class="form-label">Age</label>
                                    <input type="number" class="form-control" id="viewAge" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="viewGender" class="form-label">Gender</label>
                                    <select class="form-select" id="viewGender" readonly>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="viewCase" class="form-label">Case</label>
                                    <input type="text" class="form-control" id="viewCase" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="viewDateAdmitted" class="form-label">Date Admitted</label>
                                    <input type="date" class="form-control" id="viewDateAdmitted" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="viewDateRelease" class="form-label">Date of Release</label>
                                    <input type="date" class="form-control" id="viewDateRelease" readonly>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    <div class="content">
        <h2>Admitted Persons</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Case</th>
                    <th>Date Admitted</th>
                    <th>Date of Release</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
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

                // Fetch data from the database
                $sql = "SELECT con_id, con_lname, con_fname, con_mname, con_age, con_gender, con_case, con_date_admitted, con_date_rel FROM admitted";

                $result = $conn->query($sql);

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
                        <td align='center'>
                            <div class='dropdown'>
                                <button class='btn btn-success dropdown-toggle' type='button' id='dropdownMenuButton_" . $row['con_id'] . "' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Actions</button>
                                <ul class='dropdown-menu' aria-labelledby='dropdownMenuButton_" . $row['con_id'] . "'>
                                    <li>
                                        <a class='dropdown-item view-link' href='#' data-bs-toggle='modal' data-bs-target='#viewPersonModal'
                                            data-id='" . $row['con_id']  . "'
                                            data-lname='" . $row['con_lname'] ."'
                                            data-fname='" . $row['con_fname'] . "'
                                            data-mname='" . $row['con_mname'] . "'
                                            data-age='" . $row['con_age'] . "'
                                            data-gender='" . $row['con_gender'] . "'
                                            data-case='" . $row['con_case'] . "'
                                            data-dateadmitted='" . $row['con_date_admitted'] . "'
                                            data-daterelease='" . $row['con_date_rel'] . "'>
                                            View
                                        </a>
                                    </li>
                                    <li>
                                        <a href='#' class='dropdown-item edit-link' data-bs-toggle='modal' data-bs-target='#editPersonModal'
                                            data-id='" . $row['con_id'] . "'
                                            data-lname='" . $row['con_lname'] . "'
                                            data-fname='" . $row['con_fname'] . "'
                                            data-mname='" . $row['con_mname'] . "'
                                            data-age='" . $row['con_age'] . "'
                                            data-gender='" . $row['con_gender'] . "'
                                            data-case='" . $row['con_case'] . "'
                                            data-dateadmitted='" . $row['con_date_admitted'] . "'
                                            data-daterelease='" . $row['con_date_rel'] . "'>
                                            Edit
                                        </a>
                                    </li>
                                    <li><a class='dropdown-item delete_data' href='#' data-id='" . $row['con_id'] . "' data-url='delete_inmate.php' data-toggle='modal' data-target='#deleteConfirmationModal'>Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>";

                }
                // Close the database connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = document.getElementById('editPersonModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                var link = event.relatedTarget; // The link that triggered the modal
                var id = link.getAttribute('data-id');
                var lname = link.getAttribute('data-lname');
                var fname = link.getAttribute('data-fname');
                var mname = link.getAttribute('data-mname');
                var age = link.getAttribute('data-age');
                var gender = link.getAttribute('data-gender');
                var caseText = link.getAttribute('data-case');
                var dateAdmitted = link.getAttribute('data-dateadmitted');
                var dateRelease = link.getAttribute('data-daterelease');

                var modal = this;
                modal.querySelector('#editId').value = id;
                modal.querySelector('#editLastName').value = lname;
                modal.querySelector('#editFirstName').value = fname;
                modal.querySelector('#editMiddleName').value = mname;
                modal.querySelector('#editAge').value = age;
                modal.querySelector('#editGender').value = gender;
                modal.querySelector('#editCase').value = caseText;
                modal.querySelector('#editDateAdmitted').value = dateAdmitted;
                modal.querySelector('#editDateRelease').value = dateRelease;
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var viewModal = document.getElementById('viewPersonModal');
            viewModal.addEventListener('show.bs.modal', function (event) {
                var link = event.relatedTarget; // The link that triggered the modal
                var id = link.getAttribute('data-id');
                var lname = link.getAttribute('data-lname');
                var fname = link.getAttribute('data-fname');
                var mname = link.getAttribute('data-mname');
                var age = link.getAttribute('data-age');
                var gender = link.getAttribute('data-gender');
                var caseText = link.getAttribute('data-case');
                var dateAdmitted = link.getAttribute('data-dateadmitted');
                var dateRelease = link.getAttribute('data-daterelease');

                var modal = this;
                modal.querySelector('#viewLastName').value = lname;
                modal.querySelector('#viewFirstName').value = fname;
                modal.querySelector('#viewMiddleName').value = mname;
                modal.querySelector('#viewAge').value = age;
                modal.querySelector('#viewGender').value = gender;
                modal.querySelector('#viewCase').value = caseText;
                modal.querySelector('#viewDateAdmitted').value = dateAdmitted;
                modal.querySelector('#viewDateRelease').value = dateRelease;
            });
        });
    </script>

    <script>
    $(document).ready(function() {
        $(document).on('click', '.delete_data', function() {
            var id = $(this).data('id');
            var url = $(this).data('url');
            $('.modal-backdrop').remove();
            $('#confirmDeleteBtn').data('id', id);
            $('#confirmDeleteBtn').data('url', url);
            $('#deleteConfirmationModal').modal('show');
        });

        $('#deleteConfirmationModal button[data-bs-dismiss="modal"]').click(function() {
            $('#deleteConfirmationModal').modal('hide');
        });
        
        $('#confirmDeleteBtn').click(function() {
            var id = $(this).data('id');
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'POST',
                data: {id: id},
                success:function(data){
                    location.reload();
                },
                error:function(err){
                    console.log(err);
                    alert("An error occurred while deleting the action.");
                }
            });
        });
    });
</script>




</body>
</html>
