<?php

    /**
     * Interface for databases in the system
     * 
     * @author Joshua Kissoon
     * @since 20140104 
     * @updated 20140623
     */
    interface Database
    {

        /**
         * Connect to the database
         * 
         * @return Boolean Whether the connection was successful
         */
        public function tryConnect();

        /**
         * Connect to the database
         */
        public function connect();

        /**
         * Select a database to use
         * 
         * @param $db_name The name of the database
         */
        public function selectDatabase($db_name);

        /**
         * Queries the database to produce a result
         * 
         * @param $query The SQL statement to be executed
         * @param $variables An array of variables to replace in the query, these are passed in an array so that they can be escaped
         * 
         * @example query("SELECT * FROM user WHERE name LIKE ':name'", array(":name" => "John Smith"))
         */
        public function query($query, $variables = array(), $log_query = false);

        /**
         * Method to fetch a row from the resultset in the form of an array
         * 
         * @return A row from the resultset in the form of an array
         */
        public function fetchArray();

        /**
         * Method to fetch a row from the resultset in the form of an object
         * 
         * @return A row from the resultset in the form of an object
         */
        public function fetchObject();

        /**
         * @return Integer - The number of rows in a resultset
         */
        public function resultNumRows();

        /**
         * Gets the ID value for the last row inserted into the database
         * 
         * @return Integer - The ID of the last inserted row
         */
        public function lastInsertId();

        /**
         * Escapes a string so that it is safe to be used in a query
         * 
         * @param $value The value to be escaped
         * 
         * @return String - The escaped String
         */
        public function escapeString($value);
    }
    