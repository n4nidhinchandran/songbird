# Chapter 18: Making Your Bundle Reusable

We have created a page bundle in the previous chapter. It's not perfect but let's say we want to share it with everyone. How do we do that? Be warned, we need lots of refactoring in the code to make it sharable.

This is a long chapter. It goes through the process of creating a resuable bundle by trial and error. I think it is a good process to go through because it makes you pause and think. If you already know the process and want to skip through, simple clone the [NestablePageBundle from github](https://github.com/bernardpeh/NestablePageBundle) and follow the installation instructions in the readme file. Then, jump over to the next chapter.

## Objectives

> * Creating a Separate Repository
> * Updating Application composer.json
> * Renaming SongbirdNestablePageBundle
> * Making the Bundle Extensible


## Pre-setup

Make sure we are in the right branch. Let us branch off from the previous chapter.

```
# check your branch
-> git status
# start branching now
-> git checkout -b my_chapter18
```

## Creating a separate repository

First of all, let us create a readme file.

```
-> cd src/Songbird/NestablePageBundle
-> touch readme.md
```

Update the readme file.

Let us create the composer.json file for this repo. We will do a simple one

```
-> composer init
```

Follow the prompts. You might need to read up on software licensing. <a href="https://en.wikipedia.org/wiki/MIT_License">MIT license</a> is becoming really popular. The sample composer.json might look like this:

```
{
    "name": "Yourname/nestable-page-bundle",
    "description": "your description",
    "type": "symfony-bundle",
    "require": {
        "symfony/symfony": "~3.0"
    },
    "require-dev": {
        "doctrine/doctrine-fixtures-bundle": "~2.0"
    },
    "autoload": {
        "psr-4": { "Songbird\NestablePageBundle\": "" }
    },
    "license": "MIT",
    "authors": [
        {
            "name": "your name",
            "email": "your_email@your_email.xx"
        }
    ]
}
```

Note that we have to add the "autoload" component so that Symfony can autoload the namespace post installation. <a href="https://getcomposer.org/doc/04-schema.md#autoload">PS-4</a> is the default standard at the time of writing. Next, let us create the license in a text file

```
-> touch LICENSE
```

copy the [MIT LICENSE](http://opensource.org/licenses/MIT) and update the LICENSE file.

Init the repo

```
-> cd src/Songbird/NestablePageBundle
-> git init .
-> git add .
-> git commit -m"init commit"
```

In [github](http://github.com) (create a new acct if not done), create a new repo. Let's call it NestablePageBundle for example. Once you have created the new repo, you should see instructions on how to push your code.

```
-> git remote add origin git@github.com:your_username/NestablePageBundle.git
-> git push -u origin master
```

Let us give our first release a version number using the [semantic versioning](http://semver.org) convention.

```
-> git tag 0.1.0
-> git push --tags
```

Your repository is now available for the public to pull.

## Updating Application composer.json

Note that this composer.json is different from the one that we have just created. If we add our repo to [packagist](https://packagist.org), we could install our bundle like any other bundles. I was afraid that anyone reading this tutorial might submit their test bundle to packagist, so I thought it would be a better idea to install the bundle from git instead.

```
# composer.json
...
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/your_name/NestablePageBundle"
        }
    ],
...
    "require": {
        ...
        "your_name/nestable-page-bundle": ">0.1.0"
    }
...
```

Note that the bundle name is "nestable-page-bundle" under the "require" section. Why not NestablePageBundle following Symfony's convention? Remember the composer.json file that you have created previously? "nestable-page-bundle" is the name of the bundle as specified in that composer file.

Now lets run composer update and see what happens

```
-> cd ../../../
-> composer update
...

  - Installing your_name/nestable-page-bundle (0.1.0)
    Downloading: 100%    
```

At this point, look at the vendor directory and you will see your bundle being installed in there. That's a good start.

## Renaming SongbirdNestablePageBundle

Let us do some cleaning up. We no longer need the src/Songbird/NestablePageBundle since we have installed the bundle under vendor dir.

```
git rm -rf src/Songbird/
```
Let us check if the route is still there.

```
-> app/console debug:router | grep songbird
...
songbird_page            GET      ANY    ANY  /songbird_page/                                    
songbird_page_list       GET      ANY    ANY  /songbird_page/list                                
songbird_page_reorder    POST     ANY    ANY  /songbird_page/reorder
```

Woah!! We have already deleted src/Songbird/NestablePageBundle and we should expect to see some errors. Why are the songbird routes still there?

That's right, that shows that vendor/your_name/nestable-page-bundle is working. The *new SongbirdNestablePageBundleSongbirdNestablePageBundle()* initialised in AppKernel.php is working because of the namespace.

We have a problem. The namespace "Songbird" is no longer relevant in vendor/your-name/nestable-page-bundle since the bundle is already decoupled from Songbird CMS. We want to change the bundle's filename and namespace so that it is more intuitive. How do we do that?

Let us re-download the repo and do some mass restructuring

```
-> cd vendor/your-name
-> rm -rf nestable-page-bundle
-> git clone git@github.com:your_name/NestablePageBundle.git nestable-page-bundle
-> cd nestable-page-bundle
```

There is no quick way for this, some bash magic helps

```
# Your-Initial can be something short but has to be unique
# let us change the namespace
-> find . -type f | grep -v .git/ | while read s; do sed -i '' 's/Songbird\NestablePageBundle/{your-initial}\NestablePageBundle/g' $s ; done
# change the bundle name
-> find . -type f | grep -v .git/ | while read s; do sed -i '' 's/SongbirdNestablePage/{your-initial}NestablePage/g' $s ; done
-> find . -type f | grep -v .git/ | while read s; do sed -i '' 's/songbird_/{your_initial}_/g' $s ; done
-> find . -type f | grep -v .git/ | while read s; do sed -i '' 's/songbirdnestable/{your_initial}nestable/g' $s ; done
```

That should save us 90% of the time. Then visually walk through all the files and check that they are ok.

Lastly, rename the bundle file

```
-> git mv SongbirdNestablePageBundle.php {your-initial}NestablePageBundle.php
-> cd DependencyInjection
-> git mv SongbirdNestablePageExtension.php BpehNestablePageExtension.php
-> cd ../Resources/translations
-> git mv SongbirdNestablePageBundle.en.xlf {your-initial}NestablePageBundle.en.xlf
-> git mv SongbirdNestablePageBundle.fr.xlf {your-initial}NestablePageBundle.fr.xlf
```

Now, here is the question. How do we test our changes without commiting to git and re-run composer update? We can update our entry in vendor/composer/autoload_psr4.php

```
# vendor/composer/autoload_psr4.php
...
    # 'Songbird\NestablePageBundle\' => array($vendorDir . '/{your-name}/nestable-page-bundle'),
    '{your-initial}\NestablePageBundle\' => array($vendorDir . '/{your-name}/nestable-page-bundle'),
...
```

Now, let us update AppKernel

```
# app/config/AppKernel.php
# new SongbirdNestablePageBundleSongbirdNestablePageBundle(),
new {your-initial}NestablePageBundle{your-initial}NestablePageBundle(),
```

and routing

```
# app/config/routing.yml

# songbird_nestable_page:
#     resource: "@SongbirdNestablePageBundle/Controller/"
#    type:     annotation
#    prefix:   /

{your-initial}_nestable_page:
    resource: "@{your-initial}NestablePageBundle/Controller/"
    type:     annotation
    prefix:   /
```

let us check that the routes are working. Let's say my initial is bpeh

```
app/console debug:router | grep bpeh
bpeh_page                GET      ANY    ANY  /bpeh_page/                                        
bpeh_page_list           GET      ANY    ANY  /bpeh_page/list                                    
bpeh_page_reorder        POST     ANY    ANY  /bpeh_page/reorder
...
```

We can now install the assets.

```
-> ./scripts/assetsinstall
```

Now go your new page list url and do a quick test. In my case,

```
http://songbird.app/bpeh_page/list
```

Looks like it is working. How can we be sure? Remember our functional tests?

```
-> phpunit -c app vendor/{your-name}/nestable-page-bundle/
...
Time: 29.04 seconds, Memory: 88.50Mb

OK (5 tests, 14 assertions)
```

This is a sign of relieve... Everything is working. Remember to commit your code before moving to the next chapter. Up your nestablepagebundle tags to 0.2.0 or something else since there were major changes.

## Making the Bundle Extensible

When this bundle is initialised in AppKernel.php, running "app/console doctrine:schema:create will create the tables. This is not desirable. We want the child class to create the tables instead and inherit the properties of the parent entities. The war is not over. There is still a lot to be done!!

Let us clean up the AppKernel and Route.

```
# app/AppKernel.php
...
// new {your-inital}NestablePageBundle{your-initial}NestablePageBundle(),
...
```

and in routing.yml

```
# app/config/routing.yml

# {your-initial}_nestable_page:
# resource: "@{your-initial}NestablePageBundle/Controller/"
# type:     annotation
# prefix:   /
```

and refocus our attention to the NestablePageBundle:

```
-> cd vendor/{your-initial}/NestablePageBundle
```

First of all, we need to make The 2 entities extensible. Using [inheritance mapping](http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/inheritance-mapping.html), We will move the entities to the Model directory so that they won't be initialised by orm auto mapping. We will make the entities abstract and top level in a single-table strategy. Then, we create mapped super classes in the entity dir for each Page and pageMeta entity. The entities from AppBundle will extend from the mapped super classes. 

I'll be using my initial (bpeh) from now onwards to make life easier when referencing paths.

```
# vendor/bpeh/nestable-page-bundle/Model/PageBase.php

namespace Bpeh\NestablePageBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bpeh\NestablePageBundle\Entity\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"pagebase" = "PageBase", "page" = "Bpeh\NestablePageBundle\Entity\Page"})
 */
abstract class PageBase
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    protected $slug;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean", nullable=true)
     */
    protected $isPublished;

    /**
     * @var integer
     *
     * @ORM\Column(name="sequence", type="integer", nullable=true)
     */
    protected $sequence;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime")
     */
    protected $modified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    protected $created;


    /**
     * @ORM\ManyToOne(targetEntity="Bpeh\NestablePageBundle\Model\PageBase", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")}
     * @ORM\OrderBy({"sequence" = "ASC"})
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Bpeh\NestablePageBundle\Model\PageBase", mappedBy="parent")
     * @ORM\OrderBy({"sequence" = "ASC"})
     */
    protected $children;
   
    /**
     * @ORM\OneToMany(targetEntity="Bpeh\NestablePageBundle\Model\PageMetaBase", mappedBy="page", cascade={"persist"}))
     */
    protected $pageMetas;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return Page
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean 
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return Page
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Page
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Page
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pageMetas = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
        $this->created = new \DateTime();
    }

    /**
     * Set parent
     *
     * @param \Bpeh\NestablePageBundle\Model\PageBase $parent
     * @return Page
     */
    public function setParent(\Bpeh\NestablePageBundle\Model\PageBase $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Bpeh\NestablePageBundle\Model\PageBase 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \Bpeh\NestablePageBundle\Model\PageBase $children
     * @return Page
     */
    public function addChild(\Bpeh\NestablePageBundle\Model\PageBase $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Bpeh\NestablePageBundle\Model\Page $children
     */
    public function removeChild(\Bpeh\NestablePageBundle\Model\PageBase $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add pageMetas
     *
     * @param \Bpeh\NestablePageBundle\Model\PageMetaBase $pageMetas
     * @return Page
     */
    public function addPageMeta(\Bpeh\NestablePageBundle\Model\PageMetaBase $pageMetas)
    {
        $this->pageMetas[] = $pageMetas;

        return $this;
    }

    /**
     * Remove pageMetas
     *
     * @param \Bpeh\NestablePageBundle\Model\PageMetaBase $pageMetas
     */
    public function removePageMeta(\Bpeh\NestablePageBundle\Model\PageMetaBase $pageMetas)
    {
        $this->pageMetas->removeElement($pageMetas);
    }

    /**
     * Get pageMetas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPageMetas()
    {
        return $this->pageMetas;
    }
    
    /**
     * convert object to string
     * @return string
     */
    public function __toString()
    {
        return $this->slug;
    }
}

```
Note that we have changed all variables to protected to allow inheritance. The references to PageBase has also been changed.

Page.php now inherits from PageBase.php

```
# vendor/bpeh/nestable-page-bundle/Entity/Page.php

namespace Bpeh\NestablePageBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Bpeh\NestablePageBundle\Model\PageBase;

/** @ORM\MappedSuperclass */
class Page extends PageBase
{
}
```

We will do the same for PageMetaBase.php

```
# src/vendor/nestable-page-bundle/Model/PageMetaBase.php

<?php

namespace Bpeh\NestablePageBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageMeta
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"pagemetabase" = "PageMetaBase", "pagemeta" = "Bpeh\NestablePageBundle\Entity\PageMeta"})
 */
abstract class PageMetaBase
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="page_title", type="string", length=255)
     */
    protected $page_title;

    /**
     * @var string
     *
     * @ORM\Column(name="menu_title", type="string", length=255)
     */
    protected $menu_title;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=4)
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="text", nullable=true)
     */
    protected $short_description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="Bpeh\NestablePageBundle\Model\PageBase", inversedBy="pageMetas")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")}
     */
    protected $page;

    /**
     * constructor
     */
    public function __construct()
    {
        // default values
        $this->locale = 'en';
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set page_title
     *
     * @param string $pageTitle
     * @return PageMeta
     */
    public function setPageTitle($pageTitle)
    {
        $this->page_title = $pageTitle;

        return $this;
    }

    /**
     * Get page_title
     *
     * @return string 
     */
    public function getPageTitle()
    {
        return $this->page_title;
    }

    /**
     * Set menu_title
     *
     * @param string $menuTitle
     * @return PageMeta
     */
    public function setMenuTitle($menuTitle)
    {
        $this->menu_title = $menuTitle;

        return $this;
    }

    /**
     * Get menu_title
     *
     * @return string 
     */
    public function getMenuTitle()
    {
        return $this->menu_title;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return PageMeta
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set short_description
     *
     * @param string $shortDescription
     * @return PageMeta
     */
    public function setShortDescription($shortDescription)
    {
        $this->short_description = $shortDescription;

        return $this;
    }

    /**
     * Get short_description
     *
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->short_description;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return PageMeta
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set page
     *
     * @param \Bpeh\NestablePageBundle\Model\PageBase $page
     * @return PageMeta
     */
    public function setPage(\Bpeh\NestablePageBundle\Model\PageBase $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Bpeh\NestablePageBundle\Model\PageBase
     */
    public function getPage()
    {
        return $this->page;
    }

    
}
```

and for the child PageMeta.php

```
# src/nestable-page-bundle/Entity/PageMeta.php

namespace Bpeh\NestablePageBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Bpeh\NestablePageBundle\Model\PageMetaBase;

/** @ORM\MappedSuperclass */
class PageMeta extends PageMetaBase
{
	
}
```

We also need user to specify which child entities and form type (if they are extending the parent form type).

```
# vendor/bpeh/nestable-page-bundle/DependencyInjection/Configuration.php

namespace Bpeh\NestablePageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bpeh_nestable_page');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->scalarNode('page_entity')->defaultValue('Bpeh\NestablePageBundle\PageTestBundle\Entity\Page')->end()
                ->scalarNode('pagemeta_entity')->defaultValue('Bpeh\NestablePageBundle\PageTestBundle\Entity\PageMeta')->end()
                ->scalarNode('page_type')->defaultValue('Bpeh\NestablePageBundle\PageTestBundle\Form\PageType')->end()
                ->scalarNode('pagemeta_type')->defaultValue('Bpeh\NestablePageBundle\PageTestBundle\Form\PageMetaType')->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
```

and 

```
# vendor/bpeh/nestable-page-bundle/DependencyInjection/BpehNestablePageExtension.php
namespace Bpeh\NestablePageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BpehNestablePageExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter( 'bpeh_nestable_page.page_entity', $config[ 'page_entity' ]);
        $container->setParameter( 'bpeh_nestable_page.pagemeta_entity', $config[ 'pagemeta_entity' ]);
        $container->setParameter( 'bpeh_nestable_page.page_type', $config[ 'page_type' ]);
        $container->setParameter( 'bpeh_nestable_page.pagemeta_type', $config[ 'pagemeta_type' ]);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
```

Now in config.yml, anyone can define the page and pagemeta entities themselves.

We also need to initialise some variables when the controllers are loaded. We will do that via the event listener.

```
# vendor/bpeh/nestable-page-bundle/Resources/config/services.xml

<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <services>
        <service id="bpeh_nestable_page.init" class="Bpeh\NestablePageBundle\EventListener\ControllerListener">
            <tag name="kernel.event_listener" event="kernel.controller" method="onKernelController"/>
        </service>
    </services>
</container>
```

and in the controller listener class

```
# vendor/bpeh/nestable-page-bundle/EventListener/ControllerListener.php

namespace Bpeh\NestablePageBundle\EventListener;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Bpeh\NestablePageBundle\PageTestBundle\Controller\PageController;
use Bpeh\NestablePageBundle\PageTestBundle\Controller\PageMetaController;

class ControllerListener
{

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * controller must come in an array
         */
        if (!is_array($controller)) {
            return;
        }
        
        if ($controller[0] instanceof PageController || $controller[0] instanceof PageMetaController) {
            $controller[0]->init();
        }
    }
}
```

The Page Controller can now use the parameters as defined in config.yml to load the entities and form types.

```
# vendor/bpeh/nestable-page-bundle/Controller/PageController.php

namespace Bpeh\NestablePageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Bpeh\NestablePageBundle\Entity\Page;

/**
 * Page controller.
 *
 * @Route("/bpeh_page")
 */
class PageController extends Controller
{

    private $entity;

    private $page_type;

    public function init()
    {
        $this->entity = $this->container->getParameter('bpeh_nestable_page.page_entity');
        $this->page_type = $this->container->getParameter('bpeh_nestable_page.page_type');
    }
    
    /**
     * Lists all Page entities.
     *
     * @Route("/", name="bpeh_page")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('bpeh_page_list'));
    }

        /**
     * Lists all nested page
     *
     * @Route("/list", name="bpeh_page_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $rootMenuItems = $em->getRepository($this->entity)->findParent();

        return array(
            'tree' => $rootMenuItems,
        );
    }

    /**
     * reorder pages
     *
     * @Route("/reorder", name="bpeh_page_reorder")
     * @Method("POST")
     * @Template()
     */
    public function reorderAction()
    {
        $em = $this->getDoctrine()->getManager();
        // id of affected element
        $id = $this->get('request')->get('id');
        // parent Id
        $parentId = ($this->get('request')->get('parentId') == '') ? null : $this->get('request')->get('parentId');
        // new sequence of this element. 0 means first element.
        $position = $this->get('request')->get('position');

        $result = $em->getRepository($this->entity)->reorderElement($id, $parentId, $position); 

        return new JsonResponse(
            array('message' => $this->get('translator')->trans($result[0], array(), 'BpehNestablePageBundle')
, 'success' => $result[1])
        );

    }

    /**
     * Creates a new Page entity.
     *
     * @Route("/", name="bpeh_page_create")
     * @Method("POST")
     * @Template("BpehNestablePageBundle:Page:new.html.twig")
     */
    public function createAction(Request $request)
    {
        
        $entity = new $this->entity();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bpeh_page_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Page entity.
     *
     * @param Page $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Page $entity)
    {
        $form = $this->createForm(new $this->page_type(), $entity, array(
            'action' => $this->generateUrl('bpeh_page_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Page entity.
     *
     * @Route("/new", name="bpeh_page_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {

        $entity = new $this->entity();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Page entity.
     *
     * @Route("/{id}", name="bpeh_page_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->entity)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     * @Route("/{id}/edit", name="bpeh_page_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->entity)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Page entity.
    *
    * @param Page $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Page $entity)
    {
        $form = $this->createForm(new $this->page_type(), $entity, array(
            'action' => $this->generateUrl('bpeh_page_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Page entity.
     *
     * @Route("/{id}", name="bpeh_page_update")
     * @Method("PUT")
     * @Template("BpehNestablePageBundle:Page:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->entity)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('bpeh_page_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Page entity.
     *
     * @Route("/{id}", name="bpeh_page_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($this->entity)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Page entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('bpeh_page'));
    }

    /**
     * Creates a form to delete a Page entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bpeh_page_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
```

Likewise for PageMeta Controller

```
# vendor/bpeh/nestable-page-bundle/Controller/PageMetaController.php

namespace Bpeh\NestablePageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Bpeh\NestablePageBundle\Form\PageMetaType;
use Bpeh\NestablePageBundle\Entity\PageMeta;

/**
 * PageMeta controller.
 *
 * @Route("/bpeh_pagemeta")
 */
class PageMetaController extends Controller
{

    private $entity;

    private $pagemeta_type;

    public function init()
    {
        $this->entity = $this->container->getParameter('bpeh_nestable_page.pagemeta_entity');
        $this->pagemeta_type = $this->container->getParameter('bpeh_nestable_page.pagemeta_type');
    }

    /**
     * Lists all PageMeta entities.
     *
     * @Route("/page/{id}", name="bpeh_pagemeta")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository($this->entity)->findByPage($id);

        return array(
            'entities' => $entities,
            'pageId' => $id
        );
    }
    /**
     * Creates a new PageMeta entity.
     *
     * @Route("/pagemeta", name="bpeh_pagemeta_create")
     * @Method("POST")
     * @Template("BpehNestablePageBundle:PageMeta:new.html.twig")
     */
    public function createAction(Request $request)
    {

        $entity = new $this->entity();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('bpeh_pagemeta_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a PageMeta entity.
     *
     * @param PageMeta $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PageMeta $entity)
    {
        $form = $this->createForm(new $this->pagemeta_type(), $entity, array(
            'action' => $this->generateUrl('bpeh_pagemeta_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PageMeta entity.
     *
     * @Route("/pagemeta/new", name="bpeh_pagemeta_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {

        $entity = new $this->entity();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a PageMeta entity.
     *
     * @Route("/pagemeta/{id}", name="bpeh_pagemeta_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->entity)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PageMeta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PageMeta entity.
     *
     * @Route("/pagemeta/{id}/edit", name="bpeh_pagemeta_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->entity)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PageMeta entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a PageMeta entity.
    *
    * @param PageMeta $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PageMeta $entity)
    {
        $form = $this->createForm(new $this->pagemeta_type(), $entity, array(
            'action' => $this->generateUrl('bpeh_pagemeta_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing PageMeta entity.
     *
     * @Route("/pagemeta/{id}", name="bpeh_pagemeta_update")
     * @Method("PUT")
     * @Template("BpehNestablePageBundle:PageMeta:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($this->entity)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PageMeta entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('bpeh_pagemeta_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a PageMeta entity.
     *
     * @Route("/pagemeta/{id}", name="bpeh_pagemeta_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($this->entity)->find($id);
            $pageId = $entity->getPage()->getId();

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PageMeta entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('bpeh_pagemeta', array('id' => $pageId)));
    }

    /**
     * Creates a form to delete a PageMeta entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('bpeh_pagemeta_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
```

Once you are happy with it, give it a new tag and commit your changes again.

The bundle is now ready to be extended. To see it in action, I've created a [demo bundle](https://github.com/bernardpeh/NestablePageBundle) and you can install the demo bundle and test it for yourself.

## Summary

In this chapter, we have created a new repo for the NestablePageBundle. We have updated composer to pull the bundle from the repo and auto-loaded it according to the PSR-4 standard. We learned the hard way of creating a non-extensible bundle with the wrong namespace and then mass renaming it again. Making the entities extensible was a massive job and required a lot of refactoring in our code.

We have done so much to make NestablePageBundle as decoupled as possible. Was it worth the effort? Definitely!

Next Chapter: [Chapter 19: The Page Manager Part 2](https://github.com/bernardpeh/songbird/tree/chapter_19)

Previous Chapter: [Chapter 17: The Page Manager Part 1](https://github.com/bernardpeh/songbird/tree/chapter_17)

## Stuck? Checkout my code

```
-> git checkout -b chapter_18 origin/chapter_18
-> git clean -fd
```

## Exercises

* Delete the whole vendor directory and try doing a composer update. Did anything break?
* Update the functional test.

## References

* [Define relationship between abstract classes and interfaces](http://symfony.com/doc/current/doctrine/resolve_target_entity.html)
* [Short guide to licenses](http://www.smashingmagazine.com/2010/03/a-short-guide-to-open-source-and-similar-licenses)
* [Software licenses at a glance](http://tldrlegal.com)
* [Composer Schema](https://getcomposer.org/doc/04-schema.md)
* [Composer versioning](https://getcomposer.org/doc/articles/versions.md)