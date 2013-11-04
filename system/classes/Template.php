<?php

    /**
     * @author Joshua Kissoon
     * @date 20120712
     * @description The template class that is used to load, set variables and output a template
     */
    class Template
    {

       private $template = NULL;
       private $variables = array();

       function __construct($template = null)
       {
          if (isset($template))
          {
             $this->load($template);
          }
       }

       public function load($template)
       {
          /*
           * This function loads the template file
           */
          $template = $template . ".tpl.php";
          if (!is_file($template))
          {
             ScreenMessage::setMessage("File not found: $template", "error");
          }
          elseif (!is_readable($template))
          {
             ScreenMessage::setMessage("Could not access file: $template", "error");
          }
          else
          {
             $this->template = $template;
          }
       }

       public function __set($name, $value)
       {
          /*
           * Called when the user tries to set a variable directly
           */
          $this->set($name, $value);
       }

       public function set($name, $value)
       {
          /*
           * Set a variable within the template
           */
          if ($this->template)
          {
             $this->variables[$name] = $value;
          }
       }

       public function parse()
       {
          /*
           * Function that just returns the template file so it can be reused
           */
          if ($this->template)
          {
             ob_start();
             extract($this->variables, EXTR_SKIP);
             require $this->template;
             $content = ob_get_clean();
             return $content;
          }
       }

       public function publish()
       {
          /*
           * Outputs the template
           */
          if ($this->template)
          {
             ob_start();
             extract($this->variables, EXTR_SKIP);
             require $this->template;
             $content = ob_get_clean();
             print $content;
          }
       }

    }