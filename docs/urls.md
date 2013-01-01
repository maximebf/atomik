
# URLs

## Calling an action

Atomik provides a simple url mechanism. Whatever the page is, the url
must always point to Atomik script, i.e. *index.php*

The url should contain an HTTP GET parameter which specify which action to trigger.
This parameter can be modified in the configuration (using the *atomik.trigger* key) 
but its default name is *action*.

The value of the parameter must only contain the action name
without any extension. So for example if you have an *home.php*
file in the *app/actions* directory and/or an 
*home.phtml* file in the *app/views*
directory, you must use *home* as parameter to call this action.
Thus, the url should look like http://example.com/index.php?action=home.

For an action to be callable, an action file or a view file must at least exist.

If the action parameter is not found in the query string, Atomik will use the default
action defined in its configuration (the *app.default\_action* key).
The default is *index*.

For cleaner and prettier url you can use url rewriting. When using Apache, simply copy the 
code below into a *.htaccess* file in the same directory as Atomik's core file.

    RewriteEngine on
    RewriteRule ^app/plugins/(.+)/assets - [L]
    RewriteRule ^app/ - [L,F]
    RewriteRule ^vendor/ - [L,F]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?action=$1 [L,QSA]
			
This code will also prevent access to the *app* folder from the web but
allow access to *assets* directories provided by plugins.

When using url rewriting, you can access pages by directly appending the name to the base url.
Eg: http://example.com/home

Sometimes Atomik cannot detect if url rewriting is activated and `Atomik::url()`
still uses *index.php*. To prevent this, set the *atomik.url\_rewriting* 
configuration key to true.

The action that has been called can be found in the configuration key named *request\_uri*.
The base url of the website (ie. the path before the root of the site) can be found in the 
*atomik.base\_url* key.

When using pluggable applications (see the plugins chapter), the *request\_uri* key will
become relative to the pluggable application's root url. To retrieve the full uri you can use
*full\_request\_uri*.

## Routing urls

### Creating routes

It is now a common practice to use pretty urls. This can easily be done using Atomik's router.
The router maps urls to actions and allows you to extract parameters from these urls. An url and its
parameters is called a route.

Routes are defined in the *app.routes* configuration key.
The action must be specified as a parameter named *action*.

    Atomik::set('app.routes', array(
       'user/add' => array(
           'action' => 'user_add'
       )
    ));

As you see, the route is defined as the array key and its parameters are defined in
the sub array. You can add an unlimited number of parameters to the route. There
must be at least the *action* parameter for the route to be valid.

The real magic of the routes is the possibility to assign a parameter value with
a segment of the uri. This is done by specifying a parameter name prefixed with *:*
inside an uri segment (ie. between slashes).

Parameters defined as uri segments can be optional if they are also defined in the
parameters list.

    Atomik::set('app.routes', array(
       'archives/:year/:month' => array(
           'action' => 'archives',
           'month' => 'all'
       )
    ));

In this route, the month parameter is optional but not the year. Thus, possible urls are
http://example.com/archives/2008 or http://example.com/archives/2008/02.
In these case the year parameter will have the *2008* value and the month parameter in the 
second example will have *02* as value.

Note that routes are matched in reverse order.

Regexp routes allow you to use regular expressions to match an uri to a route. A regexp route is
defined the same way as classical ones but the route is replaced by the regexp which must use
the pound sign # as its delimiter.

