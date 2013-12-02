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
       $role->description = @$_POST['description'];
       if ($role->save())
          ScreenMessage::setMessage("Successfully Added new role", "success");
    }
    if (@$_GET['type'] == "ajax")
    {
       switch ($_GET['op'])
       {
          case "delete-role":
             hprint($_GET);
             /* Here we handle deleting a role */
             if ($USER->hasPermission("delete_role"))
                Role::delete(@$_GET['rid']);
             exit;
             break;
       }
    }

    switch (@$URL[3])
    {
       case "add":
          /* Load the Add Role form */
          $tpl = new Template($usermod_path . "templates/forms/add-role");
          $THEMER->addContent("content", $tpl->parse());
          break;
       default:
          /* Show role listing */
          $rs = $DB->query("SELECT rid FROM role");
          $roles = array();
          while ($role = $DB->fetchObject($rs))
             $roles[] = $role->rid;
          $tpl = new Template($usermod_path . "templates/inner/roles-list");
          $tpl->roles = $roles;
          $THEMER->addContent("content", $tpl->parse());
          break;
    }