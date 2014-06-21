<?php

    /**
     * Specifies the configuration of the System
     *
     * @author Joshua
     */
    interface SystemConfiguration
    {

        public static function protocol();

        public static function host();

        public static function baseUrl();

        public static function basePath();

        public static function includesPath();

        public static function classesPath();

        public static function interfacesPath();

        public static function modulesPath();

        public static function modulesUrl();
    }
    