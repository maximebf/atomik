
# Cache

This plugin provides a very simple caching mechanism. Request to the application are cached for a
certain amount of time.

Cached requests are stored on the disk in the folder specified in the *dir*
condifuration key. The default is *app/cache*.

The request is cached based on the REQUEST\_URI php variable.

By default, a request is cached for an hour. This can be changed using the *default\_time*
configuration key. The caching time can also be specified on a per request basis. The time is specified
in seconds.

To specify which action should be cached add them to the *requests* configuration key as a
key to the array. The associated value should be the cached time. Specify 0 to use the default time,
-1 for definitive caching or any other value.

    Atomik::set('plugins/Cache/requests', array(
	    'home' => 300, // cache the home for 5 minutes
	    'my-article' => -1 // cache my-article for un unlimited amount of time
    ));

