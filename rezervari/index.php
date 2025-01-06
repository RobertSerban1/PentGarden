<?php
ob_start();
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <link rel="apple-touch-icon" sizes="180x180" href="image/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="image/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="image/favicon/favicon-16x16.png">
    <link rel="manifest" href="image/favicon/site.webmanifest">
    <link rel="mask-icon" href="image/favicon/safari-pinned-tab.svg" color="#5bbad5">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.5/css/select.dataTables.min.css">
    <link rel="stylesheet" href="css/main.css">

    <?php

    require 'lib/phpPasswordHashing/passwordLib.php';
    require 'app/DB.php';
    require 'app/Util.php';
    require 'app/dao/CustomerDAO.php';
    require 'app/dao/BookingDetailDAO.php';
    require 'app/models/RequirementEnum.php';
    require 'app/models/Customer.php';
    require 'app/models/Booking.php';
    require 'app/models/Reservation.php';
    require 'app/handlers/CustomerHandler.php';
    require 'app/handlers/BookingDetailHandler.php';

    $username = $cHandler = $bdHandler = $cBookings = null;
    $isSessionExists = false;
    $isAdmin = [];
    if (isset($_SESSION["username"])) {
        $username = $_SESSION["username"];

        $cHandler = new CustomerHandler();
        $cHandler = $cHandler->getCustomerObj($_SESSION["accountEmail"]);
        $cAdmin = new Customer();
        $cAdmin->setEmail($cHandler->getEmail());

        $bdHandler = new BookingDetailHandler();
        $cBookings = $bdHandler->getCustomerBookings($cHandler);
        $isSessionExists = true;
        $isAdmin = $_SESSION["authenticated"];
    }
    if (isset($_SESSION["isAdmin"]) && isset($_SESSION["username"])) {
        $isSessionExists = true;
        $username = $_SESSION["username"];
        $isAdmin = $_SESSION["isAdmin"];
    }

    // if (isset($_COOKIE['is_admin'])) {
    //     echo $_COOKIE['is_admin'];
    //     var_dump($isAdmin);
    // }

    ?>
    <title>Home</title>
    <?php //echo '<title>Home isAdmin=' . $isAdmin . ' $isSessionExists=' . $isSessionExists . '</title>'?>
</head>
<body>

