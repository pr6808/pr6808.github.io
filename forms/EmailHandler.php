<?php
/**
 * Modular Email Handler Class
 * Handles email sending with security, validation, and error handling
 */

class EmailHandler {
    private $config;
    private $errors = [];
    
    public function __construct($config) {
        $this->config = $config;
    }
    
    /**
     * Sanitize input data
     */
    private function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email address
     */
    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Validate required fields
     */
    private function validateRequiredFields($data, $requiredFields) {
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->errors[] = ucfirst($field) . ' is required';
                return false;
            }
        }
        return true;
    }
    
    /**
     * Rate limiting check
     */
    private function checkRateLimit() {
        session_start();
        $now = time();
        $limit = $this->config['security']['rate_limit'];
        $window = $this->config['security']['rate_limit_window'];
        
        if (!isset($_SESSION['email_attempts'])) {
            $_SESSION['email_attempts'] = [];
        }
        
        $_SESSION['email_attempts'] = array_filter(
            $_SESSION['email_attempts'],
            function($timestamp) use ($now, $window) {
                return ($now - $timestamp) < $window;
            }
        );
        
        if (count($_SESSION['email_attempts']) >= $limit) {
            $this->errors[] = 'Too many requests. Please try again later.';
            return false;
        }
        
        $_SESSION['email_attempts'][] = $now;
        return true;
    }
    
    /**
     * Honeypot check for spam prevention
     */
    private function checkHoneypot($data) {
        if ($this->config['security']['honeypot_enabled']) {
            if (!empty($data['website'])) {
                $this->errors[] = 'Spam detected';
                return false;
            }
        }
        return true;
    }
    
    /**
     * Send email using PHP mail or SMTP
     */
    public function sendEmail($type, $data) {
        if (!$this->checkRateLimit()) {
            return $this->getErrorResponse();
        }
        
        if (!$this->checkHoneypot($data)) {
            return $this->getErrorResponse();
        }
        
        $sanitizedData = [];
        foreach ($data as $key => $value) {
            $sanitizedData[$key] = $this->sanitize($value);
        }
        
        if ($type === 'contact') {
            return $this->sendContactEmail($sanitizedData);
        } elseif ($type === 'newsletter') {
            return $this->sendNewsletterEmail($sanitizedData);
        }
        
        $this->errors[] = 'Invalid email type';
        return $this->getErrorResponse();
    }
    
    /**
     * Send contact form email
     */
    private function sendContactEmail($data) {
        $required = $this->config['validation']['required_fields'];
        
        if (!$this->validateRequiredFields($data, $required)) {
            return $this->getErrorResponse();
        }
        
        if (!$this->validateEmail($data['email'])) {
            $this->errors[] = 'Invalid email address';
            return $this->getErrorResponse();
        }
        
        $to = $this->config['contact']['to'];
        $subject = $this->config['contact']['subject'];
        $message = $this->buildContactMessage($data);
        
        if ($this->config['smtp']['enabled']) {
            return $this->sendViaSMTP($to, $subject, $message, $data['email'], $data['name']);
        } else {
            return $this->sendViaPHPMail($to, $subject, $message, $data['email'], $data['name']);
        }
    }
    
    /**
     * Send newsletter subscription email
     */
    private function sendNewsletterEmail($data) {
        if (empty($data['email']) || !$this->validateEmail($data['email'])) {
            $this->errors[] = 'Valid email address is required';
            return $this->getErrorResponse();
        }
        
        $to = $this->config['newsletter']['to'];
        $subject = $this->config['newsletter']['subject_prefix'] . $data['email'];
        $message = "New newsletter subscription:\n\nEmail: " . $data['email'] . "\nDate: " . date('Y-m-d H:i:s');
        
        if ($this->config['smtp']['enabled']) {
            return $this->sendViaSMTP($to, $subject, $message, $data['email'], $data['email']);
        } else {
            return $this->sendViaPHPMail($to, $subject, $message, $data['email'], $data['email']);
        }
    }
    
    /**
     * Build contact message
     */
    private function buildContactMessage($data) {
        $message = "New Contact Form Submission\n\n";
        $message .= "Name: " . $data['name'] . "\n";
        $message .= "Email: " . $data['email'] . "\n";
        $message .= "Subject: " . ($data['subject'] ?? 'Not provided') . "\n\n";
        $message .= "Message:\n" . $data['message'] . "\n\n";
        $message .= "---\n";
        $message .= "Sent: " . date('Y-m-d H:i:s') . "\n";
        $message .= "IP: " . $_SERVER['REMOTE_ADDR'];
        
        return $message;
    }
    
    /**
     * Send via PHP mail function
     */
    private function sendViaPHPMail($to, $subject, $message, $replyTo, $replyName) {
        $headers = "From: " . $this->config['smtp']['from_name'] . " <" . $this->config['smtp']['from_email'] . ">\r\n";
        $headers .= "Reply-To: " . $replyName . " <" . $replyTo . ">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        if (mail($to, $subject, $message, $headers)) {
            return $this->getSuccessResponse();
        } else {
            $this->errors[] = 'Failed to send email. Please try again later.';
            return $this->getErrorResponse();
        }
    }
    
    /**
     * Send via SMTP
     */
    private function sendViaSMTP($to, $subject, $message, $replyTo, $replyName) {
        require_once __DIR__ . '/../assets/vendor/php-email-form/php-email-form.php';
        
        try {
            $mail = new PHP_Email_Form();
            $mail->ajax = true;
            $mail->to = $to;
            $mail->from_email = $replyTo;
            $mail->from_name = $replyName;
            $mail->subject = $subject;
            
            $mail->smtp = $this->config['smtp'];
            
            $lines = explode("\n", $message);
            foreach ($lines as $line) {
                $mail->add_message($line, '', 0);
            }
            
            $result = $mail->send();
            
            if ($result === 'OK') {
                return $this->getSuccessResponse();
            } else {
                $this->errors[] = $result;
                return $this->getErrorResponse();
            }
        } catch (Exception $e) {
            $this->errors[] = 'SMTP Error: ' . $e->getMessage();
            return $this->getErrorResponse();
        }
    }
    
    /**
     * Get error response
     */
    private function getErrorResponse() {
        return implode(', ', $this->errors);
    }
    
    /**
     * Get success response
     */
    private function getSuccessResponse() {
        return 'OK';
    }
}
