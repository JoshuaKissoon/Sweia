<?php

    /**
     * Mail class that can be used to send emails throught the website. Includes email templates features, etc
     * 
     * @author Joshua Kissoon
     * @since 20130808
     * @updated 20140615 
     */

    class EMail
    {

        private $recipients = array();
        public $sender, $subject, $message;

        /**
         * Constructor of the email class doing nothing. 
         */
        function __construct()
        {            
            return $this;
        }

        /**
         * Adds a recipient to the email
         * 
         * @param $recipient The email address of the recipient
         */
        public function addRecipient($recipient)
        {
            $this->recipients[] = $recipient;
            return $this;
        }

        /**
         * Sets the sender of the email
         * 
         * @param $sender The email address of the sender
         */
        public function setSender($sender)
        {
            $this->sender = $sender;
            return $this;
        }

        /**
         * Sets the message to be sent
         * 
         * @param $message
         */
        public function setMessage($message)
        {
            $this->message = $message;
            return $this;
        }

        /**
         * Sets the subject of the email
         * 
         * @param $subject
         */
        public function setSubject($subject)
        {
            $this->subject = $subject;
            return $this;
        }

        /**
         * Composes the email, adds the necessary headers and sends the email
         */
        public function sendMail()
        {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= "From: $this->sender" . "\r\n";
            $recipients = implode(", ", $this->recipients);
            mail($recipients, $this->subject, $this->message, $headers);
        }

        /**
         * A quick single-method call that composes and sends an email at once
         * 
         * @param $recipient A Recipient to send the email to
         * @param $subject
         * @param $message
         * @param $from
         */
        public static function quickMail($recipient, $subject, $message, $from)
        {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= "From: $from" . "\r\n";
            mail($recipient, $subject, $message, $headers);
        }

    }
    