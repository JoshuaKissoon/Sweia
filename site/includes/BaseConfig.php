<?php

    /**
     * Base Configuration to be entered by the webmaster
     *
     * @author Joshua Kissoon
     * @since 20140621
     */
    class BaseConfig
    {
        /* Is the site in a specific folder within your web directory */

        const SITE_FOLDER = "sweia";

        /* Home URL */
        const HOME_URL = "home";

        /* Database Access Information */
        const DB_SERVER = "localhost";
        const DB_USER = "jsmart";
        const DB_PASS = "Pass1233";
        const DB_NAME = "jsmart_test";

        /* Themes Information */
        const THEME = "default";
        const ADMIN_THEME = "default";

        /* Value used to as a salt when hashing passwords */
        const PASSWORD_SALT = "K<47`5n9~8H5`*^Ks.>ie5&";

    }
    