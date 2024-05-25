<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION["username"];

?>

<?php
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "prison_mgmt";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
$conn->query("SET time_zone = '+08:00';"); 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT v.visitor_name, 
        DATE_FORMAT(v.visit_datetime, '%Y-%m-%d / %h:%i %p') AS visit_datetime, 
        NOW() AS system_datetime,
        v.purpose, 
        a.con_fname, 
        a.con_lname 
        FROM visitor v
        INNER JOIN admitted a ON v.inmate_id = a.con_id";

$result = $conn->query($sql);
$visitors = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $visitors[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
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

        .logo-img {
            width: 150px; /* Set the desired width for the logo */
        }
        
        .content {
            margin-left: 22%;
            padding: 20px;
        }

        .card-header .card-tools {
            margin-top: -5px;
        }

        .card-header .card-tools button {
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="homepage.php"><img src="img/police-logo.png" alt="Police Logo" class="mx-auto d-block mb-4 logo-img"></a>
        <a href="homepage.php">Homepage</a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#addVisitorModal">Add Visitor</a>
        <a href="report_visitor.php">Report</a>
    </div>

    <!-- Add Visitor Modal -->
    <div class="modal fade" id="addVisitorModal" tabindex="-1" aria-labelledby="addVisitorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVisitorModalLabel">Add Visitor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="add_visitor.php" method="POST">
                        <div class="mb-3">
                            <label for="visitorName" class="form-label">Visitor Name</label>
                            <input type="text" class="form-control" id="visitorName" name="visitorName" required>
                        </div>
                        <div class="mb-3">
                            <label for="inmateName" class="form-label">Inmate Name</label>
                            <input type="text" class="form-control" id="inmateName" name="inmateName" required>
                            <input type="hidden" id="inmateId" name="inmateId">
                            <ul id="inmateDropdown" class="dropdown-menu" style="width: 100%;"></ul>
                        </div>
                        <div class="mb-3">
                            <label for="purpose" class="form-label">Purpose</label>
                            <input type="text" class="form-control" id="purpose" name="purpose" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <h1>Visitor Information</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Visitor Name</th>
                    <th>Visit Date</th>
                    <th>Inmate First Name</th>
                    <th>Inmate Last Name</th>
                    <th>Purpose</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visitors as $visitor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($visitor['visitor_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($visitor['visit_datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($visitor['con_fname'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($visitor['con_lname'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($visitor['purpose'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#inmateName').on('input', function() {
                var query = $(this).val();
                if (query.length >= 2) {
                    $.ajax({
                        url: 'search_inmates.php',
                        type: 'GET',
                        data: { term: query },
                        dataType: 'json',
                        success: function(data) {
                            var dropdown = $('#inmateDropdown');
                            dropdown.empty();
                            if (data.length > 0) {
                                dropdown.show();
                                $.each(data, function(index, item) {
                                    dropdown.append(
                                        $('<li>').addClass('dropdown-item').attr('data-id', item.con_id).text(item.con_fname + ' ' + item.con_lname)
                                    );
                                });
                            } else {
                                dropdown.hide();
                            }
                        }
                    });
                } else {
                    $('#inmateDropdown').hide();
                }
            });

            $(document).on('click', '#inmateDropdown li', function() {
                var name = $(this).text();
                var id = $(this).attr('data-id');
                $('#inmateName').val(name);
                $('#inmateId').val(id);
                $('#inmateDropdown').hide();
            });

            $('form').submit(function(event) {
                if ($('#inmateId').val() === '') {
                    event.preventDefault(); 
                    alert('Inmate is not selected or exist, search and select in dropdown list.');
                }
            });

            $(document).click(function(event) {
                var target = $(event.target);
                if (!target.is('#inmateName') && !target.is('#inmateDropdown') && !target.closest('#inmateDropdown').length) {
                    $('#inmateDropdown').hide();
                }
            });
        });
    </script>
</body>
</html>