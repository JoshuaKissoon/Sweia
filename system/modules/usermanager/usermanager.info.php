<?php

    /*
     * Module that handles all user management tasks
     */

    $usermanagerinfo = array(
        "title" => "User Management",
        "description" => "Module that handles all User Management",
    );

    $usermanagerinfo['permissions'] = array(
        "view_users" => "View Users' Details",
        "add_user" => "Add User",
        "edit_user" => "Edit User",
        "delete_user" => "Delete User",
        "view_roles" => "View Roles",
        "add_role" => "Add Role",
        "edit_role" => "Edit Role",
        "delete_role" => "Delete Role",
        "manage_permissions" => "Manage Permissions",
    );

    $usermanagerinfo['urls'] = array(
        "admin/usermanager" => array("permission" => "view_users"),
        "admin/usermanager/users" => array("permission" => "add_user"),
        "admin/usermanager/users/add" => array("permission" => "add_user"),
        "admin/usermanager/roles" => array("permission" => "view_roles"),
        "admin/usermanager/roles/add" => array("permission" => "add_role"),
        "admin/usermanager/permissions" => array("permission" => "manage_permissions"),
    );