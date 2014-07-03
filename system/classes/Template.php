<?php

    /**
     * The template class that is used to load, set variables and output a template in HTML form
     * 
     * @author Joshua Kissoon
     * @since 20120712
     * @updated 20140623
     */
    class Template
    {

        private $template = NULL;
        private $variables = array();

        /**
         * This constructor method takes in the template file, if it's specified, we load the template
         * 
         * @param $template The path to the template file
         */
        function __construct($template = null)
        {
            if (isset($template))
            {
                $this->load($template);
            }
        }

        /**
         * Load the template file
         * 
         * @param $template The path to the template file
         */
        public function load($template)
        {
            $template = $template . ".tpl.php";
            if (!is_file($template) || !is_readable($template))
            {
                throw new InvalidTemplateException("Template file " . $template . " not found or not readable.");
            }
            else
            {
                $this->template = $template;
            }
        }

        /**
         * Implementation of PHP __set construct, sets a variable to our variables array if they try to set it directly
         * 
         * @param $name The name of the variable
         * @param $value The value to store under this name
         */
        public function __set($name, $value)
        {
            $this->set($name, $value);
        }

        /**
         * Set a variable within the template
         * 
         * @param $name The name of the variable
         * @param $value The value to store under this name
         */
        public function set($name, $value)
        {
            if ($this->template)
            {
                $this->variables[$name] = $value;
            }
        }

        /**
         * Passes the variables into the template and get the HTML code
         * 
         * @return String - The HTML code for the template
         */
        public function parse()
        {
            if (!$this->template)
            {
                return false;
            }
            ob_start();
            extract($this->variables, EXTR_SKIP);
            require $this->template;
            $content = ob_get_clean();
            return $content;
        }

        /**
         * Passes the variables into the template and get the HTML code
         * 
         * @return Outputs the HTML code to the end user
         */
        public function publish()
        {
            print $this->parse();
        }

    }
    