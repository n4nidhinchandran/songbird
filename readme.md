# Chapter 11: Customising the Login Process

In the previous chapters, we have created the admin area and wrote tests for managing users in the admin area. The login page and admin area is still looking plain at the moment. There are still lots to do but let us take a break from the backend logic and look at frontend templating. Symfony is using twig as the default templating engine. If you new to twig, have a look at [twig](http://http://twig.sensiolabs.org/doc/templates.html) website. In this chapter, we will touch up the login interface.

## Objectives

> * Define User Stories and Scenarios
> * Customise the Login Page
> * Customise the Request Password Page
> * Customise the Reset Password Process
> * Update BDD (Optional)

## Pre-setup

Make sure we are in the right branch. Let us branch off from the previous chapter.

```
# check your branch
-> git status
# start branching now
-> git checkout -b my_chapter11
```
## Defining User Stories and Scenarios

**11. Reset Password**

<table>
<tr><td><strong>Story Id</strong></td><td><strong>As a</strong></td><td><strong>I</strong></td><td><strong>So that I</strong></td></tr>
<tr><td>11.1</td><td>test1 user</td><td>want to reset my password without logging in</td><td>have a way to access my account in case I forget or loses my password.</td></tr>
</table>

<strong>Story ID 11.1: As a test1 user, I want to be able to reset my password without logging in, so that I have a way to access my account in case I forget or loses my password.</strong>

<table>
<tr><td><strong>Scenario Id</strong></td><td><strong>Given</strong></td><td><strong>When</strong></td><td><strong>Then</strong></td></tr>
<tr><td>11.1.1</td><td>Reset Password Successfully</td><td>I click on forget password in the login page and go through the whole resetting process</td><td>I should be redirected to the dashboard with a success message.</td></tr>
</table>

## Customise the Login Page

I have installed [twitter bootstrap](http://getbootstrap.com/) in the public dir and created a simple logo for Songbird. You can get all the files by checking out from chapter_11 repo. My Resources dir looks like this:

```
# src/AppBundle/Resource

public/
├── css
│   ├── bootstrap-theme.css
│   ├── bootstrap-theme.css.map
│   ├── bootstrap-theme.min.css
│   ├── bootstrap-theme.min.css.map
│   ├── bootstrap.css
│   ├── bootstrap.css.map
│   ├── bootstrap.min.css
│   ├── bootstrap.min.css.map
│   └── signin.css
├── fonts
│   ├── glyphicons-halflings-regular.eot
│   ├── glyphicons-halflings-regular.svg
│   ├── glyphicons-halflings-regular.ttf
│   ├── glyphicons-halflings-regular.woff
│   └── glyphicons-halflings-regular.woff2
├── images
│   └── logo.png
└── js
    ├── bootstrap.js
    ├── bootstrap.min.js
    ├── jquery.min.js
    └── npm.js

```

Let us create our own base layout. The idea is to extend this layout for all twig files that we create in the future.

```
# src/AppBundle/Resources/views/base.html.twig

<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{% block title %}{% endblock %}</title>
	{% block stylesheets %}
	   <link href="{{ asset('bundles/app/css/bootstrap.min.css') }}" rel="stylesheet" />
    {% endblock %}
</head>
<body>
{% block body %}

    {% block content %}{% endblock %}

{% endblock %}

{% block script %}
    <script src="{{ asset('bundles/app/js/jquery.min.js') }}"></script>
    <script src="{{ asset('bundles/app/js/bootstrap.min.js') }}"></script>
{% endblock %}

</body>
</html>
```

To override FOSUserBundle template, have a look at the vendors/friendsofsymfony/Resources/views. Thanks to inheritance, we can override login.html.twig based on the layout that we have created.

Let us create the login file
```
-> cd src/AppBundle/Resources/views
-> mkdir Security
-> touch Security/login.html.twig
```

Now the actual login file:

```
# src/AppBundle/Resources/views/Security/login.html.twig

{% extends "AppBundle::base.html.twig" %}

{% block title %}
    {{ 'layout.login'|trans({}, 'FOSUserBundle') }}
{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link href="{{ asset('bundles/app/css/signin.css') }}" rel="stylesheet" />
{% endblock %}

{% trans_default_domain 'FOSUserBundle' %}

{% block content %}

<img src="{{ asset('bundles/app/images/logo.png') }}" class="center-block img-responsive" alt="Songbird" />

<div class="container">
    <div class="text-center">
        {% if is_granted("IS_AUTHENTICATED_REMEMBERED") %}
            {{ 'layout.logged_in_as'|trans({'%username%': app.user.username}, 'FOSUserBundle') }} |
            <a href="{{ path('fos_user_security_logout') }}">
                {{ 'layout.logout'|trans({}, 'FOSUserBundle') }}
            </a>
        {% endif %}
        <form class="form-signin" action="{{ path("fos_user_security_check") }}" method="post">
            {% if error %}
                <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'security') }}</div>
            {% endif %}

            <input type="hidden" name="_csrf_token" value="{{ csrf_token }}" />

            <input type="text" id="username" name="_username" class="form-control" value="{{ last_username }}" required="required" placeholder="{{ 'security.login.username'|trans }}" />

            <input type="password" id="password" name="_password" class="form-control" required="required" placeholder="{{ 'security.login.password'|trans }}" />

            <div class="checkbox">
              <label>
                    <input type="checkbox" id="remember_me" name="_remember_me" value="on" />{{ 'security.login.remember_me'|trans }}
              </label>
            </div>

            <input class="btn btn-lg btn-primary btn-block" type="submit" id="_submit" name="_submit" value="{{ 'security.login.submit'|trans }}" />
        </form>
    </div>
</div>
{% endblock %}
```

Once we are done, we can get the assets over to the web dir.

```
-> app/console assets:install
```

This command basically copies everything under the public dir of all bundles over to the document root dir (/web).

What if we are constantly updating our css and want to see updates in the browser? We can use symlink like so.

```
-> app/console assets:install --symlink
```

unfortunately, running this command in the host will use the host dir structure which we don't want. We need the dir structure of the vm. Therefore, let us create an assetinstall script to shell into the vm and do the symlink.

```
# scripts/assetsinstall

#!/bin/bash
vagrant ssh -c "cd /home/vagrant/symfony;app/console assets:install --symlink"
```

make the script executable and lets run it.

```
-> chmod u+x scripts/assetsinstall
-> ./scripts/assetsinstall
 Trying to install assets as absolute symbolic links.

 --- ------------------- ------------------
      Bundle              Method / Error
 --- ------------------- ------------------
  ✔   FrameworkBundle     absolute symlink
  ✔   SonataCoreBundle    absolute symlink
  ✔   SonataAdminBundle   absolute symlink
  ✔   AppBundle           absolute symlink
  ✔   User                absolute symlink
 --- ------------------- ------------------


 [OK] All assets were successfully installed.

Connection to 127.0.0.1 closed.
```

Looking good, let us go to http://songbird.app/app_dev.php/login and look at our login page now

![login screen](images/login_screen.png)

## Customise the Request Password Page

A full login process should also include the password reset functionality in case the user forgets his password. Fortunately again, FOSUSerBundle has all these features built-in already. We just need to make minor tweaks to the process and customise the templates.

The password reset process is as follows:

1. User goes to the forget password page.
2. User enters the username or email.
3. User gets an email a reset link.
4. User clicks on the email and goes to a password reset page.
5. User enters the new password and click submit.
6. User automatically gets redirected to the dashboard.

We will put a link on the login page to the request password page. We can find all the links from the debug:router command (a command you should be familiar by now)

```
-> app/console debug:router | grep fos
 fos_user_security_login                  GET|POST ANY    ANY  /login
 fos_user_security_check                  POST     ANY    ANY  /login_check
 fos_user_security_logout                 GET      ANY    ANY  /logout
 fos_user_resetting_request               GET      ANY    ANY  /resetting/request
 fos_user_resetting_send_email            POST     ANY    ANY  /resetting/send-email
 fos_user_resetting_check_email           GET      ANY    ANY  /resetting/check-email
 fos_user_resetting_reset                 GET|POST ANY    ANY  /resetting/reset/{token}
```

Let us add the new request password link

```
# src/AppBundle/Resources/views/Security/login.html.twig
...
  <input class="btn btn-lg btn-primary btn-block" type="submit" id="_submit" name="_submit" value="{{ 'security.login.submit'|trans }}" />

  <div class="checkbox">
    <a href="{{ path('fos_user_resetting_request') }}">forget password</a>
  </div>
...
```

![login with forget passwd](images/login_with_forget_password.png)

By looking at vendors/friendsofsymfony/Resources/views, we can create all the required twig files to override.

```
-> cd src/AppBundle/Resources/views/
-> mkdir Resetting
-> touch Resetting/checkEmail.html.twig
-> touch Resetting/passwordAlreadyRequested.html.twig
-> touch Resetting/request.html.twig
-> touch Resetting/reset.html.twig
```

Let us create the request password page based on the base.twig.html that we have created earlier.

```
# src/AppBundle/Resources/views/Resetting/request.html.twig

{% extends "AppBundle::base.html.twig" %}

{% trans_default_domain 'FOSUserBundle' %}

{% block title %}
    {{ 'resetting.request.submit'|trans }}
{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link href="{{ asset('bundles/app/css/signin.css') }}" rel="stylesheet" />
{% endblock %}

{% block content %}

<img src="{{ asset('bundles/app/images/logo.png') }}" class="center-block img-responsive" alt="Songbird" />

<div class="container">
    <div class="text-center">
        <h3>Enter Your Username or Password</h3>
        <form class="form-signin" action="{{ path('fos_user_resetting_send_email') }}" method="post">
            {% if invalid_username is defined %}
                <div class="alert alert-danger">{{ 'resetting.request.invalid_username'|trans({'%username%': app.request.get('username')}) }}</div>
            {% endif %}
            <input type="text" id="username" name="username" class="form-control" required="required" value="{{ app.request.get('username') }}" placeholder="{{ 'resetting.request.username'|trans }}" />

            <input class="btn btn-lg btn-primary btn-block" type="submit" id="_submit" name="_submit" value="{{ 'resetting.request.submit'|trans }}" />
        </form>
    </div>
</div>
{% endblock %}
```

From the login page, click on the forget password link and you should go to the request password page

![request passwd](images/request_password.png)

Likewise we are going to customise the password request success message.

```
# src/AppBundle/Resources/views/Resetting/checkEmail.html.twig

{% extends "AppBundle::base.html.twig" %}

{% trans_default_domain 'FOSUserBundle' %}

{% block title %}
    {{ 'resetting.request.submit'|trans }}
{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link href="{{ asset('bundles/app/css/signin.css') }}" rel="stylesheet" />
{% endblock %}

{% block content %}

<img src="{{ asset('bundles/app/images/logo.png') }}" class="center-block img-responsive" alt="Songbird" />

<div class="container">
	<h3> </h3>
	<div class="text-center">
  	{{ 'resetting.check_email'|trans({'%email%': email}) }}
  </div>
</div>
{% endblock %}
```

A successful password request looks like this:

![passwd request success](images/password_request_success.png)

What if you request password reset more than once in a day? FOSUserBundle actually doesn't allow you to do that. Let us create a custom template for it.

```
# src/AppBundle/Resources/views/Resetting/passwordAlreadyRequested.html.twig

{% extends "AppBundle::base.html.twig" %}

{% trans_default_domain 'FOSUserBundle' %}

{% block title %}
    {{ 'resetting.request.submit'|trans }}
{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link href="{{ asset('bundles/app/css/signin.css') }}" rel="stylesheet" />
{% endblock %}

{% block content %}

<img src="{{ asset('bundles/app/images/logo.png') }}" class="center-block img-responsive" alt="Songbird" />

<div class="container">
        <h3>&nbsp;</h3>
        <div class="text-center">
                {{ 'resetting.password_already_requested'|trans }}
  </div>
</div>
{% endblock %}
```

A screenshot of the password already requested error:

![request already send](images/request_already_send.png)

When the password request email is send successfully, the user should request a link to reset the password. Our vm has a mailcatcher configured to check all emails fired.

Let us go to

```
http://songbird.app:1080
```

![request email](images/request_email.png)

If you click on the link, you will go to the actual reset page to enter the new password.

![reset passwd](images/reset_password.png)

Try entering a new password and see what happens. Nothing? Because we haven't add the reset.html.twig. Let us do it now.

```
# src/AppBundle/Resources/views/Resetting/reset.html.twig

{% extends "AppBundle::base.html.twig" %}

{% trans_default_domain 'FOSUserBundle' %}

{% block title %}
    {{ 'resetting.request.submit'|trans }}
{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link href="{{ asset('bundles/app/css/signin.css') }}" rel="stylesheet" />
{% endblock %}

{% block content %}

<img src="{{ asset('bundles/app/images/logo.png') }}" class="center-block img-responsive" alt="Songbird" />

<div class="container">
    <div class="text-center">
        <h3>Enter Your New Password</h3>
        <form class="form-signin" action="{{ path('fos_user_resetting_reset', {'token': token}) }}" method="post">

          {% if (app.request.get('fos_user_resetting_form')['plainPassword']['first']) is defined and (app.request.get('fos_user_resetting_form')['plainPassword']['first'] != app.request.get('fos_user_resetting_form')['plainPassword']['second']) %}
            <div class="alert alert-danger">
              {{ 'fos_user.password.mismatch' | trans({}, 'validators') }}
            </div>
          {% endif %}

          {{ form_widget(form._token) }}
                <input type="password" id="fos_user_resetting_form_plainPassword_first" name="fos_user_resetting_form[plainPassword][first]" class="form-control" required="required" placeholder="{{ 'form.new_password'|trans }}" />

                <input type="password" id="fos_user_resetting_form_plainPassword_second" name="fos_user_resetting_form[plainPassword][second]" class="form-control" required="required" placeholder="{{ 'form.new_password_confirmation'|trans }}" />

          <input class="btn btn-lg btn-primary btn-block" type="submit" id="_submit" name="_submit" value="{{ 'resetting.reset.submit'|trans }}" />
        </form>

    </div>

</div>
{% endblock %}
```

After you entering the new password and clicking submit, FOSUserBundle will try to redirect you to the fos_user_profile_show route (the profile page which we deleted earlier in route.yml). Since the route no longer exists, you will get an error saying the route no longer exists.

To see what is going on, have a look at  vendors/friendsofsymfony/user-bundle/Controller/ResettingController.php::resetAction function. The redirection magic happens after successful form submission.

```
# vendors/friendsofsymfony/user-bundle/Controller/ResettingController.php

namespace FOS\UserBundle\Controller;
...
if ($form->isValid()) {
    $event = new FormEvent($form, $request);
    $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

    $userManager->updateUser($user);

    if (null === $response = $event->getResponse()) {
        $url = $this->generateUrl('fos_user_profile_show');
        $response = new RedirectResponse($url);
    }

    $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

    return $response;
}
...
```

Let's say we want to change the redirection to user's dashboard after successful form submission. What can we do?

## Customise the Reset Password Process

We noted that the system dispatches a FOSUserEvents::RESETTING_RESET_SUCCESS event after the form submission is successful. This give us the opportunity to change the response so that the whole redirection logic could be skipped.

Let us add a new service to listen to the fos_user.resetting.reset.success event.

```
# src/Songbird/UserBundle/Resources/config/services.yml

...
    songbird.admin.user.show.filter:
        class: AppBundle\EventListener\UserCustomAction
        arguments: ['@service_container']
        tags:
            - { name: kernel.event_listener, event: sonata.admin.event.configure.show, method: sonataAdminCheckUserRights }

    songbird.admin.user.form.filter:
        class: AppBundle\EventListener\UserCustomAction
        arguments: ['@service_container']
        tags:
            - { name: kernel.event_listener, event: sonata.admin.event.configure.form, method: sonataAdminCheckUserRights }

    songbird.password.reset.success:
        class: AppBundle\EventListener\UserCustomAction
        arguments: ['@service_container']
        tags:
            - { name: kernel.event_listener, event: fos_user.resetting.reset.success, method: redirectUserAfterPasswordReset }
...
```

Noticed that we have made a few changes in addition to adding a new songbird.password.reset.success container. We have passed the container to songbird.admin.user.form.filter and songbird.admin.user.show.filter as well. We will explain this later.

The UserCustomAction class code is now updated as follows:

```
# src/AppBundle/EventListener/UserCustomAction.php

namespace AppBundle\EventListener;

use Sonata\AdminBundle\Event\ConfigureEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\Container;

class UserCustomAction
{

    /**
     * @var Container
     */
    private $container;

    /**
     * Constructor
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * show an error if user is not superadmin and tries to manage other user details
     *
     * @param  ConfigureEvent $event sonata admin event
     * @return null
     */
    public function sonataAdminCheckUserRights(ConfigureEvent $event)
    {
        // ignore this if user is admin
        if ($event->getAdmin()->isGranted('ROLE_SUPER_ADMIN')) {
            return;
        }

        $user_id = $this->container->get('request_stack')->getCurrentRequest()->get('id');

        // we can get container from ->getAdmin()->getConfigurationPool()->getContainer()
        $session_id = $this->container->get('security.token_storage')->getToken()->getUser()->getId();
        if ($user_id != $session_id) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Redirect user to another page after password reset is success
     *
     * @param  Configure $event GetResponseUserEvent
     * @return null
     */
    public function redirectUserAfterPasswordReset(FormEvent $event)
    {
        $url = $this->container->get('router')->generate('sonata_admin_dashboard');
        $event->setResponse(new RedirectResponse($url));
    }
}
```

In UserCustomAction class, we have added a constructor to have access to the container. Once we have access to the container, we have access to everything. The sonataAdminCheckUserRights function has also been refactored to use the $this->container to access other services instead.

Now try to go through the full password reset functionality and see if it works for you. If everything goes well, you should see a "The password has been reset successfully" after changing the password.

## Update BDD (Optional)

Based on the user story, let us create a new cest file

```
-> bin/codecept generate:cest acceptance As_Test1_User/IWantToResetPasswordWithoutLoggingIn -c src/AppBundle/
```

To automate the checking of emails, we need the mailcatcher module for codeception. Let us update composer

```
# composer.json
...
"require-dev": {
    "sensio/generator-bundle": "2.5.3",
    "codeception/codeception": "2.1.1",
    "doctrine/doctrine-fixtures-bundle": "2.2.0"
    "captbaritone/mailcatcher-codeception-module": "1.*"
},
...
```

update composer
```
-> composer update
```

Let us now update the acceptance.suite.yml to use the new mailcatcher library

```
# src/AppBundle/Tests/acceptance.suite.yml
class_name: AcceptanceTester
modules:
    enabled:
        - WebDriver:
            url: 'http://songbird.app'
            browser: chrome
            window_size: 1024x768
            capabilities:
                unexpectedAlertBehaviour: 'accept'
                webStorageEnabled: true
        - MailCatcher:
            url: 'http://songbird.app'
            port: '1080'
        - \Helper\Acceptance
```

we can now rebuild the libraries
```
# this command will create the mail functions for us to use
-> bin/codecept build -c src/AppBundle
```

Do a git diff to see all the mail functions added:

```
-> git diff src/AppBundle/Tests/_support/_generated/AcceptanceTesterActions.php
```

See the [mailcatcher module](https://github.com/captbaritone/codeception-mailcatcher-module) for more information.

Let us write the Cest file:

```
#src/AppBundle/Tests/acceptance/As_Test1_User/IWantToResetPasswordWithoutLoggingInCest.php

namespace As_Test1_User;
use \AcceptanceTester;
use \Common;

class IWantToResetPasswordWithoutLoggingInCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    protected function login(AcceptanceTester $I, $username=TEST1_USERNAME, $password=TEST1_PASSWORD)
    {
        Common::login($I, $username, $password);
    }

    /**
     * Scenario 11.1.1
     */
    public function resetPasswordSuccessfully(AcceptanceTester $I)
    {
        // reset emails
        $I->resetEmails();
        $I->amOnPage('/login');
        $I->click('forget password');
        $I->fillField('//input[@id="username"]', 'test1');
        $I->click('_submit');
        $I->canSee('It contains a link');

       // Clear old emails from MailCatcher
        $I->seeInLastEmail("Hello test1");
        $link = $I->grabFromLastEmail('@http://(.*)@');
        $I->amOnUrl($link);

        // The password has been reset successfully
        $I->fillField('//input[@id="fos_user_resetting_form_plainPassword_first"]', '1111');
        $I->fillField('//input[@id="fos_user_resetting_form_plainPassword_second"]', '1111');
        $I->click('_submit');
        $I->canSee('The password has been reset successfully');
        $I->canSeeInCurrentUrl('/admin/dashboard');

        // now login with the new password
        $this->login($I, TEST1_USERNAME, '1111');

        // db has been changed. update it back
        $I->amOnPage('/admin/app/user/2/edit');
        $I->fillField('//input[contains(@id, "_plainPassword_first")]', TEST1_USERNAME);
        $I->fillField('//input[contains(@id, "_plainPassword_second")]', TEST1_PASSWORD);
        $I->click('btn_update_and_edit');
        $I->canSee('has been successfully updated');

        // As a test, i should be able to login with the old password
        $this->login($I);
        $I->canSeeInCurrentUrl('/admin/dashboard');
    }
}
```

ready for testing?

```
./scripts/runtest As_Test1_User/IWantToResetPasswordWithoutLoggingInCest.php
```

## Summary

We have updated the aesthetics of the Login and request password change page. By listening to the reset password event, we redirect user to the dashboard rather than the show profile page. Finally, we wrote BDD tests to make sure this functionality is repeatable in the future.

Next Chapter: [Chapter 12: The Admin Panel Part 2](https://github.com/bernardpeh/songbird/tree/chapter_12)

Previous Chapter: [Chapter 10: BDD With Codeception (Optional)](https://github.com/bernardpeh/songbird/tree/chapter_10)

## Stuck? Checkout my code##

```
-> git checkout -b chapter_11 origin/chapter_11
-> git clean -fd
```

## Exercises

* (Optional) Try to be fancy with the login layout and css.

* (Optional) Write BDD test for this chapter.

## References

* [Sonata Admin Templating](https://sonata-project.org/bundles/admin/master/doc/reference/templates.html)

* [Twig Templating](http://twig.sensiolabs.org/doc/templates.html)

* [Codeception Mailcatcher Module](https://github.com/captbaritone/codeception-mailcatcher-module)
