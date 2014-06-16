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

    }
    