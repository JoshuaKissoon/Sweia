<?php

    /**
     * @author Joshua Kissoon
     * @date 20121220
     * @description This is the main site Registry class that manages everything within the site, all page components are placed here then it is rendered
     */
    class Registry
    {

        private $templates = array();
        private $stylesheets = array();
        private $scripts = array();
        private $regions = array();
        private $variables = array();
        private $head = array();
        public $pagetitle = "";

        /**
         * @desc Specifies the HTML Template
         * @param $file The template file location
         */
        public function setHtmlTemplate($file)
        {
            $name = rtrim($file, ".tpl.php");
            if (is_file($name . ".tpl.php"))
            {
                $this->templates['html'] = $name;
            }
            else
            {
                /* @todo Throw some error */
            }
        }

        /**
         * @desc Returns the HTML template file name
         */
        private function getHtmlTemplate()
        {
            return (isset($this->templates['html'])) ? $this->templates['html'] : TEMPLATES_PATH . "html";
        }

        /**
         * @desc Specifies the MAIN Template
         * @param $file The template file location
         */
        public function setMainTemplate($file)
        {
            $name = rtrim($file, ".tpl.php");
            if (is_file($name . ".tpl.php"))
            {
                $this->templates['main'] = $name;
            }
            else
            {
                /* @todo Throw some error */
            }
        }

        /**
         * @desc Returns the MAIN template file name
         */
        private function getMainTemplate()
        {
            return (isset($this->templates['main'])) ? $this->templates['main'] : TEMPLATES_PATH . "main";
        }

        /**
         * @desc Takes in an array of parameters of a stylesheet and stores the stylesheet
         * @param $params The parameters of the stylesheet
         * @param $weight Where on the page amoung stylesheets should it be placed
         */
        public function addCss($params, $weight = 10)
        {
            while (isset($this->stylesheets[$weight]))
            {
                $weight++;
            }
            $this->stylesheets[$weight] = $params;
        }

        /**
         * @desc Parses all the stored stylesheets to get the HTML code for all the site required stylesheets 
         * @return The HTML for the link tag for the stylesheets
         */
        private function renderCssFiles()
        {
            ksort($this->stylesheets);
            return HTML::stylesheets($this->stylesheets);
        }

        /**
         * @desc Takes in an array of parameters of a script file and stores the script
         * @param $params The parameters of the script file
         * @param $weight Where on the page amoung script files should it be placed
         * @param $header_script Whether to put the script in the site header
         */
        public function addScript($params, $weight = 10, $header_script = false)
        {
            /* Takes in an array of parameters of a script file and stores it */
            if ($header_script)
            {
                if (!isset($this->scripts['head']))
                {
                    $this->scripts['head'] = array();
                }

                while (isset($this->scripts['head'][$weight]))
                {
                    $weight++;
                }
                $this->scripts['head'][$weight] = $params;
            }
            else
            {
                while (isset($this->scripts[$weight]))
                {
                    $weight++;
                }
                $this->scripts[$weight] = $params;
            }
        }

        /**
         * @desc Parses all the stored scripts to get the HTML code for them
         * @return The HTML for the <script> link tags for the scripts to be placed in the header
         */
        public function renderHeaderScriptFiles()
        {
            if (isset($this->scripts['head']) && is_array($this->scripts['head']))
            {
                ksort($this->scripts['head']);
                $ret = HTML::scripts($this->scripts['head']);
                unset($this->scripts['head']);
                return $ret;
            }
        }

        /**
         * @desc Parses all the stored scripts to get the HTML code for them
         * @return The HTML for the <script> link tags for the scripts to be placed in the site default location (footer)
         */
        public function renderScriptFiles()
        {
            ksort($this->scripts);
            return HTML::scripts($this->scripts);
        }

        /**
         * @desc Method that adds content to the system theme
         * @param $region Which region to add this content to
         * @param $content The content to add
         */
        public function setContent($region, $content)
        {
            $this->addContent($region, $content);
        }

        /**
         * @desc Method that adds content to the system theme
         * @param $region Which region to add this content to
         * @param $content The content to add
         */
        public function addContent($region, $content)
        {
            if ($region == "head")
            {
                $this->head[] = $content;
                return true;
            }
            if (!isset($this->regions[$region]) || !is_array($this->regions[$region]))
            {
                $this->regions[$region] = array();
            }
            $this->regions[$region][] = $content;
        }

        /**
         * @desc Clears all the content currently in some region
         * @param $region Which region to clear the content from
         */
        public function clearRegion($region)
        {
            $this->regions[$region] = array();
        }

        /**
         * @desc Set the browser page title
         * @param $title The title to set it to
         */
        public function setPageTitle($title)
        {
            $this->pagetitle = $title;
        }

        /**
         * @desc Return the name of the site
         */
        public function getPageTitle()
        {
            return $this->pagetitle;
        }

        /**
         * @desc Render the theme to the browser
         */
        public function renderPage()
        {
            $this->variables['sitename'] = Sweia::getSiteName();

            /* Load main file */
            $main = new Template($this->getMainTemplate());
            foreach ($this->regions as $region => $region_array)
            {
                /* The different content added to the regions are initially stored in an array, now we implode each region into a string */
                $main->$region = implode("", $region_array);
            }

            /* Load HTML template */
            $html = new Template($this->getHtmlTemplate());

            /* Put css, scripts, header_tags and main into html variables */
            $html->stylesheets = $this->renderCssFiles();
            $html->header_scripts = $this->renderHeaderScriptFiles();
            $html->footer_scripts = $this->renderScriptFiles();
            $html->content = $main->parse();
            $html->title = isset($this->pagetitle) && valid($this->pagetitle) ? $this->getPageTitle() : Sweia::getSiteName();
            $html->head = implode("", $this->head);

            /* Publish this template */
            $html->publish();
        }

    }

    /* Initialize the theme */
    $REGISTRY = new Registry();
    