<?php

    /*
     * Here we manage role-permission mapping
     */


    $db = Sweia::getInstance()->getDB();

    if (isset($_POST['submit']) && $_POST['submit'] == "save-permissions")
    {
        /* Handle saving role-permissions */
        /* We load all roles from the database because there may some roles that all permissions are removed from that won't be in the roles list in $_POST */
        $rs = $db->query("SELECT rid FROM role");
        while ($rid = $db->fetchObject($rs))
        {
            $role = new Role($rid->rid);
            $role->clearPermissions();
            foreach ((array) @$_POST['roles'][$role->rid] as $perm)
            {
                /* For each of the roles, we load it and add the necessary permissions */
                $role->addAndSavePermission($perm);
            }
            $role->save();
        }
        ScreenMessage::setMessage("Role Permissions have successfully been updated", "success");
    }

    /* Load Permissions */
    $rs = $db->query("SELECT * FROM permission ORDER BY module");
    $permissions = array();
    while ($perm = $db->fetchObject($rs))
    {
        $permissions[$perm->permission] = $perm;
    }

    /* Load Roles */
    $rs = $db->query("SELECT rid FROM role");
    $roles = array();
    while ($role = $db->fetchObject($rs))
    {
        $roles[$role->rid] = new Role($role->rid);
    }
    $tpl = new Template($usermod_path . "templates/forms/role-permissions");
    $tpl->permissions = $permissions;
    $tpl->roles = $roles;

    Sweia::getInstance()->getThemeRegistry()->addContent("content", $tpl->parse());
    