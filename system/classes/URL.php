<?php

    /**
     * @author Joshua Kissoon
     * @date 20130323
     * @descriptin A class that is used to manipulate any URL
     */
    class URL
    {

        private $base_url;
        public $args = array();

        /*
         * @desc Get the URL sections 
         * @param $url The URL to process
         */

        public function __construct($url)
        {
            $url = explode("?", $url);
            $this->base_url = $url[0];

            /* Get the URL args */
            if (isset($url[1]))
            {
                $args = rtrim(ltrim($url[1], "&"), "&"); // Remove extra &'s from the start and end of the URL 

                $parts = explode("&", $args);
                foreach ((array) $parts as $part)
                {
                    $part = explode("=", $part);
                    $this->args[$part[0]] = $part[1];
                }
            }
        }

        /*
         * @desc Add a new argument to the URL
         */

        public function addArg($title, $value)
        {
            $this->args[$title] = $value;
        }

        /*
         * @desc Remove an argument from the URL
         * @param $title The key of the argument to remove
         */

        public function removeArg($title)
        {

            if (isset($this->args[$title]))
            {
                unset($this->args[$title]);
            }
        }

        /*
         * @desc Method that builds and returns the URL
         */

        public function getURL()
        {
            return $this->base_url . "?" . http_build_query($this->args);
        }

    }
    