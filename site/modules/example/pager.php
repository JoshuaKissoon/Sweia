<?php

    /*
     * Testing the pager class
     */

    $db = Sweia::getInstance()->getDB();
    
    $content = "Pager Class Example Usage <hr /> <br />";
    $sql = "SELECT * FROM country";
    $count = $db->query("SELECT count(code) as count FROM country");
    $count = $db->fetchObject($count)->count;
    
    $params = array(
        "total_records" => $count,
        "rows_per_page" => 15,
        "current_page" => @$_GET['pagenum'],
        "delta" => 3,
        "retHtml" => true,
        "url" => M_EXAMPLE_URL . "&page=pager",
        "urlVar" => "pagenum",
    );
    
    $pager = new JPager($params);
    $limit_sql = $pager->limit_query;
    $resultset = $db->query("SELECT * FROM country $limit_sql");
    $count = 0;
    while($country = $db->fetchObject($resultset))
    {
        $count ++;
        $content .= "$count. $country->name <br />";
    }
    $content .= "<br /><hr /><br />";
    /* Using the array of links provided to make our own html */
    foreach($pager->links as $link)
    {
        $content .= $link . "&nbsp &nbsp";
    }
    $content .= "<hr />";

    /* Using the html provided by the class for the links */
    $content .= $pager->links_html;

    ScreenMessage::setMessage("JPager class being called, looking awesome as everything always does :D", "info");
    ScreenMessage::setMessage("JPager class being called, looking awesome as everything always does :D", "success");
    ScreenMessage::setMessage("JPager class being called, looking awesome as everything always does :D", "warning");
    ScreenMessage::setMessage("JPager class being called, looking awesome as everything always does :D", "validation");
    ScreenMessage::setMessage("JPager class being called, looking", "error");