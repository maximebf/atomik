
# Developing plugins

## The plugin file

A plugin is made of one file named the same way. For example the Db plugin is in the file
*Db.php*. Plugin's file must always start with an uppercase letter.

Plugins are loaded at the beginning of a request, just after the configuration. 
The content of the file is free or it can be a class.

To build more complex plugins you can instead of a file create a folder named after your plugin.
Your PHP file goes into that folder and must be named *Plugin.php*.

When using folders, it is possible to add a sub folder named *libs* which
will automatically be added to php's include_path.

A folder must be used when creating pluggable applications.

## Configuration

As said in the "Using plugins" section, plugins can have custom configuration.
To retrieve this configuration a `$config` variable is automatically available.
It contains the array used in the configuration.

    // In the configuration file:
    
    Atomik::set('plugins', array(
        'MyPlugin' => array(
               'name' => 'Peter'
        )
    ));
    
    // In the plugin file:
    
    echo 'hello ' . $config['name'];

## Using a class

For better application design it is advice to use a class to define your plugin.
When loading a plugin, it will look for a class named like the plugin.

If this class has a static `start()` method, it will be called when the plugin is loaded
with the plugin's custom configuration as argument.

    class Db
    {
        public static function start($config)
        {
            // $config['name'] == 'Peter'
        }
    }

It is a good thing to always provide a default configuration. This can be done by merging
a default configuration array with the user's configuration.

The class can contain static methods that will be automatically registered as callback
on events. These methods have to start by "on" followed by the event name without
the double ":".

    class Db
    {
        public static onAtomikDispatchStart()
        {
            // listener for Atomik::Dispatch::Start
        }
    }

You can prevent automatic callback registration by returning false in the start method.

## Pluggable applications
        
Pluggable applications are really simple to create. Create a normal plugin using a folder. 
Create your *Plugin.php* file. Call `Atomik::registerPluggableApplication()` when
the plugin starts using the plugin name as first parameter (and eventually the pattern to 
trigger the application as the second). Create standard Atomik folders inside your plugin 
folder: *actions*, *views*, *helpers* and *layouts* and code your application normally.

    class PluggApp
    {
        public static $config = array(
            'route' => '/pluggapp/*'
        );
    
        public static start($config)
        {
            self::$config = array_merge(self::$config, $config);
            Atomik::registerPluggableApplication('PluggApp', $config['route']);
        }
    }

A pluggable application can also have a file named *Application.php* at the root of the plugin
folder. This file act the same way as the *bootstrap.php* file. It will be called before
the pluggable application is dispatched.

If Atomik detects the *Application.php* file, a *Plugin.php* file is not
necessary and the pluggable application will automatically be registered.

A pluggable application behaves as a normal Atomik application and all features are available. 
The configuration will be reseted before the dispatch occurs. These applications can provide 
their own config in their *Application.php* file like their own routes, default action... 
The *pre\_dispatch.php* and *post\_dispatch.php* files can also be used.

Note that every url will be relative to the pluggable application's root. That is to say you 
do not have to care of the route used to trigger your application. For this to work properly, 
read carefully the next section.

`Atomik::registerPluggableApplication()` as more options which are described in the
API reference.

## Assets and urls

When using the default *.htaccess* file, plugins can have an *assets* folder which
is accessible from the Web. Of course, to use this folder, the plugin must come as a folder.

You can use `Atomik::asset()` like with a normal application. However in the case of plugins, 
asset's filename will be prepended with a template defined in *atomik.plugin\_assets\_tpl*. 
The default is *app/plugins/%s/assets*. The *%s* sign will be replaced with the plugin name.

    echo Atomik::asset('css/styles.css');
    echo Atomik::pluginAsset('MyPlugin', 'css/styles.css');
    // will output app/plugins/MyPlugin/assets/css/styles.css

    Atomik::set('atomik.plugin_assets_tpl', 'plugins/%s/assets');
    echo Atomik::asset('css/styles.css');
    echo Atomik::pluginAsset('MyPlugin', 'css/styles.css');
    // will output plugins/MyPlugin/assets/css/styles.css

It is not adviced to change the plugin's assets folder name as some plugins may not work 
with your installation.

## Loading plugins programmaticaly

It is of course possible to load plugins at runtime. Atomik provides a bunch of loading methods 
so it's simpler for plugins to load plugins they depend on or to create custom plugins.

The most common method is `Atomik::loadPlugin()` which will load a plugin and use
the user plugin's configuration (from the plugins key) if one is available.

If a plugin is not available, loading it will throw an exception. To prevent that you can use
`Atomik::loadPluginIfAvailable()`.

    Atomik::loadPlugin('Db');

You can also load plugins by specifying custom configuration. This is done using 
̀Atomik::loadCustomPlugin()`.

    Atomik::loadCustomPlugin('Db', array('dbname' => 'test'));

    // load plugins from a custom directory
    Atomik::loadCustomPlugin('MyPlugin', array(), array('dirs' => '/custom/plugins/directory'));

    // using a custom plugin class name (in this case the class name will be MyPluginCustomPlugin)
    Atomik::loadCustomPlugin('MyPlugin', array(), array('classNameTemplate' => '%CustomPlugin'));

    // do not call the start() method when loading plugins
    Atomik::loadCustomPlugin('MyPlugin', array(), array('callStart' => false));

`Atomik::loadCustomPluginIfAvailable()` is also available.

Be aware that some plugins may need to listen to some specific events. If you register a plugin too late,
the events may have already occured, making the plugin malfunction.

You can check if a plugin is already loaded using `Atomik::isPluginLoaded()` or if it's
available using `Atomik::isPluginAvailable()`.

Finally, you can retreive all loaded plugins using `Atomik::getLoadedPlugins()`.

