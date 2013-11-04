<?php

    /**
     * @author Joshua Kissoon
     * @date 20121227
     * @description Class that contains user functionality specific to JSmart
     */
    class JUser
    {

       public $uid, $username, $status;
       private $password;
       private $users_table = "users";
       private static $usertbl = "users";
       private $roles = array(), $permissions = array();

       public function __construct($uid = 0)
       {
          /* Load the user info for a specific uid, or load data for an anonymous user */
          if (valid(@$uid))
          {
             $this->uid = $uid;
             return $this->load();
          }
          else
          {
             $this->uid = 0;
             $this->username = "Anonymous";
             $this->roles[1] = "anonymous";
          }
       }

       public function load($uid = null)
       {
          if (!valid($this->uid) && !valid($uid))
          {
             /* If we have no uid to load the user by */
             return false;
          }
          $this->uid = valid($this->uid) ? $this->uid : $uid;
          $this->loadUserInfo();
       }

       public function loadUserInfo()
       {
          /* Load the user information */
          global $DB;
          $where = " 1=1";
          $values = array();
          if (valid($this->uid))
          {
             /* If the UID is set, load this user's information and return true */
             $values[":uid"] = $this->uid;
             $where .= " AND uid=':uid'";
             /* Loading the personal Information */
             $sql = "SELECT * FROM $this->users_table u WHERE $where LIMIT 1";
             $rs = $DB->query($sql, $values);
             $cuser = $DB->fetchObject($rs);
             if (valid($cuser))
             {
                foreach ($cuser as $key => $value)
                {
                   $this->$key = $value;
                }
             }
             $this->loadRoles();
             $this->loadPermissions();
             return true;
          }

          /* Failed to load user */
          return false;
       }

       private function loadRoles()
       {
          /* Load this user roles */
          global $DB;
          $roles = $DB->query("SELECT ur.rid, r.role FROM user_roles ur LEFT JOIN roles r ON (r.rid = ur.rid) WHERE uid='$this->uid'");
          while ($role = $DB->fetchObject($roles))
          {
             $this->roles[$role->rid] = $role->role;
          }
       }

       public function getRoles()
       {
          return $this->roles;
       }

       private function loadPermissions()
       {
          /* Load this user Permissions */
          if (count($this->roles) < 1)
             return false;

          global $DB;

          $rids = implode(", ", array_keys($this->roles));
          $rs = $DB->query("SELECT permission FROM role_permissions WHERE rid IN ($rids)");
          while ($perm = $DB->fetchObject($rs))
          {
             $this->permissions[$perm->permission] = $perm->permission;
          }
       }

       public function setPassword($password)
       {
          /*
           * We set the password and hash it one time
           * we need to check for username since username is used in password hash
           */
          if (!valid(@$this->username))
          {
             return false;
          }
          $this->password = $this->hashPassword($password);
       }

       public function isUserPassword($password)
       {
          /*
           * Here we check if this password given here is that of the user
           */
          return ($this->password == $this->hashPassword($password)) ? true : false;
       }

       public function updatePassword($new_password)
       {
          /*
           * We set the password and hash it one time
           */
          global $DB;
          $this->password = $this->hashPassword($new_password);
          $DB->updateFields($this->users_table, array("password" => $this->password), $where = "uid='$this->uid'");
       }

       private function savePassword()
       {
          /* Save the user's password */
          global $DB;
          $DB->updateFields($this->users_table, array("password" => $this->password), $where = "uid='$this->uid'");
       }

       private function hashPassword($password)
       {
          /*
           * Right now we just use a simple md5 hash to hash the password,
           * with a salt to prevent dictionary attacks
           */
          $salt = md5($this->username . "K<47`5n9~8H5`*^Ks.>ie5&");
          return sha1($salt . $password);
       }

       public function generateTemporaryPassword($length = 12)
       {
          /* Function that randomly generates a temporary password */
          $alphNums = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
          $newString = str_shuffle(str_repeat($alphNums, rand(1, $length)));
          $password = substr($newString, rand(0, strlen($newString) - $length), $length);
          $this->setPassword($password);
          return $password;
       }

       public function addUser()
       {
          /* Add a User */
          if (!$this->isUsernameAvail() || $this->isEmailInUse())
          {
             /* Check if this user already exists */
             return false;
          }
          /* Add user to database */
          global $DB;
          $args = array(
              ":username" => @$this->username,
              ":email" => @$this->email,
              ":first_name" => @$this->first_name,
              ":last_name" => @$this->last_name,
              ":other_name" => @$this->other_name,
              ":dob" => @$this->dob,
          );
          $sql = "INSERT INTO $this->users_table (username, password, email, first_name, last_name, other_name, dob)
                VALUES(':username', '$this->password', ':email', ':first_name', ':last_name', ':other_name', ':dob')";
          $DB->query($sql, $args);
          $this->uid = $DB->lastInsertId();
          $this->savePassword();
          $this->saveRoles();
          return true;
       }

       public function addRole($rid)
       {
          /* Check if its a valid role id and add the role to the user */
          global $DB;
          $res = $DB->query("SELECT role FROM roles WHERE rid='::rid'", array('::rid' => $rid));
          $role = $DB->fetchObject($res);
          if (valid(@$role->role))
          {
             $this->roles[$rid] = $role->role;
             return true;
          }
          return false;
       }

       public function saveRoles()
       {
          /* Function that saves this user's roles to the Database */
          if (!self::isUser($this->uid))
             return false;
          /* Everything seems Legit, lets update/add roles for this user now */
          global $DB;
          /* Remove all the roles this user had */
          $DB->query("DELETE FROM user_roles WHERE uid='$this->uid'");

          foreach ((array) $this->roles as $rid => $role)
          {
             $DB->query("INSERT INTO user_roles (uid, rid) VALUES ('::uid', '::rid')", array('::rid' => $rid, '::uid' => $this->uid));
          }
          return true;
       }

       public function authenticate(&$error = array())
       {
          /* Check if the username and password is valid, and returns that user object */
          global $DB;
          $args = array(":username" => $this->username);
          $sql = "SELECT uid FROM $this->users_table WHERE username=':username' and password='$this->password' LIMIT 1";
          $cuser = $DB->fetchObject($DB->query($sql, $args));
          if (valid(@$cuser->uid))
          {
             /* Login Successful, check user status */
             $this->uid = $cuser->uid;
             $this->load();
             if ($this->checkStatus($error))
             {
                global $session;
                $session->loginUser($this);
                return true;
             }
          }
          /* Authentication failed */
          return false;
       }

       public function checkStatus(&$error)
       {
          /* Check the statuses table and check if a user with this status is allowed to login */
          global $DB;
          $res = $DB->query("SELECT user_allowed, error_msg FROM user_statuses WHERE sid = '::status'", array("::status" => $this->status));
          $data = $DB->fetchObject($res);
          if (!@$data->user_allowed)
          {
             /* If this user object is not valid */
             $error = @$data->error_msg;
             return false;
          }
          return true;
       }

       public function isUsernameAvail($username = null)
       {
          /* Checks if the username is available */
          global $DB;
          $this->username = valid($username) ? $username : $this->username;
          $DB->query("SELECT username FROM $this->users_table WHERE username='::un'", array("::un" => $this->username));
          $temp = $DB->fetchObject();
          return (@$temp->username) ? false : true;
       }

       public function isEmailInUse($email = null)
       {
          /* Checks if the username is available */
          global $DB;
          $this->email = valid($email) ? $email : @$this->email;
          $DB->query("SELECT email FROM $this->users_table WHERE email='::email'", array("::email" => $this->email));
          $temp = $DB->fetchObject();
          return (@$temp->email) ? $temp->email : false;
       }

       public static function isUser($uid)
       {
          /* Checks if this is a user of the system */
          if (!$uid)
             return false;
          
          global $DB;
          $args = array("::uid" => $uid);
          $sql = "SELECT uid FROM " . self::$usertbl . " WHERE uid='::uid'";
          $res = $DB->query($sql, $args);
          $user = $DB->fetchObject($res);
          return (valid(@$user->uid)) ? true : false;
       }

       public static function delete($uid)
       {
          if (!self::isUser($uid))
             return false;
          
          /* Delete User */
          global $DB;
          return $DB->query("DELETE FROM " . self::$usertbl . " WHERE uid='::uid'", array("::uid" => $uid));
       }

       public function setEmail($email)
       {
          if (valid(@$email))
          {
             $this->email = $email;
             return true;
          }
          else
             return false;
       }

       public function hasPermission($permission = "")
       {
          /* Check if the user has the specified permission */
          if (trim($permission) == "")
             return true;
          return (key_exists($permission, $this->permissions)) ? true : false;
       }

       public function getStatus()
       {
          /* Function that returns the user's current status */
          if (!valid($this->status))
          {
             /* If the status is not set in the user object, load it */
             global $DB;
             $this->status = $DB->getFieldValue($this->users_table, "status", "uid = $this->uid");
          }
          return $this->status;
       }

       public function setStatus($status)
       {
          /* Update this user's status */
          global $DB;

          /* Check if its a valid user's status */
          $args = array("::status" => $status);
          $res = $DB->fetchObject($DB->query("SELECT sid FROM user_statuses WHERE sid='::status'", $args));
          if (!@$res->sid)
             return false;


          /* Its a valid user status, update this user's status */
          $args['::uid'] = $this->uid;
          return $DB->query("UPDATE users SET status='::status' WHERE uid = '::uid'", $args);
       }

    }