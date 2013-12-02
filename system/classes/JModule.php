<?php

    /**
     * @author Joshua Kissoon
     * @description Class that handles all module operations
     * @date 20121218
     */
    class JModule
    {

        private $tbl = "modules";
        public $permissions = array(), $urls = array();
        public $name, $description, $type;

        /**
         * @desc If the name is specified, load the module
         */
        public function __construct($modname = null)
        {
            if ($modname)
            {
                $this->load($modname);
            }
        }

        /**
         * @desc Checks if a module already exists within the database with this name
         * @param $modname The name of the module
         * @return Boolean Whether the module exists or not
         */
        public function moduleExists($modname = null)
        {
            global $DB;
            $modname = ($modname) ? $modname : $this->name;
            $res = $DB->fetchObject($DB->query("SELECT name FROM modules WHERE name='::modname'", array("::modname" => $modname)));

            if (isset($res->name))
            {
                return ($modname == $res->name) ? true : false;
            }

            return false;
        }

        /**
         * @desc Load all of a module's information if the module exists
         * @param $modname The name of the module to load
         * @return The module's data, and return this module object
         */
        public function load($modname)
        {
            if ($this->moduleExists($modname))
            {
                global $DB;
                $mod = $DB->fetchObject($DB->query("SELECT * FROM modules WHERE name='::modname'", array("::modname" => $modname)));
                foreach ($mod as $key => $value)
                {
                    $this->$key = $value;
                }
                $this->loadPermissions();
                $this->loadUrls();
                return $this;
            }
            else
            {
                return false;
            }
        }

        /**
         * @desc Loads an array with the permissions for this module
         */
        private function loadPermissions()
        {
            global $DB;
            $this->permissions = array();
            $perms = $DB->query("SELECT * FROM permissions WHERE module='::modname'", array("::modname" => $this->name));
            while ($perm = $DB->fetchObject($perms))
            {
                $this->permissions[$perm->permission] = $perm->title;
            }
        }

        /**
         * @desc Loads an array with the URLs for this module
         */
        private function loadUrls()
        {
            global $DB;
            $this->urls = array();
            $urls = $DB->query("SELECT * FROM url_handlers WHERE module='::modname'", array("::modname" => $this->name));
            while ($url = $DB->fetchObject($urls))
            {
                $this->urls[$url->url] = $url;
            }
        }

        /**
         * @desc Add/update a module to the database
         */
        public function save()
        {
            if (!isset($this->name))
            {
                return false;
            }

            if ($this->moduleExists($this->name))
            {
                return $this->update();
            }
            else
            {
                return $this->add();
            }
        }

        /**
         * @desc Adds a new module to the database
         */
        private function add()
        {
            global $DB;
            $values = array(
                "::name" => $this->name,
                "::desc" => $this->description,
                "::type" => $this->type,
                "::status" => 1,
                "::title" => $this->title,
            );
            $sql = "INSERT INTO $this->tbl (name, title, description, type, status) VALUES ('::name', '::title', '::desc', '::type', '::status')";
            $DB->query($sql, $values);

            $this->addPermissions();
            $this->addUrls();
            return true;
        }

        /**
         * @desc Add Module permissions to the database 
         */
        private function addPermissions()
        {
            foreach ($this->permissions as $perm => $title)
            {
                $this->addPermission($perm, $title);
            }
        }

        /**
         * @desc Adds a single permission for a module to the database
         */
        private function addPermission($perm, $title)
        {
            global $DB;
            $values = array(
                '::perm' => $perm,
                '::title' => $title,
                '::modname' => $this->name
            );
            $sql = "INSERT INTO permissions (permission, title, module) VALUES ('::perm', '::title', '::modname')
                ON DUPLICATE KEY UPDATE title = '::title', module = '::modname'";
            $DB->query($sql, $values);
        }

        /**
         * @desc Add Module urls to the database 
         */
        private function addUrls()
        {
            foreach ($this->urls as $url => $data)
            {
                $this->addUrl($url, $data);
            }
        }

        /**
         * @desc Adds a single url for a module to the database
         * @param $url The URL to add to the database for this module
         * @param $data An array with data for this URL
         */
        private function addUrl($url, $data)
        {
            /* If the URL already exists for this module, delete it so it will be updated */
            if ($this->urlExists($url))
            {
                $this->deleteUrl($url);
            }

            /* Trim the URL and get the URL parts */
            $url = rtrim(ltrim($url, '/'), '/');
            $parts = explode("/", $url);
            $num_parts = ($parts[count($parts) - 1] == "%") ? 0 : count($parts);

            global $DB;
            $values = array(
                '::url' => $url, '::mod' => $this->name,
                '::perm' => isset($data['permission']) ? $data['permission'] : "",
                '::num_parts' => $num_parts,
                '::p0' => isset($parts[0]) ? $parts[0] : "",
                '::p1' => isset($parts[1]) ? $parts[1] : "",
                '::p2' => isset($parts[2]) ? $parts[2] : "",
                '::p3' => isset($parts[3]) ? $parts[3] : "",
                '::p4' => isset($parts[4]) ? $parts[4] : "",
                '::p5' => isset($parts[5]) ? $parts[5] : "",
                '::p6' => isset($parts[6]) ? $parts[6] : "",
                '::p7' => isset($parts[7]) ? $parts[7] : "",
                '::p8' => isset($parts[8]) ? $parts[8] : "",
                '::p9' => isset($parts[9]) ? $parts[9] : "",
            );
            $sql = "INSERT INTO url_handlers (url, module, permission, num_parts, p0, p1, p2, p3, p4, p5, p6, p7, p8, p9)
                VALUES ('::url', '::mod', '::perm', '::num_parts', '::p0', '::p1', '::p2', '::p3', '::p4', '::p5', '::p6', '::p7', '::p8', '::p9')";
            $DB->query($sql, $values);
        }

        /**
         * @desc Checks if a url already exists in the database for this module
         * @param $url The URL to check it's existence
         */
        private function urlExists($url)
        {
            global $DB;
            $args = array(
                '::url' => $url,
                '::mod' => $this->name,
            );
            $res = $DB->fetchObject($DB->query("SELECT url FROM url_handlers WHERE url='::url' AND module='::mod'", $args));
            if (isset($res->url))
            {
                return ($url == $res->url) ? true : false;
            }
            else
            {
                return false;
            }
        }

        /**
         * @desc Updates a current module data in the database 
         */
        private function update()
        {
            global $DB;
            $values = array(
                "::name" => $this->name,
                "::desc" => $this->description,
                "::type" => $this->type,
                "::status" => 1,
                "::title" => $this->title,
            );
            $sql = "UPDATE $this->tbl SET description = '::desc', status = '::status', type = '::type', title = '::title' WHERE name = '::name'";
            $DB->query($sql, $values);

            $this->updatePermissions();
            $this->updateUrls();
            return true;
        }

        /**
         * @desc Update permissions that already exist, and elete module permissions that are in the database but not in the new permission list
         */
        private function updatePermissions()
        {
            /* Load the old permissions */
            $new_perms = $this->permissions;        // Save the current permissions array
            $this->loadPermissions();               // Load the old permissions
            $old_perms = $this->permissions;
            $this->permissions = $new_perms;

            foreach ($this->permissions as $perm => $title)
            {
                /* For each permission, if it already exists, update it. Else, add it to the database */
                if (array_key_exists($perm, $old_perms))
                {
                    $this->updatePermission($perm, $title);
                    unset($old_perms[$perm]);   // Remove permission from old_perms array if it is a part of new permissions
                }
                else
                {
                    $this->addPermission($perm, $title);
                }
            }

            foreach ($old_perms as $perm => $title)
            {
                /* The old permissions array will only contain permissions no longer in use, remove these */
                $this->deletePermission($perm);
            }
        }

        /**
         * @desc Updates a single permission for this module
         */
        private function updatePermission($perm, $title)
        {
            global $DB;
            $values = array(
                '::perm' => $perm,
                '::title' => $title,
                '::modname' => $this->name
            );
            $sql = "UPDATE permissions SET title = '::title', module = '::modname' WHERE permission = '::perm'";
            $DB->query($sql, $values);
        }

        /**
         * @desc Update urls that already exist for this module. Delete module urls that are in the database but not in the new permission list
         */
        private function updateUrls()
        {
            /* Load the old permissions */
            $new_urls = $this->urls;        // Save the current permissions array
            $this->loadUrls();               // Load the old permissions
            $old_urls = $this->urls;
            $this->urls = $new_urls;

            foreach ($this->urls as $url => $data)
            {
                /* For each new url, if the url already exists update it. Else add it to the database */
                if (array_key_exists($url, $old_urls))
                {
                    $this->updateUrl($url, $data);
                    unset($old_urls[$url]);   // Remove permission from old_perms array if it is a part of new permissions
                }
                else
                {
                    $this->addUrl($url, $data);
                }
            }

            foreach ($old_urls as $url => $data)
            {
                /* Old urls array will only contain urls no longer in use, remove these urls */
                $this->deleteUrl($url);
            }
        }

        /**
         * @desc Updates a Single URL
         */
        private function updateUrl($url, $data)
        {
            $this->deleteUrl($url);
            $this->addUrl($url, $data);
        }

        /**
         * @desc Delete the specified url from url_handlers database table
         */
        private function deleteUrl($url)
        {
            global $DB;
            return $DB->query("DELETE FROM url_handlers WHERE url='::url'", array("::url" => $url));
        }

        /**
         * @desc Delete the specified permission from permission table and role_permissions table
         */
        private function deletePermission($perm)
        {
            global $DB;
            $DB->query("DELETE FROM role_permissions WHERE permission='::perm'", array("::perm" => $perm));
            $DB->query("DELETE FROM permissions WHERE permission='::perm'", array("::perm" => $perm));
        }

        /**
         * @desc Completely delete this module and all of it's data from the database
         */
        public function delete()
        {
            if (!$this->moduleExists())
            {
                return false;
            }

            global $DB;

            /* Delete the URLs and Permissions associated with this module */
            $rs = $DB->query("SELECT url FROM url_handlers WHERE module='::mod'", array("::mod" => $this->name));
            while ($url = $DB->fetchObject($rs))
            {
                $this->deleteUrl($url->url);
            }
            $rs2 = $DB->query("SELECT * FROM permissions WHERE module='::mod'", array("::mod" => $this->name));
            while ($perm = $DB->fetchObject($rs2))
            {
                $this->deletePermission($perm->permission);
            }

            /* Delete the module data */
            return $DB->query("DELETE FROM modules WHERE name='::mod'", array("::mod" => $this->name));
        }

    }
    