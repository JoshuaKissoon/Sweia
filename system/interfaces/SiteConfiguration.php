<?php

    /**
     * Allows each site to specify it's site-specific configuration
     *
     * @author Joshua Kissoon
     * @since 20140621
     */
    interface SiteConfiguration
    {

        public static function includesPath();

        public static function modulesPath();

        public static function modulesUrl();

        public static function librariesPath();

        public static function librariesUrl();
    }
    