<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

$start_month = isset($_GET['start_month']) ? $_GET['start_month'] : date("Y-m");
$end_month = isset($_GET['end_month']) ? $_GET['end_month'] : date("Y-m");

// Database credentials
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "prison_mgmt"; 

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize $records as null
$records = null;

// Attempt to execute the query
$sql = "SELECT v.visitor_name, v.visit_datetime, a.con_fname, a.con_lname, v.purpose
        FROM visitor v
        INNER JOIN admitted a ON v.inmate_id = a.con_id
        WHERE DATE_FORMAT(v.visit_datetime, '%Y-%m') BETWEEN '{$start_month}' AND '{$end_month}'
        ORDER BY v.visit_datetime ASC";

$result = $conn->query($sql);

if ($result === false) {
    echo "Error executing query: " . $conn->error;
} else {
    $records = $result;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Record Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
    <style>
        /* Custom styles for sidebar and content */
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

        .logo-img {
            width: 150px; /* Set the desired width for the logo */
        }
        /* Custom styles for the card */
        .card-header .card-tools {
            margin-top: -5px;
        }

        .card-header .card-tools button {
            margin-left: 5px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="homepage.php"><img src="img/police-logo.png" alt="Police Logo" class="mx-auto d-block mb-4 logo-img"></a>
        <a href="homepage.php">Homepage</a>
    </div>

    <!-- Content -->
    <div class="content py-5 px-3 bg-gradient-navy">
        <h2>Visitor Record Reports</h2>
    </div>

    <div class="row flex-column mt-4 justify-content-center align-items-center mt-lg-n4 mt-md-3 mt-sm-0" style="margin-left: 20%;">
        <!-- Filter Card -->
        <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
            <div class="card rounded-0 mb-2 shadow">
                <div class="card-body">
                    <fieldset>
                        <legend>Filter</legend>
                        <form action="" id="filter-form">
                            <div class="row align-items-end">
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="start_month" class="control-label">Start Month</label>
                                        <input type="month" class="form-control form-control-sm rounded-0" name="start_month" id="start_month" value="<?= $start_month ?>" required="required">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="end_month" class="control-label">End Month</label>
                                        <input type="month" class="form-control form-control-sm rounded-0" name="end_month" id="end_month" value="<?= $end_month ?>" required="required">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <button class="btn btn-sm btn-flat btn-primary bg-gradient-primary"><i class="fa fa-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
            <div class="card rounded-0 mb-2 shadow">
                <div class="card-header py-1">
                    <div class="card-tools">
                        <button class="btn btn-flat btn-sm btn-light bg-gradient-light border text-dark" type="button" id="print"><i class="fa fa-print"></i> Print</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container-fluid" id="printout">
                        <?php if ($records && $records->num_rows > 0): ?>
                            <table class="table table-bordered">
                                <colgroup>
                                    <col width="10%">
                                    <col width="15%">
                                    <col width="20%">
                                    <col width="20%">
                                    <col width="35%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="px-1 py-1 text-center">#</th>
                                        <th class="px-1 py-1 text-center">Date</th>
                                        <th class="px-1 py-1 text-center">Visitor Name</th>
                                        <th class="px-1 py-1 text-center">Inmate Name</th>
                                        <th class="px-1 py-1 text-center">Purpose</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    while ($row = $records->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td class="px-1 py-1 align-middle text-center"><?= $i++ ?></td>
                                        <td class="px-1 py-1 align-middle"><?= date("M d, Y", strtotime($row['visit_datetime'])) ?></td>
                                        <td class="px-1 py-1 align-middle"><?= $row['visitor_name'] ?></td>
                                        <td class="px-1 py-1 align-middle"><?= $row['con_fname'] . ' ' . $row['con_lname'] ?></td>
                                        <td class="px-1 py-1 align-middle"><?= $row['purpose'] ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div>No records found</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Header -->
    <noscript id="print-header">
    <div style="text-align: center; margin-bottom: 10px; position: relative;">
        <p style="margin: 5px 0;"><b>NATIONAL POLICE COMMISSION</b></p>
        <p style="margin: 5px 0; position: relative;">
            <img src="img/pnp_logo.png" style="width: 60px; position: absolute; left: 0; top: -10;">
            <b>PHILIPPINE NATIONAL POLICE CAPITAL REGION POLICE OFFICE</b>
            <img src="img/police-logo.png" style="width: 75px; position: absolute; right: 0; top: -10;">
        </p>
        <p style="margin: 5px 0;"><b>MANILA POLICE DISTRICT</b></p>
        <p style="margin: 5px 0;"><b>MORIONES POLICE STATION (PS-2)</b></p>
        <p>J. Nolasco corner Morga Sts. Tondo, Manila</p>
        <p style="margin: 5px 0;"><?= date("F j, Y") ?></p>
    </div>
</noscript>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        function print_r() {
            var h = $('head').clone()
            var el = $('#printout').clone()
            var ph = $($('noscript#print-header').html()).clone()
            h.find('title').text("Monthly Visitor Record Report - Print View")
            var nw = window.open("", "_blank", "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + ",height="
            + ($(window).height() * .8) + ",top=" + ($(window).height() * .1) + ",scrollbars=yes")
            nw.document.querySelector('head').innerHTML = h.html()
            nw.document.querySelector('body').innerHTML = ph[0].outerHTML + el[0].outerHTML
            nw.document.close()
            setTimeout(function(){
                nw.print()
                setTimeout(function(){
                    nw.close()
                }, 300)
            }, 200)
        }

        $('#print').click(function(){
            print_r()
        })
    </script>
</body>
</html>
