<?php

    /**
     * Provides system level functionality
     *
     * @author Joshua Kissoon
     * @since 20140624
     */
    class System
    {

        /**
         * Redirects the user to a specified URL. 
         * If no URL is set, redirects the user to any return URL or to the home page
         * 
         * @param [optional] $url A URL to redirect to in the case no parameter is set
         */
        public static function redirect($url = null)
        {
            if (isset($url))
            {
                $location = $url;
            }
            else if (isset($_GET['return_url']))
            {
                $location = $_GET['return_url'];
            }
            else
            {
                $location = SystemConfig::baseUrl();
            }

            /* Redirect to the specified URL */
            header("Location: $location");
            exit();
        }

        /**
         * Redirects the user to a specified URL
         * 
         * @param String $url An internal URL to redirect the user to
         */
        public static function redirectInternal($url)
        {
            $redirect_url = JPath::fullUrl($url);
            self::redirect($redirect_url);
        }

    }
    