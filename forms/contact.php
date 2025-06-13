<?php
/**
 * PHP Email Form handler
 */

$receiving_email_address = 'petermwewa@goldenictsolutions.com';

// Load PHP Email Form class
if (file_exists($php_email_form = 'assets/vendor/php-email-form/php-email-form.php')) {
  include($php_email_form);
} else {
  die('Unable to load PHP Email Form library!');
}

$contact = new PHP_Email_Form;
$contact->ajax = true;

$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'] ?? '';
$contact->from_email = $_POST['email'] ?? '';
$contact->subject = $_POST['subject'] ?? 'New Message from Website';

$contact->add_message($_POST['name'], 'Name');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

// Enable SMTP with Zoho SSL
$contact->smtp = array(
  'host' => 'smtp.zoho.com',
  'username' => 'petermwewa@goldenictsolutions.com',
  'password' => 'YOUR_ZOHO_APP_PASSWORD', // Replace this with your actual Zoho App Password
  'port' => '465',
  'encryption' => 'ssl'
);

// Optional: enable debug for testing
// $contact->debug = true;

echo $contact->send();