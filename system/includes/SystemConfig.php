<?php

    /**
     * Specifies the configuration of the System
     *
     * @author Joshua Kissoon
     * @since 20140621
     */
    class SystemConfig
    {

        public static function protocol()
        {
            return strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https://' : 'http://';
        }

        public static function host()
        {
            return $_SERVER['HTTP_HOST'];
        }

        public static function baseUrl()
        {
            return rtrim(SystemConfig::protocol() . SystemConfig::host() . '/' . BaseConfig::SITE_FOLDER, '/') . '/';
        }

        public static function basePath()
        {
            return rtrim($_SERVER['DOCUMENT_ROOT'] . '/' . BaseConfig::SITE_FOLDER, '/') . '/';
        }

        public static function systemsDirPath()
        {
            return SystemConfig::basePath() . "system/";
        }

        public static function systemsDirUrl()
        {
            return SystemConfig::baseUrl() . "system/";
        }

        public static function includesPath()
        {
            return SystemConfig::systemsDirPath() . 'includes/';
        }

        public static function classesPath()
        {
            return SystemConfig::systemsDirPath() . "classes/";
        }

        public static function interfacesPath()
        {
            return SystemConfig::systemsDirPath() . "interfaces/";
        }

        public static function exceptionsPath()
        {
            return SystemConfig::systemsDirPath() . "exceptions/";
        }

        public static function modulesPath()
        {
            return SystemConfig::systemsDirPath() . "modules/";
        }

        public static function modulesUrl()
        {
            return SystemConfig::systemsDirUrl() . "modules/";
        }

        public static function themesPath()
        {
            return SystemConfig::basePath() . "themes/";
        }

        public static function themesUrl()
        {
            return SystemConfig::baseUrl() . "themes/";
        }

    }
    