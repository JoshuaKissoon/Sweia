<?php

    /*
     * Here we handle managing users
     */

    if (@$_POST['submit'] == 'add-user')
    {
       /* Handling adding a user to the database */
       if (!valid(@$_POST['username']) || !valid(@$_POST['password']) || !valid(@$_POST['fname']))
       {
          ScreenMessage::setMessage("Please fill up all the fields", "warning");
       }
       $user = new User();
       $user->username = $_POST['username'];
       $user->setPassword($_POST['password']);
       $user->first_name = $_POST['fname'];
       $user->last_name = @$_POST['lname'];
       foreach ((array) @$_POST['roles'] as $rid)
          $user->addRole($rid);
       if ($user->addUser())
          ScreenMessage::setMessage("Successfully Added new user", "success");
    }
    if (@$_GET['type'] == "ajax")
    {
       switch ($_GET['op'])
       {
          case "delete-user":
             hprint($_GET);
             /* Here we handle deleting a user */
             if ($USER->hasPermission("delete_user") && User::isUser(@$_GET['uid']))
                User::delete(@$_GET['uid']);
             exit;
             break;
       }
    }

    switch (@$URL[3])
    {
       case "add":
          /* Load the Add User form */
          $tpl = new Template($usermod_path . "templates/forms/add-user");
          $rs = $DB->query("SELECT rid, role FROM roles");
          $roles = array();
          while ($r = $DB->fetchObject($rs))
          {
             $roles[$r->rid] = $r->role;
          }
          $tpl->roles = $roles;
          $THEMER->addContent("content", $tpl->parse());
          break;
       default:
          /* Show user listing */
          $rs = $DB->query("SELECT uid FROM users");
          $users = array();
          while ($user = $DB->fetchObject($rs))
             $users[] = $user->uid;
          $tpl = new Template($usermod_path . "templates/inner/users-list");
          $tpl->users = $users;
          $THEMER->addContent("content", $tpl->parse());
          break;
    }