The uri to match will always be relative (so do not use a slash at the start). To specify parameters
inside the route, you must use named subpatterns (see http://php.net/manual/en/regexp.reference.subpatterns.php).

    Atomik::set('app.routes', array(
       '#archives/(?P<year>[0-9]{4})/(?P<month>[0-9]{2})#' => array(
           'action' => 'archives',
           'month' => 'all'
       )
    ));

To provide a name to a route, simply define the *@name* parameter. Both route
types (classical and regexp) can be named. Route naming will be useful when 
using `Atomik::url()`.

    Atomik::set('app.routes', array(
       'archives/:year/:month' => array(
           '@name' => 'archives',
           'action' => 'archives',
           'month' => 'all'
       )
    ));

### Retrieving route parameters

Once the routing process is done, a configuration key named *request* is available. It contains
an associative array with parameters and their value.
    
    $params = Atomik::get('request');
    $year = Atomik::get('request.year');

    // inside actions and views:

    $params = $this['request'];
    $year = $this['request.year'];

## File extensions

Since version 2.2, Atomik can handle file extensions in urls. The file extension is optional. 
You can force the file extension to be present by setting *app.force\_uri\_extension* to true.

By default the file extension is the view context. View contexts are discussed in a later chapter.
The default view context if no extension is defined is *html*. This can be changed
in *app.views.default\_context*.

    http://example.com/home         =>	action=home format=html (won't work if app.force_uri_extension is set to true)
    http://example.com/home.html    =>	action=home format=html
    http://example.com/home.xml     =>	action=home format=xml
    http://example.com/home.foo     =>	action=home format=foo

Routes also support file extensions. You can specify a specific extension in your route. The extension
can also be a parameter. In this case, if you specify a default value for it, the extension
won't be mandatory in the url.

    Atomik::set('app.routes', array(
       ':category/:article..:format' => array(
       	    'action' => 'article'
       ),
       'home..html' => array(
            'action' => 'home',
            'format' => 'html'
       )
    ));

(Note that the dot must be escaped in keys using a double dot)

The format parameter is not automatically added in custom routes. If not specified
it will default to the value of *app.views.default\_context*.

## Building urls

Directly writing url into your code can lead to problems. When using a layout for example, it is hard to know the
relative location of the current view, to include stylesheets for example. Some urls also needs lots of concatanation
when using parameters and this can make the code less readable.

`Atomik::url()` tries to resolve those problems by providing three things:

 - Prepend the base url
 - Do not use *index.php* in the url if url rewriting is enabled (or use it if not)
 - Handle url parameters

The method works best with relative or absolute urls. It can however also works with full urls. 
In this case, the two first points won't be applied.

If null is used as the url, the current action will be used. Named routes can be used using the route
name prepended with the @ sign.

    $url = Atomik::url('home'); // /index.php?action=home if no url rewriting or /home otherwise
    $url = Atomik::url('/user/dashboard'); // /user/dashboard
    $url = Atomik::url('http://example.com'); // http://example.com
    $url = Atomik::url('@my_route'); // will use the route named my_route

You can add GET parameters to the url using an array as the second argument. You can
re-use the current request parameters by using true instead of an array. If you want 
the current parameters as well as new ones, use an array and add *\_\_merge\_GET*
as value.

    $url = Atomik::url('archives', array('year' => 2008)); // /index.php?action=archives&year=2008 if no url rewriting or /archives?year=2008 otherwise
    $url = Atomik::url('archives?year=2008', array('month' => 02)); // /archives?year=2008&month=02

    // if the page has been called with ?year=2008

    $url = Atomik::url('archives', true); // /archives?year=2008
    $url = Atomik::url('archives', array('__merge_GET', 'month' => 02)); // /archives?year=2008&month=02

The method also allows you to use embedded parameters. These are parameters in the uri (like in classical routes). 
The name of the parameter must be prepended with *:*.

    $url = Atomik::url('archives/:year', array('year' => 2008)); // /index.php?action=archives/2008 if no url rewriting or /archives/2008 otherwise
    $url = Atomik::url('archives/:year', array('year' => 2008, 'month' => 02)); // /archives/2008?month=02

Using named routes:

    $url = Atomik::url('@archives', array('year' => 2008)); // /index.php?action=archives/2008 if no url rewriting or /archives/2008 otherwise
    $url = Atomik::url('@archives', array('year' => 2008, 'month' => 02)); // /archives/2008?month=02

When creating urls that point to resources you'll never want them to have the 
*index.php* part in them. To prevent that you can use the `Atomik::asset()` method. 
It works exactly the same as `Atomik::url()` but will never use *index.php* in the url.

`Atomik::url()` is relative to the application context. For example, if this method is used 
inside views from your application, generated urls will be relative to your application's base url. 
However, if it is used inside a pluggable application (or plugin) it will be relative to the 
plugin's base url. It allows this method to be used everywhere while ensuring that the urls 
are relative to the correct base url.

It is however possible to use `Atomik::appUrl()` to always generate urls relative to your application's
base url and `Atomik::pluginUrl()` to always generate urls relative to a specific plugin. The plugin
name must be provided as the first argument of the latter, remaining arguments being the same as 
with `Atomik::url()`.

    $url = Atomik::appUrl('home'); // always /home
    $url = Atomik::pluginUrl('my_plugin', 'home'); // /my_plugin_base_url/home
    $url = Atomik::url('home'); // either /home or /my_plugin_base_url/home depending on the context

For assets, it also exists `Atomik::appAsset()` and `Atomik::pluginAsset()`.
The latter, like `Atomik::pluginUrl()`, needs a plugin name as the first argument.

Inside views, you should call any Atomik methods through `$this`:

    $url = $this->url('archives', array('year' => 2008));
