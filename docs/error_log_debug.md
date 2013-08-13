
# Error handling, logging and debugging

## Handling errors

<div class="note">You will need the Errors plugin which is bundled with Atomik</div>

By default, Atomik won't catch any exceptions or errors, PHP's normal behavior prevail. 
However, the Errors plugin enables error catching so you can display an error page to the 
user or error reports while developing.

The plugin will display a page when a 404 error is triggered. The template to render
can be specified using *404\_view*, the default one being *errors/404*:

    Atomik::set('plugins.Errors', array(
        '404_view' => 'unknown_page'
    ));

The *catch\_errors* configuration key must be set to true for Atomik to catch errors. Errors
and exceptions are treated the same way. If an error template exists (specified in *error_view*
and by default *errors/error*) it will be rendered otherwise an error report is displayed.

    Atomik::set('plugins.Errors', array(
        'error_view' => 'my_error_template',
        'catch_errors' => true
    ));

By default, uncatched errors are silently droped. You can instead let the exception be thrown
using *throw\_errors*.

    Atomik::set('plugins.Errors', array(
        'throw_errors' => true
    ));
	
## Logging

<div class="note">You will need the Logger plugin which is bundled with Atomik as well as Monolog which you'll need to install</div>

The Logger plugin provides a simple way of logging messages. It provides the ̀log()` helper
which takes two arguments, the second one being optional: the message and the level (default is `LOG_ERR` = 3).

    $this->log('an error has occured!', LOG_ERR);

As shown in the example, you should use PHP's LOG\_* constants.

The helper will simply fire an event named *Logger::Log*. Listeners will get two arguments, the message
and the level.

    function my_logger($message, $level) {
	    echo 'LOG: ' . $message;
    }
    Atomik::listenEvent('Logger::Log', 'my_logger');

Atomik provides a default logger which will save messages to a text file. To register this logger, set
the config key named *register\_default* to true.

The filename is defined in *filename*. You can also define from which level messages should
be saved by setting *level* to the minimum level. The default level is `LOG_WARNING` (4).

    Atomik::set('plugins.Logger', array(
        'register_default' => true,
        'filename' => 'log.txt'
    ));

Finally, you can define the template of the string that will be added to the log file in 
*message\_template*. You can use *%date%*, *%level%* and *%message%* which will be replaced 
by the appropriate string.

## Debugging

Atomik's only provides a simple helper named `debug()` which 
is an alias for `var_dump()`. However the method output can be hidden
by modifying the *atomik.debug* configuration key.

Also, if *atomik.debug* is true, the error reporting level will be set to the maximum.

    Atomik::debug($myVar);
    Atomik::set('atomik.debug', false);
    Atomik::debug($myVar2); // no output
    Atomik::debug($myVar2, true); // use true to force the output even if debug set to false

## Debug Bar

Atomik provides a plugin to easily integrate [PHP DebugBar](http://phpdebugbar.com).
The skeleton application comes with the debug bar thus you don't need to do the following steps.

You'll need to install PHP Debug Bar by yourself. If you are using the skeleton, this
can be done as follow:

 - add the requirement in the *composer.json* file (`"maximebf/debugbar": "1.*"`)
 - run `$ composer.phar update`

Activate the plugin in the config and enable debug mode:

    Atomik::add('plugins', 'DebugBar');
    Atomik::set('atomik.debug', true);

Render the debug bar in your layout:

    <html>
        <head>
            ...
            <?php if ($this['atomik.debug']) echo $this->renderDebugBarHead(); ?>
        </head>
        <body>
            ...
            <?php if ($this['atomik.debug']) echo $this->renderDebugBar(); ?>
        </body>
    </html>

Be aware that the debug bar includes jQuery and FontAwesome. The debug bar needs
at least jQuery to run properly. If you are using jQuery in your project, you can
disable debug bar's own version using:

    // this only includes FontAwesome
    // set to false to include none of them
    Atomik::set('plugins.DebugBar.include_vendors', 'css');