<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$Mail = new PHPMailer(true);

$Mail->isSMTP();
$Mail->SMTPAuth = true;

$Mail->Host = "smtp.hostinger.com";
$Mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$Mail->Port = 587;

$Mail->Username = "trevoemail@trevoapp.com";
$Mail->Password = 'sfrIHn96ssf8u9dhjL5s$QTx';

$Mail->isHTML(true);

return $Mail;
