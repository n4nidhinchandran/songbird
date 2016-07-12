# Chapter 7: The User Management System Part 2

We have installed the FOSUserBundle but it looks like there is still a big chunk of functionality missing. How do we (C)reate, (R)ead, (U)pdate and (D)elete a user or group for example?

You see the word "CRUD" appearing so many times because it is part of RAD. All frameworks today come with auto CRUD generation.

## Objectives

> * Creating User CRUD

> * Adding Fields to the User Form

> * What's Up with Editing the User

> * Updating Doctrine Fields Automatically

> * Making Fields Non Compulsory

> * Deleting Users

> * Cleaning Up


## Pre-setup

Make sure we are in the right branch. Let us branch off from the previous chapter.

```
# check your branch
-> git status
# start branching now
-> git checkout -b my_chapter7
```

## Automated User CRUD Generation

We will generate CRUD for the UserBundle.

```
-> app/console doctrine:generate:crud

The Entity shortcut name: AppBundle:User

By default, the generator creates two actions: list and show.
You can also ask it to generate "write" actions: new, update, and delete.

Do you want to generate the "write" actions [no]? yes

Determine the format to use for the generated CRUD.

Configuration format (yml, xml, php, or annotation) [annotation]: annotation

Determine the routes prefix (all the routes will be "mounted" under this
prefix: /prefix/, /prefix/new, ...).

Routes prefix [/user]:


  Summary before generation


You are going to generate a CRUD controller for "AppBundle:User"
using the "annotation" format.

Do you confirm generation [yes]?


  CRUD generation


Generating the CRUD code: OK
```

Now go to

```
http://songbird.app/app_dev.php/user/
```

![user list](images/chapter_7_user_list.png)

We haven't added any data yet. The database should be empty as per the previous chapter.

Let us add some data. Go to

```
http://songbird.dev/app_dev.php/user/new
```

and enter a dummy firstname and lastname, then click create.

You should see a "Integrity constraint violation: 1048 Column 'username' cannot be null" error. Why?

I am going to skip through all technicalities for now and tell you where the answer is. Look at

```
# vendor/friendsofsymfony/user-bundle/Resources/config/validation.xml
...
<property name="username">
            <constraint name="NotBlank">
                <option name="message">fos_user.username.blank</option>
                <option name="groups">
                    <value>Registration</value>
                    <value>Profile</value>
                </option>
                ...
```

It is possible to create a new user from command line, the code is at:

```
# vendor/friendsofsymfony/user-bundle/Command/CreateUserCommand.php
...
class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:user:create')
            ->setDescription('Create a user.')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'The username'),
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
```

You can infer from these lines that username, email and password are compulsory. How do we add these extra fields in the user form?

## Adding Fields to the User Form

The extra FOSUserBundle fields were not automatically added when we created the CRUD using the command line. The automated CRUD creation process cannot pick up inheritance yet (I hope one day it will), so we have to create the fields manually.

```
# src/AppBundle/Form/UserType.php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('firstname')
            ->add('lastname')
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
        ;
    }
    ...
```

Refresh the browser and if changes are not showing up, we need to delete the cache.

```
-> app/console cache:clear
```

This command is equivalent to "rm -rf app/cache/dev". It is a useful alternative to clear:cache. If no environment is set, the environment is set to develop. To delete prod cache,

```
-> app/console cache:clear -e prod
```

Let us create 2 test users, say "test" and "test1"

![create new user](images/chapter_7_create_new_user.png)

We can now list them by going to /user

![list users](images/chapter_7_list_users.png)

Now verify that the new data is inserted into the user table visually by looking at adminer.

```
http://adminer.app/
```

![user passwd exposed](images/chapter_7_user_passwd_exposed.png)

**Wow**, why was the password exposed? shouldn't the password be encrypted automatically?

No, because the CRUD that we have created automatically didn't know that the password was supposed to be encrypted before inserting into the db. Fortunately, FOSUserBundle has a service container that can help us with this. Don't worry about the word "services" for now as we will cover this in the following chapters.

