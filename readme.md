# Chapter 4: The Testing Framework Part 1 (Optional)

This chapter talks about [Codeception](http://codeception.com/). Feel free to skip it if you already have a testing framework in place.

No application is complete without going through a rigorous testing process. Software Testing is a big topic by itself.

Today, many developers know [TDD](https://en.wikipedia.org/wiki/Test-driven_development) and [BDD](https://en.wikipedia.org/wiki/Behavior-driven_development). Test First Development ensures that your software is reliable but requires a lot of patience and extra work to implement it correctly. Think of it like a quality control process. The more checks you have, the less bugs your have. Of course, you can cost cut by not having checks and hope that your product is still bug free. This is quite unlikely especially if the software is complex.

Personally, I prefer to write user stories and scenarios first (I like to think of them as pseudocode) rather than spending time coding the tests. Once I have the user stories and scenarios defined, I will jump in and code functionality A. When functionality A is completed, I will code the test cases and ensure they pass before moving on. I will repeat the cycle for functionality B before moving on to functionality C. The idea is to not break existing functionalities while adding on new functionalities.

Everyone's testing approach is different. You could implement your own approach.

I will be writing acceptance tests in most cases. There are many frameworks for acceptance testing. [Behat](http://docs.behat.org/) and [Mink](http://mink.behat.org/) is the industrial standard at the moment. In this book, we will be using a bit of phpunit but mainly [Codeception](http://codeception.com/) as our testing framework.

## Objectives

> * Pre-setup

> * Installation

> * Create a basic acceptance test

> * Run a simple acceptance test successfully

## Pre-setup

We are still in the master branch. Do a 'git status' to see which branch you are in to confirm. Let us create a new branch before doing anything.

```
-> git status
-> git checkout -b my_chapter4
```

## Installation

```
# composer.json

# add the codeception line under require-dev
"require-dev": {
    ...
    "codeception/codeception": "~2.1"
},
```

Don't worry about the specific version number of the bundle for now. The reason I used these version number was because I tested it with them and they will work if you use them.

Now we can run composer update and initialise codeception

```
-> composer update
-> bin/codecept bootstrap
```

Let us configure codecept acceptance test to work in Symfony.

```
# tests/acceptance.suite.yml
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
        - \Helper\Acceptance
```

Acceptance Testing is like Black Box Testing - We try to simulate real users interacting with our app. We ignore the inner workings of the code and only care if it works from the end user's point of view.

Here, we are using [selenium](http://seleniumhq.org) webdriver to simulate browser testing ([chrome](https://www.google.com.au/intl/en/chrome/browser/desktop/index.html)). Codeception by default comes with PhpBrowser which doesn't support javascript. Selenium is slow but is the veteran when comes to acceptance testing. We could also use a headless browser like [phantomjs](http://phantomjs.org) which is faster but I found it buggy at the time of writing. In this book, I will be using selenium.

We can now generate the acceptance actions based on the updated acceptance suite:

```
-> bin/codecept build
```

## The First Test

We know that the default Symfony comes with the AppBundle example. Let us now test the bundle by creating a test suite for it.


```
-> bin/codecept generate:cest acceptance AppBundle
```

The auto generated Cest class should look like this:

```
# tests/acceptance/AppBundleCest.php

class AppBundleCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

   ...
}
```

Let us write our own test. All new Symfony installation homepage should have a successful message.


```
# tests/acceptance/AppBundleCest.php
...
# replaced tryToTest function with InstallationTest function
public function InstallationTest(AcceptanceTester $I)
{
    $I->wantTo('Check if Symfony is installed successfully.');
    $I->amOnPage('/');
    $I->see('Welcome to');
}
```

Now run the test:

```
-> bin/codecept run acceptance AppBundleCest
```

and you should get an error complaining that there is no selenium server running...

```
[Codeception\Exception\ConnectionException]
  Curl error thrown for http POST to /session with params: {"desiredCapabilities":{"unexpectedAlertBehaviour
  ":"accept","webStorageEnabled":true,"browserName":"chrome"}}
  Failed to connect to 127.0.0.1 port 4444: Connection refused

  Please make sure that Selenium Server or PhantomJS is running.
```

Install latest version of [Java JDK](http://www.oracle.com/technetwork/java/javase/downloads/index.html),  download [selenium standalone server](http://www.seleniumhq.org/download/) and [chrome driver](http://chromedriver.storage.googleapis.com/index.html). Remember to start selenium server in a **new terminal**.

```
-> mkdir scripts
-> cd scripts
# download selenium server and chromedriver to this dir. In a new terminal, start selenium server. I am using v2.53.1 for example.
-> java -Dwebdriver.chrome.driver=./chromedriver -jar selenium-server-standalone-2.53.1.jar
```

On the previous terminal, run the acceptance test again. You should see selenium firing up a new chrome browser and running the test.

```
-> bin/codecept run acceptance AppBundleCest
...
# OK (1 test, 1 assertion)
```

The selenium server jar file is a binary. To make life easy, we are going to commit selenium-server-standalone-xxx.jar. We need to tell git not to convert the line endings (google for it if interested)

```
# .gitattributes

...
# Denote all files that are truly binary and should not be modified.
*.png binary
*.jpg binary
*.jar binary
```

Don't forget to commit your code before moving on to the next chapter.

```
-> git status
# see all files modified or created. Commit all of them
-> git add .gitignore
-> git add .gitattributes
-> git add composer.json
-> git add codeception.yml
-> git add tests
-> git add scripts
-> git commit -m"added codeception and created basic test"
# update remote repo so you dont lose it
-> git push -u origin my_chapter4
```

## Stuck? Checkout my code

```
# Remember to commit or stash your changes, then checkout mine.
-> git checkout -b chapter_4 origin/chapter_4
-> git clean -fd
```

## Summary

In this chapter, we discussed the importance of testing and touched on TDD and BDD. In our context, we will be mainly writing BDD tests. We installed codeception and selenium and wrote a simple acceptance test that tests the app/example page.

Next Chapter: [Chapter 5: The Testing Framework Part 2 (Optional)](https://github.com/bernardpeh/songbird/tree/chapter_5)

Previous Chapter: [Chapter 3: What is SongBird](https://github.com/bernardpeh/songbird/tree/chapter_3)

## Exercises (Optional)

* Try configure codeception to allow running of different acceptance profile. Can you test with PhpBrowser or phantomjs easily? Do you see any benefit of doing that? See [advanced codeception](http://codeception.com/docs/07-AdvancedUsage) for help.

## Resources

* [TDD](https://en.wikipedia.org/wiki/Test-driven_development)

* [BDD](https://en.wikipedia.org/wiki/Behavior-driven_development)

* [PhantomJS](http://phantomjs.org/download.html)

* [Codeception documentation](http://codeception.com/docs)



