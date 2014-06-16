<?php

    /**
     * Provides major core functionality for the entire system.
     * 
     * Over time Sweia will be made the Registry class for all system objects.
     * 
     * @author Joshua Kissoon
     * @since 20121214
     * @updated 20140616
     */
    class Sweia
    {

        private static $sweia = null;

        /* Database Object */
        private $DB;

        /**
         * Main class constructor private
         */
        private function Sweia()
        {
            $this->DB = new SQLiDatabase();
        }

        /**
         * Return an instance of Sweia
         */
        public static function getInstance()
        {
            if (self::$sweia == null)
            {
                self::$sweia = new Sweia();
            }

            return self::$sweia;
        }

        /**
         * Get the instance of the Database and return it
         * 
         * @return Instance of the Database
         */
        public function getDB()
        {
            return $this->DB;
        }

        /**
         * Logs a message to the database
         * 
         * @param $type The type of message to log
         * @param $message
         */
        public static function log($type, $message)
        {
            $sweia = Sweia::getInstance();
            $DB = $sweia->getDB();

            $res = $DB->query("INSERT INTO system_log (type, message) VALUES (':type', ':message')", array(":type" => $type, ":message" => $message));
            return ($res) ? true : false;
        }

        /**
         * Set a variable in the site table that can be used later 
         * 
         * @param $vid The id by which to store the variable
         * @param $value The actual value to store
         */
        public static function variableSet($vid, $value)
        {
            $sweia = Sweia::getInstance();
            $DB = $sweia->getDB();

            $args = array("::vid" => $vid, "::value" => $value);
            $sql = "INSERT INTO variable (vid, value) VALUES ('::vid', '::value')
                ON DUPLICATE KEY UPDATE value='::value'";
            $res = $DB->query($sql, $args);
            return $res;
        }

        /**
         * Retrieves a variable that was set earlier in the site variable table
         * 
         * @param $vid The id by of the variable to retrieve
         */
        public static function variableGet($vid)
        {
            $sweia = Sweia::getInstance();
            $DB = $sweia->getDB();

            $vid = $DB->escapeString($vid);
            $res = $DB->query("SELECT value FROM variable WHERE vid='::vid'", array("::vid" => $vid));
            $variable = $DB->fetchObject($res);
            if (isset($variable->value))
            {
                return $variable->value;
            }
            else
            {
                return false;
            }
        }

        /**
         * @return The website's name
         */
        public static function getSiteName()
        {
            return self::variableGet("sitename");
        }

    }
    