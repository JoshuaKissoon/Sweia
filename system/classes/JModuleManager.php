<?php

    /**
     * @author Joshua Kissoon
     * @description Class that handles all management operations on module
     * @date 20121219
     */
    class JModuleManager
    {

        private static $modtbl = "module";

        /**
         * @desc Here we simply load the main module handler file for this module: modulename.php
         */
        public static function getModule($modname)
        {
            return self::getModulePath($modname) . "$modname.php";
        }

        /**
         * @desc Here we simply load the main module handler file for this module: modulename.php
         */
        public static function getModulePath($modname)
        {
            $sweia = Sweia::getInstance();
            $DB = $sweia->getDB();

            $temp = $DB->fetchObject($DB->query("SELECT type FROM module WHERE name = '::mod'", array("::mod" => $modname)));
            if (isset($temp->type) && $temp->type == "system")
            {
                return SYSTEM_MODULES_PATH . "$modname/";
            }
            else
            {
                return SITE_MODULES_PATH . "$modname/";
            }
        }

        /**
         * @desc Here we simply load the main module handler file for this module: modulename.php
         * @param $modname The module for which to get it's URL
         */
        public static function getModuleURL($modname)
        {
            $sweia = Sweia::getInstance();
            $DB = $sweia->getDB();

            $temp = $DB->fetchObject($DB->query("SELECT type FROM module WHERE name = '::mod'", array("::mod" => $modname)));
            if (isset($temp->type) && $temp->type == "system")
            {
                $path = SYSTEM_MODULES_URL . "$modname/";
            }
            else
            {
                $path = SITE_MODULES_URL . "$modname/";
            }
            return $path;
        }

        /**
         * @desc Return a list of all modules within the system
         */
        public static function getModules()
        {
            $sweia = Sweia::getInstance();
            $DB = $sweia->getDB();

            $ret = array();
            $res = $DB->query("SELECT * FROM module");
            while ($mod = $DB->fetchObject($res))
            {
                $ret[$mod->name] = $mod;
            }
            return $ret;
        }

        /**
         * @desc Scan the modules path for all modules, check for module changes and do updates on site and system modules
         */
        public static function setupModules()
        {
            $current_modules = array();                         // Stores all the modules that are currently in the site

            /* Setup system modules */
            $sys_modtype = "system";
            $sys_modules = self::scanModulesDir(SYSTEM_MODULES_PATH);
            foreach ($sys_modules as $modname => $modpath)
            {
                if (self::setupModule($modname, $modpath, $sys_modtype))
                {
                    $current_modules[] = $modname;
                }
            }

            /* Setup site modules */
            $site_modtype = "site";
            $site_modules = self::scanModulesDir(SITE_MODULES_PATH);
            foreach ($site_modules as $modname => $modpath)
            {
                if (self::setupModule($modname, $modpath, $site_modtype))
                {
                    $current_modules[] = $modname;
                }
            }

            /* Remove the modules that are in the database but no longer on the site */
            self::deleteNullModules($current_modules);
        }

        /**
         * @desc Scan a specified modules directory for modules 
         * @param The module directory to scan
         */
        public static function scanModulesDir($dir)
        {
            if (!is_dir($dir))
            {
                return false;
            }
            $modules = array();
            foreach (new DirectoryIterator($dir) as $fileinfo)
            {
                /* Scan the module directory for modules */
                if ($fileinfo->isDot())
                {
                    continue;
                }

                $modname = $fileinfo->getFilename();
                $modpath = $dir . "$modname/";
                if (is_dir($modpath))
                {
                    $modules[$modname] = $modpath;
                }
            }
            return $modules;
        }

        /**
         * @desc Load the module data from the module file into the database
         * @param $modname The name of the module to setup
         * @param $modpath Where is the module located
         * @param $modtype Whether it's a site or system module
         */
        public static function setupModule($modname, $modpath, $modtype)
        {
            if (!is_dir($modpath))
            {
                return false;
            }

            $mod = scandir($modpath);
            if (!file_exists($modpath . "/$modname.info.xml") || !file_exists($modpath . "/$modname.php"))
            {
                return false;   // Exit if no .info or .modname file exist
            }
            unset($mod[0]);
            unset($mod[1]);

            /* Load the module's data */
            $xmldata = new SimpleXMLElement("$modpath/$modname.info.xml", null, true);
            $modinfo = json_decode(json_encode($xmldata), TRUE);
            if (isset($modinfo['information']['title']))
            {
                /* Only add the module to the site if it has a name */
                $module = new JModule();
                foreach ($modinfo['information'] as $key => $value)
                {
                    $module->$key = $value;
                }
                /* Adding the permissions */
                if (isset($modinfo['permissions']['permission']) && is_array($modinfo['permissions']['permission']))
                {
                    if (!isset($modinfo['permissions']['permission'][0]))
                    {
                        $temp = $modinfo['permissions']['permission'];
                        unset($modinfo['permissions']['permission']);
                        $modinfo['permissions']['permission'] = array($temp);
                    }

                    foreach ($modinfo['permissions']['permission'] as $perm)
                    {
                        $module->addPermission($perm['perm'], $perm['title']);
                    }
                }
                /* Adding the URLs for this module */
                if (isset($modinfo['urls']['url']) && is_array($modinfo['urls']['url']))
                {
                    foreach ($modinfo['urls']['url'] as $url)
                    {
                        $data = array();
                        if (isset($url['permission']))
                        {
                            $data['permission'] = $url['permission'];
                        }

                        if (isset($url['link']))
                        {
                            $link = $url['link'];
                        }
                        else
                        {
                            $link = $url;
                        }
                        $module->addUrl($link, $data);
                    }
                }
                $module->type = $modtype;
                $module->name = $modname;
                return $module->save();
            }
        }

        /**
         * @desc Removes from the database all modules not in the current modules list
         * @param $currentmods A list of current modules
         */
        public static function deleteNullModules($currentmods = array())
        {
            $sweia = Sweia::getInstance();
            $DB = $sweia->getDB();

            $currentmods = "'" . implode("', '", $currentmods) . "'";
            $sql = "SELECT name FROM " . self::$modtbl . " WHERE name NOT IN ($currentmods)";
            $rs = $DB->query($sql);

            while ($modname = $DB->fetchObject($rs))
            {
                /* Delete all modules that are not in the set of current modules */
                $mod = new JModule($modname->name);
                $mod->delete();
            }
        }

    }
    