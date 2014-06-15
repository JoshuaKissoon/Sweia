<?php

    /*
     * Does the basic database setup for the site
     */

    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', TRUE);
    require_once 'system/bootstrap.inc.php';

    /* Load Modules */
    JModuleManager::setupModules();

    /* Setup Super Admin User Account */