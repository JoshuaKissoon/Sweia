<!DOCTYPE html>
<!-- This is the main template file for the site This template contains the overall layout and includes the main site stylesheets and scripts -->
<html>

    <head>
        <?php if (isset($title)): ?>
                <title>
                    <?= $title; ?>
                </title>
            <?php endif; ?>
        <meta charset="UTF-8">
        <meta name="HandheldFriendly" content="true" />
        <meta name="MobileOptimized" content="320" />
        <meta name="Viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!-- Adding Stylesheets -->
        <?php if (isset($stylesheets)): ?>
                <?= $stylesheets; ?>
            <?php endif; ?>

        <!--Adding Header Scripts-->
        <?php if (isset($header_scripts)): ?>
                <?= $header_scripts; ?>
            <?php endif; ?>

        <!--Other head data-->
        <?php if (isset($head)): ?>
                <?php print $head; ?>
            <?php endif; ?>
    </head>

    <body class="<?php print implode(" ", JPath::urlArgs()); ?>">
        <section id="status-messages"><?php print Theme::getFormattedScreenMessages(); ?></section>
            <?php if (isset($content)): ?>
                    <?php print $content; ?>
                <?php endif; ?>

        <!--Adding Footer Scripts-->
        <?php if (isset($footer_scripts)): ?>
                <?= $footer_scripts; ?>
            <?php endif; ?>
    </body>
</html>