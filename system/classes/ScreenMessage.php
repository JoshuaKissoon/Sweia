<?php

    /**
     * Class that handles messages that are outputted to the user
     * 
     * @author Joshua Kissoon
     * @date 20121212
     */
    class ScreenMessage
    {

        /**
         * Message Type Constants 
         */
        const MESSAGE_TYPE_INFO = "info";
        const MESSAGE_TYPE_SUCCESS = "success";
        const MESSAGE_TYPE_WARNING = "warning";
        const MESSAGE_TYPE_ERROR = "error";

        /**
         * Saves a message in session to show to the user on the next page load
         * 
         * @param $message Message to store
         * @param $type Either info, success, warning or error
         */
        public static function setMessage($message, $type = ScreenMessage::MESSAGE_TYPE_INFO)
        {
            if (!isset($_SESSION['screen_messages']) || !is_array($_SESSION['screen_messages']))
            {
                $_SESSION['screen_messages'] = array();
            }
            if (!isset($_SESSION['screen_messages'][$type]) || !is_array($_SESSION['screen_messages'][$type]))
            {
                $_SESSION['screen_messages'][$type] = array();
            }
            if (valid($message))
            {
                $_SESSION['screen_messages'][$type][] = $message;
            }
            else
            {
                return false;
            }
        }

        /**
         * Add multiple messages at once
         * 
         * @param $messages An array of messages to store
         * @param $itype Either info, success, warning or error
         */
        public static function setMessages($messages, $itype = "info")
        {
            if (!is_array($messages))
            {
                $messages = array($messages);
            }

            foreach ($messages as $message)
            {
                $msg = is_array($message) ? $message['msg'] : $message;
                $type = (is_array($message) && isset($message['type'])) ? $message['type'] : $itype;
                self::setMessage($msg, $type);
            }
        }

        /**
         * Messages are stored in an array in session, This method grabs, formats and returns these messages
         * 
         * @return Array - messages that are in the session
         */
        public static function getMessages()
        {
            if (isset($_SESSION['screen_messages']))
            {
                $msgs = $_SESSION['screen_messages'];
                unset($_SESSION['screen_messages']);   // Clear the messages after they have been displayed
                return $msgs;
            }
            return array();
        }

    }
    