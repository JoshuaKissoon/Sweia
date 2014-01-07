<?php
    /**
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */
?>

<section id="signup-section" class="row">
    <section id="signup-left" class="column large-6 small-12">
        <?php if (isset($signup_left)): ?>
                <?php print $signup_left; ?>
            <?php endif; ?>
    </section>
    <section id="signup-right" class="column large-6 small-12">
        <?php if (isset($signup_right)): ?>
                <?php print $signup_right; ?>
            <?php endif; ?>
    </section>
</section>