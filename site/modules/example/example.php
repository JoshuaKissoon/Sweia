<?php

    $content = "Example Module Called";
    
    $url = Sweia::getInstance()->getURL();

    $url[1] = (isset($url[1]) && valid($url[1])) ? $url[1] : "pager";

    switch ($url[1])
    {
        case "pager":
            require "./pager.php";
            break;
    }