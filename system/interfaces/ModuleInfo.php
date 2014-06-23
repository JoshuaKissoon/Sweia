<?php

    /**
     * Interface specifying the structure of a module's modinfo class.
     * 
     * The modinfo class provides information about a module.
     *
     * @author Joshua Kissoon
     * @since 20140623
     */
    interface ModuleInfo
    {

        /**
         * @return String - The unique name for this module
         */
        public function getName();

        /**
         * @return String - A short description of this module
         */
        public function getDescription();

        /**
         * Get the set of URLs that the module handles
         * 
         * @return Array[ModuleUrl] An array of ModuleUrls handled by the module
         */
        public function getUrls();

        /**
         * Get the set of permissions added by this module
         * 
         * @return Array[Permission] An array of Permission handled by this module
         */
        public function getPermissions();
    }
    