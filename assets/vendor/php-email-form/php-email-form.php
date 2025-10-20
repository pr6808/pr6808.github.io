<?php

class PHP_Email_Form {
    public $ajax = false;
    public $to;
    public $from_name;
    public $from_email;
    public $subject;
    public $smtp;
    public $debug = false;
    
    private $messages = array();
    
    public function add_message($content, $label, $priority = 0) {
        $this->messages[] = array(
            'label' => $label,
            'content' => $content,
            'priority' => $priority
        );
    }
    
    public function send() {
        if (empty($this->to)) {
            return 'Email recipient (to) is not set.';
        }
        
        if (empty($this->from_email)) {
            return 'Email sender (from_email) is not set.';
        }
        
        $email_body = '';
        foreach ($this->messages as $message) {
            $email_body .= $message['label'] . ": " . $message['content'] . "\n";
        }
        
        $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
        $headers .= "Reply-To: " . $this->from_email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        if (!empty($this->smtp)) {
            return $this->send_smtp($email_body);
        } else {
            if (mail($this->to, $this->subject, $email_body, $headers)) {
                return 'OK';
            } else {
                return 'Error: Unable to send email. Please check server mail configuration.';
            }
        }
    }
    
    private function send_smtp($email_body) {
        if (!isset($this->smtp['host']) || !isset($this->smtp['username']) || 
            !isset($this->smtp['password']) || !isset($this->smtp['port'])) {
            return 'SMTP configuration is incomplete.';
        }
        
        require_once __DIR__ . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            if ($this->debug) {
                $mail->SMTPDebug = 2;
            }
            
            $mail->isSMTP();
            $mail->Host = $this->smtp['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp['username'];
            $mail->Password = $this->smtp['password'];
            $mail->SMTPSecure = isset($this->smtp['encryption']) ? $this->smtp['encryption'] : 'tls';
            $mail->Port = $this->smtp['port'];
            
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($this->to);
            $mail->addReplyTo($this->from_email, $this->from_name);
            
            $mail->isHTML(false);
            $mail->Subject = $this->subject;
            $mail->Body = $email_body;
            
            $mail->send();
            return 'OK';
        } catch (Exception $e) {
            if ($this->debug) {
                return "SMTP Error: {$mail->ErrorInfo}";
            }
            return 'Error: Unable to send email via SMTP. Please try again later.';
        }
    }
}
