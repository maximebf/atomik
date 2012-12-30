
# Using plugins

## Installing a plugin

Plugins are stored in the *app/plugins* directory. 
Simply copy the plugin file or folder into this directory.

## Activating and configuring a plugin

Plugins are not automatically activated. To do so, it's needed to add an
entry in the *plugins* configuration key.

    Atomik::set('plugins', array(
        'Db',
        'Cache'
    ));

Some plugins need custom configuration which can be specified in the
plugins config key.

    Atomik::set('plugins', array(
        'Db' => array(
            'dsn'      => 'mysql:host=localhost;dbname=atomik',
            'username' => 'atomik',
            'password' => 'atomik'
        ),
        'Cache'
    ));

## Pluggable applications

Pluggable applications are a great new thing introduced in version 2.2. It allows any
plugin to act as a complete application. It can have its own actions, views, layouts,
configuration... Let's say you need a blog, just drop in the Blog plugin and you're done!

Pluggable applications are then connected to an uri. When this uri is accessed, the
application starts.

### Activating and accessing pluggable applications

As these applications are plugins, activating them is as simple as dropping them in the
*app/plugins* folder and adding their name to the *plugins* key.

The application is then available at /pluginName. In the previous example it would
be /blog.

Most pluggable applications should provide a configuration key to modify the default uri. 
In the case of the Blog plugin, let say it's *route*.

While the key is named route, the way to specify an uri here is not the same as with routes: 
it's simply an uri. We'll call it a pattern. If you want to trigger an application from /app 
the pattern would be */app*.
However, accessing /app/index would not trigger the application! To enable this you have to
use the \* wildcard at the end of the pattern so that all children also triggers the app. 
The final pattern would be */app/\**.

    Atomik::set('plugins.Blog', array(
        'route' => '/my-blog/*'
    ));

If a plugin does not have a configuration key to modify the route, this can be done by calling
`Atomik::registerPluggableApplication()` from the bootstrap file. This method takes as
first argument the plugin name and as second the pattern.

    Atomik::registerPluggableApplication('MyPluggableApp', '/my-app/*')

When available, use the plugin configuration as it could override any predefined pattern.
You can connect any application to the root of your application using */**.

### Overrides

Using pluggable applications is great! They do everything for you. However, you'll sometimes want
to customize these applications. Atomik provides an easy way to do that: overrides.

With overrides you will be able to replace any action, view, layout or helper from a pluggable
application.

Overrides are stored in *app/overrides*. In this directory, create a folder named
after the plugin. This folder can then contain the classic Atomik folders: *actions*, 
*views*, *helpers* and *layouts*.

For example, to override the *index* view from the Blog plugin, you would create the file 
*app/overrides/Blog/views/index.phtml*.

Some plugins may allow your actions and views from your *app* folder to be accessible
from the application. This is not considered overrides as plugins have priority in this case. But it
can be a nice way to add features to a pluggable application. You cannot enable this yourself, only
plugin can do it, see the plugin documentation.

