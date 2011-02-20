
# Includes

Includes are php files containing common logic that you include in your actions.

Includes are stored in the *app/includes* and/or the *app/libs* 
directories. This can be changed in *atomik/dirs/includes*.

To include a file from one of these directories use the *Atomik::needed()* 
method. It takes as first argument the path to the filename you wish to include relative to the
previous directories and without the extension.

    // includes app/includes/common.php
    Atomik::needed('common');

You can use sub directories. To include a file stored at *app/includes/libs/db.php*:

    Atomik::needed('libs/db');

*Atomik::needed()* also allows you to include classes using
their name. To do so, classes have to follow the PEAR naming convention
(http://pear.php.net/manual/en/standards.naming.php) or use PHP 5.3 namespaces.

    // app/libraries/Atomik/Db.php
    Atomik::needed('Atomik_Db');
    Atomik::needed('Atomik\Db');

*Atomik::needed()* is automatically registered as an spl\_autoload handler.
This can be modified by setting *false* to the configuration key named *atomik/class\_autoload*.
