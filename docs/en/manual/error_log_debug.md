
# Error handling, logging and debugging

## Handling errors

By default, Atomik won't catch any exceptions or errors, PHP's normal behavior prevail. However it is possible
to enable error catching so you can display an error page to the user or error reports while developing.

The *atomik/catch\_errors* configuration key must be set to true for Atomik to catch errors. Errors
and exceptions are treated the same way.

By default, an error report will be displayed. The report can be hidden for security purpose by setting false to
the *atomik/display\_errors* configuration key.

A custom error page can be used as explained in the configuration chapter.
			
While disabled by default, it is adviced to use Atomik's error catching mechanism coupled with a custom error page.
This avoid critical information to be displayed to any visitor of your website.

All exceptions thrown by Atomik are of type *Atomik\_Exception*.
	
## Logging

Logging should be an important part of any application. To provide a unified way of logging, Atomik provides since
version 2.2 an *Atomik::log()* method. It takes two arguments, the second one being optional:
the message and the level (default is LOG\_ERR = 3).

    Atomik::log('an error has occured!', LOG_ERR);

As shown in the example, you should use PHP's LOG\_* constants.

The method will simply fire an event named *Atomik::Log*. Listeners will get two arguments, the message
and the level (the same as the *log()* method).

    function my_logger($message, $level) {
	    echo 'LOG: ' . $message;
    }
    Atomik::listenEvent('Atomik::Log', 'my_logger');

Atomik provides a default logger which will save messages to a text file. To register this logger, set
the config key named *atomik/log/register\_default* to true.

The filename is defined in *atomik/files/log*. You can also define from which level messages should
be saved by setting *atomik/log/level* to the minimum level. The default level is LOG\_WARNING (4).

Finally, you can define the template of the string that will be added to the log file in 
*atomik/log/message\_template*. You can use *%date%*, *%level%* and
*%message%* which will be replaced by the appropriate string.

## Debugging

Atomik's only provides a simple method named *Atomik::debug()* which 
is an alias for var\_dump(). However the method output can be hidden
by modifying the *app/debug* configuration key.

Also, if *app/debug* is true, the error reporting level will be set to the maximum.

    Atomik::debug($myVar);
    Atomik::set('app/debug', false);
    Atomik::debug($myVar2); // no output
    Atomik::debug($myVar2, true); // use true to force the output even if debug set to false

