<?php
    /**
     * @desc The login page template
     */
?>

<section id="login-section" class="row">
    <section id="login-left" class="column large-6 small-12">
        <?php if (isset($section_left)): ?>
                <?php print $section_left; ?>
            <?php endif; ?>
    </section>
    <section id="login-right" class="column large-6 small-12">
        <?php if (isset($section_right)): ?>
                <?php print $section_right; ?>
            <?php endif; ?>
    </section>
</section>