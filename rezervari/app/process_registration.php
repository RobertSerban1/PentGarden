<?php

require '../lib/phpPasswordHashing/passwordLib.php';

require 'DB.php';
require 'Util.php';
require 'dao/CustomerDAO.php';
require 'models/Customer.php';
require 'handlers/CustomerHandler.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitBtn"])) {
    $errors_ = null;

    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors_ .= Util::displayAlertV1("Te rog introdu o adresa de email valida.", "warning");
    }
    if (strlen($_POST["password"]) < 4 || strlen($_POST["password2"]) < 4) {
        $errors_ .= Util::displayAlertV1("Parola trebuie sa aiba cel putin 4 caractere.", "warning");
    }
    if (!empty($_POST["password"]) && !empty($_POST["password2"])) {
        if ($_POST["password"] != $_POST["password2"]) {
            $errors_ .= Util::displayAlertV1("Parola nu se potriveste.", "warning");
        }
    }

    if (!empty($errors_)) {
        echo $errors_;
    } else {
        $customer = new Customer();
        $customer->setFullName(Util::sanitize_xss($_POST["fullName"]));
        $customer->setEmail(Util::sanitize_xss($_POST["email"]));
        $customer->setPhone(Util::sanitize_xss($_POST["phoneNumber"]));
        $customer->setPassword(Util::sanitize_xss($_POST["password"]));

        $handler = new CustomerHandler();
        $handler->insertCustomer($customer);
        echo Util::displayAlertV1($handler->getExecutionFeedback(), "info");
    }
}

