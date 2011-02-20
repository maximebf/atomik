
# Configuration

There is a serious compatibility break in the configuration between Atomik 2.1 and 2.2.
Many keys have changed, check the appendix A for more information. The way the bootstrap
file is included has also been changed. The old bootstrap file has now been replaced with
the configuration file.

## Configuration file

Atomik provides a default configuration for everything (to fullfill the convention
over configuration principle). However, you can override it and provide plugin's
configuration or even your own.

Three file formats are available for the configuration: PHP (which is the default), INI or JSON.
The format will be choosed depending on the file extension (either php, ini or json - in lower case).

The file is by default located in the app directory and named *config*.
This can be changed in *atomik/files/config*. Do NOT specify the extension.

When using PHP, the script can return an array that will be use with *Atomik::set()*.
You can also directly use accessors in the file.
    
    return array(
        'my_key' => 'my value',
        'plugins' => array(
            'Db' => array(
                'dsn' => 'mysql:host=localhost',
                'username' => 'root'
            )
        ),
        'atomik/files' => array(
            'pre_dispatch' = 'pre.php'
            'post_dispatch' = 'post.php'
        )
    );

When using INI, you can use dots in keys to specify multi-dimensional keys. You can use slashes
in JSON keys or simply use child objects.

INI categories will be treated as parent keys and also dimensionized.
    
    my_key = my value

    [plugins]
    Db.dsn = mysql:host=localhost
    Db.username = root

    [atomik.files]
    pre_dispatch = pre.php
    post_dispatch = post.php

When using JSON, the data must be wrapped in an object.

    {
	    "my_key": "my value",

	    "plugins" : {
		    "Db": {
			    "dsn": "mysql:host=localhost",
			    "username": "root"
		    }
	    },
	
	    "atomik/files": {
		    "pre_dispatch": "pre.php",
		    "post_dispatch": "post.php"
	    }
    }

## Bootsrapping

Once the configuration loaded, Atomik will setup the environment and load plugins. Once ready,
it will try to load a bootstrap file. It can be used to prepare the application, load additional
libraries or plugins...

The file must be named *bootstrap.php* and located in the *app* directory.
In this file, you can use accessors (the *set()* method of course)
to define configuration keys.

The name of this file can be changed using the *atomik/files/bootstrap* configuration key.

## Custom directory structure

As said in the installation chapter, the directory structure can be customized.
This can be done by modifying entries in the *atomik/dirs* configuration key.

Each keys in the dirs array represent a type of directory. Their value can be a string for
a single path or an array for mutliple paths.

## Pre and post dispatch files

Atomik allows you to create two files: *pre\_dispatch.php* and 
*post\_dispatch.php* in the *app* directory. These files
will be called respectively before and after the dispatch process.

Their filename can be changed using the *atomik/files/pre\_dispatch* and
*atomik/files/post\_dispatch* configuration keys.

## Custom error pages

When an error occures, Atomik will display an error report. You can instead display a custom error page.
Create a file named *error.php* in the *app* directory.

The content can be anything you want. Beware that the layout won't be applied on this page. You also have
access to a variable named *$exception* which contains the thrown exception.

It is also possible to customize 404 error pages. Just create a *404.php* file in the
*app* directory. Like the error page, the layout won't be applied.

The filename of these files can be changed using the *atomik/files/error* and
*atomik/files/404* configuration keys.

## Advanced configuration

Sometimes it may be needed to create custom distributions with specific configuration. By default, when you
include the atomik core class, it will automatically start the dispatch process. This can be turned off
by setting the *ATOMIK\_AUTORUN* constant to false. You can then do custom configuration using 
accessors and finally launch atomik using *Atomik::run()*.

    <?php
    define('ATOMIK_AUTORUN', false);
    require 'Atomik.php';

    // my configuration
    Atomik::set(array(
	    // .. custom config
    ));

    // launch Atomik
    Atomik::run();

