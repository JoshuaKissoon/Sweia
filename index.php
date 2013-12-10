<?php

    /*
     * This is the website index page that handles all requests throughout the site
     */
    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', TRUE);
    require_once 'system/bootstrap.inc.php';

    /* Load the data used throughout the site */
    $USER = new JSmartUser(@$_SESSION['uid']);
    $URL = JPath::urlArgs();

    if (!$SESSION->isLoggedIn())
    {
        /* If the user is not logged in, try loading the session and login data from cookies */
        $SESSION->loadDataFromCookies();
    }

    /* Get the modules that handles this URL */
    $handlers = JPath::getUrlHandlers();
    foreach ($handlers as $handler)
    {
        /* If the user has the permission to access this module for this URL, load the module */
        if ($USER->hasPermission($handler['permission']))
        {
            include JModuleManager::getModule($handler['module']);
        }
    }

    /* Render the theme after the necessary module is finished with its operations */
    $REGISTRY->renderPage();
    exit;
    