<?php

    /**
     * This is the main site Registry class that manages everything within the site, all page components are placed here then it is rendered
     * 
     * @author Joshua Kissoon
     * @since 20121220
     * @updated 20140623
     */
    class ThemeRegistry
    {

        private $templates = array();
        private $stylesheets = array();
        private $scripts = array();
        private $regions = array();
        private $variables = array();
        private $head = array();
        public $pagetitle = "";

        /**
         * Specifies the HTML Template
         * 
         * @param $file The template file location
         */
        public function setHtmlTemplate($file)
        {
            $name = rtrim($file, ".tpl.php");
            $this->templates['html'] = $name;
        }

        /**
         * @return String - The HTML template file name
         */
        private function getHtmlTemplate()
        {
            return (isset($this->templates['html'])) ? $this->templates['html'] : SiteConfig::templatesPath() . "html";
        }

        /**
         * Specifies the main Template
         * 
         * @param $file The template file location
         */
        public function setMainTemplate($file)
        {
            $name = rtrim($file, ".tpl.php");
            $this->templates['main'] = $name;
        }

        /**
         * @return String - The main template file name
         */
        private function getMainTemplate()
        {
            return (isset($this->templates['main'])) ? $this->templates['main'] : SiteConfig::templatesPath() . "main";
        }

        /**
         * Takes in an array of parameters of a stylesheet and stores the stylesheet
         * 
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
         * Parses all the stored stylesheets to get the HTML code for all the site required stylesheets 
         * 
         * @return String - The HTML code for the link tag for the stylesheets
         */
        private function renderCssFiles()
        {
            ksort($this->stylesheets);
            return HTML::stylesheets($this->stylesheets);
        }

        /**
         * Takes in an array of parameters of a script file and stores the script
         * 
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
         * Parses all the stored scripts to get the HTML code for them
         * 
         * @return String - The HTML code for the <script> link tags for the scripts to be placed in the header
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
         * Parses all the stored scripts to get the HTML code for them
         * 
         * @return String - The HTML code for the <script> link tags for the scripts to be placed in the site default location (footer)
         */
        public function renderScriptFiles()
        {
            ksort($this->scripts);
            return HTML::scripts($this->scripts);
        }

        /**
         * Method that adds content to the system theme
         * 
         * @param $region Which region to add this content to
         * @param $content The content to add
         */
        public function setContent($region, $content)
        {
            $this->addContent($region, $content);
        }

        /**
         * Method that adds content to the system theme
         * 
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
         * Clears all the content currently in some region
         * 
         * @param $region Which region to clear the content from
         */
        public function clearRegion($region)
        {
            $this->regions[$region] = array();
        }

        /**
         * Set the browser page title
         * 
         * @param $title The title to set it to
         */
        public function setPageTitle($title)
        {
            $this->pagetitle = $title;
        }

        /**
         * @return String - The name of the site
         */
        public function getPageTitle()
        {
            return $this->pagetitle;
        }

        /**
         * Render the theme to the browser
         */
        public function renderPage()
        {
            $this->variables['sitename'] = Utility::getSiteName();

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
            $html->title = isset($this->pagetitle) && valid($this->pagetitle) ? $this->getPageTitle() : Utility::getSiteName();
            $html->head = implode("", $this->head);

            /* Publish this template */
            $html->publish();
        }

    }
    