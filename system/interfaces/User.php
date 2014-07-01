<?php

    /**
     * A system interface that every user class should inherit from, contains the basic methods for a user class to function in the system
     * 
     * @author Joshua Kissoon
     * @since 20140101
     */
    interface User extends DatabaseObject
    {

        /**
         * @desc Method that returns the username used to identify this user
         */
        public function getEmail();
    }
    