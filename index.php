<?php

    /*
     * This is the website index page that handles all requests throughout the site
     */
    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', TRUE);
    require_once 'system/bootstrap.inc.php';

    /**
     * @section Loading User Data
     */
    if (isset($_SESSION['uid']))
    {
        if ($_SESSION['user_type'] == JSmartAdmin::$user_type)
        {
            $USER = new JSmartAdmin($_SESSION['uid']);
        }
        else
        {
            $USER = new JSmartUser($_SESSION['uid']);
        }
    }
    else
    {
        $USER = new JSmartUser();
    }

    /**
     * @section Loading Data from cookies
     */
    if (!$SESSION->isLoggedIn())
    {
        /* If the user is not logged in, try loading the session and login data from cookies */
        $SESSION->loadDataFromCookies();
    }

    /**
     * @section Loading Global Variables
     */
    $URL = JPath::urlArgs();

    /**
     * @section Render the theme after the necessary module is finished with its operations 
     */
    $REGISTRY->renderPage();
    exit;
    