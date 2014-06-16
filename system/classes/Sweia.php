<?php

    /**
     * @author Joshua Kissoon
     * @date 20121214
     * @description A class with methods specific to JSmart sites
     */
    class Sweia
    {

        /**
         * @desc Logs a message to the database
         * @param $type The type of message to log
         * @param $message
         */
        public static function log($type, $message)
        {
            global $DB;
            $res = $DB->query("INSERT INTO system_log (type, message) VALUES (':type', ':message')", array(":type" => $type, ":message" => $message));
            return ($res) ? true : false;
        }

        /**
         * @desc Set a variable in the site table that can be used later 
         * @param $vid The id by which to store the variable
         * @param $value The actual value to store
         */
        public static function variableSet($vid, $value)
        {
            global $DB;
            $args = array("::vid" => $vid, "::value" => $value);
            $sql = "INSERT INTO variable (vid, value) VALUES ('::vid', '::value')
                ON DUPLICATE KEY UPDATE value='::value'";
            $res = $DB->query($sql, $args);
            return $res;
        }

        /**
         * @desc Retrieves a variable that was set earlier in the site variable table
         * @param $vid The id by of the variable to retrieve
         */
        public static function variableGet($vid)
        {
            global $DB;
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
    