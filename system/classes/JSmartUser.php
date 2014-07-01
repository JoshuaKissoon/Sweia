<?php

    /**
     * Class that contains core user functionality
     * 
     * @author Joshua Kissoon
     * @since 20121227
     * @updated 20140623
     */
    class JSmartUser implements User
    {

        public $uid, $status;
        private $password;
        private $roles = array(), $permissions = array();

        /* Class Metadata */
        public static $user_type = "jsmartuser";
        
        /**
         * Error handlers 
         */
        public static $ERROR_INCOMPLETE_DATA = 00001;

        /* Some constants of what data is loaded */
        private $is_permissions_loaded = false;

        /**
         * Constructor method for the user class, loads the user
         * 
         * @param $uid The id of the user to load
         * 
         * @return Boolean - Whether the load was successful or not
         */
        public function __construct($uid = null)
        {
            if (isset($uid) && self::isExistent($uid))
            {
                $this->uid = $uid;
                return $this->load();
            }
            return false;
        }

        /**
         * Checks if this is a user of the system
         * 
         * @param $uid The user of the user to check for
         * 
         * @return Boolean Whether this is a system user or not
         */
        public static function isExistent($uid)
        {
            if (!valid($uid))
            {
                return false;
            }

            $db = Sweia::getInstance()->getDB();

            $args = array("::uid" => $uid);
            $sql = "SELECT uid FROM " . SystemTables::DB_TBL_USER . " WHERE uid='::uid'";
            $res = $db->query($sql, $args);
            $user = $db->fetchObject($res);
            return (isset($user->uid) && valid($user->uid)) ? true : false;
        }

        /**
         * Method that loads the user data from the database
         * 
         * @param $uid The id of the user to load
         * 
         * @return Boolean - Whether the load was successful or not
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
         * Method that loads the basic user information from the database
         * 
         * @return Boolean - Whether the load was successful or not
         */
        public function loadUserInfo()
        {
            if (!valid($this->uid))
            {
                return false;
            }
            $db = Sweia::getInstance()->getDB();

            $args = array(":uid" => $this->uid);
            $sql = "SELECT * FROM " . SystemTables::DB_TBL_USER . " u WHERE uid=':uid' LIMIT 1";
            $rs = $db->query($sql, $args);
            $cuser = $db->fetchObject($rs);
            if (isset($cuser->uid) && valid($cuser->uid))
            {
                foreach ($cuser as $key => $value)
                {
                    $this->$key = $value;
                }
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * Hash the password and set the user's object password. 
         * This method does not save the password to the database.
         */
        public function setPassword($password)
        {
            $this->password = $this->hashPassword($password);
        }

        /**
         * Check if this password given here is that of the user
         */
        public function isUserPassword($password)
        {
            return ($this->password == $this->hashPassword($password)) ? true : false;
        }

        /**
         * Saves the user's password to the database
         * 
         * @return Boolean - whether the save was successful
         */
        private function savePassword()
        {
            $db = Sweia::getInstance()->getDB();
            return $db->updateFields(SystemTables::DB_TBL_USER, array("password" => $this->password), "uid='$this->uid'");
        }

        /**
         * Hashes the user's password using a salt
         * 
         * @return String - The hashed password
         */
        private function hashPassword()
        {
            $salted = md5($this->password . BaseConfig::PASSWORD_SALT);
            return sha1($salted);
        }
        
        /**
         * @todo
         */
        public function hasMandatoryData()
        {
            
        }

        /**
         * Save the data of this user to the database, if it's a new user, then create this new user
         */
        public function save()
        {
            if (isset($this->uid) && self::isExistent($this->uid))
            {
                return $this->update();
            }
            else
            {
                return $this->insert();
            }
        }

        /**
         * Adds a new user to the system
         */
        public function insert()
        {
            if (self::isEmailInUse($this->email))
            {
                return false;
            }
            if (!valid($this->email) || !valid($this->password))
            {
                return JSmartUser::$ERROR_INCOMPLETE_DATA;
            }

            $db = Sweia::getInstance()->getDB();

            $args = array(
                ":email" => $this->email,
                ":first_name" => isset($this->first_name) ? $this->first_name : "",
                ":last_name" => isset($this->last_name) ? $this->last_name : "",
                ":other_name" => isset($this->other_name) ? $this->other_name : "",
                ":dob" => isset($this->dob) ? $this->dob : "",
                ":password" => $this->password,
            );

            $sql = "INSERT INTO " . SystemTables::DB_TBL_USER . " (password, email, first_name, last_name, other_name, dob)
                VALUES(':password', ':email', ':first_name', ':last_name', ':other_name', ':dob')";
            if ($db->query($sql, $args))
            {
                $this->uid = $db->lastInsertId();
                $this->saveRoles();
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * Updates the user data to the database
         * @todo
         */
        private function update()
        {
            
        }

        /**
         * Check if the email and password is valid
         * 
         * @return Boolean - whether the user credentials is valid
         */
        public function authenticate()
        {
            $db = Sweia::getInstance()->getDB();

            $args = array(":email" => $this->email, "::password" => $this->password);
            $sql = "SELECT uid FROM " . SystemTables::DB_TBL_USER . " WHERE email=':email' and password='::password' LIMIT 1";
            $cuser = $db->fetchObject($db->query($sql, $args));
            if (isset($cuser->uid) && valid($cuser->uid))
            {
                $this->uid = $cuser->uid;
                $this->load();
                return true;
            }
            return false;
        }

        /**
         * Checks if an email address is in use 
         */
        public static function isEmailInUse($email)
        {
            if (!valid($email))
            {
                return false;
            }

            $db = Sweia::getInstance()->getDB();

            $res = $db->query("SELECT email FROM " . SystemTables::DB_TBL_USER . " WHERE email='::email'", array("::email" => $email));
            $temp = $db->fetchObject($res);
            return (isset($temp->email) && valid($temp->email)) ? true : false;
        }

        /**
         * Deletes a user from the system
         * 
         * @param $uid The user ID of the user to delete
         * 
         * @return Boolean - Whether the user was deleted or not
         */
        public static function delete($uid)
        {
            if (!self::isExistent($uid))
            {
                return false;
            }

            $db = Sweia::getInstance()->getDB();
            return $db->query("DELETE FROM " . SystemTables::DB_TBL_USER . " WHERE uid='::uid'", array("::uid" => $uid));
        }

        /**
         * Set the user's email
         * 
         * @return Boolean - Whether the email was successfully set
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
         * Grabs the user's status from the database
         * 
         * @return String - The user's current status
         */
        public function getStatus()
        {
            if (!valid($this->status))
            {
                /* If the status is not set in the user object, load it */
                $db = Sweia::getInstance()->getDB();
                $this->status = $db->getFieldValue(SystemTables::DB_TBL_USER, "status", "uid = $this->uid");
            }
            return $this->status;
        }

        /**
         * Update this user's status
         * 
         * @param $sid The status id of the user's new status
         * 
         * @return Boolean - Whether the operation was successful
         */
        public function setStatus($sid)
        {
            if (!valid($sid))
            {
                return false;
            }

            $db = Sweia::getInstance()->getDB();

            /* Check if its a valid user's status */
            $args = array("::status" => $sid);
            $res = $db->fetchObject($db->query("SELECT sid FROM " . SystemTables::DB_TBL_USER_STATUS . " WHERE sid='::status'", $args));
            if (!isset($res->sid) || !valid($res->sid))
            {
                return false;
            }

            /* Its a valid user status, update this user's status */
            $args['::uid'] = $this->uid;
            return $db->query("UPDATE user SET status='::status' WHERE uid = '::uid'", $args);
        }

        /**
         * @return Integer - The userId
         */
        public function getId()
        {
            return $this->uid;
        }

        /**
         * @return String - the user's email
         */
        public function getEmail()
        {
            return $this->email;
        }

        /**
         * Each user will have a system type
         * 
         * @return String - What type of user it is
         */
        public function getUserType()
        {
            return self::$user_type;
        }

        /**
         * Adds a new role to a user
         * 
         * @param $rid the id of the role to add
         * 
         * @return Boolean - Whether the operation was successful
         */
        public function addRole($rid)
        {
            $db = Sweia::getInstance()->getDB();

            $res = $db->query("SELECT role FROM " . SystemTables::DB_TBL_ROLE . " WHERE rid='::rid'", array('::rid' => $rid));
            $role = $db->fetchObject($res);
            if (isset($role->role) && valid($role->role))
            {
                $this->roles[$rid] = $role->role;
                return true;
            }
            return false;
        }

        /**
         * Saves this user's roles to the Database
         * 
         * @return Boolean - Whether the operation was successful
         */
        public function saveRoles()
        {
            if (!self::isExistent($this->uid))
            {
                return false;
            }

            $db = Sweia::getInstance()->getDB();

            /* Remove all the roles this user had */
            $db->query("DELETE FROM " . SystemTables::DB_TBL_USER_ROLE . " WHERE uid='$this->uid'");

            foreach ((array) $this->roles as $rid => $role)
            {
                $db->query("INSERT INTO " . SystemTables::DB_TBL_USER_ROLE . " (uid, rid) VALUES ('::uid', '::rid')", array('::rid' => $rid, '::uid' => $this->uid));
            }

            return true;
        }

        /**
         * Loads the roles that a user have
         * 
         * @return Array - The set of user roles
         */
        private function loadRoles()
        {
            $db = Sweia::getInstance()->getDB();

            $roles = $db->query("SELECT ur.rid, r.role FROM " . SystemTables::DB_TBL_USER_ROLE . " ur LEFT JOIN role r ON (r.rid = ur.rid) WHERE uid='$this->uid'");
            while ($role = $db->fetchObject($roles))
            {
                $this->roles[$role->rid] = $role->role;
            }

            /* If the currently logged in user is this user, add the authenticated user role to this user */
            if (Session::loggedInUid() == $this->uid)
            {
                $this->roles[2] = "authenticated";
            }
            
            return $this->roles;
        }

        /**
         * @return Array - The roles this user have
         */
        public function getRoles()
        {
            return $this->roles;
        }

        /**
         * Checks whether this user works with the permission system
         * 
         * @return Boolean on whether the user uses the permission system or not
         */
        public function usesPermissionSystem()
        {
            return true;
        }

        /**
         * Load the permissions for this user from the database
         */
        private function loadPermissions()
        {
            if (count($this->roles) < 1)
            {
                return false;
            }

            $db = Sweia::getInstance()->getDB();

            $rids = implode(", ", array_keys($this->roles));
            $rs = $db->query("SELECT permission FROM " . SystemTables::DB_TBL_ROLE_PERMISSION . " WHERE rid IN ($rids)");
            while ($perm = $db->fetchObject($rs))
            {
                $this->permissions[$perm->permission] = $perm->permission;
            }
        }

        /**
         * Check if the user has the specified permission
         * 
         * @param $permission The permission to check if the user have
         * 
         * @return Boolean - Whether the user has the permission
         */
        public function hasPermission($permission)
        {
            if ($this->uid == 1)
            {
                return true;
            }
            if (!valid($permission))
            {
                return false;
            }

            if (!$this->is_permissions_loaded)
            {
                $this->loadPermissions();
            }

            return (key_exists($permission, $this->permissions)) ? true : false;
        }

    }
    