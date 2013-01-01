
# Configuration

## Configuration file

Atomik provides a default configuration for everything (to fullfill the convention
over configuration principle). However, you can override it and provide plugin's
configuration or even your own.

Three file formats are available for the configuration: PHP (which is the default), INI or JSON.
The format will be chosen depending on the file extension (either php, ini or json - in lower case).

The file is by default located in the app directory and named *config*.
This can be changed in *atomik.files.config*. Do NOT specify the extension.

When using PHP, the script can return an array that will be use with `Atomik::set()`.
You can also directly use accessors in the file.
    
    return array(
        'my_key' => 'my value',
        'plugins' => array(
            'Db' => array(
                'dsn' => 'mysql:host=localhost',
                'username' => 'root'
            )
        ),
        'atomik.files' => array(
            'pre_dispatch' = 'pre.php'
            'post_dispatch' = 'post.php'
        )
    );

When using INI, you can use dots in keys to specify multi-dimensional keys. You can use dots
in JSON keys or child objects.

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
	
	    "atomik.files": {
		    "pre_dispatch": "pre.php",
		    "post_dispatch": "post.php"
	    }
    }

## Bootsrapping

Once the configuration is loaded, Atomik will setup the environment and load plugins. Once ready,
it will try to load a bootstrap file. It can be used to prepare the application, load additional
libraries or plugins...

The file must be named *bootstrap.php* and located in the *app* directory.
In this file, you can use accessors (the `set()` method of course)
to define configuration keys.

The name of this file can be changed using the *atomik.files.bootstrap* configuration key.

## Custom directory structure

The directory structure can be customized by modifying entries in the *atomik.dirs* configuration key.

Each keys in the *dirs* array represent a type of directory. Their value can be a string for
a single path or an array for mutliple paths.

If the path is relative, it must be relative to the root directory of your application.

For the *plugins*, *helpers* and *includes* keys, directories can be associated to
a namespace. Let's say you have the Doctrine library in /usr/share/php/doctrine, the
sources being in lib/Doctrine:

    Atomik::add('atomik.dirs.includes', array('Doctrine' => '/usr/share/php/doctrine/lib/Doctrine'));

## Pre and post dispatch files

Atomik allows you to create two files: *pre\_dispatch.php* and 
*post\_dispatch.php* in the *app* directory. These files
will be called respectively before and after the dispatch process.

Their filename can be changed using the *atomik.files.pre\_dispatch* and
*atomik.files.post\_dispatch* configuration keys.
