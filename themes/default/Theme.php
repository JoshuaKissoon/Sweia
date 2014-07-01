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
            $themeRegistry = Sweia::getInstance()->getThemeRegistry();

            /* Adding foundation */
            $themeRegistry->addCss(SiteConfig::themeLibrariessUrl() . "foundation/foundation-5.3.0/css/foundation.min.css");
            $themeRegistry->addScript(SiteConfig::themeLibrariessUrl() . "foundation/foundation-5.3.0/js/vendor/modernizr.js", 1);
            $themeRegistry->addScript(SiteConfig::themeLibrariessUrl() . "foundation/foundation-5.3.0/js/foundation.min.js");

            /* Adding JQuery */
            $themeRegistry->addScript(SiteConfig::themeLibrariessUrl() . "jquery/jquery-2.1.1.min.js", 2);

            $themeRegistry->addCss(SiteConfig::themeCssUrl() . "style.css");
            $themeRegistry->addCss(array("file" => SiteConfig::themeCssUrl() . "print.css", "media" => "print"));
            $themeRegistry->addCss(array('file' => SiteConfig::themeCssUrl() . 'tablet.css', 'media' => 'all and (min-width: 400px) and (max-width: 900px)'));
            $themeRegistry->addCss(array('file' => SiteConfig::themeCssUrl() . 'mobile.css', 'media' => 'all and (min-width: 0px) and (max-width: 400px)'));


            $themeRegistry->addScript(SiteConfig::themeScriptsUrl() . "main.min.js", 20);
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
            $template = new Template(SiteConfig::templatesPath() . "/inner/screen-messages");
            $template->messages = $messages;
            $template->message_count = count($messages);
            return $template->parse();
        }

    }
    