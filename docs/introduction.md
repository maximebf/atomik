
# Introduction

Atomik is an open-source micro framework for PHP 5.3+.
Atomik is build with the KISS (Keep It Simple Stupid) and DRY (Don't Repeat Yourself)
principles in mind as well as speed and security. It is also an ideal introduction for 
beginners to the world of web development frameworks. 

Here's a list of some Atomik features:

 - Very small footprint
 - Open Source (MIT License)
 - Very simple to use
 - Easy to use router for pretty URLs
 - Powerful templating: helpers, layouts, content types...
 - Flash messages
 - Errors handling
 - Intuitive architecture for beginners
 - Respect good programming practices
 - Plugins and pluggable applications
 - Highly extensible
 - Uses existing libraries

The manual is licensed under the Creative Commons Attribution license.

## Requirements

 - HTTP Server. Apache with mod_rewrite is a good choice.
 - PHP 5.3 or greater

## Installation

The best way to install Atomik is using [Composer](http://getcomposer.org)
and the [Atomik Skeleton Application](https://github.com/maximebf/atomik-skeleton). 
The skeleton is a base Atomik application with a basic directory structure which 
let you start building your project in a matter of seconds!

    $ php composer.phar create-project atomik/skeleton /path/to/my/install/folder

Navigate to your website in your browser (ie. <http://localhost>) where you should
see a congratulation message.

If you're not comfortable using Composer, you can download the skeleton as a zip archive
from [here](https://github.com/maximebf/atomik/releases).

If you want to activate pretty URLs under Apache, rename the *htaccess.example* file
to *.htaccess*.

## About the skeleton

Atomik Skeleton Application is a base Atomik application with a basic directory structure 
which let you start building your project in a matter of seconds!

It includes [Twitter Bootstrap](http://getbootstrap.com/), [jQuery](http://jquery.com) and
[PHP DebugBar](http://phpdebugbar.com).

The skeleton comes with debug mode activated. Don't forget to change *atomik.debug* to
`false` in the config file when you switch to production mode.

## Directory structure

Your application per se goes into the *app* directory. Actions and views have their own 
directories under *app/actions* and *app/views*.

Helpers and plugins are located in *app/helpers* and *app/plugins*.
*app/includes* will be added to PHP's include path.

The configuration is stored in *app/config.php*.

When using the provided Apache *.htaccess* file, the *app* directory is not 
accessible from the web.

If you do not use the provided *.htaccess* file, do not forget to allow *assets*
folders in plugins directories. Such a path can look like *app/plugins/MyPlugin/assets*.

## Advanced installation

It is also possible to install and configure Atomik from scratch using Composer.
In the directory of your project, create a *composer.json* file with the
following requirements:

    {
        "require": {
            "atomik/atomik": ">=3.0.0"
        }
    }

Run composer from this directory to install atomik:

    $ php composer.phar install

Atomik will be installed in the *vendor* directory, along any other
dependencies you add to the *require* hash in your *composer.json* file.

Create the directory structure. Create the *index.php* file as follow:

    <?php
    require 'vendor/autoload.php';
    Atomik::run();

`Atomik::run()` takes as first argument the root directory of your app.
The default value is '.' which is the current directory.

Remember that in a production environment, it is always better to remove the 
application files from the webroot, thus usually using a root directory one
level above the publicly accessible one (ie. using `Atomik::run('..')`).
