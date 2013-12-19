<?php

    /**
     * @desc A general class containing the main methods for the theming system to work with everything else
     * @author Joshua Kissoon
     * @date 20131202
     */
    class Theme
    {

        /**
         * @desc Add the theme's libraries and scripts 
         */
        public static function init()
        {
            global $REGISTRY;

            /* Adding Modernizr */
            $REGISTRY->addScript(THEME_LIBRARIES_URL . "foundation/js/modernizr.js", 1, true);

            /* Adding JQuery */
            $REGISTRY->addScript(THEME_LIBRARIES_URL . "jquery/jquery-2.0.3.min.js", 2, true);
            
            
            /* Adding foundation */
            $REGISTRY->addCss(THEME_LIBRARIES_URL . "foundation/css/foundation.min.css");
            $REGISTRY->addScript(THEME_LIBRARIES_URL . "foundation/js/foundation.min.js");

            $REGISTRY->addCss(CSS_URL . "style.css");
            $REGISTRY->addCss(array("file" => CSS_URL . "print.css", "media" => "print"));
            $REGISTRY->addCss(array('file' => CSS_URL . 'tablet.css', 'media' => 'all and (min-width: 400px) and (max-width: 900px)'));
            $REGISTRY->addCss(array('file' => CSS_URL . 'mobile.css', 'media' => 'all and (min-width: 0px) and (max-width: 400px)'));
            
            
            $REGISTRY->addScript(THEME_SCRIPTS_URL . "main.min.js", 20);
        }

        /**
         * @desc Formats the screen messages
         * @return The formatted screen messages
         */
        public static function getFormattedScreenMessages()
        {
            /* Get the messages from the screen messages class */
            $messages = ScreenMessage::getMessages();

            if (count($messages) < 1)
            {
                return false;
            }

            /* If there are messages, generate the ul */
            $template = new Template(TEMPLATES_PATH . "/inner/screen-messages");
            $template->messages = $messages;
            $template->message_count = count($messages);
            return $template->parse();
        }

    }
    