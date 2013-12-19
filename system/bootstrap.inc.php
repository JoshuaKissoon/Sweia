<?php
    /* Require the settings file */
    require_once 'site/default/settings.php';

    /* Bootstrapping the site */
    _jsmart_constants_initialize();


    /* Load the main constant files */
    require_once INCLUDES_PATH . 'constants.inc.php';
    
    /* Load Interfaces */
    require_once INTERFACES_PATH . 'User.php';

    /* Load System Files */
    require_once INCLUDES_PATH . 'functions.inc.php';
    require_once CLASSES_PATH . 'Database.php';
    require_once CLASSES_PATH . 'JSmart.php';
    require_once CLASSES_PATH . 'ScreenMessage.php';
    require_once CLASSES_PATH . 'JModule.php';
    require_once CLASSES_PATH . 'JModuleManager.php';
    require_once CLASSES_PATH . 'EMail.php';
    require_once CLASSES_PATH . 'Image.php';
    require_once CLASSES_PATH . 'Session.php';
    require_once CLASSES_PATH . 'Template.php';
    require_once CLASSES_PATH . 'Registry.php';
    require_once CLASSES_PATH . 'Role.php';
    require_once CLASSES_PATH . 'JSmartUser.php';
    require_once CLASSES_PATH . 'JSmartAdmin.php';
    require_once CLASSES_PATH . 'HTML.php';
    require_once CLASSES_PATH . 'JPager.php';
    require_once CLASSES_PATH . 'System.php';
    require_once CLASSES_PATH . 'JPath.php';
    require_once CLASSES_PATH . 'URL.php';
    require_once THEME_PATH . 'Theme.php';

    /* Load the site specific includes now */
    require_once SITE_DEFAULT_FOLDER_PATH . 'constants.inc.php';

    /* Initialize the theme */
    Theme::init();

    /* Load the modules for this url */
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
    