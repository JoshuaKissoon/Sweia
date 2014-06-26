<?php

    /**
     * Does the initial bootstrap operations for the site
     */
    /* Require the configuration files */
    require_once 'site/includes/BaseConfig.php';
    require_once 'utilities/SystemConfig.php';
    require_once 'site/utilities/SiteConfig.php';

    /* Autoloader for classes and interfaces */
    spl_autoload_register("jsmart_load_system_classes");
    spl_autoload_register("jsmart_load_system_interfaces");
    spl_autoload_register("jsmart_load_system_exceptions");

    function jsmart_load_system_classes($class)
    {
        $file = SystemConfig::classesPath() . $class . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
        
        $file = SystemConfig::includesPath() . $class . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
        
        $file = SystemConfig::utilitiesPath() . $class . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    }

    function jsmart_load_system_interfaces($interface)
    {
        $file = SystemConfig::interfacesPath() . $interface . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    }

    function jsmart_load_system_exceptions($name)
    {
        $file = SystemConfig::exceptionsPath() . $name . '.php';
        if (file_exists($file))
        {
            require_once $file;
        }
    }

    /* Load System Files & Classes */
    require_once SystemConfig::includesPath() . 'functions.inc.php';
    require_once SiteConfig::themePath() . 'Theme.php';

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

    /* Load the core site and system files */
    require_once SystemConfig::includesPath() . 'system.inc.php';
    require_once SiteConfig::includesPath() . 'site.inc.php';

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