<?php

    /**
     * @description Class that handles generating different html elements
     * @author Joshua Kissoon
     * @date 20121020
     */
    class HTML
    {

       public static function stylesheet($stylesheet)
       {
          /*
           * @description Takes in a stylesheet file and returns the stylesheet include html code
           * @params array('file'=>'main.css', media='screen')
           * @return <link href="theme/css/main.css" rel="stylesheet" type="text/css" media="screen">
           */

          if (!is_array($stylesheet))
          {
             /*
              * If the stylesheets variable is just a stylesheet name eg: style.css, instead of an array
              * put that into an array so that foreach would work
              */
             $stylesheet = array('file' => $stylesheet);
          }
          /*
           * Check if the stylesheet name is in href or in file variable.
           * And check if media variable is actually set
           */
          $file = isset($stylesheet['file']) ? $stylesheet['file'] : @$stylesheet['href'];
          $media = isset($stylesheet['media']) ? $stylesheet['media'] : "screen, projection";
          $tag = "<link href='$file' rel='stylesheet' type='text/css' media='$media'>";
          return $tag;
       }

       public static function stylesheets($stylesheets)
       {
          /*
           * @description load n stylesheets
           */
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

       public static function metaTag($meta)
       {
          /*
           * @description Takes in meta information and returns the meta html code
           * @params array('name'=>'description', content='smart Website', 'http-equiv'=>'content-type')
           * @return <meta http-equiv="content-type" name="author" content="Hege Refsnes" />
           */
          $tags = array();
          /*
           * If the $meta variable is just a name eg: just a description,
           * put that into an array format
           */
          if (!is_array($meta))
          {
             $meta = array("name" => $metas);
          }
          /*
           * Go through the meta array to add everything
           */
          $tag = "<meta";
          foreach ($meta as $key => $value)
          {
             $tag .= " $key='$value' ";
          }
          $tag .= " />";
          return $tag;
       }

       public static function script($script)
       {
          /*
           * @description Takes in script information and returns the script html code
           * @params array('file'=>'jsc.js')
           * @return <script src="..." type="text/javascrip" />
           */
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

       public static function scripts($scripts)
       {
          /*
           * @description load n scripts
           */
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

       public static function grid($data, $cols, $options = array())
       {
          /*
           * @description Creates an html grid
           * @params
           *  $data - an array of data to put in the grid
           *  $cols - the amount of columns this grid is going to have
           *  $options - different options that can be applied to the grid
           */
          $grid = "<div class='grid-wrapper'><table class='table grid $title'>";
          $num_items = count($data);                // The amount of items in this grid
          $total_rows = ceil($num_items / $cols);         // How many rows would this grid have?
          $items_added_to_row = 0;
          $current_row = 1;
          foreach ($data as $item)
          {
             if ($items_added_to_row == 0)
             {
                /* If the count is 0 means this is a new row in the table */
                if ($current_row == 1)
                {
                   /* This is the first row in the grid */
                   $grid .= "<tr class='grid-row row-$current_row row-first'>";
                }
                else if ($current_row == $total_rows)
                {
                   /* This is the last row in the grid */
                   $grid .= "<tr class='grid-row row-$current_row row-last'>";
                }
                else
                {
                   $grid .= "<tr class='grid-row row-$current_row'>";
                }
             }
             $grid .= "<td class='grid-cell cell-$count'>$item</td>";
             $items_added_to_row++;

             if ($count == $cols)
             {
                /*
                 * If the amount of items in this row is the same as the amount of columns required,
                 * end this row, and set count to 0 so a new row would be started next loop
                 * Also increase the row count
                 */
                $grid .= "</tr>";
                $items_added_to_row = 0;
                $rowcount++;
             }
          }
          $grid .= "</table></div>";
          return $grid;
       }

       public static function table($header, $data, $options = array())
       {
          /*
           * Returns the html for a table with the data sent
           * @params
           *  $header - table header
           *  $data - an array of arrays each containing a row of data
           */
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