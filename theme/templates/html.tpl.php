<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<!-- This is the main template file for the site This template contains the overall layout and includes the main site stylesheets and scripts -->
<html>
    <head>
        <title><?php print @$title; ?></title>
        <?= @$header_stylesheets; ?>
        <?= @$header_scripts; ?>
        <?php print @$head; ?>
        <meta name="HandheldFriendly" content="true" />
        <meta name="MobileOptimized" content="320" />
        <meta name="Viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
    <body class="<?php print implode(" ", JPath::urlArgs()); ?>">
        <div id="status-messages"><?php print ScreenMessage::getMessages(); ?></div>
        <?php print @$content; ?>
        <?= @$stylesheets; ?>
        <?= @$scripts; ?>
    </body>
</html>