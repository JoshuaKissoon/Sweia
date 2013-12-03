<?php

    /**
     * @author Joshua Kissoon
     * @date 20121219
     * @description Handles all URL events within the site
     */
    class JPath
    {

        /**
         * @return The Site Base URL 
         */
        public static function baseURL()
        {
            return BASE_URL;
        }

        /**
         * @return Returns the Site Base Path 
         */
        public static function basePath()
        {
            return BASE_PATH;
        }

        /**
         *  @return The relative URL from which the request came from
         */
        public static function requestUrl()
        {
            $url = $_SERVER["REQUEST_URI"];
            if (valid(SITE_FOLDER))
            {
                /* If the Site is within a subfolder, remove it from the URL arguments */
                $folder = rtrim(SITE_FOLDER, '/') . '/';
                $url = str_replace($folder, "", $url);
            }
            return rtrim(ltrim($url, '/'), "/");
        }

        /**
         * @return The full URL of the page which the user is on
         */
        public static function fullRequestUrl()
        {
            return BASE_URL . self::requestUrl();
        }

        /**
         * @desc Gets the URL query
         * @return The URL Query
         */
        public static function getUrlQ()
        {
            if (!isset($_GET['urlq']))
            {
                return "";
            }

            $url = $_GET['urlq'];
            return rtrim(ltrim($url, "/"), "/");
        }

        /**
         * @return An array of arguments within the URL currently being viewed
         */
        public static function urlArgs($index = null)
        {
            $url = self::getUrlQ();
            $eurl = explode('/', $url);
            return ($index) ? $eurl[$index] : $eurl;
        }

        /**
         * @desc Finds the modules that handles a URL
         * @param $url The URL for which to check
         * @return The modules that handles this URL
         */
        public static function getUrlHandlers($url = "")
        {
            if(!valid($url))
            {
                $url = self::getUrlQ();
            }
            if (!valid($url))
            {
                $url = HOME_URL;
            }
            $url_parts = explode("/", $url);
            $num_parts = count($url_parts);

            $sql = "SELECT uh.module, uh.permission, md.status FROM url_handler uh LEFT JOIN module md ON (uh.module = md.name) WHERE (num_parts='$num_parts' OR num_parts='0') AND md.status = 1";
            $c = 0;
            $args = array();
            foreach ($url_parts as $part)
            {
                $sql .= " AND (p$c = '::p$c' OR p$c = '%')";
                $args["::p$c"] = $part;
                $c++;
            }
            $sql .= " ORDER BY num_parts DESC";
            global $DB;
            $rs = $DB->query($sql, $args);
            $handlers = array();
            while ($handler = $DB->fetchObject($rs))
            {
                /* Store the handlers */
                $handlers[$handler->module] = array(
                    "module" => $handler->module,
                    "permission" => $handler->permission
                );
            }
            return $handlers;
        }

        /**
         * @description Parses a set of menus and:
         *      -> removes those items the specified user don't have premission to access
         *      -> Append the Site Base URL to each of the menu items if they don't already contain the base url
         * @param $menu An array in the form $url => $title
         * @param $uid The user from whose POV to parse the menu, the currently logged in user is default
         */
        public static function parseMenu($menu, $uid = null)
        {
            /* If no user was specified, parse the menu for the current user */
            global $USER;
            $uid = $USER->uid; //hprint($menu);hprint($USER);
            foreach ($menu as $url => $menuItem)
            {
                /* Remove the site base URL from the front of the menu if it exists there */
                $url1 = str_replace(BASE_URL, "", $url);
                $url = ltrim(rtrim($url1));

                /* Remove this URL from the menu */
                unset($menu[$url]);

                if (self::userHasURLAccessPermission($uid, $url))
                {
                    /* If the user has the necessary permission to access the URL, add the URL back to the menu with the SITE_URL prepended to the URL */
                    $url = self::absoluteUrl($url);
                    $menu[$url] = $menuItem;
                }
            }
            return $menu;
        }

        /**
         * @desc Checks if the user has permission to access this URL 
         * @param $uid The user's id
         * @param $url The URL to check if the user has access to
         */
        public static function userHasURLAccessPermission($uid, $url)
        {
            global $DB;

            $res = $DB->query("SELECT permission FROM url_handler WHERE url='::url'", array("::url" => $url));
            $tmp = $DB->fetchObject($res);

            if (!isset($tmp->permission) || !valid($tmp->permission))
            {
                /* If the URL has no permission, return true that the user has the permission to access the URL */
                return true;
            }

            /* If the URL has some permission, check if the user has the necessary permission to access the URL */
            $args = array("::url" => $url, "::uid" => $uid);
            $sql = "SELECT u.uid, ur.rid, rp.permission, uh.url FROM user u
                    LEFT JOIN user_role ur ON (u.uid = ur.uid) LEFT JOIN role_permission rp ON (rp.rid = ur.rid)
                    LEFT JOIN url_handler uh ON (uh.permission = rp.permission)
                    WHERE uh.url='::url' AND u.uid='::uid' GROUP BY u.uid";
            $res2 = $DB->query($sql, $args);
            $tmp2 = $DB->fetchObject($res2);

            return valid($tmp2->uid) ? true : false;
        }

        /**
         * @desc An old function to call absoluteUrl
         */
        public static function fullUrl($url)
        {
            return self::absoluteUrl($url);
        }

        /**
         * @desc Creates an absolute site URL given a relative URL
         * @param $url the relative URL
         * @return The full site URL for a given URL string 
         */
        public static function absoluteUrl($url)
        {
            return self::baseURL() . "?urlq=" . ltrim($url, "/");
        }

    }
    