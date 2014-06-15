<?php

    /**
     * @author Joshua Kissoon
     * @date 20120712
     * @description The template class that is used to load, set variables and output a template
     */
    class Template
    {

        private $template = NULL;
        private $variables = array();

        /**
         * @desc This constructor method takes in the template file, if it's specified, we load the template
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
         * @desc Load the template file
         * @param $template The path to the template file
         */
        public function load($template)
        {
            $template = $template . ".tpl.php";
            if (!is_file($template))
            {
                ScreenMessage::setMessage("File not found: $template", "error");
            }
            elseif (!is_readable($template))
            {
                ScreenMessage::setMessage("Could not access file: $template", "error");
            }
            else
            {
                $this->template = $template;
            }
        }

        /**
         * @desc Implementation of PHP __set construct, sets a variable to our variables array if they try to set it directly
         * @param $name The name of the variable
         * @param $value The value to store under this name
         */
        public function __set($name, $value)
        {
            $this->set($name, $value);
        }

        /**
         * Set a variable within the template
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
         * @desc Passes the variables into the template and get the HTML code
         * @return The HTML code for the template
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
         * @desc Passes the variables into the template and get the HTML code
         * @return Outputs the HTML code to the end user
         */
        public function publish()
        {
            if (!$this->template)
            {
                return false;
            }
            ob_start();
            extract($this->variables, EXTR_SKIP);
            require $this->template;
            $content = ob_get_clean();
            print $content;
        }
    }
    