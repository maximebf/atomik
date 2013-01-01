
# Scripts

<div class="note">You will need the Console plugin which is bundled with Atomik</div>

The Console plugin allows Atomik to be used in a terminal. It allows other plugins to provide
custom commands and to create scripts to better administer your application.

It is built on top of [ConsoleKit](https://github.com/maximebf/ConsoleKit) which you'll
need to install.

To call your application from the command line, use the following command

	php index.php [command] [args]

Where *index.php* is Atomik's core file.

## Creating custom scripts

You can create ConsoleKit Commands inside the *app/scripts* folder (which can be changed
using the *scripts\_dir* config key).

Let's create a script in *app/scripts/CleanupDbCommand.php*

    <?php

    class CleanupDbCommand extends ConsoleKit\Command
    {
        public function execute(array $args, array $opts)
        {
            $this->writeln(sprintf("cleaning %s", $args[0]));
        }
    }

To call this script use the following command:

    $ php index.php cleanup-db dbname

## Registering commands

Instead of using files, you can manually register commands using ̀Console::register()`:

    Atomik\Console::register('cleanup-db', function($args, $opts, $console) {
        // code
    });

## Built-in commands

The plugin provides one built-in command to generate new actions and views. 
Just specify a name and the action file and the view file will be generated. 
You can generate multiple pages by separating them by a space

    php index.php generate home
    php index.php generate photos about
