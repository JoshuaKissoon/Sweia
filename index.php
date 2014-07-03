<?php

    /*
     * This is the website index page that handles all requests throughout the site
     */
    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', TRUE);
    require_once 'system/bootstrap.inc.php';


    /**
     * @section Render the theme after the necessary module is finished with its operations 
     */
    Sweia::getInstance()->getThemeRegistry()->renderPage();
    exit;
    