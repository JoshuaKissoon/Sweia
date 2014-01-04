<?php

    /**
     * @author Joshua Kissoon
     * @created 20140104 
     * @desc Interface for databases in the system
     */
    interface Database
    {

        /**
         * @desc Connect to the database
         * @return Boolean Whether the connection was successful
         */
        public function tryConnect();

        /**
         * @desc Connect to the database
         */
        public function connect();

        /**
         * @desc Select a database to use
         * @param $db_name The name of the database
         */
        public function selectDatabase($db_name);

        /**
         * @desc Queries the database to produce a result
         * @param $query The SQL statement to be executed
         * @param $variables An array of variables to replace in the query, these are passed in an array so that they can be escaped
         * @example query("SELECT * FROM user WHERE name LIKE ':name'", array(":name" => "John Smith"))
         */
        public function query($query, $variables = array(), $log_query = false);

        /**
         * @desc Method to fetch a row from the resultset in the form of an array
         */
        public function fetchArray();

        /**
         * @desc Method to fetch a row from the resultset in the form of an object
         */
        public function fetchObject();

        /**
         * @desc Returns the number of rows in a resultset
         */
        public function resultNumRows();

        /**
         * @desc Gets the ID value for the last row inserted into the database
         * @return The ID of the last inserted row
         */
        public function lastInsertId();

        /**
         * @desc Escapes a string so that it is safe to be used in a query
         * @param $value The value to be escaped
         */
        public function escapeString($value);
    }
    