<?php

    /**
     * Class that handles generating different html elements
     * 
     * @author Joshua Kissoon
     * @since 20121020
     */
    class HTML
    {

        /**
         * Takes in a stylesheet file and returns the stylesheet include html code
         * 
         * @param $stylesheet is a stylesheet in the form: array('file' => 'main.css', media => 'screen')
         * 
         * @return The HTML code for a stylesheet link
         * 
         * @example <link href="theme/css/main.css" rel="stylesheet" type="text/css" media="screen">
         * 
         * @updated 20140623
         */
        public static function stylesheet($stylesheet)
        {
            if (!is_array($stylesheet))
            {
                $stylesheet = array('file' => $stylesheet);
            }
            /*
             * Check if the stylesheet name is in href or in file variable.
             * And check if media variable is actually set
             */
            $file = isset($stylesheet['file']) ? $stylesheet['file'] : (isset($stylesheet['href']) ? $stylesheet['href'] : "");
            $media = isset($stylesheet['media']) ? $stylesheet['media'] : "screen, projection";
            $tag = "<link href='$file' rel='stylesheet' type='text/css' media='$media'>";
            return $tag;
        }

        /**
         * Creates the HTML link code for several stylesheets by repeatedly calling the stylesheet method above
         * 
         * @param $stylesheets An array of arrays of stylesheets' information
         * 
         * @return The HTML code for the set of stylesheets
         */
        public static function stylesheets($stylesheets)
        {
            if (!is_array($stylesheets))
            {
                $stylesheets = array("file" => $stylesheets);
            }
            $tags = array();
            foreach ($stylesheets as $stylesheet)
            {
                $tags[] = self::stylesheet($stylesheet);
            }
            return implode("\n", $tags);
        }

        /**
         * Takes in a meta info and returns the meta include html code
         * 
         * @param $meta is an array with the meta information: array('name'=>'description', content='smart Website', 'http-equiv'=>'content-type')
         * 
         * @return The HTML code for a meta tag
         * 
         * @example <meta http-equiv="content-type" name="author" content="Hege Refsnes" />
         */
        public static function metaTag($meta)
        {
            if (!is_array($meta))
            {
                $meta = array("name" => $meta);
            }

            /* Go through the meta array to add everything */
            $tag = "<meta";
            foreach ($meta as $key => $value)
            {
                $tag .= " $key='$value' ";
            }
            $tag .= " />";
            return $tag;
        }

        /**
         * Takes in script information and returns the script html code
         * 
         * @param $script An array with the script information: array('file'=>'jsc.js')
         * 
         * @return The HTML code for the script link
         * 
         * @example <script src="..." type="text/javascrip" />
         */
        public static function script($script)
        {

            if (!is_array($script))
            {
                /*
                 * If the $scripts variable is just a script name eg: script.js,
                 * put that into an array so that foreach would work
                 */
                $script = array("file" => $script);
            }
            $file = isset($script['file']) ? $script['file'] : $script['href'];
            $type = isset($script['type']) ? $script['type'] : "text/javascript";
            $tag = "<script src='$file' type='$type'></script>";
            return $tag;
        }

        /**
         * Creates the HTML link code for several sripts by repeatedly calling the script method above
         * 
         * @param $scripts An array of arrays of scripts' information
         * 
         * @return The HTML code for the set of scripts
         */
        public static function scripts($scripts)
        {
            if (!is_array($scripts))
            {
                $scripts = array("file" => $scripts);
            }
            $tags = array();
            foreach ($scripts as $script)
            {
                $tags[] = self::script($script);
            }
            return implode("\n", $tags);
        }

        /**
         * Creates an html grid
         * 
         * @param $data An array of data to put in the grid
         * @param $cols Ahe amount of columns this grid is going to have
         * @param $options Different options that can be applied to the grid
         * 
         * @return String - An HTML string for the grid
         */
        public static function grid($data, $cols, $options = array())
        {
            $id = isset($options["id"]) ? $options["id"] : "";      // Get the grid ID

            $grid = "<div class='grid-wrapper' id='$id'><table class='table grid'>";
            $rowcount = 1;
            $count = 0;
            foreach ($data as $item)
            {
                /* If the count is 0 means this is a new row in the table */
                if ($count == 0)
                {
                    $grid .= "<tr class='grid-row row-$rowcount'>";
                }

                $grid .= "<td class='grid-cell cell-$count'>$item</td>";
                $count++;

                if ($count == $cols)
                {
                    /* End of row, close it and reset the necessary variables */
                    $grid .= "</tr>";
                    $count = 0;
                    $rowcount++;
                }
            }
            $grid .= "</table></div>";
            return $grid;
        }

        /**
         * Generates the HTML code for a table
         * 
         * @param $header Table headers
         * @param $data An array of arrays each containing a row of data
         * 
         * @return The html for a table with the data sent
         */
        public static function table($header, $data, $options = array())
        {
            $tclass = isset($options['tclass']) ? $options['tclass'] : "";
            $table = "<div class='table-wrapper $tclass-wrapper'><table class='table $tclass'>";

            /* Setup table header */
            $table .= "<thead>";
            $table .= "<tr class='header'>";
            foreach ($header as $h)
            {
                $table .= "<th>$h</th>";
            }
            $table .= "</tr></thead>";

            /* Setup table body */
            $table .= "<tbody>";
            $rclass = isset($options['rclass']) ? $options['rclass'] : "";
            $row_class = "odd";
            foreach ($data as $row)
            {
                $table .= "<tr class='$row_class body-tr $rclass'>";
                foreach ($row as $key => $body)
                {
                    $table .= "<td class='$key'>$body</td>";
                }
                $table .= "</tr>";
                $row_class = ($row_class == "odd") ? "even" : "odd";
            }
            $table .= "</tbody>";
            $table .= "</table></div>";
            return $table;
        }

    }
    