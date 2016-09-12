# Chapter 19: The Page Manager Part 2

In this chapter, we are going to integrate NestablePageBundle with EasyAdminBundle. We are also going to improve the cms by integrating a wysiwyg editor (ckeditor) and create a custom locale dropdown.


## Objectives

> * Define User Stories
> * Integration with EasyAdminBundle
> * Install CKEditor
> * Create Custom Locale Selector Form Type
> * Update BDD Tests (Optional)

## Pre-setup

Make sure we are in the right branch. Let us branch off from the previous chapter.

```
# check your branch
-> git status
# start branching now
-> git checkout -b my_chapter18
```

## Pre-setup

Make sure we are in the right branch. Let us branch off from the previous chapter.

```
# check your branch
-> git status
# start branching now
-> git checkout -b my_chapter19
```

## Define User Stories

**19. Page Management**

<table>
<tr><td><strong>Story Id</strong></td><td><strong>As a</strong></td><td><strong>I</strong></td><td><strong>So that I</strong></td></tr>
<tr><td>19.1</td><td>an admin</td><td>want to manage pages</td><td>update them anytime.</td></tr>
<tr><td>19.2</td><td>test1 user</td><td>don't want to manage pages</td><td>don't breach security</td></tr>
</table>

<strong>Story ID 19.1: As an admin, I want to manage pages, so that I can update them anytime.</strong>

<table>
<tr><td><strong>Scenario Id</strong></td><td><strong>Given</strong></td><td><strong>When</strong></td><td><strong>Then</strong></td></tr>
<tr><td>19.11</td><td>List Pages</td><td>I go to page list url</td><td>I can see 2 elements under the about slug</td></tr>
<tr><td>19.12</td><td>Show Contact Us Page</td><td>I go to contact_us page</td><td>I should see the word "contact_us" and the word "Created"</td></tr>
<tr><td>19.13</td><td>Reorder home</td><td>I drag and drop the home menu to under the about menu</td><td>I should see "reordered successfully message" in the response and see 3 items unter the about menu</td></tr>
<tr><td>19.14</td><td>edit home page meta</td><td>I go to edit homepage url and update the menu title of "Home" to "Home1" and click update</td><td>I should see the ckeditor, locale dropdown with 2 entries only and the text "successfully updated" message upon clicking update</td></tr>
<tr><td>19.15</td><td>Create and delete test page</td><td>go to page list and click "Add new" and fill in details and click "Create" button, then go to newly created test page and create 2 new test meta. Delete one testmeta and then delete the whole test page</td><td>I should see the first pagemeta being created and deleted. Then see the second testmeta being deleted when the page is being deleted.</td></tr>
</table>

<strong>Story ID 19.2: As test1 user, I don't want to manage pages, so that I dont breach security.</strong>

<table>
<tr><td><strong>Scenario Id</strong></td><td><strong>Given</strong></td><td><strong>When</strong></td><td><strong>Then</strong></td></tr>
<tr><td>19.21</td><td>List pages</td><td>I go to the page management url</td><td>I should get a access denied message</td></tr>
<tr><td>19.22</td><td>show about us page</td><td>I go to show about us url</td><td>I should get a access denied message</td></tr>
<tr><td>19.23</td><td>edit about us page</td><td>I go to edit about us url</td><td>I should get a access denied message</td></tr>
</table>

## Integration with EasyAdminBundle

## Install CKEditor

## Create Custom Locale Selector Form Type

If you are looking at the pagemeta page, say http://songbird.app/app_dev.php/admin/?entity=PageMeta&action=new for example, you should have noticed by now that user can enter anything under the locale textbox. What if we want to load only the languages that we defined in the config file (ie, english and french)? It is a good idea to create our own select form type.

```
# src/AppBundle/Form/LocaleType.php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocaleType extends AbstractType
{
    private $localeChoices;

    public function __construct(array $localeChoices)
    {
        foreach ($localeChoices as $v) {
            $this->localeChoices[$v] = $v;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->localeChoices,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'locale';
    }
}
```

We then need to define the class in the service.xml

```
# src/AppBundle/Resources/config/services.xml
...
<service id="app.form.type.locale" class="AppBundle\Form\LocaleType">
    <argument>%supported_lang%</argument>
    <tag name="form.type" alias="locale" />
</service>
...
```

Go to any pagemeta edit page (ie http://songbird.dev/app_dev.php/admin/app/page/1/edit for example) and you should see the locale dropdown updated to only 2 enties.

## Update BDD Tests (Optional)

Let us create the cest files,

```
-> bin/codecept generate:cest -c src/AppBundle acceptance As_An_Admin/IWantToManagePages
-> bin/codecept generate:cest -c src/AppBundle acceptance As_Test1_User/IDontWantToManagePages
```

Create the test cases from the scenarios above and make sure all your tests passes before moving on.

Remember to commit all your code before moving on to the next chapter.

## Summary

In this chapter, we have extended our NestablePageBundle in EasyAdmin. We have installed CKEditor in our textarea and created a customised locale dropdown based on values from our config.yml file. Our CMS is looking more complete now.

Next Chapter: [Chapter 20: The Front View](https://github.com/bernardpeh/songbird/tree/chapter_20)

Previous Chapter: [Chapter 18: Making Your Bundle Reusable](https://github.com/bernardpeh/songbird/tree/chapter_18)


## Stuck? Checkout my code

```
-> git checkout -b chapter_19 origin/chapter_19
-> git clean -fd
```

## Exercises

* TinyMCE is also a widely used WYSIWYG editor. How do you integrate it in Sonata Media?
* What if you want to add a new user field to the Page Management System? What is going to happen to the page if the user is deleted? 

## References

* [Create custom form type](http://symfony.com/doc/current/cookbook/form/create_custom_field_type.html)
* [EasyAdmin Templating](https://github.com/javiereguiluz/EasyAdminBundle/blob/master/Resources/doc/book/3-list-search-show-configuration.md)
* [Adding Wysiwyg Editor](https://github.com/javiereguiluz/EasyAdminBundle/blob/master/Resources/doc/tutorials/wysiwyg-editor.md)