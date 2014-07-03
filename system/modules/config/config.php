<?php

    /* Define config module constants */
    define("CONFIG_URL", SystemConfig::baseUrl() . "config/");
    define("CONFIG_PATH", JModuleManager::getModulePath("config"));

    $url = JPath::urlArgs();
    switch (@$url[1])
    {
        case "modules":
            require CONFIG_PATH . "modules.php";
            break;
    }

    $themeRegistry = Sweia::getInstance()->getThemeRegistry();
    
    /* Set the site title */
    $themeRegistry->setSiteTitle(Utility::getSiteName() . " Configuration");

    /* Load the admin site navbar and sidebar */
    $navbar = new Template(CONFIG_PATH . "templates/menus/navbar");
    $navbar->items = config_navbar();
    $themeRegistry->addContent("header_left", $navbar->parse());



    function config_navbar()
    {
        $items = array(
            CONFIG_URL . "modules" => "Modules",
        );
        return $items;
    }
