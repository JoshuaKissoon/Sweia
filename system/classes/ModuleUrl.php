<?php

    /**
     * A class used by modules to specify information about a URL that the module handles.
     * 
     * Data in this class will include the URL and the permissions needed to access this module at the URL.
     *
     * @author Joshua Kissoon
     * @since 20140623
     */
    class ModuleUrl
    {

        private $url;
        private $permissions = array();

        /**
         * Construct a new ModuleUrl object
         * 
         * @param $url The url handled by this module. URL relative to the site base URL.
         */
        public function __construct($url)
        {
            $this->url = $url;
        }

        /**
         * @return String The URL this object is referring to
         */
        public function getUrl()
        {
            return $this->url;
        }

        /**
         * Add a new permission that a user need to access this url for this module
         */
        public function addPermission($permission)
        {
            $this->permissions[] = $permission;
        }

    }
    