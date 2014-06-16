<?php

    $content = "Example Module Called";
    
    $URL = Sweia::getInstance()->getURL();

    $URL[1] = (isset($URL[1]) && valid($URL[1])) ? $URL[1] : "pager";

    switch ($URL[1])
    {
        case "pager":
            require "./pager.php";
            break;
    }