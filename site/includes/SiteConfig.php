<?php

    /**
     * Allows each site to specify it's site-specific configuration
     *
     * @author Joshua Kissoon
     * @since 20140621
     */
    class SiteConfig implements SiteConfiguration
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
            return SiteConfig::basePath() . "site/includes";
        }

        public static function modulesPath()
        {
            return SiteConfig::basePath() . "site/modules/";
        }

        public static function modulesUrl()
        {
            return SiteConfig::baseUrl() . "site/modules/";
        }

        public static function librariesPath()
        {
            return SiteConfig::basePath() . "site/libraries/";
        }

        public static function librariesUrl()
        {
            return SiteConfig::baseUrl() . "site/libraries/";
        }

    }
    