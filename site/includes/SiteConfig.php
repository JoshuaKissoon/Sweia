<?php

    /**
     * Allows each site to specify it's site-specific configuration
     *
     * @author Joshua Kissoon
     */
    class SiteConfig
    {

        public static $useAdminTheme = false;   // Whether to use the admin theme or not

        public static function includesPath()
        {
            return SystemConfig::basePath() . "site/includes/";
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

        public static function adminThemePath()
        {
            return SystemConfig::themesPath() . BaseConfig::ADMIN_THEME . "/";
        }

        public static function adminThemeUrl()
        {
            return SystemConfig::themesUrl() . BaseConfig::ADMIN_THEME . "/";
        }

        public static function siteThemePath()
        {
            return SystemConfig::themesPath() . BaseConfig::THEME . "/";
        }

        public static function siteThemeUrl()
        {
            return SystemConfig::themesUrl() . BaseConfig::THEME . "/";
        }

        public static function themePath()
        {
            if (SiteConfig::$useAdminTheme)
            {
                return SiteConfig::adminThemePath();
            }
            else
            {
                return SiteConfig::siteThemePath();
            }
        }

        public static function themeUrl()
        {
            if (SiteConfig::$useAdminTheme)
            {
                return SiteConfig::adminThemeUrl();
            }
            else
            {
                return SiteConfig::siteThemeUrl();
            }
        }

        public static function templatesPath()
        {
            return SiteConfig::themePath() . "templates/";
        }

        public static function templatesUrl()
        {
            return SiteConfig::themeUrl() . "templates/";
        }

        public static function themeCssUrl()
        {
            return SiteConfig::themeUrl() . "css/";
        }

        public static function themeImagesUrl()
        {
            return SiteConfig::themeUrl() . "images/";
        }

        public static function themeScriptsUrl()
        {
            return SiteConfig::themeUrl() . "scripts/";
        }

        public static function themeLibrariessUrl()
        {
            return SiteConfig::themeUrl() . "libraries/";
        }

        public static function filesDirectory()
        {
            return SystemConfig::basePath() . "files/";
        }

        public static function filesTemporaryDirectory()
        {
            return SystemConfig::basePath() . "files/tmp/";
        }

        public static function adminUrlDirectory()
        {
            return "admin";
        }

    }
    