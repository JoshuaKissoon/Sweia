<?php

    $content = "Example Module Called";

    switch (@$_GET['page'])
    {
        case "pager":
            require M_EXAMPLE_PATH . "/pager.php";
            break;
    }