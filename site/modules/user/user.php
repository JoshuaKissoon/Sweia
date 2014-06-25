<?php

    error_reporting(E_ALL | E_WARNING | E_NOTICE);
    ini_set('display_errors', TRUE);

    include 'user.settings.php';
    include SiteConfig::librariesPath() . 'recaptcha/recaptchalib.php';

    if (isset($_POST['submit']))
    {
        switch ($_POST['submit'])
        {
            case "signup":
                user_signup_form_submit($_POST);
                break;
            case "login":
                user_login_form_submit($_POST);
                break;
        }
    }
    
    $url = Sweia::getInstance()->getURL();
    $themeRegistry = Sweia::getInstance()->getThemeRegistry();

    if (isset($url[1]))
    {
        switch ($url[1])
        {
            case "signup":
                $themeRegistry->addContent("content", user_signup_page());
                break;
            case "email-verification":
                user_email_verify($_GET);
                break;
            case "login":
                $themeRegistry->addContent("content", user_login_page());
                break;
            case "logout":
                if (isset($_GET['redirect_to']))
                {
                    user_logout($_GET['redirect_to']);
                }
                else
                {
                    user_logout();
                }
                break;
        }
    }

    function user_signup_page()
    {
        $tpl = new Template(USER_MODULE_PATH . "templates/inner/signup-page");

        /* Add the signup form to the left */
        $tpl->signup_left = user_signup_form();

        $signup_right = new Template(USER_MODULE_PATH . "templates/inner/signup-information");
        $tpl->signup_right = $signup_right->parse();

        return $tpl->parse();
    }

    function user_signup_form()
    {
        $form = new Template(USER_MODULE_PATH . "templates/forms/signup");

        $form->form_action = JPath::absoluteUrl("user/signup");
        $form->recaptcha = recaptcha_get_html(RECAPTCHA_PUBLIC_KEY);

        return $form->parse();
    }

    function user_signup_form_submit($values)
    {
        /* Validate the signup form */
        $errors = user_signup_form_validate($values);
        if ($errors)
        {
            ScreenMessage::setMessages($errors, ScreenMessage::MESSAGE_TYPE_ERROR);
            return;
        }

        /* If we got till here mean's everything's good */
        $user = new JSmartUser();
        $user->email = $values['email'];
        $user->setPassword($values['password']);
        if ($user->save())
        {
            /* Send email verification email */
            $sweia = Sweia::getInstance();
            $db = $sweia->getDB();
            $url_code = random_alphanumeric_string(45);
            $args = array("::uid" => $user->uid, "::url_code" => $url_code);
            $db->query("INSERT INTO user_email_verification (uid, url_code) VALUES ('::uid', '::url_code')", $args);

            $emailtpl = new Template(USER_MODULE_PATH . "templates/email/verification-email");
            $emailtpl->link = JPath::absoluteUrl("user/email-verification/&code=$url_code&email=$user->email&uid=$user->uid");

            $mail = new EMail();
            $mail->setSubject("Email Verification")->setMessage($emailtpl->parse())->addRecipient($user->email)->setSender(Utility::variableGet("site_sender_email"));
            $mail->sendMail();

            /* redirect this user to the main landing page showing a message to verify email */
            $message = "Congratulations!! You have successfully signed up for " . Utility::getSiteName() . ". Please verify your email address and enjoy using the site :).";
            ScreenMessage::setMessage($message, ScreenMessage::MESSAGE_TYPE_SUCCESS);
            System::redirect(SystemConfig::baseUrl());
        }
        else
        {
            ScreenMessage::setMessages("Sorry, we were unable to create your account.", ScreenMessage::MESSAGE_TYPE_SUCCESS);
        }
    }

    /**
     * @desc Function that validates the signup form
     * @return Array of errors or false if there are no errors
     */
    function user_signup_form_validate($values)
    {
        $errors = array();
        if (!isset($values['password']) || !valid($values['password']) || !isset($values['password-confirm']) || !valid($values['password-confirm']) || !isset($values['email']) || !valid($values['email']))
        {
            $errors[] = "All fields are required";
        }
        else
        {
            if ($_POST['password'] != $_POST['password-confirm'])
            {
                $errors[] = "Your passwords don't match";
            }
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            {
                $errors[] = "Invalid Email address";
            }
            if (JSmartUser::isEmailInUse($_POST['email']))
            {
                $errors[] = "Email address is already used.";
            }
            $captcha_resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
            if (!$captcha_resp->is_valid)
            {
                $errors[] = "Invalid Captcha, please enter the correct code";
            }
        }

        return (count($errors) > 0) ? $errors : false;
    }

    /**
     * @desc Here we verify a user's email address after they have clicked on the verify link in the email sent to them
     */
    function user_email_verify($values)
    {
        /* Check whether any user is already logged into the site */
        if (Session::isLoggedIn())
        {
            ScreenMessage::setMessage("You are already Logged In, no verification possible", "warning");
            System::redirect(SystemConfig::baseUrl());
        }

        /* Check if the code in the URL is that of a valid user */
        $sweia = Sweia::getInstance();
        $db = $sweia->getDB();
        $sql = "SELECT uevid, uid, status FROM user_email_verification WHERE url_code = '::url_code' LIMIT 1";
        $args = array("::url_code" => $_GET['code']);
        $ver_data = $db->fetchObject($db->query($sql, $args));
        if (!isset($ver_data->uid) || !valid($ver_data->uid) || $ver_data->uid != intval($values['uid']))
        {
            /* Invalid verification code, show error message */
            ScreenMessage::setMessage("Invalid Verification Data", "error");
            System::redirect(SystemConfig::baseUrl());
        }

        /* Check that the user's status is awaiting email verification */
        $usrstatus = $db->fetchObject($db->query("SELECT status FROM user WHERE uid='$ver_data->uid'"));
        if ($usrstatus->status != 5 && $ver_data->status != 2)
        {
            ScreenMessage::setMessage("Email verification was completed earlier.", "warning");
            System::redirect(SystemConfig::baseUrl());
        }

        /* If all data was valid, now confirm this user's email address by setting the user to active and login the user */
        $user = new JSmartUser($ver_data->uid);
        $user->setStatus(1);

        $args2 = array("::uevid" => $ver_data->uevid, "::date_verified" => date("Y-m-d H:i:s"));
        $db->query("UPDATE user_email_verification SET status='1', date_verified = '::date_verified' WHERE uevid='::uevid'", $args2);

        /* Tell the user they have successfully verified their email address and redirect them to the home page to login */
        ScreenMessage::setMessage("Your email address was successfully verifed, please login to continue. ", "success");
        System::redirect(SystemConfig::baseUrl());
    }

    function user_login_page()
    {
        $tpl = new Template(USER_MODULE_PATH . "templates/inner/login-page");

        /* Add the signup form to the left */
        $tpl->section_left = user_login_form();

        /* Something will go to the right later */
        $tpl->section_right = "";

        return $tpl->parse();
    }

    function user_login_form()
    {
        $form = new Template(USER_MODULE_PATH . "templates/forms/login");
        $form->form_action = JPath::absoluteUrl("user/login");
        $form->signup_link = JPath::absoluteUrl("user/signup");
        return $form->parse();
    }

    /**
     * @desc Check the user's credentials and login the user.
     */
    function user_login_form_submit($values)
    {
        $user = new JSmartUser();
        $user->email = $values['email'];
        $user->setPassword($values['password']);
        if ($user->authenticate())
        {
            /* The user is authenticated, lets log them in */
            Session::loginUser($user);
            ScreenMessage::setMessage("Logged in Successfully.", ScreenMessage::MESSAGE_TYPE_SUCCESS);
            System::redirect(SystemConfig::baseUrl());
        }
        else
        {
            ScreenMessage::setMessages("Invalid email and/or password. Please try again.", ScreenMessage::MESSAGE_TYPE_WARNING);
        }
    }

    /**
     * @desc Logout the user
     */
    function user_logout($redirect_url = NULL)
    {
        if (!$redirect_url)
        {
            $redirect_url = SystemConfig::baseUrl();
        }

        if (Session::isLoggedIn())
        {
            Session::logoutUser();
        }
        ScreenMessage::setMessage("Logged out Successfully.", ScreenMessage::MESSAGE_TYPE_SUCCESS);
        System::redirect(JPath::absoluteUrl($redirect_url));
    }
    