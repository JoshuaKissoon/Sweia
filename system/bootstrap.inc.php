<?php

    /* Require the settings file */
    require_once 'site/default/settings.php';

    /* Bootstrapping the site */
    _jsmart_constants_initialize();


    /* Load the main constant files */
    require_once INCLUDES_PATH . 'constants.inc.php';

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
    require_once CLASSES_PATH . 'Themer.php';
    require_once CLASSES_PATH . 'JUser.php';
    require_once CLASSES_PATH . 'User.php';
    require_once CLASSES_PATH . 'HTML.php';
    require_once CLASSES_PATH . 'JPager.php';
    require_once CLASSES_PATH . 'System.php';
    require_once CLASSES_PATH . 'JPath.php';
    require_once CLASSES_PATH . 'Role.php';
    require_once CLASSES_PATH . 'URL.php';
    require_once THEME_PATH . 'Theme.php';

    /* Load the site specific includes now */
    require_once SITE_DEFAULT_FOLDER_PATH . 'includes.inc.php';
    require_once SITE_DEFAULT_FOLDER_PATH . 'constants.inc.php';

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
    