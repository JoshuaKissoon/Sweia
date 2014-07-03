<?php

    $usermod_path = JModuleManager::getModulePath("usermanager");
    $usermod_url = JModuleManager::getModuleURL("usermanager");
    $usermod_rel_url = "admin/usermanager/";
    
    $themeRegistry = Sweia::getInstance()->getThemeRegistry();

    $themeRegistry->addContent("left_sidebar", users_get_menu());
    $themeRegistry->addCss($usermod_url . "usermanager.css");
    
    $url = Sweia::getInstance()->getURL();
    
    switch(@$url[2])
    {
       case "users":
          require "users.php";
          break;
       case "roles":
          require 'roles.php';
          break;
       case "permissions":
          require 'permissions.php';
          break;
    }

    function users_get_menu()
    {
       /* Returns the user's menu */
       global $usermod_path, $usermod_rel_url;
       $menu = array(
           $usermod_rel_url . "users" => array(
               "title" => "Users",
               "class" => "users users-mi",
           ),
           $usermod_rel_url . "users/add" => array(
               "title" => "Add User",
               "class" => "add-user users-mi",
           ),
           $usermod_rel_url . "roles" => array(
               "title" => "Roles",
               "class" => "roles users-mi",
           ),
           $usermod_rel_url . "roles/add" => array(
               "title" => "Add Role",
               "class" => "add-role users-mi",
           ),
           $usermod_rel_url . "permissions" => array(
               "title" => "Permissions",
               "class" => "perm users-mi",
           ),
       );
       $tpl = new Template($usermod_path . "templates/menus/main-menu");
       $tpl->menu_items = JPath::parseMenu($menu);
       return $tpl->parse();
    }