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
         * Redirects the user to a specified URL if no redirect_to url argument is set
         * 
         * @param [optional] $url A URL to redirect to in the case no parameter is set
         */
        public static function redirectTo($url = null)
        {
            if (isset($_GET['return_url']))
            {
                $location = $_GET['return_url'];
            }
            else
            {
                $location = $url;
            }

            /* Redirect to the specified URL */
            header("Location: $location");
            exit();
        }

    }
    