<?php

    /**
     * Does the initial bootstrap operations for the site
     */
    /* Require the settings file */
    require_once 'site/default/settings.php';

    /* Initialize site constants */
    _jsmart_constants_initialize();


    /* Load the main constant files */
    require_once SYSTEM_INCLUDES_PATH . 'constants.inc.php';

    /* Autoloader for classes and interfaces */
    spl_autoload_register("jsmart_load_system_classes");
    spl_autoload_register("jsmart_load_system_interfaces");

    function jsmart_load_system_classes($class)
    {
        $file = CLASSES_PATH . $class . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    }

    function jsmart_load_system_interfaces($interface)
    {
        $file = INTERFACES_PATH . $interface . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    }

    /* Load System Files & Classes */
    require_once SYSTEM_INCLUDES_PATH . 'functions.inc.php';
    require_once THEME_PATH . 'Theme.php';

    /* Load the site specific includes now */
    require_once SITE_DEFAULT_FOLDER_PATH . 'constants.inc.php';

    /* Get an instance of the Sweia object */
    $sweia = Sweia::getInstance();
    $sweia->bootstrap();

    /**
     * @section Testing the database connectivity
     */
    if (!$sweia->getDB()->tryConnect())
    {
        die("Database connectivity error, please check the database access details");
    }

    /* Loading User */
    if (Session::isLoggedIn())
    {
        $sweia->setUser(new JSmartUser($_SESSION['uid']));
    }
    else
    {
        $sweia->setUser(new JSmartUser());
    }

    /* Load the core site and system files */
    require_once SYSTEM_INCLUDES_PATH . 'system.inc.php';
    require_once SITE_INCLUDES_PATH . 'site.inc.php';

    /**
     * @section Load the modules for this url 
     */
    $handlers = JPath::getUrlHandlers();
    foreach ($handlers as $handler)
    {
        if (!isset($handler['permission']) || !valid($handler['permission']))
        {
            /* There is no permission for this module at the current URL, just load it */
            include_once JModuleManager::getModule($handler['module']);
        }
        else if ($sweia->getUser()->usesPermissionSystem() && $sweia->getUser()->hasPermission($handler['permission']))
        {
            /* If the user has the permission to access this module for this URL, load the module */
            include_once JModuleManager::getModule($handler['module']);
        }
    }

    /**
     * Initialize constants that are commonly used and will be used a lot throughout the site
     */
    function _jsmart_constants_initialize()
    {
        /* Generating our Base Path and Base URL */
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];

        define("BASE_URL", rtrim($protocol . $host . '/' . SITE_FOLDER, '/') . '/');
        define("BASE_PATH", rtrim($_SERVER['DOCUMENT_ROOT'] . '/' . SITE_FOLDER, '/') . '/');
        define("SYSTEM_INCLUDES_PATH", BASE_PATH . 'system/includes/');
    }
    