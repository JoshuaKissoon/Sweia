<?php

    /*
     * This is the website index page that handles all requests throughout the site
     */
    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', TRUE);
    require_once 'system/bootstrap.inc.php';

    /**
     * @section Loading Global Variables
     * @note This has to be done first since some modules and classes use these Global Variables
     */
    $URL = JPath::urlArgs();
    
    /**
     * @section Loading User Data
     */
    if (Session::isLoggedIn())
    {
        if ($_SESSION['user_type'] == JSmartUser::$user_type)
        {
            $USER = new JSmartUser($_SESSION['uid']);
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
    if (!Session::isLoggedIn())
    {
        /* If the user is not logged in, try loading the session and login data from cookies */
        Session::loadDataFromCookies();
    }

    /**
     * @section Load the modules for this url 
     */
    $handlers = JPath::getUrlHandlers();
    foreach ($handlers as $handler)
    {
        if (!isset($handler['permission']) || !valid($handler['permission']))
        {
            /* There is no permission for this module at the current URL, just load it */
            include JModuleManager::getModule($handler['module']);
        }
        else if ($USER->usesPermissionSystem() && $USER->hasPermission($handler['permission']))
        {
            /* If the user has the permission to access this module for this URL, load the module */
            include JModuleManager::getModule($handler['module']);
        }
    }

    /**
     * @section Render the theme after the necessary module is finished with its operations 
     */
    $REGISTRY->renderPage();
    exit;
    