For the sake of curiousity, let us see all the FOSUserBundle service containers.
```
-> app/console debug:container | grep fos

 fos_user.change_password.form.factory                              FOSUserBundleFormFactoryFormFactory
 fos_user.change_password.form.type                                 FOSUserBundleFormTypeChangePasswordFormType
 fos_user.group.form.factory                                        FOSUserBundleFormFactoryFormFactory
 fos_user.group.form.type                                           FOSUserBundleFormTypeGroupFormType
 fos_user.group_manager                                             FOSUserBundleDoctrineGroupManager
 fos_user.listener.authentication                                   FOSUserBundleEventListenerAuthenticationListener
 fos_user.listener.flash                                            FOSUserBundleEventListenerFlashListener
 fos_user.listener.resetting                                        FOSUserBundleEventListenerResettingListener
 fos_user.mailer                                                    FOSUserBundleMailerMailer
 fos_user.profile.form.factory                                      FOSUserBundleFormFactoryFormFactory
 fos_user.profile.form.type                                         FOSUserBundleFormTypeProfileFormType
 fos_user.registration.form.factory                                 FOSUserBundleFormFactoryFormFactory
 fos_user.registration.form.type                                    FOSUserBundleFormTypeRegistrationFormType
 fos_user.resetting.form.factory                                    FOSUserBundleFormFactoryFormFactory
 fos_user.resetting.form.type                                       FOSUserBundleFormTypeResettingFormType
 fos_user.security.interactive_login_listener                       FOSUserBundleEventListenerLastLoginListener
 fos_user.security.login_manager                                    FOSUserBundleSecurityLoginManager
 fos_user.user_manager                                              FOSUserBundleDoctrineUserManager
 fos_user.username_form_type                                        FOSUserBundleFormTypeUsernameFormType
 fos_user.util.email_canonicalizer                                  FOSUserBundleUtilCanonicalizer
 fos_user.util.token_generator                                      FOSUserBundleUtilTokenGenerator
 fos_user.util.user_manipulator                                     FOSUserBundleUtilUserManipulator
 fos_user.util.username_canonicalizer                               alias for "fos_user.util.email_canonicalizer"
```

The logic for all user related actions is stored in FOS\UserBundle\Doctrine\UserManager. The service for that class is fos_user.user_manager. Let us use the service in UserController.php

```
# src/AppBundle/Controller/UserController.php
...
    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $user->setPlainPassword($user->getPassword());
            $userManager->updateUser($$user);
            // $em = $this->getDoctrine()->getManager();
            // $em->persist($user);
            // $em->flush();

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
...
    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\UserType', $user, array('passwordRequired' => false));
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            // we get the values that user submitted
            $user->setPlainPassword($request->request->get('user')['password']['first']);
            $userManager->updateUser($user);
            // $em = $this->getDoctrine()->getManager();
            // $em->persist($user);
            // $em->flush();

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
...
```

The persist and flush statement in doctrine is a standard way to prepare and save queries to db. I commented it off because if you look at the updateUser function in FOS\UserBundle\Doctrine\UserManager, this part was already done.

Let us try creating a new user called "test3" and view it again in adminer

![encrypted passwd](images/chapter_7_encrypted_passwd.png)

The test3 user password is now encrypted. Update the password of another user and you will see that the encryption is working.

## What's Up With Editing the User

Now, let's try editing the test user. We are going to change the first name for example,

![edit user](images/chapter_7_edit_user.png)

The form is stopping us from editing because the password is a compulsory field. How do we fix that?

Let us pass a passwordRequired variable into the UserType class. If the variable is false, the password field will not be compulsory.

```
# src/AppBundle/Controller/UserController

...
    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\UserType', $user, array('passwordRequired' => false));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
...
```

and in UserType.php,

```
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('firstname')
            ->add('lastname')
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => $options['passwordRequired'],
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'passwordRequired' => true,
        ));
    }
}
```

If the password field is null, it means that user doesn't want to update the password. We will need to override FOSUserBundle setPassword function.

```
# src/AppBundle/Entity/User.php
...
    /**
     * Override parent's method. Don't set passwd if its null.
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        if ($password) {
            $this->password = $password;
        }
        return $this;
    }
...
```

## Updating Doctrine Fields Automatically

We like to have 2 more fields. We like to know when the user is being created and when it is being updated. How do we do that? HasLifeCycleCallBacks() is the magic.

