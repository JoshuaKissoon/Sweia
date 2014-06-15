<div id="login-form-wrapper" class="form-wrapper">
    <form id="login-form" method="post" action="<?= $form_action; ?>">
        <h4>Login</h4>        
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

        <hr />

        <div class="row">
            <div class="column small-12">
                <button type="submit" name="submit" class="small" value="login">Login</button>
            </div>
        </div>
        
        <div class="row">
            <div class="column small-12">
                Don't have an account? <a href="<?=$signup_link?>">Signup Here</a>
            </div>
        </div>
        
    </form>
</div>