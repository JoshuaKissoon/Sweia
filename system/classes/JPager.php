<?php

    /**
     * Class that handles pagination throughout the website
     * 
     * @author Joshua Kissoon
     * @since 20121211
     * @updated 20140615
     */
    class JPager
    {
        /* Pageination variables */

        private $rows_per_page = 20, $total_records, $num_pages, $current_page = 1;
        public $links = array(), $links_html = "";
        public $offset; /* Current offset to be used in SQL query to start row number retrieval from */
        public $delta = 4;  // Number of page links to display before and after the current page

        /* URL Variables */
        private $url = "";   // The URL to add to the pager links
        private $pageno_url_key = "pager_page";   // The Pager class url variable to use

        public function __construct()
        {
            return $this;
        }

        /**
         *  Setup the pager query and links
         * 
         * @param $params all parameters are in the $params array containing
         *      -> total_records - the total records to be displayed
         *      -> rows_per_page - the total records to be displayed per page
         *      -> current_page - the current page to be shown
         *      -> delta - Number of page links to display before and after the current page
         *      -> html_options - Array with html options for the html links
         *      -> url - URL of the page we are on
         *      -> url_var - The page number var to append to the url
         */
        public function init($params)
        {

            $this->total_records = isset($params['total_records']) ? $params['total_records'] : $this->total_records;
            $this->rows_per_page = isset($params['rows_per_page']) ? $params['rows_per_page'] : $this->rows_per_page;
            $this->current_page = (isset($params['current_page']) && ($params['current_page'] > 0)) ? $params['current_page'] : $this->current_page;
            $this->delta = (isset($params['delta']) && ($params['delta'] > 0)) ? $params['delta'] : $this->delta;
            $this->html_options = (isset($params['html_options']) && is_array($params['html_options'])) ? $params['html_options'] : array();
            $this->total_records = $params['total_records'];
            $this->pageno_url_key = isset($params['url_var']) ? $params['url_var'] : $this->pageno_url_key;

            /* Setting URL variables */
            $this->url = ($params['url']) ? $params['url'] : $this->url;
            if (strpos($this->url, "?") === false)
            {
                $this->url = $this->url . "?";
            }

            /* Remove the page number from the URL if it exists */
            $url = new UrlManipulator($this->url);
            $url->removeArg($this->pageno_url_key);
            $this->url = $url->getURL();

            /* Compute paginated values */
            $this->calculatePages();
            $this->buildLinks();
        }

        /**
         * Calculates the number of pages of records we will have 
         */
        private function calculatePages()
        {
            $this->num_pages = ceil($this->total_records / $this->rows_per_page);
        }

        /**
         * Calculate offset based on rows per page and then update the query
         */
        public function getRowOffset()
        {
            if ($this->current_page > $this->num_pages)
            {
                $this->current_page = $this->num_pages;
            }
            if ($this->current_page < 1)
            {
                $this->current_page = 1;
            }

            return $this->offset = ($this->current_page - 1) * $this->rows_per_page;
        }

        /**
         * @return Integer - the number of rows per page
         */
        public function getRowsPerPage()
        {
            return $this->rows_per_page;
        }

        /**
         * @return String - the url key for the page number
         */
        public function getPageNoUrlKey()
        {
            return $this->pageno_url_key;
        }

        /**
         * Build pagination links
         */
        public function buildLinks()
        {
            $this->links = array();
            $starting_offset = (($this->current_page - $this->delta) > 0) ? ($this->current_page - $this->delta) : 1;
            $ending_offset = (($this->current_page + $this->delta) <= $this->num_pages) ? ($this->current_page + $this->delta) : $this->num_pages;
            for ($i = $starting_offset; $i <= $ending_offset; $i++)
            {
                if (($this->current_page - $i) == 1)
                {
                    /* If this is the previous item, insert a previous link */
                    $prev = "<a href='$this->url&$this->pageno_url_key=$i'><<</a>";
                    $this->links = array_merge(array("prev" => $prev), $this->links);
                }
                else if (($this->current_page - $i) == -1)
                {
                    /* If this is the next item, save it to insert at the end of the array */
                    $next = "<a href='$this->url&$this->pageno_url_key=$i'>>></a>";
                }
                $this->links[$i] = "<a href='$this->url&$this->pageno_url_key=$i'>$i</a>";
            }
            if (isset($next) && valid($next))
            {
                $this->links["next"] = $next;
            }
        }

        /**
         * Method that generates the html links
         */
        public function getLinks()
        {
            return $this->links_html;
        }

        /**
         * Generates the HTML code for the pagination links
         * 
         * @return HTML code for the pagination links
         */
        public function getLinksHtmlCode()
        {
            $class = isset($this->html_options['class']) ? $this->html_options['class'] : "pager-link";
            $this->links_html = "<ul id='pager-links-wrapper' class='clearfix'>";
            foreach ($this->links as $id => $link)
            {
                if ($id == $this->current_page)
                {
                    $this->links_html .= "<li class='$class $id current-page'>$link</li>";
                }
                else
                {
                    $this->links_html .= "<li class='$class $id'>$link</li>";
                }
            }
            $this->links_html .= "</ul>";

            return $this->links_html;
        }

    }
    