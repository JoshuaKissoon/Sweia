<?php

    /* Define any Site settings and values */

    /*
     * What is the URL that loads the home page of the site
     * This is home by default
     * A module can be added to handle this URL
     */

    /* Is the site in a specific folder within your web directory */
    define("SITE_FOLDER", "jsmart");

    /* Home URL */
    define("HOME_URL", "home");

    /**
     * @section Database Access Information
     */
    defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
    defined('DB_USER') ? null : define("DB_USER", "jsmart");
    defined('DB_PASS') ? null : define("DB_PASS", "Pass1233");
    defined('DB_NAME') ? null : define("DB_NAME", "jsmart_test");
    
    
    /**
     * @section Security
     */
    define("JSMART_SITE_SALT", "K<47`5n9~8H5`*^Ks.>ie5&");