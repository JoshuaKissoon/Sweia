<?php

    /**
     * @author Joshua Kissoon
     * @description Class that handles all management operations on modules
     * @date 20121219
     */
    class JModuleManager
    {

       private static $modtbl = "modules";

       public static function getModule($modname)
       {
          /*
           * Here we simply load the main module handler file for this module: modulename.php
           */
          return self::getModulePath($modname) . "$modname.php";
       }

       public static function getModulePath($modname)
       {
          /*
           * Here we simply load the main module handler file for this module: modulename.php
           */
          global $DB;
          $temp = $DB->fetchObject($DB->query("SELECT type FROM modules WHERE name = '::mod'", array("::mod" => $modname)));
          if (@$temp->type == "system")
             return SYSTEM_MODULES_PATH . "$modname/";
          else
             return SITE_MODULES_PATH . "$modname/";
       }

       public static function getModuleURL($modname)
       {
          /*
           * Here we simply load the main module handler file for this module: modulename.php
           */
          global $DB;
          $temp = $DB->fetchObject($DB->query("SELECT type FROM modules WHERE name = '::mod'", array("::mod" => $modname)));
          if (@$temp->type == "system")
             $path = SYSTEM_MODULES_URL . "$modname/";
          else
             $path = SITE_MODULES_URL . "$modname/";
          return $path;
       }

       public static function getModules()
       {
          /* Return a list of all modules within the system */
          global $DB;
          $ret = array();
          $res = $DB->query("SELECT * FROM modules");
          while ($mod = $DB->fetchObject($res))
          {
             $ret[$mod->name] = $mod;
          }
          return $ret;
       }

       public static function setupModules()
       {
          /* Function that Scan the modules path for all modules */
          /*
           * Scan for module changes and do updates on site and system modules
           */
          $current_modules = array();                         // Stores all the modules that are currently in the site

          /* Setup system modules */
          $modtype = "system";
          $modules = self::scanModulesDir(SYSTEM_MODULES_PATH);
          foreach ($modules as $modname => $modpath)
          {
             if (self::setupModule($modname, $modpath, $modtype))
                $current_modules[] = $modname;
          }

          /* Setup site modules */
          $modtype = "site";
          $modules = self::scanModulesDir(SITE_MODULES_PATH);
          foreach ($modules as $modname => $modpath)
          {
             if (self::setupModule($modname, $modpath, $modtype))
                $current_modules[] = $modname;
          }

          /* Remove the modules that are in the database but no longer on the site */
          self::deleteNullModules($current_modules);
       }

       public static function scanModulesDir($dir)
       {
          /* Here we scan a specified modules directory for modules */
          $modules = array();
          if (is_dir($dir))
          {
             foreach (new DirectoryIterator($dir) as $fileinfo)
             {
                /* Scan the module directory for modules */
                if ($fileinfo->isDot())
                   continue;
                $modname = $fileinfo->getFilename();
                $modpath = $dir . "$modname/";
                if (is_dir($modpath))
                {
                   $modules[$modname] = $modpath;
                }
             }
          }
          return $modules;
       }

       public static function setupModule($modname, $modpath, $modtype)
       {
          /* Load the module data from the module file into the database */
          if (is_dir($modpath))
          {
             /* If the file is a directory, load the module directory contents */
             $mod = scandir($modpath);
             if (file_exists($modpath . "/$modname.info.php") && file_exists($modpath . "/$modname.php"))
             {
                /* If the module info and .module file exists, load the module .info file */
                unset($mod[0]);
                unset($mod[1]);

                /*
                 * Load the module .info file
                 * And load the module info array within the info file into a local variable $modinfo
                 */
                require "$modpath/$modname.info.php";
                $info = $modname . "info";
                $modinfo = @$$info;
                if (isset($modinfo["title"]))
                {
                   /* Only add the module to the site if it has a name */
                   $module = new JModule();
                   foreach ($modinfo as $key => $value)
                      $module->$key = $value;
                   $module->type = $modtype;
                   $module->name = $modname;
                   return $module->save();
                }
             }
          }
          return false;
       }

       public static function deleteNullModules($currentmods = array())
       {
          /*
           * Takes in a list of all the current modules,
           * and removes from the database all modules not in this list
           * Also remove module associations
           */
          global $DB;
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