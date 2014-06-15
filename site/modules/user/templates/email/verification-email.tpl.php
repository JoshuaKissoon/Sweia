<div id="pms-verification-email">
    <div id="thank-you-msg">
        Thank you for signing up 
        <?php if (isset($sitename)): ?>
                for <?= $sitename; ?> 
            <?php endif; ?>
        <br />
        Please verify your email by clicking on the link below.
    </div>
    <div id="link">
        <a href="<?php print $link; ?>">Verification link</a>
    </div>
</div>