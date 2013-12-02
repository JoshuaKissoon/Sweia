<?php

    /* Define config module constants */
    define("CONFIG_URL", BASE_URL . "config/");
    define("CONFIG_PATH", JModuleManager::getModulePath("config"));

    $url = JPath::urlArgs();
    switch (@$url[1])
    {
        case "modules":
            require CONFIG_PATH . "modules.php";
            break;
    }

    /* Set the site title */
    $THEMER->setSiteTitle(JSmart::getSiteName() . " Configuration");

    /* Load the admin site navbar and sidebar */
    $navbar = new Template(CONFIG_PATH . "templates/menus/navbar");
    $navbar->items = config_navbar();
    $THEMER->addContent("header_left", $navbar->parse());



    function config_navbar()
    {
        $items = array(
            CONFIG_URL . "modules" => "Modules",
        );
        return $items;
    }
