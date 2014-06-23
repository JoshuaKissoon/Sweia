<?php

    /**
     * Class that handles all management operations on module
     * 
     * @author Joshua Kissoon
     * @since 20121219
     */
    class JModuleManager
    {

        const DB_TBL_MODULES = "module";

        /**
         * Load the main module handler file for this module: modulename.php
         */
        public static function getModule($modname)
        {
            return self::getModulePath($modname) . "$modname.php";
        }

        /**
         * Get the folder path for the module
         */
        public static function getModulePath($modname)
        {
            $db = Sweia::getInstance()->getDB();

            $temp = $db->fetchObject($db->query("SELECT type FROM module WHERE name = '::mod'", array("::mod" => $modname)));
            if (isset($temp->type) && $temp->type == "system")
            {
                return SystemConfig::modulesPath() . "$modname/";
            }
            else
            {
                return SiteConfig::modulesPath() . "$modname/";
            }
        }

        /**
         * Here we simply load the main module handler file for this module: modulename.php
         * 
         * @param $modname The module for which to get it's URL
         */
        public static function getModuleURL($modname)
        {
            $db = Sweia::getInstance()->getDB();

            $temp = $db->fetchObject($db->query("SELECT type FROM module WHERE name = '::mod'", array("::mod" => $modname)));
            if (isset($temp->type) && $temp->type == "system")
            {
                $path = SystemConfig::modulesUrl() . "$modname/";
            }
            else
            {
                $path = SiteConfig::modulesUrl() . "$modname/";
            }
            return $path;
        }

        /**
         * Return a list of all modules within the system
         */
        public static function getModules()
        {
            $db = Sweia::getInstance()->getDB();

            $ret = array();
            $res = $db->query("SELECT * FROM module");
            while ($mod = $db->fetchObject($res))
            {
                $ret[$mod->name] = $mod;
            }
            return $ret;
        }

        /**
         * Scan the modules path for all modules, check for module changes and do updates on site and system modules
         */
        public static function setupModules()
        {
            $current_modules = array();   // Stores all the modules that are currently in the site

            /* Setup system modules */
            $sys_modtype = "system";
            $sys_modules = self::scanModulesDir(SystemConfig::modulesPath());
            foreach ($sys_modules as $modname => $modpath)
            {
                if (self::setupModule($modname, $modpath, $sys_modtype))
                {
                    $current_modules[] = $modname;
                }
            }

            /* Setup site modules */
            $site_modtype = "site";
            $site_modules = self::scanModulesDir(SiteConfig::modulesPath());
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
         * Scan a specified modules directory for modules 
         * 
         * @param The module directory to scan
         * 
         * @return Array - An array of all modules in the given directory
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
         * Load the module data from the module file into the database
         * 
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
         * Removes from the database all modules not in the current modules list
         * 
         * @param $currentmods A list of current modules
         */
        public static function deleteNullModules($currentmods = array())
        {
            $db = Sweia::getInstance()->getDB();

            $currentmods = "'" . implode("', '", $currentmods) . "'";
            $sql = "SELECT name FROM " . JModuleManager::DB_TBL_MODULES . " WHERE name NOT IN ($currentmods)";
            $rs = $db->query($sql);

            while ($modname = $db->fetchObject($rs))
            {
                /* Delete all modules that are not in the set of current modules */
                $mod = new JModule($modname->name);
                $mod->delete();
            }
        }

    }
    