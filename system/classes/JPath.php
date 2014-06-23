<?php

    /**
     * Class that is used to handle the current site URL
     * 
     * @author Joshua Kissoon
     * @since 20121219
     * @updated 20140623
     */
    class JPath
    {

        /**
         * @return The relative URL from which the request came from
         */
        public static function requestUrl()
        {
            $url = $_SERVER["REQUEST_URI"];
            if (valid(BaseConfig::SITE_FOLDER))
            {
                /* If the Site is within a subfolder, remove it from the URL arguments */
                $folder = rtrim(BaseConfig::SITE_FOLDER, '/') . '/';
                $url = str_replace($folder, "", $url);
            }
            return rtrim(ltrim($url, '/'), "/");
        }

        /**
         * @return The full URL of the page which the user is on
         */
        public static function fullRequestUrl()
        {
            return SystemConfig::baseUrl() . self::requestUrl();
        }

        /**
         * Gets the URL query
         * 
         * @return String - The URL Query
         */
        public static function getUrlQ()
        {
            if (!isset($_GET['urlq']))
            {
                return BaseConfig::HOME_URL;
            }
            $url = $_GET['urlq'];
            $curl = rtrim(ltrim($url, "/"), "/");
            return (isset($curl) && valid($curl)) ? $curl : BaseConfig::HOME_URL;
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
         * Finds the modules that handles a URL
         * 
         * @param $url The URL for which to check
         * 
         * @return The modules that handles this URL
         */
        public static function getUrlHandlers($url = null)
        {
            if (!valid($url))
            {
                $url = self::getUrlQ();
            }
            if (!valid($url))
            {
                $url = BaseConfig::HOME_URL;
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

            $db = Sweia::getInstance()->getDB();

            $rs = $db->query($sql, $args);
            $handlers = array();

            /* Store the handlers */
            while ($handler = $db->fetchObject($rs))
            {
                $handlers[$handler->module] = array("module" => $handler->module, "permission" => $handler->permission);
            }
            return $handlers;
        }

        /**
         * Parses a set of menus and:
         *      -> removes those items the specified user don't have premission to access
         *      -> Append the Site Base URL to each of the menu items if they don't already contain the base url
         * 
         * @param $menu An array in the form $url => $title
         * @param $uid The user from whose POV to parse the menu, the currently logged in user is default
         * 
         * @return String - The parsed menu
         */
        public static function parseMenu($menu, $uid = null)
        {
            /* If no user was specified, parse the menu for the current user */
            $user = Sweia::getInstance()->getUser();

            $uid = $user->uid;
            foreach ($menu as $url => $menuItem)
            {
                /* Remove the site base URL from the front of the menu if it exists there */
                $url1 = str_replace(SystemConfig::baseUrl(), "", $url);
                $url = ltrim(rtrim($url1));

                /* Remove this URL from the menu */
                unset($menu[$url]);

                $handlers = JPath::getUrlHandlers($url);
                foreach ($handlers as $handler)
                {
                    if (!isset($handler['permission']) || !valid($handler['permission']))
                    {
                        /* There is no permission for this handler, add the URL to the menu */
                        $url = self::absoluteUrl($url);
                        $menu[$url] = $menuItem;
                        break;
                    }
                    else if ($user->usesPermissionSystem() && $user->hasPermission($handler['permission']))
                    {
                        /* The user has the permission, add the URL to the menu */
                        $url = self::absoluteUrl($url);
                        $menu[$url] = $menuItem;
                        break;
                    }
                }
            }
            return $menu;
        }

        /**
         * A support function to call absoluteUrl
         */
        public static function fullUrl($url)
        {
            return self::absoluteUrl($url);
        }

        /**
         * Creates an absolute site URL given a relative URL
         * 
         * @param $url the relative URL
         * 
         * @return The full site URL for a given URL string 
         */
        public static function absoluteUrl($url)
        {
            /* Replace the Base URL if it's already in the string */
            $url = str_replace(SystemConfig::baseUrl(), "", $url);

            /* Remove excess slashes from the URL */
            $url_trimmed = rtrim(ltrim($url, "/"), "/");

            return SystemConfig::baseUrl() . "?urlq=" . ltrim($url_trimmed, "/");
        }

    }
    