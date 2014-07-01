<?php

    /*
     * Here we handle managing users
     */

    if (isset($_POST['submit']) && $_POST['submit'] == 'add-user')
    {
        /* Handling adding a user to the database */
        if (!isset($_POST['username']) || !valid($_POST['username']) ||
                !isset($_POST['password']) || !valid($_POST['password']) ||
                !isset($_POST['fname']) || !valid($_POST['fname']))
        {
            ScreenMessage::setMessage("Please fill up all the fields", "warning");
        }
        
        $user = new JSmartUser();
        $user->username = $_POST['username'];
        $user->setPassword($_POST['password']);
        $user->first_name = $_POST['fname'];
        $user->last_name = isset($_POST['lname']) ? $_POST['lname'] : "";

        if (isset($_POST['roles']) && is_array($_POST['roles']))
        {
            foreach ($_POST['roles'] as $rid)
            {
                $user->addRole($rid);
            }
        }

        if ($user->insert())
        {
            ScreenMessage::setMessage("Successfully added a new user.", "success");
        }
    }

    if (isset($_GET['type']) && $_GET['type'] == "ajax")
    {
        switch ($_GET['op'])
        {
            case "delete-user":
                /* Check if there is a integer value */
                if (!isset($_GET['uid']) || !is_numeric($_GET['uid']))
                {
                    exit;
                }

                /* Here we handle deleting a user */
                $user = Sweia::getInstance()->getUser();
                if ($user->hasPermission("delete_user") && JSmartUser::isExistent($_GET['uid']))
                {
                    JSmartUser::delete($_GET['uid']);
                }
                exit;
                break;
        }
    }

    $url = Sweia::getInstance()->getURL();
    $db = Sweia::getInstance()->getDB();

    if (isset($url[3]))
    {
        switch ($url[3])
        {
            case "add":
                /* Load the Add User form */
                $tpl = new Template($usermod_path . "templates/forms/add-user");
                $rs = $db->query("SELECT rid, role FROM role");
                $roles = array();
                while ($r = $db->fetchObject($rs))
                {
                    $roles[$r->rid] = $r->role;
                }
                $tpl->roles = $roles;
                $themeRegistry->addContent("content", $tpl->parse());
                break;
            default:
                /* Show user listing */
                $rs = $db->query("SELECT uid FROM user");
                $users = array();
                while ($user = $db->fetchObject($rs))
                {
                    $users[] = $user->uid;
                }
                $tpl = new Template($usermod_path . "templates/inner/users-list");
                $tpl->users = $users;
                $themeRegistry->addContent("content", $tpl->parse());
                break;
        }
    }