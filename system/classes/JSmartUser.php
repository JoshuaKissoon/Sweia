<?php

    /**
     * @author Joshua Kissoon
     * @date 20121227
     * @description Class that contains user functionality for core JSmart users
     */
    class JSmartUser implements User
    {

        public $uid, $status;
        private $password;
        private $roles = array(), $permissions = array();

        /* Class Metadata */
        public static $user_type = "jsmartuser";

        /* Database Tables */
        private static $user_tbl = "user";
        private static $user_status_tbl = "user_status";
        private static $user_role_tbl = "user_role";

        /* Define error handlers */
        public static $ERROR_INCOMPLETE_DATA = 00001;

        /**
         * @desc Constructor method for the user class, loads the user
         * @param $uid The id of the user to load
         * @return Whether the load was successful or not
         */
        public function __construct($uid = null)
        {
            if (isset($uid) && self::isUser($uid))
            {
                $this->uid = $uid;
                return $this->load();
            }
            return false;
        }

        /**
         * @desc Checks if this is a user of the system
         * @param $uid The user of the user to check for
         * @return Boolean Whether this is a system user or not
         */
        public static function isUser($uid)
        {
            if (!valid($uid))
            {
                return false;
            }

            global $DB;
            $args = array("::uid" => $uid);
            $sql = "SELECT uid FROM " . self::$user_tbl . " WHERE uid='::uid'";
            $res = $DB->query($sql, $args);
            $user = $DB->fetchObject($res);
            return (isset($user->uid) && valid($user->uid)) ? true : false;
        }

        /**
         * @desc Method that loads the user data from the database
         * @param $uid The id of the user to load
         * @return Whether the load was successful or not
         */
        public function load($uid = null)
        {
            if (!valid($this->uid) && !valid($uid))
            {
                /* If we have no uid to load the user by */
                return false;
            }
            $this->uid = valid($this->uid) ? $this->uid : $uid;
            if ($this->loadUserInfo())
            {
                $this->loadRoles();
                $this->loadPermissions();
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * @desc Method that loads the basic user information from the database
         * @return Whether the load was successful or not
         */
        public function loadUserInfo()
        {
            if (!valid($this->uid))
            {
                return false;
            }
            global $DB;
            $args = array(":uid" => $this->uid);
            $sql = "SELECT * FROM " . self::$user_tbl . " u WHERE uid=':uid' LIMIT 1";
            $rs = $DB->query($sql, $args);
            $cuser = $DB->fetchObject($rs);
            if (isset($cuser->uid) && valid($cuser->uid))
            {
                foreach ($cuser as $key => $value)
                {
                    $this->$key = $value;
                }
            }
            else
            {
                return false;
            }
        }

        /**
         * @desc Hash the password and set the user's object password. The password is not permanently saved to the database
         */
        public function setPassword($password)
        {
            if (!isset($this->email) || !valid($this->email))
            {
                return false;
            }
            $this->password = $this->hashPassword($password);
        }

        /**
         * @desc Here we check if this password given here is that of the user
         */
        public function isUserPassword($password)
        {
            return ($this->password == $this->hashPassword($password)) ? true : false;
        }

        /**
         * @desc Saves the user's password to the database
         * @return Boolean whether the save was successful
         */
        private function savePassword()
        {
            global $DB;
            return $DB->updateFields(self::$user_tbl, array("password" => $this->password), "uid='$this->uid'");
        }

        /**
         * @desc Hashes the user's password using a salt
         * @return The hashed password
         * @todo Move the salt to the main settings.php file so that the website owner can update their hash
         */
        private function hashPassword($password)
        {
            $salt = md5($this->email . JSMART_SITE_SALT);
            return sha1($salt . $password);
        }

        /**
         * @desc Save the data of this user to the database, if it's a new user, then create this new user
         */
        public function save()
        {
            if (isset($this->uid) && self::isUser($this->uid))
            {
                return $this->updateUser();
            }
            else
            {
                return $this->addUser();
            }
        }

        /**
         * @desc Adds a new user to the database
         */
        private function addUser()
        {
            if (self::isEmailInUse($this->email))
            {
                return false;
            }
            if (!valid($this->email) || !valid($this->password))
            {
                return JSmartUser::$ERROR_INCOMPLETE_DATA;
            }
            global $DB;
            $args = array(
                ":email" => $this->email,
                ":first_name" => isset($this->first_name) ? $this->first_name : "",
                ":last_name" => isset($this->last_name) ? $this->last_name : "",
                ":other_name" => isset($this->other_name) ? $this->other_name : "",
                ":dob" => isset($this->dob) ? $this->dob : "",
                ":password" => $this->password,
            );

            $sql = "INSERT INTO " . self::$user_tbl . " (password, email, first_name, last_name, other_name, dob)
                VALUES(':password', ':email', ':first_name', ':last_name', ':other_name', ':dob')";
            if ($DB->query($sql, $args))
            {
                $this->uid = $DB->lastInsertId();
                $this->saveRoles();
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * @desc Updates the user data to the database
         */
        private function updateUser()
        {
            
        }

        /**
         * @desc Check if the email and password is valid
         * @return Boolean whether the data is valid or not
         */
        public function authenticate()
        {
            global $DB;
            $args = array(":email" => $this->email, "::password" => $this->password);
            $sql = "SELECT uid FROM " . self::$user_tbl . " WHERE email=':email' and password='::password' LIMIT 1";
            $cuser = $DB->fetchObject($DB->query($sql, $args));
            if (isset($cuser->uid) && valid($cuser->uid))
            {
                $this->uid = $cuser->uid;
                $this->load();
                return true;
            }
            return false;
        }

        /**
         * @desc Checks if an email address is in use 
         */
        public static function isEmailInUse($email)
        {
            if (!valid($email))
            {
                return false;
            }
            global $DB;
            $res = $DB->query("SELECT email FROM " . self::$user_tbl . " WHERE email='::email'", array("::email" => $email));
            $temp = $DB->fetchObject($res);
            return (isset($temp->email) && valid($temp->email)) ? true : false;
        }

        /**
         * @desc Deletes a user from the system
         * @param $uid The user ID of the user to delete
         * @return Boolean Whether the user was deleted or not
         */
        public static function delete($uid)
        {
            if (!self::isUser($uid))
            {
                return false;
            }

            global $DB;
            return $DB->query("DELETE FROM " . self::$user_tbl . " WHERE uid='::uid'", array("::uid" => $uid));
        }

        /**
         * @desc Set the user's email
         * @return Boolean Whether the email was successfully set
         */
        public function setEmail($email)
        {
            if (valid($email))
            {
                $this->email = $email;
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * @desc Grabs the user's status from the database
         * @return The user's current status
         */
        public function getStatus()
        {
            if (!valid($this->status))
            {
                /* If the status is not set in the user object, load it */
                global $DB;
                $this->status = $DB->getFieldValue(self::$user_tbl, "status", "uid = $this->uid");
            }
            return $this->status;
        }

        /**
         * @desc Update this user's status
         * @param $sid The status id of the user's new status
         * @return Whether the user's status is valid or not
         */
        public function setStatus($sid)
        {
            if (!valid($sid))
            {
                return false;
            }

            global $DB;

            /* Check if its a valid user's status */
            $args = array("::status" => $sid);
            $res = $DB->fetchObject($DB->query("SELECT sid FROM " . self::$user_status_tbl . " WHERE sid='::status'", $args));
            if (!isset($res->sid) || !valid($res->sid))
            {
                return false;
            }

            /* Its a valid user status, update this user's status */
            $args['::uid'] = $this->uid;
            return $DB->query("UPDATE user SET status='::status' WHERE uid = '::uid'", $args);
        }

        /**
         * @desc Method that returns the user's ID number, most likely as used in the database
         */
        public function getUserID()
        {
            return $this->uid;
        }

        /**
         * @desc Method that returns the user's ID number, most likely as used in the database
         */
        public function getEmail()
        {
            return $this->email;
        }

        /**
         * @desc Each user will have a system type
         * @return String What type of user it is
         */
        public function getUserType()
        {
            return self::$user_type;
        }

        /* USER ROLE MANAGEMENT */

        /**
         * @desc Adds a new role to a user
         */
        public function addRole($rid)
        {
            global $DB;
            $res = $DB->query("SELECT role FROM " . Role::$role_tbl . " WHERE rid='::rid'", array('::rid' => $rid));
            $role = $DB->fetchObject($res);
            if (isset($role->role) && valid($role->role))
            {
                $this->roles[$rid] = $role->role;
                return true;
            }
            return false;
        }

        /**
         * @desc Saves this user's roles to the Database
         */
        public function saveRoles()
        {
            if (!self::isUser($this->uid))
            {
                return false;
            }

            global $DB;

            /* Remove all the roles this user had */
            $DB->query("DELETE FROM " . self::$user_role_tbl . " WHERE uid='$this->uid'");

            foreach ((array) $this->roles as $rid => $role)
            {
                $DB->query("INSERT INTO " . self::$user_role_tbl . " (uid, rid) VALUES ('::uid', '::rid')", array('::rid' => $rid, '::uid' => $this->uid));
            }

            return true;
        }

        /**
         * @desc Loads the roles that a user have
         */
        private function loadRoles()
        {
            global $DB;
            $roles = $DB->query("SELECT ur.rid, r.role FROM " . self::$user_role_tbl . " ur LEFT JOIN role r ON (r.rid = ur.rid) WHERE uid='$this->uid'");
            while ($role = $DB->fetchObject($roles))
            {
                $this->roles[$role->rid] = $role->role;
            }
        }

        /**
         * @return The roles this user have
         */
        public function getRoles()
        {
            return $this->roles;
        }

        /* USER PERMISSION MANAGEMENT */

        /**
         * @desc Checks whether this user works with the permission system
         * @return Boolean on whether the user uses the permission system or not
         */
        public function usesPermissionSystem()
        {
            return true;
        }

        /**
         * @desc Load the permissions for this user from the database
         */
        private function loadPermissions()
        {
            if (count($this->roles) < 1)
            {
                return false;
            }

            global $DB;

            $rids = implode(", ", array_keys($this->roles));
            $rs = $DB->query("SELECT permission FROM " . Role::$role_permission_tbl . " WHERE rid IN ($rids)");
            while ($perm = $DB->fetchObject($rs))
            {
                $this->permissions[$perm->permission] = $perm->permission;
            }
        }

        /**
         * @desc Check if the user has the specified permission
         * @param $permission The permission to check if the user have
         * @return Boolean Whether the user has the permission
         */
        public function hasPermission($permission)
        {
            if (!valid($permission))
            {
                return false;
            }
            return (key_exists($permission, $this->permissions)) ? true : false;
        }

    }
    