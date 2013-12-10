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

        /* Define error handlers */
        public static $ERROR_INCOMPLETE_DATA = 00001;

        /**
         * @desc Role class constructor, if a role id is given, then we load the role
         * @param $rid The id of a role to load from the database
         * @return Boolean Whether the role was successfully loaded
         */
        public function __construct($rid = null)
        {
            if (self::isRole($rid))
            {
                $this->rid = $rid;
                return $this->load();
            }
        }

        /**
         * @desc Check if a $rid is that of a valid role 
         * @param $rid The rid to check for
         * @return Boolean Whether the given rid is that of a valid role or not
         */
        public static function isRole($rid)
        {
            global $DB;
            $res = $DB->query("SELECT rid FROM role WHERE rid = '::rid'", array("::rid" => $rid));
            $role = $DB->fetchObject($res);
            return (isset($role->rid) && valid($role->rid)) ? true : false;
        }

        /**
         * @desc Loads all the data for a role
         * @return Whether the data was successfully loaded or not
         */
        public function load()
        {
            global $DB;
            $res = $DB->fetchObject($DB->query("SELECT * FROM role WHERE rid='::rid'", array("::rid" => $this->rid)));
            if (isset($res->rid) && $res->rid == $this->rid)
            {
                foreach ($res as $key => $value)
                {
                    $this->$key = $value;
                }
                $this->loadPermissions();
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * @desc Adds a new permission to this role
         * @return Boolean Whether the new permission was successfully added
         */
        public function addAndSavePermission($perm)
        {
            global $DB;

            /* Check if this is a valid permission */
            $res = $DB->fetchObject($DB->query("SELECT permission FROM permission WHERE permission='::perm'", array("::perm" => $perm)));
            if (!valid($res->permission))
            {
                return false;
            }

            /* It is a valid permission, so now we add it to the role */
            $this->permissions[$perm] = $perm;
            return true;
        }

        /**
         * @desc Removes all permissions from this role 
         */
        public function clearPermissions()
        {
            $this->permissions = array();
        }

        /**
         * @desc Load all permissions for this role from the database
         * @return Integer The number of permissions that are associated with this role
         */
        private function loadPermissions()
        {
            global $DB;
            $res = $DB->query("SELECT permission FROM role_permission WHERE rid = '::rid'", array("::rid" => $this->rid));
            while ($perm = $DB->fetchObject($res))
            {
                $this->permissions[$perm->permission] = $perm->permission;
            }
            return count($this->permissions);
        }

        /**
         * @desc Updates the permissions for this role by adding, editing and deleting permissions as necessary
         * @return Whether the operation was successful or not
         */
        public function savePermissions()
        {
            /* First we delete all the permissions that are there in the database */
            global $DB;
            $res = $DB->query("DELETE FROM role_permission WHERE rid='::rid'", array("::rid" => $this->rid));

            if (!$res)
            {
                return false;
            }

            /* Add the permissions that are currently here */
            foreach ($this->permissions as $perm)
            {
                $args = array(
                    '::rid' => $this->rid,
                    '::permission' => $perm,
                );
                return $DB->query("INSERT INTO role_permission (rid, permission) VALUES ('::rid', '::permission')", $args);
            }
        }

        /**
         * @desc Either update a role if we're using a current role or create a new role
         * @return Boolean Whether the operation was successful or not
         */
        public function save()
        {
            if (isset($this->rid) && self::isRole($this->rid))
            {
                return $this->update();
            }
            else
            {
                return $this->create();
            }
        }

        /**
         * @desc Here we update a role
         * @return Boolean Whether the update was successful or not
         */
        private function update()
        {
            return $this->savePermissions();
        }

        /**
         * @desc Add a new role to the database
         * @return Whether the creation of the role was successful or not
         */
        private function create()
        {
            if (!isset($this->role) || !valid($this->role))
            {
                return self::$ERROR_INCOMPLETE_DATA;
            }

            global $DB;
            $args = array(
                '::role' => $this->role,
                '::description' => $this->description,
            );
            $sql = "INSERT INTO role (role, description) VALUES ('::role', '::description')";
            if ($DB->query($sql, $args))
            {
                $this->rid = $DB->lastInsertId();
                $this->savePermissions();
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * @desc Checks if a role has a permission 
         * @param $perm The permission to check for
         * @return Boolean whether the role has the permission or not
         */
        public function hasPermission($perm)
        {
            return (array_key_exists($perm, $this->permissions)) ? true : false;
        }

        /**
         * @desc Delete this role from the system 
         * @param $rid The role to delete
         * @return Boolean Whether the deletion was successful or not
         */
        public static function delete($rid)
        {
            if (!self::isRole($rid))
            {
                return false;
            }

            /* Remove this role from all user's and permissions and then delete all of it's data */
            global $DB;
            $args = array("::rid" => $rid);
            if ($DB->query("DELETE FROM user_role WHERE rid = '::rid'", $args))
            {
                $DB->query("DELETE FROM role_permission WHERE rid = '::rid'", $args);
                if ($DB->query("DELETE FROM role WHERE rid = '::rid'", $args))
                {
                    return true;
                }
            }
            return false;
        }

    }
    