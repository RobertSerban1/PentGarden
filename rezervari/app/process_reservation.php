<?php

ob_start();
session_start();

require 'DB.php';
require 'Util.php';
require 'dao/BookingReservationDAO.php';
require 'models/Booking.php';
require 'models/Reservation.php';
require 'models/Pricing.php';
require 'models/StatusEnum.php';
require 'handlers/BookingReservationHandler.php';

if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"][1] == "false") {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["readySubmit"])) {
        $startDate = $endDate = null;
        $errors_ = null;

        if (empty($_POST["start"])) {
            $errors_ .= Util::displayAlertV1("Introdu o data de inceput.", "info");
        }
        if (empty($_POST["end"])) {
            $errors_ .= Util::displayAlertV1("Introdu o data de sfarsit.", "info");
        }
        if (!DateTime::createFromFormat('Y-m-d', $_POST["start"])) {
            $errors_ .= Util::displayAlertV1("Data de inceput invalida.", "info");
        }
        if (!DateTime::createFromFormat('Y-m-d', $_POST["end"])) {
            $errors_ .= Util::displayAlertV1("Data de sfarsit invalida.", "info");
        }
        if (empty($_POST["type"])) {
            $errors_ .= Util::displayAlertV1("Te rog introdu tipul camerei.", "info");
        }
        if (empty($_POST["adults"])) {
            $errors_ .= Util::displayAlertV1("Te rog introdu numarul de adulti.", "info");
        }

        try {
            $startDate = new DateTime($_POST["start"]);
            $endDate = new DateTime($_POST["end"]);
            if ($endDate <= $startDate) {
                $errors_ .= Util::displayAlertV1("Data de sfarsit nu poate fi mai mica sau egala decat data de sfarsit.", "info");
            }
        } catch (Exception $e) {
            $errors_ .= Util::displayAlertV1("Tipul datei este invalida!", "info");
        }

        if (!empty($errors_)) {
            echo $errors_;
        } else {
            $r = new Reservation();
            $r->setCid(Util::sanitize_xss($_POST["cid"]));
            $r->setStatus(\models\StatusEnum::PENDING_STR);
            $r->setNotes(null);
            $r->setStart(Util::sanitize_xss($_POST["start"]));
            $r->setEnd(Util::sanitize_xss($_POST["end"]));
            $r->setType(Util::sanitize_xss($_POST["type"]));
            $r->setRequirement(Util::sanitize_xss($_POST["requirement"]));
            $r->setAdults(Util::sanitize_xss($_POST["adults"]));
            $r->setChildren(Util::sanitize_xss($_POST["children"]));
            $r->setRequests(Util::sanitize_xss($_POST["requests"]));
            $unique = uniqid();
            $r->setHash($unique);

            $p = new Pricing();
            $p->setBookedDate(Util::sanitize_xss($_POST['bookedDate']));
            $p->setNights(Util::sanitize_xss($_POST['numNights']));
            $p->setTotalPrice(Util::sanitize_xss($_POST['totalPrice']));

            $brh = new BookingReservationHandler($r, $p);
            $temp = $brh->create();
            $out = array(
                "success" => "true",
                "response" => Util::displayAlertV2($brh->getExecutionFeedback(), $temp)
            );
            echo json_encode($out, JSON_PRETTY_PRINT);
        }
    }
} else {
    echo "esec";
}
