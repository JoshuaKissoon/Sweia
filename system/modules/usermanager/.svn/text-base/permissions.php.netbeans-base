<?php

    /*
     * Here we manage role-permission mapping
     */

    if (@$_POST['submit'] == "save-permissions")
    {
       /* Handle saving role-permissions */
       /* We load all roles from the database because there may some roles that all permissions are removed from that won't be in the roles list in $_POST */
       $rs = $DB->query("SELECT rid FROM roles");
       while ($rid = $DB->fetchObject($rs))
       {
          $role = new Role($rid->rid);
          $role->clearPermissions();
          foreach ((array) @$_POST['roles'][$role->rid] as $perm)
          {
             /* For each of the roles, we load it and add the necessary permissions */
             $role->addPermission($perm);
          }
          $role->save();
       }
       ScreenMessage::setMessage("Role Permissions have successfully been updated", "success");
    }

    /* Load Permissions */
    $rs = $DB->query("SELECT * FROM permissions ORDER BY module");
    $permissions = array();
    while ($perm = $DB->fetchObject($rs))
    {
       $permissions[$perm->permission] = $perm;
    }

    /* Load Roles */
    $rs = $DB->query("SELECT rid FROM roles");
    $roles = array();
    while ($role = $DB->fetchObject($rs))
    {
       $roles[$role->rid] = new Role($role->rid);
    }
    $tpl = new Template($usermod_path . "templates/forms/role-permissions");
    $tpl->permissions = $permissions;
    $tpl->roles = $roles;
    $THEME->addContent("content", $tpl->parse());