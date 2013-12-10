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
        
        
    }
    