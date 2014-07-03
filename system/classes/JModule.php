<?php

    /**
     * Class that handles all module operations
     * 
     * @author Joshua Kissoon
     * @since 20121218
     */
    class JModule
    {

        private $tbl = "module";
        public $permissions = array(), $urls = array();
        public $name, $description, $type;

        /**
         * If the name is specified, load the module
         */
        public function __construct($modname = null)
        {
            if ($modname)
            {
                $this->load($modname);
            }
        }

        /**
         * Checks if a module already exists within the database with this name
         * 
         * @param $modname The name of the module
         * 
         * @return Boolean Whether the module exists or not
         */
        public function moduleExists($modname = null)
        {
            $sweia = Sweia::getInstance();
            $db = $sweia->getDB();
            $modname = ($modname) ? $modname : $this->name;
            $res = $db->fetchObject($db->query("SELECT name FROM module WHERE name='::modname'", array("::modname" => $modname)));

            if (isset($res->name))
            {
                return ($modname == $res->name) ? true : false;
            }

            return false;
        }

        /**
         * Load all of a module's information if the module exists
         * 
         * @param $modname The name of the module to load
         * 
         * @return The module's data, and return this module object
         */
        public function load($modname)
        {
            if ($this->moduleExists($modname))
            {
                $sweia = Sweia::getInstance();
                $db = $sweia->getDB();
                $mod = $db->fetchObject($db->query("SELECT * FROM module WHERE name='::modname'", array("::modname" => $modname)));
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
         * Loads an array with the permissions for this module
         */
        private function loadPermissions()
        {
            $sweia = Sweia::getInstance();
            $db = $sweia->getDB();
            $this->permissions = array();
            $perms = $db->query("SELECT * FROM permission WHERE module='::modname'", array("::modname" => $this->name));
            while ($perm = $db->fetchObject($perms))
            {
                $this->permissions[$perm->permission] = $perm->title;
            }
        }

        /**
         * Loads an array with the URLs for this module
         */
        private function loadUrls()
        {
            $sweia = Sweia::getInstance();
            $db = $sweia->getDB();
            $this->urls = array();
            $urls = $db->query("SELECT * FROM url_handler WHERE module='::modname'", array("::modname" => $this->name));
            while ($url = $db->fetchObject($urls))
            {
                $this->urls[$url->url] = $url;
            }
        }

        /**
         * Add/update a module to the database
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
         * Adds a new module to the database
         */
        private function add()
        {
            $sweia = Sweia::getInstance();
            $db = $sweia->getDB();
            $values = array(
                "::name" => $this->name,
                "::desc" => $this->description,
                "::type" => $this->type,
                "::status" => 1,
                "::title" => $this->title,
            );
            $sql = "INSERT INTO $this->tbl (name, title, description, type, status) VALUES ('::name', '::title', '::desc', '::type', '::status')";
            $db->query($sql, $values);

            $this->savePermissions();
            $this->saveUrls();
            return true;
        }

        /**
         * Adds a permission to this module's premission array, this is not yet saved to the DB
         * 
         * @param $perm
         * @param $title
         */
        public function addPermission($perm, $title)
        {
            $this->permissions[$perm] = $title;
        }

        /**
         * Add Module permissions to the database 
         */
        private function savePermissions()
        {
            foreach ($this->permissions as $perm => $title)
            {
                $this->savePermission($perm, $title);
            }
        }

        /**
         * Adds a single permission for a module to the database
         */
        private function savePermission($perm, $title)
        {
            $sweia = Sweia::getInstance();
            $db = $sweia->getDB();

            $values = array(
                '::perm' => $perm,
                '::title' => $title,
                '::modname' => $this->name
            );
            $sql = "INSERT INTO permission (permission, title, module) VALUES ('::perm', '::title', '::modname')
                ON DUPLICATE KEY UPDATE title = '::title', module = '::modname'";
            $db->query($sql, $values);
        }

        /**
         * Adds a permurlission to this module's urls array, this is not yet saved to the DB
         * 
         * @param $url
         * @param $data
         */
        public function addUrl($url, $data)
        {
            $this->urls[$url] = $data;
        }

        /**
         * Add Module urls to the database 
         */
        private function saveUrls()
        {
            foreach ($this->urls as $url => $data)
            {
                $this->saveUrl($url, $data);
            }
        }

        /**
         * Adds a single url for a module to the database
         * 
         * @param $url The URL to add to the database for this module
         * @param $data An array with data for this URL
         */
        private function saveUrl($url, $data)
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

            $pos = array_search("*", $parts);
            $placeholder = "";
            if ($pos !== FALSE)
            {
                $parts[$pos] = "%";
                $num_parts = 0;
                $placeholder = "%";
            }

            $sweia = Sweia::getInstance();
            $db = $sweia->getDB();
            $values = array(
                '::url' => $url, '::mod' => $this->name,
                '::perm' => isset($data['permission']) ? $data['permission'] : "",
                '::num_parts' => $num_parts,
                '::p0' => isset($parts[0]) ? $parts[0] : $placeholder,
                '::p1' => isset($parts[1]) ? $parts[1] : $placeholder,
                '::p2' => isset($parts[2]) ? $parts[2] : $placeholder,
                '::p3' => isset($parts[3]) ? $parts[3] : $placeholder,
                '::p4' => isset($parts[4]) ? $parts[4] : $placeholder,
                '::p5' => isset($parts[5]) ? $parts[5] : $placeholder,
                '::p6' => isset($parts[6]) ? $parts[6] : $placeholder,
                '::p7' => isset($parts[7]) ? $parts[7] : $placeholder,
                '::p8' => isset($parts[8]) ? $parts[8] : $placeholder,
                '::p9' => isset($parts[9]) ? $parts[9] : $placeholder,
            );
            $sql = "INSERT INTO url_handler (url, module, permission, num_parts, p0, p1, p2, p3, p4, p5, p6, p7, p8, p9)
                VALUES ('::url', '::mod', '::perm', '::num_parts', '::p0', '::p1', '::p2', '::p3', '::p4', '::p5', '::p6', '::p7', '::p8', '::p9')";
            $db->query($sql, $values);
        }

        /**
         * Checks if a url already exists in the database for this module
         * 
         * @param $url The URL to check it's existence
         */
        private function urlExists($url)
        {
            $sweia = Sweia::getInstance();
            $db = $sweia->getDB();

            $args = array(
                '::url' => $url,
                '::mod' => $this->name,
            );
            $res = $db->fetchObject($db->query("SELECT url FROM url_handler WHERE url='::url' AND module='::mod'", $args));
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
         * Updates a current module data in the database 
         */
        private function update()
        {
            $db = Sweia::getInstance()->getDB();

            $values = array(
                "::name" => $this->name,
                "::desc" => $this->description,
                "::type" => $this->type,
                "::status" => 1,
                "::title" => $this->title,
            );
            $sql = "UPDATE $this->tbl SET description = '::desc', status = '::status', type = '::type', title = '::title' WHERE name = '::name'";
            $db->query($sql, $values);

            $this->updatePermissions();
            $this->updateUrls();
            return true;
        }

        /**
         * Update permissions that already exist, and elete module permissions that are in the database but not in the new permission list
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
                    $this->savePermission($perm, $title);
                }
            }

            foreach ($old_perms as $perm => $title)
            {
                /* The old permissions array will only contain permissions no longer in use, remove these */
                $this->deletePermission($perm);
            }
        }

        /**
         * Updates a single permission for this module
         */
        private function updatePermission($perm, $title)
        {
            $db = Sweia::getInstance()->getDB();

            $values = array(
                '::perm' => $perm,
                '::title' => $title,
                '::modname' => $this->name
            );
            $sql = "UPDATE permission SET title = '::title', module = '::modname' WHERE permission = '::perm'";
            $db->query($sql, $values);
        }

        /**
         * Update urls that already exist for this module. Delete module urls that are in the database but not in the new permission list
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
                    $this->saveUrl($url, $data);
                }
            }

            foreach ($old_urls as $url => $data)
            {
                /* Old urls array will only contain urls no longer in use, remove these urls */
                $this->deleteUrl($url);
            }
        }

        /**
         * Updates a Single URL
         */
        private function updateUrl($url, $data)
        {
            $this->deleteUrl($url);
            $this->saveUrl($url, $data);
        }

        /**
         * Delete the specified url from url_handler database table
         */
        private function deleteUrl($url)
        {
            $db = Sweia::getInstance()->getDB();
            return $db->query("DELETE FROM url_handler WHERE url='::url'", array("::url" => $url));
        }

        /**
         * Delete the specified permission from permission table and role_permission table
         */
        private function deletePermission($perm)
        {
            $db = Sweia::getInstance()->getDB();

            $db->query("DELETE FROM role_permission WHERE permission='::perm'", array("::perm" => $perm));
            $db->query("DELETE FROM permission WHERE permission='::perm'", array("::perm" => $perm));
        }

        /**
         * Completely delete this module and all of it's data from the database
         */
        public function delete()
        {
            if (!$this->moduleExists())
            {
                return false;
            }

            $db = Sweia::getInstance()->getDB();

            /* Delete the URLs and Permissions associated with this module */
            $rs = $db->query("SELECT url FROM url_handler WHERE module='::mod'", array("::mod" => $this->name));
            while ($url = $db->fetchObject($rs))
            {
                $this->deleteUrl($url->url);
            }
            $rs2 = $db->query("SELECT * FROM permission WHERE module='::mod'", array("::mod" => $this->name));
            while ($perm = $db->fetchObject($rs2))
            {
                $this->deletePermission($perm->permission);
            }

            /* Delete the module data */
            return $db->query("DELETE FROM module WHERE name='::mod'", array("::mod" => $this->name));
        }

    }
    