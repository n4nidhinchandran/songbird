# Chapter 20: The Front View

Going to "http://songbird.app/" has nothing at the moment because we have so far been focusing on the the admin area and not touched the frontend. In this chapter, we will create an automatic route based on the slug and display the frontend view when the slug matches. Any route that matches "/" and "/home" will be using the index template while the rest of the pages will be using the view template.

We will create a simple home and subpages using bootstrap and use [smartmenus javascript library](http://www.smartmenus.org/) to create the top menu which will render the the submenus as well.

Lastly, we'll add a language toggle so that the page can render different languages easily. The menu and page content will be rendered based on the toggled language. To get the menu to display different languages, we will create a custom twig function (an extension called MenuLocaleTitle).

## Objectives

> * Define User Stories
> * Creating the Frontend
> * Update BDD

## Pre-setup

Make sure we are in the right branch. Let us branch off from the previous chapter.

```
# check your branch
-> git status
# start branching now
-> git checkout -b my_chapter20
```

## Define User Stories

**20. Frontend**

<table>
<tr><td><strong>Story Id</strong></td><td><strong>As a</strong></td><td><strong>I</strong></td><td><strong>So that I</strong></td></tr>
<tr><td>20.1</td><td>an test3 user</td><td>want to browse the frontend</td><td>I can get the information I want.</td></tr>
</table>

<strong>Story ID 20.1: As test3 user, I want to browse the frontend, so that I can get the information I want.</strong>

<table>
<tr><td><strong>Scenario Id</strong></td><td><strong>Given</strong></td><td><strong>When</strong></td><td><strong>Then</strong></td></tr>
<tr><td>20.11</td><td>Home page is working</td><td>I go to the / or /home</td><td>I can see the jumbotron class and the text "Welcome to SongBird CMS Demo"</td></tr>
<tr><td>20.12</td><td>Menus are working</td><td>I mouseover the about menu</td><td>I should see 2 menus under the about menu</td></tr>
<tr><td>20.13</td><td>Subpages are working</td><td>I click on contact memu</td><td>I should see the text "This project is hosted in"</td></tr>
<tr><td>20.14</td><td>Login menu is working</td><td>I click on login memu</td><td>I should see 2 menu items only</td></tr>
<tr><td>20.15</td><td>Internalisation is working on homepage</td><td>I change language to french</td><td>I should see all menu and homepage in french</td></tr>
</table>

## Creating the Frontend

Let create a new frontend controller

```
# src/AppBundle/Controller/FrontendController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class FrontendController extends Controller
{
	/**
	 * @Route("/{slug}", name="app_frontend_index", requirements = {"slug" = "^((|home)$)"})
	 * @Template()
	 * @Method("GET")
	 * @param Request $request
	 *
	 * @return array
	 */
	public function indexAction(Request $request)
	{
		$page = $this->getDoctrine()->getRepository('AppBundle:Page')->findOneBySlug($request->get('_route_params')['slug']);
		$pagemeta = $this->getDoctrine()->getRepository('AppBundle:PageMeta')->findPageMetaByLocale($page, $request->getLocale());
		$rootMenuItems = $this->getDoctrine()->getRepository('AppBundle:Page')->findParent();

		return array(
			'pagemeta' => $pagemeta,
			'tree' => $rootMenuItems,
		);
	}


	/**
	* @Route("/{slug}", name="app_frontend_view")
	* @Template()
	* @Method("GET")
	*/
	public function pageAction(Request $request)
	{

		$page = $this->getDoctrine()->getRepository('AppBundle:Page')->findOneBySlug($request->get('_route_params')['slug']);
		$pagemeta = $this->getDoctrine()->getRepository('AppBundle:PageMeta')->findPageMetaByLocale($page, $request->getLocale());
		$rootMenuItems = $this->getDoctrine()->getRepository('AppBundle:Page')->findParent();

		return array(
			'pagemeta' => $pagemeta,
			'tree' => $rootMenuItems,
			);
	}
}
```

With the new routes added, we will move the frontend routes to the last priority, so routes like /login will be executed first.

```
# app/config/routing.yml
...
fos_user_security:
  resource: "@FOSUserBundle/Resources/config/routing/security.xml"

fos_user_resetting:
  resource: "@FOSUserBundle/Resources/config/routing/resetting.xml"
  prefix: /resetting

frontend:
  resource: "@AppBundle/Controller/FrontendController.php"
  type:     annotation
```

Let us update the frontend base view.

```
# src/AppBundle/Resources/Views/frontend.html.twig

<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{% block title %}{% endblock %}</title>
        {% block stylesheets %}
           <link href="{{ asset('minified/css/styles.css') }}" rel="stylesheet" />
        {% endblock %}
</head>
<body>

{% set urlPrefix = (app.environment == 'dev') ? '/app_dev.php/' : '/' %}

{% block body %}

	<div class="container">
        {% block topnav %}
        <ul id="top_menu" class="sm sm-clean">
            <li id="page_logo">
                <a href="{{ urlPrefix }}" alt="songbird">
                    <img src="{{ asset('bundles/app/images/logo_small.png') }}" alt="Songbird">
                </a>
            </li>
            {% if tree is defined %}
                {% include "AppBundle:Page:tree.html.twig" with { 'tree':tree } %}
            {% endif %}
            <li>
            {% if is_granted("IS_AUTHENTICATED_REMEMBERED") %}
                <a href="{{ path('fos_user_security_logout') }}">
                    {{ 'layout.logout'|trans({}, 'FOSUserBundle') }}
                </a>
            {% else %}
                <a href="{{ path('fos_user_security_login') }}">
                    {{ 'layout.login'|trans({}, 'FOSUserBundle') }}
                </a>
            {% endif %}
            </li>
            <li id="frontend_lang_toggle">
                <select id="lang" name="lang">
                    {% for lang in supported_lang %}
                        <option value="{{ lang }}">{{ lang }}</option>
                    {% endfor %}
                </select>
            </li>
        </ul>

        {% endblock %}

        <div class="clearfix vspace"></div>

        {% block content %}{% endblock %}

        {% block footer %}
        <hr />
        <footer>
                <p class="text-center">© Songbird {{ "now" | date("Y")}}</p>
        </footer>
        {% endblock %}
    </div>
{% endblock %}

{% block script %}
    <script src="{{ asset('minified/js/javascript.js') }}"></script>
    <script>
    $(function() {
        $('#top_menu').smartmenus();
        // select the box based on locale
        $('#lang').val('{{ app.request.getLocale() }}');
        // redirect user if user change locale
        $('#lang').change(function() {
            window.location='{{ urlPrefix }}'+$(this).val()+'/locale';
        });
    });
    </script>
{% endblock %}

</body>
</html>
```

We will now create a homepage view.

```
# src/AppBundle/Resources/views/Frontend/index.html.twig

{% extends "AppBundle::base.html.twig" %}

{% block title %}
	{{ page.getPageMetas()[0].getPageTitle() }}
{% endblock %}

{% block content %}
{% if page is not null %}
<div class="jumbotron">
	<h1>{{ page.getPageMetas()[0].getShortDescription() | raw }}</h1>
</div>

{{ page.getPageMetas()[0].getContent() | raw }}

{% endif %}
{% endblock %}

```

and pages view

```
# src/AppBundle/Resources/views/Frontend/page.html.twig

{% extends "AppBundle::base.html.twig" %}

{% block title %}
	{{ page.getPageMetas()[0].getPageTitle() }}
{% endblock %}

{% block content %}

{% if page is not null %}
<h1>{{ page.getPageMetas()[0].getShortDescription() | raw }}</h1>

{{ page.getPageMetas()[0].getContent() | raw }}

{% endif %}

{% endblock %}
```

and lastly, recursive view for the menu

```
# src/AppBundle/Resources/views/Frontend/tree.html.twig

{% for v in tree %}
    <li>
        <a href="{{ v.getSlug() }}">{{ getMenuLocaleTitle(v.getSlug()) }}</a>      
        {% set children = v.getChildren()|length %}
        {% if children > 0 %}
            <ul>
                {% include "AppBundle:Page:tree.html.twig" with { 'tree':v.getChildren() } %}
            </ul>
        {% endif %}
    </li>
{% endfor %}
```
Note the new getMenuLocaleTitle function in the twig. We will create a custom function usable by twig - Twig Extension.

```
# src/AppBundle/Twig/Extension/MenuLocaleTitle.php

namespace AppBundle\Twig\Extension;

/**
 * Twig Extension to get Menu title based on locale
 */
class MenuLocaleTitle extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

	private $container;

    public function __construct($em, $request)
    {
        $this->em = $em;
        $this->request = $request->getCurrentRequest();
    }

    public function getName()
    {
        return 'menu_locale_title_extension';
    }

    public function getFunctions()
    {
        return array(
            'getMenuLocaleTitle' => new \Twig_Function_Method($this, 'getMenuLocaleTitle')
        );
    }

    public function getMenuLocaleTitle($slug)
    {
    	
    	$page = $this->em->getRepository('AppBundle:Page')->findOneBySlug($slug);
	    $pagemeta = $this->em->getRepository('AppBundle:PageMeta')->findPageMetaByLocale($page, $this->request->getLocale());

    	return $pagemeta->getMenuTitle();
    }
}
```

we now need to make this class available as a service.

```
# src/AppBundle/Resources/config/services.yml
...
  menu_locale_title.twig_extension:
    class: AppBundle\Twig\Extension\MenuLocaleTitle
    arguments:
      - "@doctrine.orm.entity_manager"
      - "@service_container"
    tags:
      - { name: twig.extension }
...
```

Since we have added a new top navbar, we need to remove the SongBird logo from the login and password reset pages. Update the following pages as you see fit:

```
src/AppBundle/Resources/views/Resetting/checkEmail.html.twig
src/AppBundle/Resources/views/Resetting/passwordAlreadyRequested.html.twig
src/AppBundle/Resources/views/Resetting/request.html.twig
src/AppBundle/Resources/views/Resetting/reset.html.twig
src/AppBundle/Resources/views/Security/login.html.twig
```

Let us update bower.json to pull in smartmenus js.

```
-> bower install smartmenus --S
```

then make gulp to pull the libraries in

```
# gulpfile.js
...
// Minify JS
gulp.task('js', function () {
    return gulp.src(['bower_components/jquery/dist/jquery.js',
        'bower_components/bootstrap/dist/js/bootstrap.js',
        'bower_components/smartmenus/dist/jquery.smartmenus.js'])
        .pipe(concat('javascript.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/minified/js'));
});

// Minify CSS
gulp.task('css', function () {
    return gulp.src([
        'bower_components/bootstrap/dist/css/bootstrap.css',
        'bower_components/smartmenus/dist/css/sm-core-css.css',
        'bower_components/smartmenus/dist/css/sm-clean/sm-clean.css',
        'src/AppBundle/Resources/public/less/*.less',
        'src/AppBundle/Resources/public/sass/*.scss',
        'src/AppBundle/Resources/public/css/*.css'])
        .pipe(gulpif(/[.]less/, less()))
        .pipe(gulpif(/[.]scss/, sass()))
        .pipe(concat('styles.css'))
        .pipe(uglifycss())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('web/minified/css'));
});
...
```

Lastly, let us update the stylesheets. We might as well update them in scss

```
# src/AppBundle/Resources/public/sass/styles.scss

// variables
$black: #000;
$white: #fff;
$radius: 6px;
$spacing: 20px;
$font_big: 16px;

body {
    color: $black;
    background: $white;
    padding-top: $spacing;
}
.vspace {
    height: $spacing;
}
.skin-black {
    .logo {
        background-color: $black;
    }
    .left-side {
        background-color: $black;
    }
}
.sidebar {
    ul {
        padding-top: $spacing;
    }
    li {
        padding-top: $spacing;
    }
}
.admin_top_left {
    padding-top: $spacing 0 0 $spacing;
}
#top_menu {
    padding: $spacing 0 $spacing;

    #page_logo {
        padding: 0 $spacing;
        margin: -$spacing/2 0;
    }

    #login_link {
        float:right;
    }

    #frontend_lang_toggle {
        float: right;
        padding: $spacing/2 $spacing;
    }
}
.sm-clean {
    border-radius: $radius;

}
// admin area
#page_logo img {
    display: inline;
    max-height: 100%;
    max-width: 50%;
}
#page_menu li {
    line-height: $spacing;
    padding-top: $spacing;
}
.navbar-brand img {
    margin-top: -8px;
}

.form-signin {
    max-width: 330px;
    padding: 15px;
    margin: 0 auto;
    .form-signin-heading {
        margin-bottom: $spacing;
    }
    .checkbox {
        margin-bottom: $spacing;
        font-weight: normal;
    }
    .form-control {
        position: relative;
        height: auto;
        box-sizing: border-box;
        padding: $spacing;
        font-size: $font_big;
        &:focus {
            z-index: 2;
        }
    }
    input[type="email"] {
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    input[type="password"] {
        margin-bottom: $spacing;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
}
.form-control {
    margin-top: $spacing;
    margin-bottom: $spacing;
}

```

Then empty our .css files

```
-> echo "" > src/AppBundle/Resources/public/css/signin.css
-> echo "" > src/AppBundle/Resources/public/css/style.css
```

Now run gulp and refresh the homepage and everything should renders.

```
-> gulp
```

Go to homepage and this should be the end result.



## Update BDD

Let us create the cest file:

```
-> bin/codecept generate:cest -c src/AppBundle acceptance As_Test3_User/IWantToViewTheFrontend
```

Write your test and make sure everything passes.

If you see all the tests passes, you should be happy

```
-> ./scripts/runtest
Dropped database for connection named `songbird`
Created database `songbird` for connection named default
ATTENTION: This operation should not be executed in a production environment.

Creating database schema...
Database schema created successfully!
  > purging database
  > loading AppBundle\DataFixtures\ORM\LoadPageData
  > loading [1] AppBundle\DataFixtures\ORM\LoadUserData
  > loading [2] AppBundle\DataFixtures\ORM\LoadMediaData
Codeception PHP Testing Framework v2.1.1
Powered by PHPUnit 4.7.7 by Sebastian Bergmann and contributors.

Acceptance Tests (56) ----------------------------------------------------------
Wrong login credentials (As_An_Admin\IWantToLoginCest::wrongLoginCredentials)                                               Ok
See my dashboard content (As_An_Admin\IWantToLoginCest::seeMyDashboardContent)                                               Ok
Logout successfully (As_An_Admin\IWantToLoginCest::logoutSuccessfully)                                                  Ok
Access admin without logging in (As_An_Admin\IWantToLoginCest::AccessAdminWithoutLoggingIn)                                         Ok
View gallery list (As_An_Admin\IWantToManageAllGalleriesCest::viewGalleryList)                                        Ok
Show gallery1 (As_An_Admin\IWantToManageAllGalleriesCest::showGallery1)                                           Ok
Edit gallery3 (As_An_Admin\IWantToManageAllGalleriesCest::editGallery3)                                           Ok
Add and delete media under gallery3 (As_An_Admin\IWantToManageAllGalleriesCest::AddAndDeleteMediaUnderGallery3)                         Ok
Add and delete new gallery (As_An_Admin\IWantToManageAllGalleriesCest::AddAndDeleteNewGallery)                                 Ok
View media list (As_An_Admin\IWantToManageAllMediaCest::viewMediaList)                                              Ok
Show file1 (As_An_Admin\IWantToManageAllMediaCest::showFile1)                                                  Ok
Edit file3 (As_An_Admin\IWantToManageAllMediaCest::editFile3)                                                  Ok
Upload and delete media (As_An_Admin\IWantToManageAllMediaCest::uploadAndDeleteMedia)                                       Ok
List all profiles (As_An_Admin\IWantToManageAllUsersCest::listAllProfiles)                                            Ok
Show test3 user (As_An_Admin\IWantToManageAllUsersCest::showTest3User)                                              Ok
Edit test3 user (As_An_Admin\IWantToManageAllUsersCest::editTest3User)                                              Ok
Create and delete new user (As_An_Admin\IWantToManageAllUsersCest::createAndDeleteNewUser)                                     Ok
List pages (As_An_Admin\IWantToManagePagesCest::listPages)                                                     Ok
Show contact us page (As_An_Admin\IWantToManagePagesCest::showContactUsPage)                                             Ok
Reorder home (As_An_Admin\IWantToManagePagesCest::reorderHome)                                                   Ok
Edit homepage meta (As_An_Admin\IWantToManagePagesCest::editHomepageMeta)                                              Ok
Create and delete test page (As_An_Admin\IWantToManagePagesCest::createAndDeleteTestPage)                                       Ok
List user log (As_An_Admin\IWantToAccessUserLogCest::listUserLog)                                                 Ok
Show user log1 (As_An_Admin\IWantToAccessUserLogCest::showUserLog1)                                                Ok
Create user log (As_An_Admin\IWantToAccessUserLogCest::createUserLog)                                               Ok
View gallery list (As_Test1_User\IDontWantToManageAllGalleriesCest::viewGalleryList)                                  Ok
Show gallery1 (As_Test1_User\IDontWantToManageAllGalleriesCest::showGallery1)                                     Ok
Edit gallery3 (As_Test1_User\IDontWantToManageAllGalleriesCest::editGallery3)                                     Ok
View media list (As_Test1_User\IDontWantToManageAllMediaCest::viewMediaList)                                        Ok
Show file1 (As_Test1_User\IDontWantToManageAllMediaCest::showFile1)                                            Ok
Edit file3 (As_Test1_User\IDontWantToManageAllMediaCest::EditFile3)                                            Ok
List all profiles (As_Test1_User\IShouldNotBeAbleToManageOtherProfilesCest::listAllProfiles)                          Ok
Show test2 profile (As_Test1_User\IShouldNotBeAbleToManageOtherProfilesCest::showTest2Profile)                         Ok
Edit test2 profile (As_Test1_User\IShouldNotBeAbleToManageOtherProfilesCest::editTest2Profile)                         Ok
See admin dashboard content (As_Test1_User\IShouldNotBeAbleToManageOtherProfilesCest::seeAdminDashboardContent)                 Ok
List pages (As_Test1_User\IDontWantToManagePagesCest::listPages)                                               Ok
Show about us page (As_Test1_User\IDontWantToManagePagesCest::showAboutUsPage)                                         Ok
Edit about us page (As_Test1_User\IDontWantToManagePagesCest::editAboutUsPage)                                         Ok
List user log (As_Test1_User\IDontWantToAccessUserLogCest::listUserLog)                                           Ok
Show log1 (As_Test1_User\IDontWantToAccessUserLogCest::showLog1)                                              Ok
Editlog1 (As_Test1_User\IDontWantToAccessUserLogCest::Editlog1)                                              Ok
Wrong login credentials (As_Test1_User\IWantToLoginCest::wrongLoginCredentials)                                             Ok
See my dashboard content (As_Test1_User\IWantToLoginCest::seeMyDashboardContent)                                             Ok
Logout successfully (As_Test1_User\IWantToLoginCest::logoutSuccessfully)                                                Ok
Access admin without logging in (As_Test1_User\IWantToLoginCest::AccessAdminWithoutLoggingIn)                                       Ok
Show my profile (As_Test1_User\IWantToManageMyOwnProfileCest::showMyProfile)                                        Ok
Hid uneditable fields (As_Test1_User\IWantToManageMyOwnProfileCest::hidUneditableFields)                                  Ok
Update firstname only (As_Test1_User\IWantToManageMyOwnProfileCest::updateFirstnameOnly)                                  Ok
Update password only (As_Test1_User\IWantToManageMyOwnProfileCest::updatePasswordOnly)                                   Ok
Reset password successfully (As_Test1_User\IWantToResetPasswordWithoutLoggingInCest::resetPasswordSuccessfully)                 Ok
Locale in french (As_Test1_User\IWantToSwitchLanguageCest::localeInFrench)                                           Ok
Account disabled (As_test3_user\IDontWantTologinCest::AccountDisabled)                                               Ok
Home page working (As_test3_user\IWantToViewTheFrontendCest::homePageWorking)                                         Ok
Menus are working (As_test3_user\IWantToViewTheFrontendCest::menusAreWorking)                                         Ok
Sub pages are working (As_test3_user\IWantToViewTheFrontendCest::subPagesAreWorking)                                      Ok
Login menu working (As_test3_user\IWantToViewTheFrontendCest::loginMenuWorking)                                        Ok
-------------------------------------------------------------------------------


Time: 3.7 minutes, Memory: 29.00Mb

OK (56 tests, 114 assertions)
```

## Summary

In this chapter, we have created the frontend controllers and views. We used smartmenus to render the menus and converted our css to sass. Finally, we wrote BDD tests to make sure our frontend renders correctly. The CMS is now complete.

Next Chapter: [Chapter 21: Conclusion](https://github.com/bernardpeh/songbird/tree/chapter_21)

Previous Chapter: [Chapter 19: The Page Manager Part 2](https://github.com/bernardpeh/songbird/tree/chapter_19)


## Stuck? Checkout my code

```
-> git checkout -b chapter_20 origin/chapter_20
-> git clean -fd
```

## Exercises

* Try extending the NestablePageBundle so that you can have multiple menus, say a top and bottom menu?
* One of the argument against using a language toggle is that it is bad for SEO. Language toggle can be good for usability. Can you think of a way to overcome the SEO issue?
 
## References

* [Controllers best practice](http://symfony.com/doc/current/best_practices/controllers.html)
* [Smart Menus](http://www.smartmenus.org/)
* [Twig Extension](http://symfony.com/doc/current/cookbook/templating/twig_extension.html)