<header>
    <div style="background-color:#fcf6ef;" id="navbarHeader" style="">
        <div class="container">
            <div class="row">
                <div class="col-sm-1 col-md-4 py-4">
                </div>
                <div class="col-sm-4  py-4 text-center">
                    <?php if ($isSessionExists) { ?>
                    <h4 style="color: #c2884f;"><?php echo $username; ?></h4>
                    <ul class="list-unstyled">
                        <?php if ($isAdmin[1] == "true" && isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "true") { ?>
                        <li><a href="admin.php" style="color: #c2884f;">Gestionati rezervarile clientilor</a></li>
                        <?php } else { ?>
                        <li><a href="#" class="my-reservations" style="color: #c2884f;">Vezi rezervarile mele</a></li>
                        <li>
                            <a href="#" style="color: #c2884f;" data-toggle="modal" data-target="#myProfileModal">Actualizeaza profil</a>
                        </li>
                        <?php } ?>
                        <li><a href="#" id="sign-out-link" style="color: #c2884f;">Delogheaza-te</a></li>
                    </ul>
                    <?php } else { ?>
                    <h4>
                        <a style="color: #c2884f;" href="sign-in.php">Autentificare</a> 
                        <a href="register.php"style="color: #c2884f;">Inregistrare </a>
                    </h4>
                   
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
     <div class="container my-3" id="my-reservations-div">
        <h4>Reservari</h4>
        <table id="myReservationsTbl" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th class="text-hide p-0" data-bookId="12">12</th>
                <th scope="col">Data de inceput</th>
                <th scope="col">Data de sfarsit</th>
                <th scope="col">Tipul camerei</th>
                <th scope="col">Cerinte</th>
                <th scope="col">Adulti</th>
                <th scope="col">Copii</th>
                <th scope="col">Cereri</th>
                <th scope="col">Perioada</th>
                <th scope="col">Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($cBookings) && $bdHandler->getExecutionFeedback() == 1) { ?>
                <?php   foreach ($cBookings as $k => $v) { ?>
                    <tr>
                        <th scope="row"><?php echo ($k + 1); ?></th>
                        <td class="text-hide p-0"><?php echo $v["id"]; ?></td>
                        <td><?php echo $v["start"]; ?></td>
                        <td><?php echo $v["end"]; ?></td>
                        <td><?php echo $v["type"]; ?></td>
                        <td><?php echo $v["requirement"]; ?></td>
                        <td><?php echo $v["adults"]; ?></td>
                        <td><?php echo $v["children"]; ?></td>
                        <td><?php echo $v["requests"]; ?></td>
                        <td><?php echo $v["timestamp"]; ?></td>
                        <td><?php echo $v["status"]; ?></td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
</header>

<main role="main">

    <section class="jumbotron text-center" style="background-color:#fcf6ef;">
        <div class="container pt-lg-5 pl-5 px-5">
            <h1 class="display-3">Rezervati acum!</h1>
            <p class="lead text-muted">Rezerva vacanta ta de vis!</p>
            <p>
                <?php if ($isSessionExists) { ?>
                <a href="#" class="btn btn-success my-2" data-toggle="modal" data-target=".book-now-modal-lg">Rezerva acum!</a>
                <?php } else { ?>
                <a href="#" class="btn btn-success my-2" data-toggle="modal" data-target=".sign-in-to-book-modal">Rezerva acum!</a>
                <?php } ?>
            </p>
        </div>
    </section>


    <div class="album py-5" style="background-color:#fff;">
        <div class="container">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="card mb-4 box-shadow">
                        <div class="card-header" style="text-align: center;">
                            <h5 class="my-0 font-weight-normal">Penthouse</h5>
                        </div>
                        <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" src="image/jacuzii.jpg" data-holder-rendered="true" style="height: 225px; width: 100%; display: block;">
                        <div class="card-body">
                            <p class="card-text">Bucurati-va de lux si confort in acest penthouse exclusivist situat intr-o locatie de top! Cu o rezervare surprinzator de accesibila, acest penthouse ofera o experienta de neuitat. Dotat cu facilitati moderne si o vedere panoramica spectaculoasa, veti avea parte de relaxare totala si intimitate. Descoperiti eleganta unui spatiu generos, perfect pentru evadari romantice sau vacante de lux. Rezervati acum si transformati-va sejurul intr-o amintire de nepretuit, intr-o atmosfera rafinata si exclusivista!</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if ($isSessionExists) { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-rtype="Double" data-toggle="modal" data-target=".book-now-modal-lg">
                                    Rezerva!
                                </button>
                                <?php } else { ?>
                                <button type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target=".sign-in-to-book-modal">
                                    Rezerva!
                                </button>
                                <?php } ?>
                                <small class="text-muted">~300 ron / noapte</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "false") : ?>
    <div class="modal fade book-now-modal-lg" tabindex="-1" role="dialog" aria-labelledby="bookNowModalLarge" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Formular de rezervare</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="reservationModalBody">
                    <?php if ($isSessionExists == 1 && $isAdmin[1] == "false") { ?>
                        <form role="form" autocomplete="off" method="post" id="multiStepRsvnForm">
                            <div class="rsvnTab">
                                <?php if ($isSessionExists) { ?>
                                    <input type="number" name="cid" value="<?php echo $cHandler->getId() ?>" hidden>
                                <?php } ?>
                                <div class="form-group row">
                                    <label for="startDate" class="col-sm-3 col-form-label">Check-in
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control"
                                                   name="startDate"  min="<?php echo Util::dateToday('0'); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="endDate" class="col-sm-3 col-form-label">Check-out
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control"  min="<?php echo Util::dateToday('1'); ?>" name="endDate" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="roomType">Room type
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <select required class="custom-select mr-sm-2"  name="roomType">
                                            <option value="<?php echo \models\RequirementEnum::DELUXE; ?>">Camera de Lux</option>
                                            <option value="<?php echo \models\RequirementEnum::DOUBLE; ?>">Camera Dubla</option>
                                            <option value="<?php echo \models\RequirementEnum::SINGLE; ?>">Camera Single</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="roomRequirement">Cerintele camerei</label>
                                    <div class="col-sm-9">
                                        <select class="custom-select mr-sm-2"  name="roomRequirement">
                                            <option value="no preference" selected>Fara preferinte</option>
                                            <option value="non smoking">Nefumator</option>
                                            <option value="smoking">Fumator</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="adults">Adulti
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <select required class="custom-select mr-sm-2"  name="adults">
                                            <option selected value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="children">Copii</label>
                                    <div class="col-sm-9">
                                        <select class="custom-select mr-sm-2"  name="children">
                                            <option selected value="0">-</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="specialRequests">Cereri speciale</label>
                                    <div class="col-sm-9">
                                        <textarea rows="3" maxlength="500"  name="specialRequests" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <button type="button" class="btn btn-info" style="margin-left: 0.8em;" data-container="body" data-toggle="popover"
                                            data-placement="right" data-content="Ora de check-in incepe la ora 15:00. Daca sunteti planificat mai tarziu, va rugam sa ne contactati telefonic.">
                                        Politica de check-in
                                    </button>
                                </div>
                            </div>

                            <div class="rsvnTab">
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="bookedDate">Data rezervata</label>
                                    <div class="col-sm-9 bookedDateTxt">
                                        July 13, 2019
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="roomPrice">Pretul camerei</label>
                                    <div class="col-sm-9 roomPriceTxt">235.75</div> 
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="numNights"><span class="numNightsTxt">3</span> Nopti </label>
                                    <div class="col-sm-9">
                                        <span class="roomPricePerNightTxt">69.63</span> ron / noapte
                                    </div>
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="numNights">De la - Pana la</label>
                                    <div class="col-sm-9 fromToTxt">
                                        Mon. July 4 to Wed. July 6
                                    </div>
                                    <label class="col-sm-3 col-form-label font-weight-bold">Taxe </label>
                                    <div class="col-sm-9">
                                        <span class="taxesTxt">0</span> ron
                                    </div>
                                    <label class="col-sm-3 col-form-label font-weight-bold">Total </label>
                                    <div class="col-sm-9">
                                        <span class="totalTxt">0.00</span> ron
                                    </div>
                                </div>
                            </div>
                            <div style="text-align:center;margin-top:40px;">
                                <span class="step"></span>
                                <span class="step"></span>
                            </div>

                        </form>
                        <div style="overflow:auto;">
                            <div style="float:right;">
                                <button type="button" class="btn btn-success" id="rsvnPrevBtn" onclick="rsvnNextPrev(-1)">Inapoi</button>
                                <button type="button" class="btn btn-success" id="rsvnNextBtn" onclick="rsvnNextPrev(1)" readySubmit="false">Inainte</button>
                            </div>
                        </div>
                    <?php } else { ?>
                        <p>Rezervarea este inregistrata clientilor.</p>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="modal sign-in-to-book-modal" tabindex="-1" role="dialog" aria-labelledby="signInToBookModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Este necesara conectarea.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>Trebuie sa te <a href="sign-in.php">autentifici</a> ca sa poti rezerva apartamentul.</h4>
                </div>
            </div>
        </div>
    </div>

    <?php if(($isSessionExists == 1 && $isAdmin[1] == "false") && isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "false") : ?>
    <div class="modal" id="myProfileModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actualizare profil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php if ($isSessionExists) { ?>
                            <form class="form" role="form" autocomplete="off" id="update-profile-form" method="post">
                                <input type="number" id="customerId" hidden
                                       name="customerId" value="<?php echo $cHandler->getId(); ?>" >
                                <div class="form-group">
                                    <label for="updateFullName">Nume complet</label>
                                    <input type="text" class="form-control" id="updateFullName"
                                           name="updateFullName" value="<?php echo $cHandler->getFullName(); ?>" >
                                </div>
                                <div class="form-group">
                                    <label for="updatePhoneNumber">Numar de telefon</label>
                                    <input type="text" class="form-control" id="updatePhoneNumber"
                                           name="updatePhoneNumber" value="<?php echo $cHandler->getPhone(); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="updateEmail">Email</label>
                                    <input type="email" class="form-control" id="updateEmail"
                                           name="updateEmail" value="<?php echo $cHandler->getEmail(); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="updatePassword">Parola noua</label>
                                    <input type="password" class="form-control" id="updatePassword"
                                           name="updatePassword"
                                           title="Cel putin 4 caractere!">
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary btn-md float-right"
                                           name="updateProfileSubmitBtn" value="Update">
                                </div>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<footer class="container">
</footer>
<script src="js/utilityFunctions.js"></script>

<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
        integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
        crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js"></script>
<script src="js/animatejscx.js"></script>
<script src="js/form-submission.js"></script>
<script>
    $(document).ready(function () {
      let reservationDiv = $("#my-reservations-div");
      reservationDiv.hide();
      $(".my-reservations").click(function () {
        reservationDiv.slideToggle("slow");
      });
      $('#myReservationsTbl').DataTable();

      // dynamically entered room type value on show modal
      $('.book-now-modal-lg').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let roomType = button.data('rtype');
        let modal = $(this);
        modal.find('.modal-body select[name=roomType]').val(roomType);
      });

      // check-in policies popover
      $('[data-toggle="popover"]').popover();

    });
</script>
<script src="js/multiStepsRsvn.js"></script>
</body>
</html>