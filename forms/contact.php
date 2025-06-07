<?php
/**
 * PHP Email Form handler
 * Template from: https://bootstrapmade.com/php-email-form/
 */

$receiving_email_address = 'goldenictsolutionsltd@yahoo.com';

// Load the PHP Email Form library
if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
  include($php_email_form);
} else {
  die('Unable to load the "PHP Email Form" Library!');
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

// Uncomment this section to use SMTP (optional):

$contact->smtp = array(
  'host' => 'smtp.mail.yahoo.com',
  'username' => 'goldenictsolutionsltd@yahoo.com',
  'password' => 'YOUR_APP_PASSWORD',
  'port' => '587',
  'encryption' => 'tls'
);

echo $contact->send();
