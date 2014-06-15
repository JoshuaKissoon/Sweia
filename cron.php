<?php

    /*
     * This is our cron file, file that updates thi site
     */
    require_once 'system/initialize.inc.php';

    /* Cleanup our temporary Folder */

    /* Log this cron run */
    smart_log("cron", "Cron ran successfully");