<?php

    /**
     * Contains basic configuration to be entered by the web master
     *
     * @author Joshua Kissoon
     * @since 20140621
     */
    class BasicConfig
    {
        
        /* Is the site in a specific folder within your web directory */
        const SITE_FOLDER = "sweia";

        /* Home URL */
        const HOME_URL = "home";

        /* Database access information */
        const DB_SERVER = "localhost";
        const DB_USER = "jsmart";
        const DB_PASS = "Pass1233";
        const DB_NAME = "jsmart_test";
        
        /* Theme information */
        const THEME = "default";
        const ADMIN_THEME = "default";

        /* Security salt used for hashing passwords, etc */
        const PASSWORD_SALT = "K<47`5n9~8H5`*^Ks.>ie5&";

    }
    