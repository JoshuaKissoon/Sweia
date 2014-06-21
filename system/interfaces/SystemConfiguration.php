<?php

    /**
     * Specifies the configuration of the System
     *
     * @author Joshua Kissoon
     * @since 20140621
     */
    interface SystemConfiguration
    {

        public static function includesPath();

        public static function classesPath();

        public static function interfacesPath();

        public static function modulesPath();

        public static function modulesUrl();
        
        public static function themesPath();
        
        public static function themesUrl();
    }
    