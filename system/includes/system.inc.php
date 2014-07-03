<?php

    /**
     * This file is the controller for the entire system. 
     * It loads and runs all the necessary system objects and handles core system functionalities.
     * 
     * @author Joshua Kissoon
     * @since 20140616
     */
    $url = Sweia::getInstance()->getURL();


    /* If we're at admin section! load the admin template */
    if (isset($url[0]) && $url[0] == SiteConfig::adminUrlDirectory())
    {
        SiteConfig::$useAdminTheme = true;
    }
    
    /**
     * Check if the user is logged in and load the login form if necessary 
     */
    if (!Session::isLoggedIn() || !Session::validateUserSessionData())
    {
        /* include a file for login template */
    }