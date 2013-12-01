<?php

    /* Add the theme's libraries and scripts */

    $THEME->addCss(THEME_LIBRARIES_URL . "foundation/css/foundation-grid.min.css");
    $THEME->addCss(CSS_URL . "style.css");
    $THEME->addCss(CSS_URL . "jsmart.css");
    $THEME->addCss(array('file' => CSS_URL . 'mobile.css', 'media' => 'all and (min-width: 0px) and (max-width: 700px)'));
    
    
    $THEME->addScript(THEME_LIBRARIES_URL . "foundation/js/modernizr.js");
    $THEME->addScript(THEME_LIBRARIES_URL . "foundation/js/foundation-grid.min.js");