<?php

    /**
     * An exception thrown when a template is not found
     *
     * @author Joshua Kissoon
     */
    class InvalidTemplateException extends Exception
    {

        const EXCEPTION_CODE = 0001;

        public function __construct($message)
        {
            parent::__construct($message, InvalidTemplateException::EXCEPTION_CODE, null);
        }

    }
    