<!-- Just a Blank template with place holders, this can be used on any page -->
<div id="body-wrapper" class="<?= isset($wrapper_class) ? $wrapper_class : ""; ?>">
    <div id="header-wrapper" class="row">
        <div id="header">
            <div id="header-content">
                <div id="header-left">

                    <?php if (isset($sitename)): ?>
                            <div id="site-name">
                                <a href="<?php print BASE_URL; ?>">
                                    <?php print $sitename; ?>
                                </a>
                            </div>
                        <?php endif; ?>

                    <?php if (isset($header_left)): ?>
                            <?php print $header_left; ?>
                        <?php endif; ?>
                </div>
                <div id="header-right">
                    <?php if (isset($header_right)): ?>
                            <?= $header_right; ?>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div id="main-wrapper" class="clearfix row">
        <div id="main" class="clearfix">
            <div id="main-left" class="section">
                <?php if (isset($main_left)): ?>
                        <?= $main_left; ?>
                    <?php endif; ?>

            </div>
            <div id="content" class="section">
                <?php if (isset($content)): ?>
                        <?= $content; ?>
                    <?php endif; ?>
            </div>
            <div id="main-right" class="section">
                <?php if (isset($main_right)): ?>
                        <?= $main_right; ?>
                    <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="footer-wrapper" class="row">
        <div id="footer">
            <?php if (isset($footer)): ?>
                    <?= $footer; ?>
                <?php endif; ?>
        </div>
    </div>
</div>