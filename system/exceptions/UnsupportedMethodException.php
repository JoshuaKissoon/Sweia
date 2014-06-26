<?php

    /**
     * An exception thrown when a method is not supported by a class
     *
     * @author Joshua Kissoon
     * @since 20140623
     */
    class UnsupportedMethodException extends Exception
    {

        const EXCEPTION_CODE = 0002;

        public function __construct($message)
        {
            parent::__construct($message, InvalidTemplateException::EXCEPTION_CODE, null);
        }

    }
    