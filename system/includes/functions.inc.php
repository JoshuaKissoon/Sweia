<?php

    /*
     * @author Joshua Kissoon
     * @date Very Long Ago
     * @file This file contains general functions that are not specific to any class or application
     */

    /**
     * @desc Checks the validity of an expression
     * @return Boolean Whether the expression is valid or not
     */
    function valid($expression = "")
    {
        if (!isset($expression))
        {
            return false;
        }

        if (is_array($expression) || is_object($expression))
        {
            return true;
        }

        $ex = trim($expression);
        if (isset($ex) && !is_null($ex) && $ex != "")
        {
            return true;
        }
        return false;
    }

    function hprint($data, $show_html = false)
    {
        /*
         * Takes in an array or an object and prints it out hiearchically to the screen
         */
        if ($show_html)
        {
            /* If html is needed to be shown, html elements needs to be sanitized to be displayed on the screen */
            print htmlentities('<pre>' . print_r($data, TRUE) . '</pre>');
        }
        else
        {
            print '<pre>' . print_r($data, TRUE) . '</pre>';
        }
    }

    function random_alphanumeric_string($length = 12)
    {
        /* Function that returns a random AlphaNumeric String of a specified length */
        $alphNums = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $newString = str_shuffle(str_repeat($alphNums, rand(1, $length)));
        return substr($newString, rand(0, strlen($newString) - $length), $length);
    }

    function random_password($length = 0)
    {
        /* Function that generates a random password of a specified length */
        $length = ($length == 0) ? 10 : $length;
        $characters = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM!@#$%^&*()_+=";
        $token = "";
        for ($i = 0; $i < $length; $i++)
        {
            $value = strlen($characters) - 1;
            $token .= $characters[rand(0, $value)];
        }
        return $token;
    }

    function string_teaser($string, $length, $add_dots = false, $concat_end = "")
    {
        /*
         * Functions that trims a string to a specific length
         * @params
         *  $string - The string to trim
         *  $length - the length to trim this string to
         *  $add_dots - add dots to the end of the trimmed string ?
         *  $concat_end - any value to concatenate to the end of the trimmed string
         */
        if ($add_dots)
        {
            $end = " ...";
        }
        if (strlen($string) > $length)
        {
            return substr($string, 0, $length) . @$end . " $concat_end";
        }
        else
        {
            return $string . " $concat_end";
        }
    }

    function get_ordinal_number($number)
    {
        if ($number == 1)
        {
            return "first";
        }
        else if ($number == 2)
        {
            return "second";
        }
        else if ($number == 3)
        {
            return "third";
        }
        else if ($number == 4)
        {
            return "fourth";
        }
        else if ($number == 5)
        {
            return "fifth";
        }
        else if ($number == 6)
        {
            return "sixth";
        }
    }

    function get_age_years($dob)
    {
        list($Y, $m, $d) = explode("-", $dob);
        return( date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y );
    }
    