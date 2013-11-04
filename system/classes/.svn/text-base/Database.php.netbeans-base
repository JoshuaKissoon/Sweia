<?php

    /*
     * Class with database Functions
     */

    /**
     * Description of Database
     *
     * Database abstraction layer is here
     *
     * Some things to note:
     *   - Magic quotes is not utilized here, we use escape_string since magic_quotes will be deprecated from php 5.4
     */
    class Database
    {

       private $connection;
       public $resultset, $last_query, $current_row, $field_value;

       function __construct()
       {
          /*
           * Automatically connect to the database when the class is initialized
           */
          $this->connect();
       }

       public function connect()
       {
          /*
           * Connect to the databse
           */
          $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
          if (!$this->connection)
          {
             die("Database Connection failed");
          }
          else
          {
             $db_select = mysqli_select_db($this->connection, DB_NAME);
             if (!$db_select)
             {
                die("Database selection failed: " . mysqli_error($this->connection));
             }
          }
       }

       public function selectDatabase($database = NULL)
       {
          if ($database)
          {
             /* Select the specified database */
             mysqli_select_db($this->connection, $database);
          }
          else
          {
             /* If no database specified, select the default database */
             mysqli_select_db($this->connection, DB_NAME);
          }
       }

       public function query($query, $variables = array(), $log_query = false)
       {
          /*
           * This function takes in the query, and an array of variables to replace in the query
           * variables are passed in an array so that they can be escaped before being entered into the database
           *
           * So a function call may be like:
           * query("SELECT * FROM users WHERE name LIKE ':name'", array(":name" => "John Smith"))
           */
          foreach ((array) $variables as $key => $value)
          {
             $value = mysqli_real_escape_string($this->connection, @$value);
             $query = str_replace($key, $value, $query);
          }
          $this->last_query = $query;
          $this->resultset = mysqli_query($this->connection, $query);

          if (!$this->resultset)
          {
             /* If we had an error while making a query, log it into the database */
             $message = "Error: " . mysqli_error($this->connection) . " Last Query: $this->last_query";
             $message = $this->escapeString($message);
             $res = mysqli_query($this->connection, "INSERT INTO logs (type, message) VALUES ('mysql', '$message')");
          }
          if ($log_query)
          {
             $res = mysqli_query($this->connection, "INSERT INTO logs (type, message) VALUES ('mysqli_query', '$this->last_query')");
          }
          return $this->resultset;
       }

       public function updateFields($table, $fields_values, $where = "1=1")
       {
          /*
           * This function is used to quickly update a field or fields in a table
           * @arguments
           *  $table - the name of the table to update
           *  $fields_values - an associative array where the key is the fieldname and the value is the value
           *  $where - limiting the rows to update
           */
          $sql = "UPDATE $table SET ";
          $last_element = end($fields_values);
          $count = 0;
          $values = array();
          foreach ($fields_values as $key => $value)
          {
             $count++;
             $s = " $key='::$count::', ";
             $values["::$count::"] = $value;
             if ($last_element == $value)
             {
                $s = " $key='::$count::'";
             }
             $sql .= $s;
          }
          $sql .= " WHERE $where";
          $res = $this->query($sql, $values);
          return $res;
       }

       public function getFieldValue($table, $field_name, $where = "1=1")
       {
          /*
           * This function is used to quickly grab the data from a field from a specified table
           * @arguments
           *  $table - the name of the table to update
           *  $field - the field which to return
           *  $where - limiting the rows to update
           */
          $sql = "SELECT $field_name FROM $table WHERE $where LIMIT 1";
          $res = $this->fetchObject($this->query($sql));
          if ($res)
          {
             $this->field_value = $field_name;
             return $res->$field_name;
          }
       }

       public function fetchArray($resultset = null)
       {
          if (!$resultset)
          {
             return false;
          }
          $this->current_row = mysqli_fetch_array(@$resultset);
          return $this->current_row;
       }

       public function fetchObject($resultset = null)
       {
          if (!$resultset)
          {
             return false;
          }
          $this->current_row = mysqli_fetch_object(@$resultset);
          return $this->current_row;
       }

       public function resultNumRows($resultset = null)
       {
          /*
           * Returns the amount of rows there were in the resultset
           */
          if (!$resultset)
             $resultset = $this->resultset;
          return mysqli_num_rows($resultset);
       }

       public function lastInsertId()
       {
          /*
           * Returns the ID value for the last row inserted into the database
           */
          return mysqli_insert_id($this->connection);
       }

       public function escapeString($value)
       {
          if (get_magic_quotes_gpc())
          {
             // undo any magic quote effects so mysqli_real_escape_string can do the work
             $value = stripslashes($value);
          }
          return mysqli_real_escape_string($this->connection, $value);
       }

    }

    /* Automatically initialize the database class when the file is included */
    $DB = new Database();