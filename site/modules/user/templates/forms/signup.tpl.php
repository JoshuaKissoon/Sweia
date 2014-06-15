<?php
    /**
     * @file Contain the signup form
     */
?>
<div class="form-wrapper">
    <script type="text/javascript">
        /* Changing the re-captcha theme */
        var RecaptchaOptions = {
            theme: 'clean'
        };
    </script>
    <form id="signup" method="post" action="<?= $form_action; ?>">
        <h4>Sign Up</h4>

        <hr />

        <div class="row">
            <div class="column large-3 medium-4">
                <label for="email" class="right inline">Email: </label>
            </div>
            <div class="column large-9 medium-8">
                <input type="email" name="email" />
            </div>
        </div>

        <div class="row">
            <div class="column large-3 medium-4">
                <label for="password" class="right inline">Password: </label>
            </div>
            <div class="column large-9 medium-8">
                <input type="password" name="password" />
            </div>
        </div>

        <div class="row">
            <div class="column large-3 medium-4">
                <label for="password-confirm" class="right inline">Password Confirm: </label>
            </div>
            <div class="column large-9 medium-8">
                <input type="password" name="password-confirm" placeholder="Confirm Password" />
            </div>
        </div>

        <div class="row">
            <div class="column large-3 medium-4">
                <label for="task" class="right inline">Verify You're Human: </label>
            </div>
            <div class="column large-9 medium-8">
                <?php print $recaptcha; ?>
            </div>
        </div>

        <hr />

        <div class="row form-actions">
            <div class="column small-12">
                <button type="submit" name="submit" class="small" value="signup">Sign Up</button>
            </div>
        </div>

    </form>
</div>