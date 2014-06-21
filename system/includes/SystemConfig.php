<?php

    /**
     * Specifies the configuration of the System
     *
     * @author Joshua
     */
    class SystemConfig implements SystemConfiguration
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
            return rtrim(SystemConfig::protocol() . SystemConfig::host() . '/' . SITE_FOLDER, '/') . '/';
        }

        public static function basePath()
        {
            return rtrim($_SERVER['DOCUMENT_ROOT'] . '/' . SITE_FOLDER, '/') . '/';
        }

        public static function includesPath()
        {
            return SystemConfig::basePath() . 'system/includes/';
        }

        public static function classesPath()
        {
            return SystemConfig::basePath() . "system/classes/";
        }

        public static function interfacesPath()
        {
            return SystemConfig::basePath() . "system/interfaces/";
        }

        public static function modulesPath()
        {
            return SystemConfig::basePath() . "system/modules/";
        }

        public static function modulesUrl()
        {
            return SystemConfig::baseUrl() . "system/modules/";
        }

    }
    