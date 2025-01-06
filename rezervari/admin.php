<?php
ob_start();
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.5/css/select.dataTables.min.css">
    <link rel="stylesheet" href="css/main.css">
    <?php

    require 'lib/phpPasswordHashing/passwordLib.php';
    require 'app/DB.php';
    require 'app/Util.php';
    require 'app/models/StatusEnum.php';
    require 'app/models/RequirementEnum.php';
    require 'app/dao/CustomerDAO.php';
    require 'app/dao/BookingDetailDAO.php';
    require 'app/models/Customer.php';
    require 'app/models/Booking.php';
    require 'app/models/Reservation.php';
    require 'app/handlers/CustomerHandler.php';
    require 'app/handlers/BookingDetailHandler.php';

    $username = null;
    $isSessionExists = $isAdmin = false;
    $pendingReservation = $confirmedReservation = $totalCustomers = $totalReservations = null;
    $allBookings = $cCommon = $allCustomer = null;
    if (isset($_SESSION["username"]))
    {
        $username = $_SESSION["username"];
        $isSessionExists = true;

        $cHandler = new CustomerHandler();
        $cHandler = $cHandler->getCustomerObj($_SESSION["accountEmail"]);

        $cAdmin = new Customer();
        $cAdmin->setEmail($cHandler->getEmail());

        // display all reservations
        $bdHandler = new BookingDetailHandler();
        $allBookings = $bdHandler->getAllBookings();
        $cCommon = new CustomerHandler();
        $allCustomer = $cCommon->getAllCustomer();

        // reservation stats
        $pendingReservation = $bdHandler->getPending();
        $confirmedReservation = $bdHandler->getConfirmed();
        $totalCustomers = $cCommon->totalCustomersCount();
        $totalReservations = count($bdHandler->getAllBookings());
    }
    if (isset($_SESSION["isAdmin"]) && isset($_SESSION["username"])) {
        $isSessionExists = true;
        $username = $_SESSION["username"];
        $isAdmin = $_SESSION["isAdmin"];
    }

    ?>

    <title>Rezervari</title>
</head>
<body>

<header>
    <div class="collapse show" style="background-color: #fcf6ef;" id="navbarHeader">
        <div class="container">
            <div class="row">
                    <div class="col-sm-4 offset-md-4 py-4 text-center">
                    <!-- User full name or email if logged in -->
                    <?php if ($isSessionExists) { ?>
                    <h4 style="color: #c2884f;" ><?php echo $username; ?></h4>
                    <ul class="list-unstyled">
                        <li><a href="index.php" style="color: #c2884f;">Acasa</a></a></li>
                        <li><a href="#" id="sign-out-link" style="color: #c2884f;">Delogare</a></li>
                    </ul>
                    <?php } else { ?>
                    <h4  style="color: #c2884f;">
                        <a class="text-black" href="sign-in.php">Inregistrare</a> <span class="text-white">or</span>
                        <a href="register.php" class="text-white">Autentificare</a>
                    </h4>
                    <p class="text-muted">Conectati-va pentru a putea profita de preturile noastre.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    </header>

<main role="main">

    <?php if ($isSessionExists && $isAdmin) { ?>
                            </div>
                   </div>
    </div>

    <div class="container" id="tableContainer">
        <ul class="nav nav-tabs" id="adminTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="reservation-tab" data-toggle="tab" href="#reservation" role="tab"
                   aria-controls="reservation" aria-selected="true">Rezervare</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="customers-tab" data-toggle="tab" href="#customers" role="tab"
                   aria-controls="customers" aria-selected="false">Clienti</a>
            </li>
        </ul>
        <div class="tab-content py-3" id="adminTabContent">
            <div class="tab-pane fade show active" id="reservation" role="tabpanel" aria-labelledby="reservation-tab">
                <table id="reservationDataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th class="text-hide p-0" data-bookId="12">12</th>
                        <th scope="col">Email</th>
                        <th scope="col">Inceput</th>
                        <th scope="col">Sfarsit</th>
                        <th scope="col">Tipul camerei</th>
                        <th scope="col">Perioada</th>
                        <th scope="col">Status</th>
                        <th scope="col">Observatii</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($allBookings)) { ?>
                        <?php   foreach ($allBookings as $k => $v) { ?>
                            <tr>
                                <th scope="row"><?php echo ($k + 1); ?></th>
                                <td class="text-hide p-0" data-id="<?php echo $v["id"]; ?>">
                                    <?php echo $v["id"]; ?>
                                </td>
                                <?php $cid = $v["cid"]; ?>
                                <td><?php echo $cCommon->getCustomerObjByCid($cid)->getEmail(); ?></td>
                                <td><?php echo $v["start"]; ?></td>
                                <td><?php echo $v["end"]; ?></td>
                                <td><?php echo $v["type"]; ?></td>
                                <td><?php echo $v["timestamp"]; ?></td>
                                <td><?php echo $v["status"]; ?></td>
                                <td><?php echo $v["notes"]; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="my-3">
                    <div class="row">
                        <div class="col-6">
                            <label class="text-secondary font-weight-bold">Selectate:</label>
                            <button type="button" id="confirm-booking" class="btn btn-outline-success btn-sm">Confirmare
                            </button>
                            <button type="button" id="cancel-booking" class="btn btn-outline-danger btn-sm">Anulare
                            </button>
                        </div>
                        <div class="col-6 text-right">
                            View:
                            <input type="radio" name="viewOption" value="confirmed">&nbsp;Confirmare&nbsp;
                            <input type="radio" name="viewOption" value="pending">&nbsp;Asteptare
                            <input type="radio" name="viewOption" value="all">&nbsp;Toate
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="customers" role="tabpanel" aria-labelledby="customers-tab">
                <table id="customerTable" class="table table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nume complet</th>
                        <th scope="col">Email</th>
                        <th scope="col">Telefon</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($allCustomer)) { ?>
                        <?php foreach ($cCommon->getAllCustomer() as $key => $value) { ?>
                        <tr>
                            <td scope="row"><?php echo ($key + 1); ?></td>
                            <td><?php echo $value->getFullName(); ?></td>
                            <td><?php echo $value->getEmail(); ?></td>
                            <td><?php echo $value->getPhone(); ?></td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmare rezervarile selectate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Esti sigur?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirmTrue">Da</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Nu</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Anulare rezervari selectate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Esti sigur?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="cancelTrue">Da</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Nu</button>
                </div>
            </div>
        </div>
    </div>

    <?php } ?>

</main>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
        integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
        crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js"></script>
<script src="js/form-submission.js"></script>
<script src="js/admin.js"></script>
</body>
</html>