<?php

    /**
     * Specifies the configuration of the System
     *
     * @author Joshua Kissoon
     * @since 20140621
     */
    class SystemConfig implements SystemConfiguration
    {

        public static function includesPath()
        {
            return SiteConfig::basePath() . 'system/includes/';
        }

        public static function classesPath()
        {
            return SiteConfig::basePath() . "system/classes/";
        }

        public static function interfacesPath()
        {
            return SiteConfig::basePath() . "system/interfaces/";
        }

        public static function modulesPath()
        {
            return SiteConfig::basePath() . "system/modules/";
        }

        public static function modulesUrl()
        {
            return SiteConfig::baseUrl() . "system/modules/";
        }

        public static function themesPath()
        {
            return SiteConfig::basePath() . "themes/";
        }

        public static function themesUrl()
        {
            return SiteConfig::baseUrl() . "themes/";
        }

    }
    