```
# src/AppBundle/Entity/User.php
...
/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
	...
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $modified;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $created;

	/**
	 * @ORM\PrePersist
	 */
	public function prePersist()
	{
	    // update the modified time
	    $this->setModified(new \DateTime());

	    // for newly created entries
	    if ($this->getCreated() == null) {
	        $this->setCreated(new \DateTime('now'));
	    }
	}

        /**
         * @ORM\PreUpdate
         */
        public function preUpdate()
        {
            // update the modified time
            $this->setModified(new \DateTime());
        }
...
```

The "@ORM\HasLifecycleCallbacks()" tells doctrine to run callback functions (in this case, prePersist or preUpdate) before creating or updating an entry.

Let us auto-generate the setters and getters for the new $modified and $created variables.

```
-> app/console doctrine:generate:entities AppBundle:User
```

Verify that the getters and setters have been added to src/AppBundle/Entity/User.php. The schema is now changed and we need to update it.

```
# run this and you will see what the sql is doing
-> app/console doctrine:schema:update --dump-sql

# once you are comfortable with that, force update it
-> app/console doctrine:schema:update --force
```

Try adding a new user and see if adminer records the created and modified time correctly.

## Making Fields Non Compulsory

Noticed that when we add or edit a user, all the fields are compulsory. What if we want the first and last name to be non compulsory? We can do that easily with ORM annotation.

```
# src/AppBundle/Entity/User.php
...
/**
 * @var integer
 *
 * @ORMColumn(name="id", type="integer")
 * @ORMId
 * @ORMGeneratedValue(strategy="AUTO")
 */
protected $id;

/**
 * @var string
 *
 * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
 */
private $firstname;

/**
 * @var string
 *
 * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
 */
private $lastname;
...
```

Note that the variables should be private unless you want people to extend your class. "Id" needs to be protected not private because we are overriding the parent's variable.

Now try editing an entry and leave the first or last name empty. You should not get any error alerts.

## Deleting Users

No problem. This should work out of the box. Test it out in your browser to convince yourself.

## Cleaning Up

let us clean up the Controller by deleting the DefaultController.php and its related files

```
-> rm src/AppBundle/Entity/User.php~
-> git rm src/AppBundle/Controller/DefaultController.php
-> git rm -rf src/AppBundle/Resources/views/Default
-> git rm src/AppBundle/Tests/Controller/DefaultControllerTest.php
```

Run a quick test again and make sure that whatever you have done doesn't break anything. If the test doesn't work, you will need to recreate the database and create a new admin user in /user/new.

You will soon realised that you need a consistent set of test data to make testing easier. This is why data fixtures are so important.

## Summary

We have created User CRUD using command line, digged into the code and fixed up a few things. Even though things still doesn't work out of the box, we owed a lot to RAD to help us create a user management system in a short time. In reality, most admin packages should allow you to configure user management system out of the box. It is still a good practice for us to go through it.

In addition to the basic CRUD, we have added 4 extra fields (firstname, lastname, created, modified). Unlike username, email and password fields, the firstname and lastname fields are not compulsory. On the edit page, the password field is also not compulsory.

Remember to commit all your changes before moving on.

Next Chapter: [Chapter 8: Fixtures, Fixtures, Fixtures](https://github.com/bernardpeh/songbird/tree/chapter_8)

Previous Chapter: [Chapter 6: The Testing Framework Part 1](https://github.com/bernardpeh/songbird/tree/chapter_6)


## Stuck? Checkout my code

```
-> git checkout -b chapter_7 origin/chapter_7
-> git clean -fd
```

## Exercises (Optional)

* FOSUserBundle provides a functionality to manage users via command line. Try adding a user from the command line.

* Looking at AppBundle\Form\UserType, what happens if you change the password field to be called "plainPassword" instead? What changes would you make to the UserController.php class if that is the case?

* Can you think of another way to pass variable from the controller to the form?

## References

* [FOSUserBundle Doc](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/index.md)

* [Repeated fields in forms](http://symfony.com/doc/current/reference/forms/types/repeated.html)

* [Service Container](http://symfony.com/doc/current/book/service_container.html)

* [Dependency Injection](http://symfony.com/doc/current/components/dependency_injection/introduction.html)