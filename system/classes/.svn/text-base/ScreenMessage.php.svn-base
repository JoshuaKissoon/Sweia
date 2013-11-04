<?php

    /**
     * @author Joshua Kissoon
     * @date 20121212
     * @description Class that handles messages that are outputted to the user
     */
    class ScreenMessage
    {

       public static function setMessage($message, $type = 'info')
       {
          /*
           * Saves a message in session to show to the user on the next page load
           * @params
           *  $message - Message to store
           *  $type - either info, success, warning, validation, or error
           */
          if (!valid(@$_SESSION['screen_messages']))
          {
             $_SESSION['screen_messages'] = array();
          }
          if (!valid(@$_SESSION['screen_messages'][$type]))
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

       public static function getMessages()
       {
          /*
           * Messages are stored in an array in session,
           * when this function is called, it returns an <ul> with the messages that are in session
           */
          if (isset($_SESSION['screen_messages']))
          {
             /* If there are messages, generate the ul */
             $template = new Template(TEMPLATES_PATH . "/inner/screen-messages");
             $template->messages = $_SESSION['screen_messages'];
             $template->message_count = count($_SESSION['screen_messages']);
             unset($_SESSION['screen_messages']);   // Clear the messages after they have been displayed
             return $template->parse();
          }
          return "";
       }

    }