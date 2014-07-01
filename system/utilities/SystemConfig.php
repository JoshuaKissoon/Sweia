<?php

    /**
     * Specifies the configuration of the System
     *
     * @author Joshua Kissoon
     * @since 20140621
     * @updated 20140623
     */
    class SystemConfig
    {

        /**
         * @return String The protocol used by the current URL. Whether http or https
         */
        public static function protocol()
        {
            return strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https://' : 'http://';
        }

        /**
         * @return String The HTTP_HOST
         */
        public static function host()
        {
            return $_SERVER['HTTP_HOST'];
        }

        /**
         * @return String The Base URL of the website
         */
        public static function baseUrl()
        {
            return rtrim(SystemConfig::protocol() . SystemConfig::host() . '/' . BaseConfig::SITE_FOLDER, '/') . '/';
        }

        /**
         * @return String The base Path of the website
         */
        public static function basePath()
        {
            return rtrim($_SERVER['DOCUMENT_ROOT'] . '/' . BaseConfig::SITE_FOLDER, '/') . '/';
        }

        /**
         * @return String The path of the directory containing system files
         */
        public static function systemsDirPath()
        {
            return SystemConfig::basePath() . "system/";
        }

        /**
         * @return String The url of the directory containing system files
         */
        public static function systemsDirUrl()
        {
            return SystemConfig::baseUrl() . "system/";
        }

        /**
         * @return String The Path of the directory containing include files of the core system
         */
        public static function includesPath()
        {
            return SystemConfig::systemsDirPath() . 'includes/';
        }

        /**
         * @return String The Path of the directory containing include files of the core system
         */
        public static function includesUrl()
        {
            return SystemConfig::systemsDirUrl() . 'includes/';
        }

        /**
         * @return String The Path of the directory containing include files of the core system
         */
        public static function utilitiesPath()
        {
            return SystemConfig::systemsDirPath() . 'utilities/';
        }

        /**
         * @return String The Path of the directory containing class files of the core system
         */
        public static function classesPath()
        {
            return SystemConfig::systemsDirPath() . "classes/";
        }

        /**
         * @return String The Path of the directory containing interface files of the core system
         */
        public static function interfacesPath()
        {
            return SystemConfig::systemsDirPath() . "interfaces/";
        }

        /**
         * @return String The Path of the directory containing exception class files of the core system
         */
        public static function exceptionsPath()
        {
            return SystemConfig::systemsDirPath() . "exceptions/";
        }

        /**
         * @return String The Path of the directory containing system modules
         */
        public static function modulesPath()
        {
            return SystemConfig::systemsDirPath() . "modules/";
        }

        /**
         * @return String The URL of the directory containing system modules
         */
        public static function modulesUrl()
        {
            return SystemConfig::systemsDirUrl() . "modules/";
        }

        /**
         * @return String The Path of the directory containing all themes
         */
        public static function themesPath()
        {
            return SystemConfig::basePath() . "themes/";
        }

        /**
         * @return String The URL of the directory containing all themes
         */
        public static function themesUrl()
        {
            return SystemConfig::baseUrl() . "themes/";
        }

        /**
         * @return String The URL of the directory containing scripts for the currently in-use theme
         */
        public static function scriptsUrl()
        {
            return SystemConfig::systemsDirUrl() . "scripts/";
        }

        /**
         * @return String The path of the directory containing templates for the currently in-use theme
         */
        public static function templatesPath()
        {
            return SystemConfig::systemsDirPath() . "templates/";
        }

    }
    