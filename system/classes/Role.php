<?php

    /**
     * A class that handles user roles
     * 
     * @author Joshua Kissoon
     * @since 20130316
     * @updated 20140623
     */
    class Role
    {

        public $rid, $role, $description;
        private $permissions = array();

        /**
         * Error handlers 
         */
        public static $ERROR_INCOMPLETE_DATA = 00001;

        /**
         * Role class constructor, if a role id is given, then we load the role
         * 
         * @param $rid The id of a role to load from the database
         * 
         * @return Boolean - Whether the role was successfully loaded
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
         * Check if a $rid is that of a valid role 
         * 
         * @param $rid The rid to check for
         * 
         * @return Boolean Whether the given rid is that of a valid role or not
         */
        public static function isRole($rid)
        {
            $db = Sweia::getInstance()->getDB();

            $res = $db->query("SELECT rid FROM " . SystemTables::DB_TBL_ROLE . " WHERE rid = '::rid'", array("::rid" => $rid));
            $role = $db->fetchObject($res);
            return (isset($role->rid) && valid($role->rid)) ? true : false;
        }

        /**
         * Loads all the data for a role and store it locally in this role object
         * 
         * @return Boolean - Whether the data was successfully loaded or not
         */
        public function load()
        {
            $db = Sweia::getInstance()->getDB();

            $res = $db->fetchObject($db->query("SELECT * FROM " . SystemTables::DB_TBL_ROLE . " WHERE rid='::rid'", array("::rid" => $this->rid)));
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
         * Adds a new permission to this role
         * 
         * @return Boolean - Whether the new permission was successfully added
         */
        public function addAndSavePermission($perm)
        {
            $db = Sweia::getInstance()->getDB();

            /* Check if this is a valid permission */
            $res = $db->fetchObject($db->query("SELECT permission FROM permission WHERE permission='::perm'", array("::perm" => $perm)));
            if (!valid($res->permission))
            {
                return false;
            }

            /* It is a valid permission, so now we add it to the role */
            $this->permissions[$perm] = $perm;
            return true;
        }

        /**
         * Removes all permissions from this role 
         */
        public function clearPermissions()
        {
            $this->permissions = array();
        }

        /**
         * Load all permissions for this role from the database
         * 
         * @return Integer - The number of permissions that are associated with this role
         */
        private function loadPermissions()
        {
            $db = Sweia::getInstance()->getDB();

            $res = $db->query("SELECT permission FROM " . SystemTables::DB_TBL_ROLE_PERMISSION . " WHERE rid = '::rid'", array("::rid" => $this->rid));
            while ($perm = $db->fetchObject($res))
            {
                $this->permissions[$perm->permission] = $perm->permission;
            }
            return count($this->permissions);
        }

        /**
         * Updates the permissions for this role by adding, editing and deleting permissions as necessary
         * 
         * @return Boolean - Whether the operation was successful or not
         */
        public function savePermissions()
        {
            /* First we delete all the permissions that are there in the database */
            $db = Sweia::getInstance()->getDB();

            $res = $db->query("DELETE FROM " . SystemTables::DB_TBL_ROLE_PERMISSION . " WHERE rid='::rid'", array("::rid" => $this->rid));

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
                return $db->query("INSERT INTO " . SystemTables::DB_TBL_ROLE_PERMISSION . " (rid, permission) VALUES ('::rid', '::permission')", $args);
            }
        }

        /**
         * Either update a role if we're using a current role or create a new role
         * 
         * @return Boolean - Whether the operation was successful or not
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
         * Here we update a role to the database from the current role object
         * 
         * @return Boolean - Whether the update was successful or not
         */
        private function update()
        {
            return $this->savePermissions();
        }

        /**
         * Add a new role to the database
         * 
         * @return Whether the creation of the role was successful or not
         */
        private function create()
        {
            if (!isset($this->role) || !valid($this->role))
            {
                return self::$ERROR_INCOMPLETE_DATA;
            }

            $db = Sweia::getInstance()->getDB();

            $args = array(
                '::role' => $this->role,
                '::description' => $this->description,
            );
            $sql = "INSERT INTO " . SystemTables::DB_TBL_ROLE . " (role, description) VALUES ('::role', '::description')";
            if ($db->query($sql, $args))
            {
                $this->rid = $db->lastInsertId();
                $this->savePermissions();
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * Checks if a role has a permission 
         * 
         * @param $perm The permission to check for
         * 
         * @return Boolean - Whether the role has the permission or not
         */
        public function hasPermission($perm)
        {
            return (array_key_exists($perm, $this->permissions)) ? true : false;
        }

        /**
         * Delete this role from the system 
         * 
         * @param $rid The role to delete
         * 
         * @return Boolean - Whether the deletion was successful or not
         */
        public static function delete($rid)
        {
            if (!self::isRole($rid))
            {
                return false;
            }

            /* Remove this role from all user's and permissions and then delete all of it's data */
            $db = Sweia::getInstance()->getDB();

            $args = array("::rid" => $rid);
            if ($db->query("DELETE FROM " . SystemTables::DB_TBL_USER_ROLE . " WHERE rid = '::rid'", $args))
            {
                $db->query("DELETE FROM " . SystemTables::DB_TBL_ROLE_PERMISSION . " WHERE rid = '::rid'", $args);
                if ($db->query("DELETE FROM " . SystemTables::DB_TBL_ROLE . " WHERE rid = '::rid'", $args))
                {
                    return true;
                }
            }
            return false;
        }

    }
    