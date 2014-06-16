<?php

    /*
     * Here we handle managing Roles
     */

    if (@$_POST['submit'] == 'add-role')
    {
        /* Handling adding a role to the database */
        if (!valid(@$_POST['role']))
        {
            ScreenMessage::setMessage("Please fill up all the fields", "warning");
        }
        $role = new Role();
        $role->role = $_POST['role'];
        $role->description = isset($_POST['description']) ? $_POST['description'] : "";
        if ($role->save())
        {
            ScreenMessage::setMessage("Successfully Added new role", "success");
        }
    }
    if (isset($_GET['type']) && $_GET['type'] == "ajax")
    {
        switch ($_GET['op'])
        {
            case "delete-role":
                $user = Sweia::getInstance()->getUser();
                /* Here we handle deleting a role */
                if ($user->hasPermission("delete_role"))
                {
                    Role::delete(@$_GET['rid']);
                }
                exit;
                break;
        }
    }

    $url = Sweia::getInstance()->getURL();
    $themeRegistry = Sweia::getInstance()->getThemeRegistry();
    $db = Sweia::getInstance()->getDB();

    switch (@$url[3])
    {
        case "add":
            /* Load the Add Role form */
            $tpl = new Template($usermod_path . "templates/forms/add-role");
            $themeRegistry->addContent("content", $tpl->parse());
            break;
        default:
            /* Show role listing */
            $rs = $db->query("SELECT rid FROM role");
            $roles = array();
            while ($role = $db->fetchObject($rs))
            {
                $roles[] = $role->rid;
            }
            $tpl = new Template($usermod_path . "templates/inner/roles-list");
            $tpl->roles = $roles;
            $themeRegistry->addContent("content", $tpl->parse());
            break;
    }