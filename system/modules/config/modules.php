<?php

    /*
     * File that handles Module operations
     */

    $themeRegistry = Sweia::getInstance()->getThemeRegistry();

    /* Set the modules sidebar menu */
    $menu = new Template(CONFIG_PATH . "templates/menus/sidebar-menu");
    $menu->items = config_modules_menu();
    $themeRegistry->addContent("main_left", $menu->parse());

    switch (@$url[2])
    {
        case "update":
            /* Update the modules list */
            JModuleManager::setupModules();
            ScreenMessage::setMessage("Module Successfully updated", "success");
        default:
            config_list_modules();
            break;
    }

    function config_list_modules()
    {
        /*
         * Here we list the modules
         */
        $themeRegistry = Sweia::getInstance()->getThemeRegistry();
        $tpl = new Template(CONFIG_PATH . "templates/inner/module-list");
        $tpl->modules = JModuleManager::getModules();

        /* Add this content to the Content area within the theme */
        $themeRegistry->addContent("content", $tpl->parse());
    }

    function config_modules_menu()
    {
        $modurl = CONFIG_URL . "modules/";
        $menu = array(
            $modurl . "enabled" => "Enabled Modules",
            $modurl . "disabled" => "Diabled Modules",
            $modurl . "update" => "Update Modules",
        );


        return $menu;
    }
    