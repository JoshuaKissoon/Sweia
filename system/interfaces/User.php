<?php

    /**
     * @author Joshua Kissoon
     * @desc A system interface that every user class should inherit from, contains the basic methods for a user class to function in the system
     * @date
     */
    interface User
    {
        /**
         * @desc Method that returns the user's ID number, most likely as used in the database
         */
        public function getUserID();
        
        /**
         * @desc Method that returns the username used to identify this user
         */
        public function getUsername();
        
        /**
         * @desc Method that checks if this id is that of a user
         * @param $uid The id of the user
         * @return Boolean Whether this is a user or not
         */
        public static function isUser($uid);
        
        /**
         * @desc Save the data of this user to the database, if it's a new user, then create this new user
         */
        public function save();
    }
    