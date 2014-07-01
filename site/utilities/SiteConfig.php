<?php

    /**
     * Allows each site to specify it's site-specific configuration
     *
     * @author Joshua Kissoon
     * @since 20140621
     */
    class SiteConfig
    {

        public static $useAdminTheme = false;   // Whether to use the admin theme or not

        /**
         * @return String The Path of the directory containing include files of the site
         */

        public static function includesPath()
        {
            return SystemConfig::basePath() . "site/includes/";
        }

        /**
         * @return String The Path of the directory containing site modules
         */
        public static function modulesPath()
        {
            return SystemConfig::basePath() . "site/modules/";
        }

        /**
         * @return String The URL of the directory containing site modules
         */
        public static function modulesUrl()
        {
            return SystemConfig::baseUrl() . "site/modules/";
        }

        /**
         * @return String The path of the directory containing site libraries
         */
        public static function librariesPath()
        {
            return SystemConfig::basePath() . "site/libraries/";
        }

        /**
         * @return String The URL of the directory containing site libraries
         */
        public static function librariesUrl()
        {
            return SystemConfig::baseUrl() . "site/libraries/";
        }

        /**
         * @return String The path of the directory containing the admin theme
         */
        public static function adminThemePath()
        {
            return SystemConfig::themesPath() . BaseConfig::ADMIN_THEME . "/";
        }

        /**
         * @return String The URL of the directory containing the admin theme
         */
        public static function adminThemeUrl()
        {
            return SystemConfig::themesUrl() . BaseConfig::ADMIN_THEME . "/";
        }

        /**
         * @return String The path of the directory containing the site theme
         */
        public static function siteThemePath()
        {
            return SystemConfig::themesPath() . BaseConfig::THEME . "/";
        }

        /**
         * @return String The URL of the directory containing the site theme
         */
        public static function siteThemeUrl()
        {
            return SystemConfig::themesUrl() . BaseConfig::THEME . "/";
        }

        /**
         * @return String The path of the directory containing the currently used theme for whatever section of the site the user is currently on
         */
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

        /**
         * @return String The URL of the directory containing the currently used theme for whatever section of the site the user is currently on
         */
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

        /**
         * @return String The path of the directory containing templates for the currently in-use theme
         */
        public static function templatesPath()
        {
            return SiteConfig::themePath() . "templates/";
        }

        /**
         * @return String The URL of the directory containing templates for the currently in-use theme
         */
        public static function templatesUrl()
        {
            return SiteConfig::themeUrl() . "templates/";
        }

        /**
         * @return String The URL of the directory containing css for the currently in-use theme
         */
        public static function themeCssUrl()
        {
            return SiteConfig::themeUrl() . "css/";
        }

        /**
         * @return String The URL of the directory containing image files for the currently in-use theme
         */
        public static function themeImagesUrl()
        {
            return SiteConfig::themeUrl() . "images/";
        }

        /**
         * @return String The URL of the directory containing scripts for the currently in-use theme
         */
        public static function themeScriptsUrl()
        {
            return SiteConfig::themeUrl() . "scripts/";
        }

        /**
         * @return String The URL of the directory containing libraries used in all themes
         */
        public static function themeLibrariessUrl()
        {
            return SystemConfig::themesUrl() . "libraries/";
        }

        /**
         * @return String The path of the directory that is used to store files such as images, etc
         */
        public static function filesDirectory()
        {
            if (BaseConfig::FILES_DIR_RELATIVE)
            {
                return SystemConfig::basePath() . BaseConfig::FILES_DIR . "/";
            }
            else
            {
                return BaseConfig::FILES_DIR . "/";
            }
        }

        /**
         * @return String The url of the directory that is used to store files such as images, etc
         */
        public static function filesUrl()
        {
            if (BaseConfig::FILES_URL_RELATIVE)
            {
                return SystemConfig::basePath() . BaseConfig::FILES_URL . "/";
            }
            else
            {
                return BaseConfig::FILES_URL . "/";
            }
        }

        /**
         * @return String The path of the directory used to store temporary files such as images, etc
         */
        public static function filesTemporaryDirectory()
        {
            return SiteConfig::filesDirectory() . "tmp/";
        }

        /**
         * The administration url is used to decide when to use the admin theme
         * 
         * @return String The URL of the administration section of the website
         */
        public static function adminUrlDirectory()
        {
            return "admin";
        }

    }
    