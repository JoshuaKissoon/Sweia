<?php

    /**
     * @file Bootstrap handler for JSmart
     * @desc Does the initial bootstrap operations for the site
     */
    /* Require the settings file */
    require_once 'site/default/settings.php';

    /* Initialize site constants */
    _jsmart_constants_initialize();


    /* Load the main constant files */
    require_once INCLUDES_PATH . 'constants.inc.php';

    /* Load Interfaces */
    require_once INTERFACES_PATH . 'User.php';

    /* Autoloader for system classes */
    spl_autoload_register(function($class)
    {
        $file = CLASSES_PATH . $class . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    });

    /* Load System Files & Classes */
    require_once INCLUDES_PATH . 'functions.inc.php';
    require_once CLASSES_PATH . 'Database.php';
    require_once CLASSES_PATH . 'Registry.php';
    require_once THEME_PATH . 'Theme.php';

    /* Load the site specific includes now */
    require_once SITE_DEFAULT_FOLDER_PATH . 'constants.inc.php';

    /**
     * @section System Initialization
     */
    Theme::init();  // Initialize the theme
    session_start();    // Start the session

    /**
     * @section Testing the database connectivity
     */
    $DB = new Database();
    if (!$DB->tryConnect())
    {
        die("Database connectivity error, please check the database access details");
    }

    /**
     * @section Loading the different handlers for a URL
     */
    function _jsmart_constants_initialize()
    {
        /* Add our constants that are commonly used and will be used a lot throughout the site */

        /* Generating our Base Path and Base URL */
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];

        define("BASE_URL", rtrim($protocol . $host . '/' . SITE_FOLDER, '/') . '/');
        define("BASE_PATH", rtrim($_SERVER['DOCUMENT_ROOT'] . '/' . SITE_FOLDER, '/') . '/');
        define("INCLUDES_PATH", BASE_PATH . 'system/includes/');
    }
    