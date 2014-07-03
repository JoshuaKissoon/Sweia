<?php

    /**
     * Sweia  is the Registry class for all system objects.
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
        private $URL;
        private $themeRegistry;
        private $user;

        /**
         * Main class constructor private
         */
        private function __construct()
        {
            $this->DB = new SQLiDatabase();
            $this->URL = JPath::urlArgs();
            $this->themeRegistry = new ThemeRegistry();
        }

        /**
         * @return Sweia - an instance of Sweia
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
         * Run necessary bootstrap operations in the entire system
         */
        public function bootstrap()
        {
            Theme::init();          // Initialize the theme
            Session::init();        // Initialize the session
        }

        /**
         * Get the instance of the Database and return it
         * 
         * @return Database Instance of the Database
         */
        public function getDB()
        {
            return $this->DB;
        }

        /**
         * @return The URL object[] with the different arguments of the URL
         */
        public function getURL()
        {
            return $this->URL;
        }

        /**
         * @return ThemeRegistry - The Theme Registry
         */
        public function getThemeRegistry()
        {
            return $this->themeRegistry;
        }

        /**
         * Method used to set the global user object
         */
        public function setUser($user)
        {
            $this->user = $user;
        }

        /**
         * @return User - The user object of the logged in system user
         */
        public function getUser()
        {
            return $this->user;
        }

    }
    