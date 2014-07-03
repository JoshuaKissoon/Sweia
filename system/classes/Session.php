<?php

    /**
     * Manages the current php session
     * 
     * @author Joshua Kissoon
     * @since 20121212
     * @updated 20140616
     */
    class Session
    {

        public static $USER_SESSION_TBL = "user_session";

        /**
         * Initialize the session
         */
        public static function init()
        {
            session_start();

            /* Load user data from cookies if the user is not logged in */
            if (!Session::isLoggedIn())
            {
                /* If the user is not logged in, try loading the session and login data from cookies */
                Session::loadDataFromCookies();
            }
        }

        /**
         * Destroy the current session 
         */
        public static function destroy()
        {
            session_destroy();
        }

        /**
         * Creates a new session and logs in a user
         * 
         * @param User The user to log in
         */
        public static function loginUser(User $user)
        {
            session_regenerate_id(true);
            $_SESSION['auid'] = $user->getUserID();
            $_SESSION['logged_in'] = true;
            $_SESSION['logged_in_email'] = $user->getEmail();
            $_SESSION['user_type'] = $user->getUserType();

            /* Add the necessary data to the class */
            $_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['aussid'] = 1;

            /* Now we create the necessary cookies for the user and save the session data */
            setcookie("jsmartsid", session_id(), time() + 3600 * 300, "/");

            /* Save the entire session data to the database */
            $args = array(
                "::auid" => $_SESSION['auid'],
                "::sid" => session_id(),
                "::ipaddress" => $_SESSION['ipaddress'],
                "::aussid" => $_SESSION['aussid'],
                "::data" => json_encode($_SESSION),
                "::user_agent" => $_SERVER['HTTP_USER_AGENT']
            );

            /* Save the session data to the database */
            $db = Sweia::getInstance()->getDB();
            $db->query("INSERT INTO " . SystemTables::DB_TBL_USER_SESSION . " (auid, sid, ipaddress, aussid, data, user_agent) VALUES('::auid', '::sid', '::ipaddress', '::aussid', '::data', '::user_agent')", $args);
            if ($db->lastInsertId() < 1)
            {
                ScreenMessage::setMessage("Fail");
                return false;
            }
        }

        /**
         * Try to load the user's data from cookies 
         * 
         * @return Boolean whether the load was successful or not
         */
        public static function loadDataFromCookies()
        {
            if (!isset($_COOKIE['jsmartsid']))
            {
                return false;
            }

            /* If there is a cookie, check if there exists a valid database session and load it */
            $db = Sweia::getInstance()->getDB();

            $res = $db->query("SELECT * FROM " . self::$USER_SESSION_TBL . " WHERE sid='::sid' LIMIT 1", array("::sid" => $_COOKIE['jsmartsid']));
            if ($db->resultNumRows() < 1)
            {
                /* The session is non-existent, delete it */
                self::invalidateSessionCookie();
                return false;
            }

            /* Session is existent, lets get it's data */
            $row = $db->fetchObject($res);
            if ($row->status != 1)
            {
                /* Session has exipred, invalidate it */
                self::invalidateSessionCookie();
                self::invalidateSessionDB(session_id());
                return false;
            }

            /* The session is valid, Load all of the data into session, generate a new sid and update it in the database */
            $data = json_decode($row->data, true);
            foreach ($data as $key => $value)
            {
                $_SESSION[$key] = $value;
            }

            /* Add the necessary data to the class */
            session_regenerate_id(true);
            $_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];

            /* update the session id to the database */
            $args = array("::ausid" => $row->ausid, "::sid" => session_id());
            return $db->query("UPDATE " . SystemTables::DB_TBL_USER_SESSION . " SET sid = '::sid' WHERE ausid='::ausid'", $args);
        }

        /**
         * Logout the user and destroy the session 
         */
        public static function logoutUser()
        {
            /* Invalidate the database session */
            self::invalidateSessionDB(session_id());

            /* Destroy the session variables */
            unset($_SESSION['auid']);
            unset($_SESSION['logged_in']);
            unset($_SESSION['user_type']);
            unset($_SESSION['logged_in_email']);
            unset($_SESSION['ipaddress']);
            unset($_SESSION['aussid']);
            unset($_SESSION['user_agent']);

            /* Destroy the PHP Session */
            self::destroy();
        }

        /**
         * Invalidate a session from the database
         * 
         * @param $session_id The id of the session to invalidate
         */
        public static function invalidateSessionDB($session_id)
        {
            $db = Sweia::getInstance()->getDB();
            /* Set the session's status to 0 in the database */
            $db->query("UPDATE " . SystemTables::DB_TBL_USER_SESSION . " SET aussid = '0' WHERE sid='::sid'", array("::sid" => $session_id));
        }

        /**
         * Invalidate the current session cookie
         */
        public static function invalidateSessionCookie()
        {
            setcookie("jsmartsid", "", time() - 3600);
        }

        /**
         * Checks whether a user is logged in
         * 
         * @return Boolean - Whether the user is logged in or not
         */
        public static function isLoggedIn()
        {
            return (isset($_SESSION['logged_in']) && ($_SESSION['logged_in'] === true));
        }

        /**
         * @return The auid of the logged in user
         */
        public static function loggedInUid()
        {
            return isset($_SESSION['auid']) ? $_SESSION['auid'] : false;
        }

        /**
         * @author Sandeep Gantait
         * @since 20140701
         * Validate the user's validation from the session data
         * 
         * @return Boolean if the logged in user's session data is valid
         */
        public static function validateUserSessionData()
        {
            $db = Sweia::getInstance()->getDB();
            $db_user_agent = $db->getFieldValue(SystemTables::DB_TBL_USER_SESSION, "user_agent", "auid = " . $_SESSION['auid']);
            $db_session_ip = $db->getFieldValue(SystemTables::DB_TBL_USER_SESSION, "ipaddress", "auid = " . $_SESSION['auid']);
            if ($db_session_ip == $_SESSION['ipaddress'] && $db_user_agent == $_SESSION['user_agent'])
            {
                return true;
            }
            else
            {
                self::logoutUser();
                ScreenMessage::setMessage("Invalid Session Data");
                return false;
            }
        }

    }
    