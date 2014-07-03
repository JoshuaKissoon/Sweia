<!-- Just a Blank template with place holders, this can be used on any page -->
<header id="top" class="row">
    <section id="header-left">
        <?php if (isset($header_left)): ?>
                <?php print $header_left; ?>
            <?php endif; ?>
    </section>
    <section id="header-right">
        <?php if (isset($header_right)): ?>
                <?= $header_right; ?>
            <?php endif; ?>
    </section>
</header>

<section id="main" class="row">
    <section id="first-section">
        <?php if (isset($main_left)): ?>
                <?= $main_left; ?>
            <?php endif; ?>
    </section>
    <section id="content">
        <?php if (isset($content)): ?>
                <?= $content; ?>
            <?php endif; ?>
    </section>
    <section id="last-section">
        <?php if (isset($main_right)): ?>
                <?= $main_right; ?>
            <?php endif; ?>
    </section>
</section>

<footer id="bottom" class="row">
    <?php if (isset($footer)): ?>
            <?= $footer; ?>
        <?php endif; ?>
</footer>