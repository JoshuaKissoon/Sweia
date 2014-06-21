<?php

    /**
     * Allows each site to specify it's site-specific configuration
     *
     * @author Joshua Kissoon
     */
    class SiteConfig implements SiteConfiguration
    {
        
        public static function includesPath()
        {
            return SystemConfig::basePath() . "site/includes";
        }

        public static function modulesPath()
        {
            return SystemConfig::basePath() . "site/modules/";
        }

        public static function modulesUrl()
        {
            return SystemConfig::baseUrl() . "site/modules/";
        }

        public static function librariesPath()
        {
            return SystemConfig::basePath() . "site/libraries/";
        }

        public static function librariesUrl()
        {
            return SystemConfig::baseUrl() . "site/libraries/";
        }

    }
    