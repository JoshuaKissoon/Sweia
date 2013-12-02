<?php

    /**
     * @author Joshua Kissoon
     * @date 20130316
     * @description A class that handles user roles
     */
    class Role
    {

       public $rid, $role, $description;
       private $permissions = array();

       public function __construct($rid = null)
       {
          if (self::isRole($rid))
          {
             $this->rid = $rid;
             $this->load();
          }
       }

       public static function isRole($rid)
       {
          /* Check if the $rid given is that of a valid role */
          global $DB;
          $res = $DB->query("SELECT rid FROM role WHERE rid = '::rid'", array("::rid" => $rid));
          $role = $DB->fetchObject($res);
          if (valid(@$role->rid))
             return true;
          else
             return false;
       }

       public function load()
       {
          /* Loads all the data for the role */
          global $DB;
          $res = $DB->fetchObject($DB->query("SELECT * FROM role WHERE rid='$this->rid'"));
          foreach ($res as $key => $value)
             $this->$key = $value;
          $this->loadPermissions();
          return true;
       }

       public function addPermission($perm)
       {
          /*
           * Adds a permission to this role 
           * First we check if this is a valid permission
           * Then we add it to the role
           */
          global $DB;
          $res = $DB->fetchObject($DB->query("SELECT permission FROM permission WHERE permission='::perm'", array("::perm" => $perm)));
          if (!valid($res->permission))
             return false;

          /* It is a valid permission, so now we add it to the role */
          $this->permissions[$perm] = $perm;
       }

       public function clearPermissions()
       {
          /* Removes all permissions from this role */
          $this->permissions = array();
       }

       private function loadPermissions()
       {
          global $DB;
          $res = $DB->query("SELECT permission FROM role_permission WHERE rid = '::rid'", array("::rid" => $this->rid));
          while ($perm = $DB->fetchObject($res))
             $this->permissions[$perm->permission] = $perm->permission;
       }

       public function save()
       {
          if (self::isRole(@$this->rid))
             return $this->update();
          else
             return $this->add();
       }

       private function update()
       {
          $this->savePermissions();
          return true;
       }

       private function add()
       {
          /* Add a new role to the database */
          global $DB;
          $args = array(
              '::role' => $this->role,
              '::description' => $this->description,
          );
          $sql = "INSERT INTO role (role, description) VALUES ('::role', '::description')";
          $DB->query($sql, $args);
          $this->rid = $DB->lastInsertId();
          $this->savePermissions();
          return true;
       }

       private function savePermissions()
       {
          /*
           * Adds/Updates the necessary permissions for this role
           * First we delete all the permissions that are there in the database
           * then add the permissions that are currently here
           */
          global $DB;
          $DB->query("DELETE FROM role_permission WHERE rid='::rid'", array("::rid" => $this->rid));
          foreach ($this->permissions as $perm)
          {
             $args = array(
                 '::rid' => $this->rid,
                 '::permission' => $perm,
             );
             $DB->query("INSERT INTO role_permission (rid, permission) VALUES ('::rid', '::permission')", $args);
          }
       }

       public function hasPermission($perm)
       {
          /* Checks if a role has a permission */
          return (array_key_exists($perm, $this->permissions)) ? true : false;
       }

       public static function delete($rid)
       {
          /* Delete this role from the system */
          if (!self::isRole($rid))
             return false;

          /* Remove this role from all user's and permissions and then delete all of it's data */
          global $DB;
          $args = array("::rid" => $rid);
          $DB->query("DELETE FROM user_role WHERE rid = '::rid'", $args);
          $DB->query("DELETE FROM role_permission WHERE rid = '::rid'", $args);
          $DB->query("DELETE FROM role WHERE rid = '::rid'", $args);
          return true;
       }

    }