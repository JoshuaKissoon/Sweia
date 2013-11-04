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

       public function __construct($modname = null)
       {
          if ($modname)
          {
             /*
              * If the name is specified, load the module
              */
             $this->load($modname);
          }
       }

       public function moduleExists($modname = null)
       {
          /*
           * Checks if a module already exists within the database with this name
           */
          global $DB;
          $modname = ($modname) ? $modname : $this->name;
          $res = $DB->fetchObject($DB->query("SELECT name FROM modules WHERE name='::modname'", array("::modname" => $modname)));
          $name = @$res->name;
          return ($modname == @$name) ? true : false;
       }

       public function load($modname)
       {
          if ($this->moduleExists($modname))
          {
             /*
              * Here we load all module information if the module exists
              */
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

       private function loadPermissions()
       {
          /*
           * Loads an array with the permissions for this module
           */
          global $DB;
          $this->permissions = array();
          $perms = $DB->query("SELECT * FROM permissions WHERE module='::modname'", array("::modname" => $this->name));
          while ($perm = $DB->fetchObject($perms))
          {
             $this->permissions[$perm->permission] = $perm->title;
          }
       }

       private function loadUrls()
       {
          /*
           * Loads an array with the URLs for this module
           */
          global $DB;
          $this->urls = array();
          $urls = $DB->query("SELECT * FROM url_handlers WHERE module='::modname'", array("::modname" => $this->name));
          while ($url = $DB->fetchObject($urls))
          {
             $this->urls[$url->url] = $url;
          }
       }

       public function save()
       {
          /*
           * Add/update a module to the database
           */
          if (!isset($this->name))
             return false;

          if ($this->moduleExists($this->name))
          {
             return $this->update();
          }
          else
          {
             return $this->add();
          }
       }

       private function add()
       {
          /* Adds a new module to the database */
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

       private function addPermissions()
       {
          /*
           * Add Module permissions
           */
          foreach ($this->permissions as $perm => $title)
          {
             $this->addPermission($perm, $title);
          }
       }

       private function addPermission($perm, $title)
       {
          /* Adds a single permission */
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

       private function addUrls()
       {
          /* Add Module urls */
          global $DB;
          foreach ($this->urls as $url => $data)
          {
             $this->addUrl($url, $data);
          }
       }

       private function addUrl($url, $data)
       {
          /* If the permission associated with this URL does not exist, show an error */
          if (valid(@$data['permission']) && !key_exists(@$data['permission'], $this->permissions))
          {
             $values['::perm'] = "";
             ScreenMessage::setMessage("$this->name: Permission {$data['permission']} does not exist, url $url added without any permission", "error");
          }

          /* If the URL already exists for this module, delete it so it will be updated */
          if ($this->urlExists($url))
             $this->deleteUrl($url);

          /* Trim the URL and get the URL parts */
          $url = ltrim($url, '/');
          $url = rtrim($url, '/');
          $parts = explode("/", $url);
          $num_parts = ($parts[count($parts) - 1] == "%") ? 0 : count($parts);

          global $DB;
          $values = array(
              '::url' => $url,
              '::mod' => $this->name,
              '::perm' => @$data['permission'],
              '::num_parts' => $num_parts,
              '::p0' => @$parts[0],
              '::p1' => @$parts[1],
              '::p2' => @$parts[2],
              '::p3' => @$parts[3],
              '::p4' => @$parts[4],
              '::p5' => @$parts[5],
              '::p6' => @$parts[6],
              '::p7' => @$parts[7],
              '::p8' => @$parts[8],
              '::p9' => @$parts[9],
          );
          $sql = "INSERT INTO url_handlers (url, module, permission, num_parts, p0, p1, p2, p3, p4, p5, p6, p7, p8, p9)
                VALUES ('::url', '::mod', '::perm', '::num_parts', '::p0', '::p1', '::p2', '::p3', '::p4', '::p5', '::p6', '::p7', '::p8', '::p9')";
          $DB->query($sql, $values);
       }

       private function urlExists($url)
       {
          /*
           * Checks if a url already exists
           */
          global $DB;
          $args = array(
              '::url' => $url,
              '::mod' => $this->name,
          );
          $temp = $DB->fetchObject($DB->query("SELECT url FROM url_handlers WHERE url='::url' AND module='::mod'", $args));
          $temp = @$temp->url;
          return ($url == @$temp) ? true : false;
       }

       private function update()
       {
          /* Updates a current module that is in the database */
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

       private function updatePermissions()
       {
          /*
           * Update permissions that already exist
           * Delete module permissions that are in the database but not in the new permission list
           */
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

       private function updatePermission($perm, $title)
       {
          /* Updates a single permission */
          global $DB;
          $values = array(
              '::perm' => $perm,
              '::title' => $title,
              '::modname' => $this->name
          );
          $sql = "UPDATE permissions SET title = '::title', module = '::modname' WHERE permission = '::perm'";
          $DB->query($sql, $values);
       }

       private function updateUrls()
       {
          /*
           * Update urls that already exist
           * Delete module urls that are in the database but not in the new permission list
           */
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

       private function updateUrl($url, $data)
       {
          /* Updates a Single URL */
          $this->deleteUrl($url);
          $this->addUrl($url, $data);
       }

       private function deleteUrl($url)
       {
          /*
           * Delete the specified url from url_handlers
           */
          global $DB;
          return $DB->query("DELETE FROM url_handlers WHERE url='::url'", array("::url" => $url));
       }

       private function deletePermission($perm)
       {
          /*
           * Delete the specified permission from permission table and role_permissions table
           */
          global $DB;
          $DB->query("DELETE FROM role_permissions WHERE permission='::perm'", array("::perm" => $perm));
          $DB->query("DELETE FROM permissions WHERE permission='::perm'", array("::perm" => $perm));
       }

       public function delete()
       {
          /* Delete a module */
          if (!$this->moduleExists())
             return false;

          global $DB;

          /* Delete the URLs and Permissions associated with this module */
          $rs = $DB->query("SELECT url FROM url_handlers WHERE module='::mod'", array("::mod" => $this->name));
          while ($url = $DB->fetchObject($rs))
          {
             $this->deleteUrl($url->url);
          }
          $rs = $DB->query("SELECT * FROM permissions WHERE module='::mod'", array("::mod" => $this->name));
          while ($perm = $DB->fetchObject($rs))
          {
             $this->deletePermission($perm->permission);
          }

          /* Delete the module data */
          return $DB->query("DELETE FROM modules WHERE name='::mod'", array("::mod" => $this->name));
       }

    }