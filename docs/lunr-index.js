
var index = lunr(function () {
    this.field('body');
    this.ref('url');
});

var documentTitles = {};



documentTitles["/docs/introduction.html#introduction"] = "Introduction";
index.add({
    url: "/docs/introduction.html#introduction",
    title: "Introduction",
    body: "# Introduction  Atomik is an open-source micro framework for PHP 5.3+. Atomik is build with the KISS (Keep It Simple Stupid) and DRY (Don't Repeat Yourself) principles in mind as well as speed and security. It is also an ideal introduction for  beginners to the world of web development frameworks.   Here's a list of some Atomik features:   - Very small footprint  - Open Source (MIT License)  - Very simple to use  - Easy to use router for pretty URLs  - Powerful templating: helpers, layouts, content types...  - Flash messages  - Errors handling  - Intuitive architecture for beginners  - Respect good programming practices  - Plugins and pluggable applications  - Highly extensible  - Uses existing libraries  The manual is licensed under the Creative Commons Attribution license.  "
});

documentTitles["/docs/introduction.html#requirements"] = "Requirements";
index.add({
    url: "/docs/introduction.html#requirements",
    title: "Requirements",
    body: "## Requirements   - HTTP Server. Apache with mod_rewrite is a good choice.  - PHP 5.3 or greater  "
});

documentTitles["/docs/introduction.html#installation"] = "Installation";
index.add({
    url: "/docs/introduction.html#installation",
    title: "Installation",
    body: "## Installation  The best way to install Atomik is using [Composer](http://getcomposer.org) and the [Atomik Skeleton Application](https://github.com/maximebf/atomik-skeleton).  The skeleton is a base Atomik application with a basic directory structure which  let you start building your project in a matter of seconds!      $ php composer.phar create-project atomik/skeleton /path/to/my/install/folder  Navigate to your website in your browser (ie. &lt;http://localhost&gt;) where you should see a congratulation message.  If you're not comfortable using Composer, you can download the skeleton as a zip archive from [here](https://github.com/maximebf/atomik/releases).  If you want to activate pretty URLs under Apache, rename the *htaccess.example* file to *.htaccess*.  "
});

documentTitles["/docs/introduction.html#about-the-skeleton"] = "About the skeleton";
index.add({
    url: "/docs/introduction.html#about-the-skeleton",
    title: "About the skeleton",
    body: "## About the skeleton  Atomik Skeleton Application is a base Atomik application with a basic directory structure  which let you start building your project in a matter of seconds!  It includes [Twitter Bootstrap](http://getbootstrap.com/), [jQuery](http://jquery.com) and [PHP DebugBar](http://phpdebugbar.com).  The skeleton comes with debug mode activated. Don't forget to change *atomik.debug* to `false` in the config file when you switch to production mode.  "
});

documentTitles["/docs/introduction.html#directory-structure"] = "Directory structure";
index.add({
    url: "/docs/introduction.html#directory-structure",
    title: "Directory structure",
    body: "## Directory structure  Your application per se goes into the *app* directory. Actions and views have their own  directories under *app/actions* and *app/views*.  Helpers and plugins are located in *app/helpers* and *app/plugins*. *app/includes* will be added to PHP's include path.  The configuration is stored in *app/config.php*.  When using the provided Apache *.htaccess* file, the *app* directory is not  accessible from the web.  If you do not use the provided *.htaccess* file, do not forget to allow *assets* folders in plugins directories. Such a path can look like *app/plugins/MyPlugin/assets*.  "
});

documentTitles["/docs/introduction.html#advanced-installation"] = "Advanced installation";
index.add({
    url: "/docs/introduction.html#advanced-installation",
    title: "Advanced installation",
    body: "## Advanced installation  It is also possible to install and configure Atomik from scratch using Composer. In the directory of your project, create a *composer.json* file with the following requirements:      {         \&quot;require\&quot;: {             \&quot;atomik/atomik\&quot;: \&quot;&gt;=3.0.0\&quot;         }     }  Run composer from this directory to install atomik:      $ php composer.phar install  Atomik will be installed in the *vendor* directory, along any other dependencies you add to the *require* hash in your *composer.json* file.  Create the directory structure. Create the *index.php* file as follow:      &lt;?php     require 'vendor/autoload.php';     Atomik::run();  `Atomik::run()` takes as first argument the root directory of your app. The default value is '.' which is the current directory.  Remember that in a production environment, it is always better to remove the  application files from the webroot, thus usually using a root directory one level above the publicly accessible one (ie. using `Atomik::run('..')`). "
});



documentTitles["/docs/introduction.html#introduction"] = "Introduction";
index.add({
    url: "/docs/introduction.html#introduction",
    title: "Introduction",
    body: "# Introduction  Atomik is an open-source micro framework for PHP 5.3+. Atomik is build with the KISS (Keep It Simple Stupid) and DRY (Don't Repeat Yourself) principles in mind as well as speed and security. It is also an ideal introduction for  beginners to the world of web development frameworks.   Here's a list of some Atomik features:   - Very small footprint  - Open Source (MIT License)  - Very simple to use  - Easy to use router for pretty URLs  - Powerful templating: helpers, layouts, content types...  - Flash messages  - Errors handling  - Intuitive architecture for beginners  - Respect good programming practices  - Plugins and pluggable applications  - Highly extensible  - Uses existing libraries  The manual is licensed under the Creative Commons Attribution license.  "
});

documentTitles["/docs/introduction.html#requirements"] = "Requirements";
index.add({
    url: "/docs/introduction.html#requirements",
    title: "Requirements",
    body: "## Requirements   - HTTP Server. Apache with mod_rewrite is a good choice.  - PHP 5.3 or greater  "
});

documentTitles["/docs/introduction.html#installation"] = "Installation";
index.add({
    url: "/docs/introduction.html#installation",
    title: "Installation",
    body: "## Installation  The best way to install Atomik is using [Composer](http://getcomposer.org) and the [Atomik Skeleton Application](https://github.com/maximebf/atomik-skeleton).  The skeleton is a base Atomik application with a basic directory structure which  let you start building your project in a matter of seconds!      $ php composer.phar create-project atomik/skeleton /path/to/my/install/folder  Navigate to your website in your browser (ie. &lt;http://localhost&gt;) where you should see a congratulation message.  If you're not comfortable using Composer, you can download the skeleton as a zip archive from [here](https://github.com/maximebf/atomik/releases).  If you want to activate pretty URLs under Apache, rename the *htaccess.example* file to *.htaccess*.  "
});

documentTitles["/docs/introduction.html#about-the-skeleton"] = "About the skeleton";
index.add({
    url: "/docs/introduction.html#about-the-skeleton",
    title: "About the skeleton",
    body: "## About the skeleton  Atomik Skeleton Application is a base Atomik application with a basic directory structure  which let you start building your project in a matter of seconds!  It includes [Twitter Bootstrap](http://getbootstrap.com/), [jQuery](http://jquery.com) and [PHP DebugBar](http://phpdebugbar.com).  The skeleton comes with debug mode activated. Don't forget to change *atomik.debug* to `false` in the config file when you switch to production mode.  "
});

documentTitles["/docs/introduction.html#directory-structure"] = "Directory structure";
index.add({
    url: "/docs/introduction.html#directory-structure",
    title: "Directory structure",
    body: "## Directory structure  Your application per se goes into the *app* directory. Actions and views have their own  directories under *app/actions* and *app/views*.  Helpers and plugins are located in *app/helpers* and *app/plugins*. *app/includes* will be added to PHP's include path.  The configuration is stored in *app/config.php*.  When using the provided Apache *.htaccess* file, the *app* directory is not  accessible from the web.  If you do not use the provided *.htaccess* file, do not forget to allow *assets* folders in plugins directories. Such a path can look like *app/plugins/MyPlugin/assets*.  "
});

documentTitles["/docs/introduction.html#advanced-installation"] = "Advanced installation";
index.add({
    url: "/docs/introduction.html#advanced-installation",
    title: "Advanced installation",
    body: "## Advanced installation  It is also possible to install and configure Atomik from scratch using Composer. In the directory of your project, create a *composer.json* file with the following requirements:      {         \&quot;require\&quot;: {             \&quot;atomik/atomik\&quot;: \&quot;&gt;=3.0.0\&quot;         }     }  Run composer from this directory to install atomik:      $ php composer.phar install  Atomik will be installed in the *vendor* directory, along any other dependencies you add to the *require* hash in your *composer.json* file.  Create the directory structure. Create the *index.php* file as follow:      &lt;?php     require 'vendor/autoload.php';     Atomik::run();  `Atomik::run()` takes as first argument the root directory of your app. The default value is '.' which is the current directory.  Remember that in a production environment, it is always better to remove the  application files from the webroot, thus usually using a root directory one level above the publicly accessible one (ie. using `Atomik::run('..')`). "
});



documentTitles["/docs/get-started.html#get-started"] = "Get started";
index.add({
    url: "/docs/get-started.html#get-started",
    title: "Get started",
    body: "# Get started  In this tutorial, you will learn how to create a simple blogging application in a few minutes. Our blog will list posts and allow you to create new ones which will be stored in a database. We will use a few plugins in the process.  Requirements:   - PHP 5.3  - A webserver (eg. Apache)  - [Sqlite](http://www.sqlite.org/)  Install Atomik using the skeleton app as explained in the Installation section. Atomik provides a skeleton application to get you started quickly.   All future paths reference in this tutorial will be relative to your installation folder. This path should be accessible from a web browser.  "
});

documentTitles["/docs/get-started.html#the-database"] = "The database";
index.add({
    url: "/docs/get-started.html#the-database",
    title: "The database",
    body: "## The database  As said before, our blog application will need a database. To keep it simple, we'll use Sqlite which is a very simple database engine that stores data into a single file.  Here is the database schema for this tutorial. Save it in *schema.sql*:      CREATE TABLE posts (         id       INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,         title    VARCHAR(200) NOT NULL,         content  TEXT NOT NULL     );  Now let's create the database file:      $ sqlite3 -init schema.sql example.db     sqlite&gt; .exit  Will now need to configure the Db plugin so it can access the database. This plugin simply creates a new `PDO` instance to which it adds some useful methods.  Open the *app/config.php* file. You'll see that we use the `Atomik::set()` method to define configuration options. This method is one of many that allow you to manipulate the global store, a place where you can store data shared accross your app.  In the plugins section, (line 5) add the following lines:      // ...     'plugins' =&gt; array(         // ...         'Db' =&gt; array(             'dsn' =&gt; 'sqlite:example.db'         )     ),     // ...  The Db plugins creates a `PDO` instance accessible through the *db* key in the global store. This is all we need to connect to the database.  "
});

documentTitles["/docs/get-started.html#listing-posts"] = "Listing posts";
index.add({
    url: "/docs/get-started.html#listing-posts",
    title: "Listing posts",
    body: "## Listing posts      A page in Atomik is made of two files: the first one is dedicated to the business logic,  it is called an action. The second one is called a view and holds the presentation code,  which in most cases is HTML.  Actions are located in the *app/actions* folder and views in the *app/views* folder. For example, for a page named home, we would need a *home.php* file in the actions folder and/or a *home.phtml* file in the views folder.  &lt;div class=\&quot;note\&quot;&gt;Both files are not mandatory when creating a page. At least one of them must exists.&lt;/div&gt;  The default page is named *index*. We will list posts on this one. We'll first need to retrieve  posts from the database. In the action file (*app/actions/index.php*), add the following lines:      &lt;?php     $posts = $this['db']-&gt;select('posts');  Here we access the global store through `$this` which represent the `Atomik` object. The `select()` function from the Db plugin creates an SQL SELECT statement.  Variables defined in the action are automatically available in the view. Thus, we can iterate  through the `$posts` variable to list our posts.      &lt;h1&gt;Blog&lt;/h1&gt;     &lt;ul&gt;        &lt;?php foreach ($posts as $post): ?&gt;             &lt;li&gt;&lt;?= $this-&gt;escape($post['title']) ?&gt;&lt;/li&gt;         &lt;?php endforeach; ?&gt;     &lt;/ul&gt;     &lt;a href=\&quot;&lt;?= $this-&gt;url('add') ?&gt;\&quot;&gt;Add a new post&lt;/a&gt;  (Atomik automatically converts short tags to the long form)  It is a good practice to escape content before outputting it which is the goal of the `escape()` helper. The `url()` helper generates urls from action names.  Helpers are utility functions available through `$this` in actions and views. Atomik comes bundled with a few of them to help you get started quickly.  Now, navigate to [http://localhost](http://localhost). Don't worry if nothing shows up, we havn't created any post yet.  "
});

documentTitles["/docs/get-started.html#creating-new-posts"] = "Creating new posts";
index.add({
    url: "/docs/get-started.html#creating-new-posts",
    title: "Creating new posts",
    body: "## Creating new posts  We're going to create a new page called *add*. Let's start by creating the view (*app/views/add.phtml*).      &lt;h1&gt;New post&lt;/h1&gt;     &lt;?= $form = $this-&gt;form($this-&gt;url('add')) ?&gt;       &lt;p&gt;         &lt;label for=\&quot;title\&quot;&gt;Title:&lt;/label&gt;         &lt;?= $form-&gt;input('title') ?&gt;       &lt;/p&gt;       &lt;p&gt;         &lt;label for=\&quot;content\&quot;&gt;Content:&lt;/label&gt;         &lt;?= $form-&gt;textarea('content') ?&gt;       &lt;/p&gt;       &lt;?= $form-&gt;buttons('Add post') ?&gt;     &lt;/form&gt;  In this view, we've used the `form()` helper which returns an object with functions to easily render common HTML inputs.  Our action should only be executed when there's POST data. Atomik allows you to create action files for specific HTTP methods. To do so, add a suffix to the action name with a dot followed  by the HTTP method in lower case. Our action file will thus be named *app/actions/add.post.php*.  As a side note if you're new to web programming, the HTTP protocol defines multiple verbs (or methods). Each one carry a meaning and eventually some data. The most common one is GET. It is the method your browser uses to request a page. To send some data from the browser to the server, the browser will perform a POST request.  The first thing we need to do is filter the data. This is always an important step when dealing with data from an outside source as it is a common vectore of attack. We're going to use the `filter()` helper.  This helper is built on top of PHP's [filter extension](http://fr2.php.net/manual/en/book.filter.php).  This method works in two ways: it can filter a single value or it can filter an entire array. We're obviously going to use the latter as we're going to filter the `$_POST` array.  To filter an array, the method needs a set of rules: an array listing the requirements for each fields in the input data. For each field, we can use a filter and define if it's required. The default filter is to sanitize strings (`FILTER_SANITIZE_STRING`) and we'll use that one. We're only going to set fields as required.      $fields = array(         'title' =&gt; array('required' =&gt; true),         'content' =&gt; array('required' =&gt; true)     );  If the validation fails, the method will return false. In this case it will generate some error  messages stored in *app.filters.messages*. We can then use the `flash()` helper to display them  to the user.      if (($data = $this-&gt;filter($_POST, $fields)) === false) {         $this-&gt;flash($this['app.filters.messages'], 'error');         return;     }  Now that our data has been validated we're going to insert them in the database. We'll use the `insert()` method from the Db plugin.      $this['db']-&gt;insert('posts', $data);  Finally, we'll add a flash message announcing that the operation has been successful. We'll also redirect the user to the index page.      $this-&gt;flash('Post successfully added!', 'success');     $this-&gt;redirect('index');  Below is the complete action:      &lt;?php          $fields = array(         'title' =&gt; array('required' =&gt; true),         'content' =&gt; array('required' =&gt; true)     );          if (($data = $this-&gt;filter($_POST, $fields)) === false) {         $this-&gt;flash($this['app.filters.messages'], 'error');         return;     }          $this['db']-&gt;insert('posts', $data);          $this-&gt;flash('Post successfully added!', 'success');     $this-&gt;redirect('index');   "
});

documentTitles["/docs/get-started.html#viewing-a-post"] = "Viewing a post";
index.add({
    url: "/docs/get-started.html#viewing-a-post",
    title: "Viewing a post",
    body: "## Viewing a post  We are now going to create a page named *view* to show a single post.  The page will need a request parameter named *id* which must contain the id of a post. Let's create the action file (*app/actions/view.php*) with these simple lines:      &lt;?php          if (!isset($this['request.id'])) {         $this-&gt;flash('Missing id parameter', 'error');         $this-&gt;redirect('index');     }          $post = $this['db']-&gt;selectOne('posts', array('id' =&gt; $this['request.id']));  First we check if the id parameter is set. The *request* array from the global store contains all the parameters given through the URL and the `$_GET` array.  The view (*app/views/view.phtml*) is also very simple:      &lt;h1&gt;&lt;?= $this-&gt;escape($post['title']) ?&gt;&lt;/h1&gt;     &lt;p&gt;         Published the &lt;?= $post['publish_date'] ?&gt;     &lt;/p&gt;     &lt;p&gt;         &lt;?= $this-&gt;escape($post['content']) ?&gt;     &lt;/p&gt;  Finally, we're going to modify the index view to add a link on post titles. Replace the line where the post title is echoed with:      &lt;li&gt;         &lt;a href=\&quot;&lt;?= $this-&gt;url('view', array('id' =&gt; $post['id'])) ?&gt;\&quot;&gt;             &lt;?= $this-&gt;escape($post['title']) ?&gt;         &lt;/a&gt;     &lt;/li&gt; "
});



documentTitles["/docs/global-store.html#the-global-store"] = "The global store";
index.add({
    url: "/docs/global-store.html#the-global-store",
    title: "The global store",
    body: "# The global store  Atomik provides a global store where anything can be saved for the time of a request. This global store acts like an associative array with key/value pairs. It's mainly used to store the configuration.  "
});

documentTitles["/docs/global-store.html#accessing-the-store"] = "Accessing the store";
index.add({
    url: "/docs/global-store.html#accessing-the-store",
    title: "Accessing the store",
    body: "## Accessing the store  Accessors are methods provided by the Atomik class that allow you to  access the global store. They are six of them: *get*, *set*, *add*, *prepend*, *has* and *delete*.  The `get()` method allows you to retrieve the value associated to the key passed as first argument. If a second argument is  specified it will be use as a default value in the case where the key is not found.      echo Atomik::get('key');     echo Atomik::get('keyThatDoesntExist', 'defaultValue');  There's also a `Atomik::getRef()` method to obtain a reference to the value. However this method do not have a default value parameter and it will return null if the key is not found.  The `set()` method allows you to define a key and its associated value. It will overwrite any existing value.  This accessor can also take an array as argument to set multiple key/value pairs at once. This array will be merged with the store.      Atomik::set('key', 'value');      Atomik::set(array( 	    'key1' =&gt; 'value1', 	    'key2' =&gt; 'value2'     ));  The `add()` method works like the `set()` method but rather than replacing values when they already exists, adds them. For example if the key points to an array, the value will be added to this array as a new item. If the key points to a value which is not an array, it will be transformed to one.  `prepend()` is exactly the same but adds the value at the beginning of the array.      Atomik::set('key1', array('item1'));     Atomik::add('key1', 'item2');     Atomik::add('key1', array('item3', 'item4'));     $array = Atomik::get('key1'); // array('item1', 'item2', 'item3', 'item4')  The `has()` and `delete()` methods only take a key as argument. The first one checks if the key exists and the second deletes the key and its value. The method also returns the value which had the deleted key or false if the key didn't exist.      if (Atomik::has('key')) { 	    Atomik::delete('key');     }  "
});

documentTitles["/docs/global-store.html#nested-arrays"] = "Nested arrays";
index.add({
    url: "/docs/global-store.html#nested-arrays",
    title: "Nested arrays",
    body: "## Nested arrays  Dots can be used to access nested arrays. Each segment in the key has to point to a nested array unless it's the last one.      Atomik::set('users', array( 	    'paul' =&gt; array( 		    'id' =&gt; 1, 		    'age' =&gt; 20 	    ), 	    'peter' =&gt; array( 		    'id' =&gt; 2, 		    'age' =&gt; 33 	    )     ));          $paul = Atomik::get('users.paul'); // returns an array     $paulAge = Atomik::get('users.paul.age'); // returns 20     $peterId = Atomik::get('users.peter.id'); // returns 2          Atomik::set('users.sofia', array( 	    'id' =&gt; 3, 	    'age' =&gt; 25     ));          $sofiaAge = Atomik::get('users.sofia.age');  You can also use paths in sub arrays when setting some values.      Atomik::set(array( 	    'users' =&gt; array( 		    'paul.age' =&gt; 22, 		    'paul.friends' =&gt; array( 			    'peter.age' =&gt; 20 		    ) 	    )     ));          echo Atomik::get('users.paul.age'); // 22          var_export(Atomik::get('users.paul.friends'));     array( 	    'peter' =&gt; array( 		    'age' =&gt; 20 	    )     )  `Atomik::dimensionizeArray()` can be used to *dimensionize* any array.  When using an array, be aware that it will be *dimensionized* before being merged. This is done using `Atomik::dimensionizeArray()`. It can be avoided using false as the third argument of `set()`.  To avoid a key to be dimensionized, you can escape dots using double dots:      Atomik::set('routes', array(         '/users..json' =&gt; array('action' =&gt; 'users', 'format' =&gt; 'json')     ));  "
});

documentTitles["/docs/global-store.html#accessing-the-global-store-from-actions-and-views"] = "Accessing the global store from actions and views";
index.add({
    url: "/docs/global-store.html#accessing-the-global-store-from-actions-and-views",
    title: "Accessing the global store from actions and views",
    body: "## Accessing the global store from actions and views  You should always access the global store through `$this` from actions and views. Get values using the same syntax as with arrays:      $this['users'] = array(         'paul' =&gt; array(             'id' =&gt; 1,             'age' =&gt; 20         )     );     if (isset($this['users.paul'])) {         $age = $this['users.paul.age'];         unset($this['users.paul']);     }  Accessors are also available as methods of ̀$this`:      $age = $this-&gt;get('users.paul.age', 21);  "
});

documentTitles["/docs/global-store.html#using-accessors-with-any-array"] = "Using accessors with any array";
index.add({
    url: "/docs/global-store.html#using-accessors-with-any-array",
    title: "Using accessors with any array",
    body: "## Using accessors with any array  Accessors can be used with any array. You need to pass as argument an array (the position of the argument depends on the method). See the API guide for  more information. Still, here's an example:      $array = array();     Atomik::set('key', 'value', $array);     echo Atomik::get('key', null, $array); "
});



documentTitles["/docs/configuration.html#configuration"] = "Configuration";
index.add({
    url: "/docs/configuration.html#configuration",
    title: "Configuration",
    body: "# Configuration  "
});

documentTitles["/docs/configuration.html#configuration-file"] = "Configuration file";
index.add({
    url: "/docs/configuration.html#configuration-file",
    title: "Configuration file",
    body: "## Configuration file  Atomik provides a default configuration for everything (to fullfill the convention over configuration principle). However, you can override it and provide plugin's configuration or even your own.  Three file formats are available for the configuration: PHP (which is the default), INI or JSON. The format will be chosen depending on the file extension (either php, ini or json - in lower case).  The file is by default located in the app directory and named *config*. This can be changed in *atomik.files.config*. Do NOT specify the extension.  When using PHP, the script can return an array that will be use with `Atomik::set()`. You can also directly use accessors in the file.          return array(         'my_key' =&gt; 'my value',         'plugins' =&gt; array(             'Db' =&gt; array(                 'dsn' =&gt; 'mysql:host=localhost',                 'username' =&gt; 'root'             )         ),         'atomik.files' =&gt; array(             'pre_dispatch' = 'pre.php'             'post_dispatch' = 'post.php'         )     );  When using INI, you can use dots in keys to specify multi-dimensional keys. You can use dots in JSON keys or child objects.  INI categories will be treated as parent keys and also dimensionized.          my_key = my value      [plugins]     Db.dsn = mysql:host=localhost     Db.username = root      [atomik.files]     pre_dispatch = pre.php     post_dispatch = post.php  When using JSON, the data must be wrapped in an object.      { 	    \&quot;my_key\&quot;: \&quot;my value\&quot;,  	    \&quot;plugins\&quot; : { 		    \&quot;Db\&quot;: { 			    \&quot;dsn\&quot;: \&quot;mysql:host=localhost\&quot;, 			    \&quot;username\&quot;: \&quot;root\&quot; 		    } 	    }, 	 	    \&quot;atomik.files\&quot;: { 		    \&quot;pre_dispatch\&quot;: \&quot;pre.php\&quot;, 		    \&quot;post_dispatch\&quot;: \&quot;post.php\&quot; 	    }     }  "
});

documentTitles["/docs/configuration.html#bootsrapping"] = "Bootsrapping";
index.add({
    url: "/docs/configuration.html#bootsrapping",
    title: "Bootsrapping",
    body: "## Bootsrapping  Once the configuration is loaded, Atomik will setup the environment and load plugins. Once ready, it will try to load a bootstrap file. It can be used to prepare the application, load additional libraries or plugins...  The file must be named *bootstrap.php* and located in the *app* directory. In this file, you can use accessors (the `set()` method of course) to define configuration keys.  The name of this file can be changed using the *atomik.files.bootstrap* configuration key.  "
});

documentTitles["/docs/configuration.html#custom-directory-structure"] = "Custom directory structure";
index.add({
    url: "/docs/configuration.html#custom-directory-structure",
    title: "Custom directory structure",
    body: "## Custom directory structure  The directory structure can be customized by modifying entries in the *atomik.dirs* configuration key.  Each keys in the *dirs* array represent a type of directory. Their value can be a string for a single path or an array for mutliple paths.  If the path is relative, it must be relative to the root directory of your application.  For the *plugins*, *helpers* and *includes* keys, directories can be associated to a namespace. Let's say you have the Doctrine library in /usr/share/php/doctrine, the sources being in lib/Doctrine:      Atomik::add('atomik.dirs.includes', array('Doctrine' =&gt; '/usr/share/php/doctrine/lib/Doctrine'));  "
});

documentTitles["/docs/configuration.html#pre-and-post-dispatch-files"] = "Pre and post dispatch files";
index.add({
    url: "/docs/configuration.html#pre-and-post-dispatch-files",
    title: "Pre and post dispatch files",
    body: "## Pre and post dispatch files  Atomik allows you to create two files: *pre\_dispatch.php* and  *post\_dispatch.php* in the *app* directory. These files will be called respectively before and after the dispatch process.  Their filename can be changed using the *atomik.files.pre\_dispatch* and *atomik.files.post\_dispatch* configuration keys. "
});



documentTitles["/docs/urls.html#urls"] = "URLs";
index.add({
    url: "/docs/urls.html#urls",
    title: "URLs",
    body: "# URLs  "
});

documentTitles["/docs/urls.html#calling-an-action"] = "Calling an action";
index.add({
    url: "/docs/urls.html#calling-an-action",
    title: "Calling an action",
    body: "## Calling an action  Atomik provides a simple url mechanism. Whatever the page is, the url must always point to Atomik script, i.e. *index.php*  The url should contain an HTTP GET parameter which specify which action to trigger. This parameter can be modified in the configuration (using the *atomik.trigger* key)  but its default name is *action*.  The value of the parameter must only contain the action name without any extension. So for example if you have an *home.php* file in the *app/actions* directory and/or an  *home.phtml* file in the *app/views* directory, you must use *home* as parameter to call this action. Thus, the url should look like http://example.com/index.php?action=home.  For an action to be callable, an action file or a view file must at least exist.  If the action parameter is not found in the query string, Atomik will use the default action defined in its configuration (the *app.default\_action* key). The default is *index*.  For cleaner and prettier url you can use url rewriting. When using Apache, simply copy the  code below into a *.htaccess* file in the same directory as Atomik's core file.      RewriteEngine on     # Allow access to assets folder     RewriteRule ^app/plugins/(.+)/assets - [L]     RewriteRule ^vendor/maximebf/debugbar/src/DebugBar/Resources - [L]     # forbid access to files and folders under app and vendor     RewriteRule ^app/.*$ - [L,F]     RewriteRule ^vendor/.*$ - [L,F]     # rewrite to index.php     RewriteCond %{REQUEST_FILENAME} !-f     RewriteCond %{REQUEST_FILENAME} !-d     RewriteRule ^(.*)$ index.php?action=$1 [L,QSA] 			 This code will also prevent access to the *app* folder from the web but allow access to *assets* directories provided by plugins.  When using url rewriting, you can access pages by directly appending the name to the base url. Eg: http://example.com/home  Sometimes Atomik cannot detect if url rewriting is activated and `Atomik::url()` still uses *index.php*. To prevent this, set the *atomik.url\_rewriting*  configuration key to true.  The action that has been called can be found in the configuration key named *request\_uri*. The base url of the website (ie. the path before the root of the site) can be found in the  *atomik.base\_url* key.  When using pluggable applications (see the plugins chapter), the *request\_uri* key will become relative to the pluggable application's root url. To retrieve the full uri you can use *full\_request\_uri*.  "
});

documentTitles["/docs/urls.html#routing-urls"] = "Routing urls";
index.add({
    url: "/docs/urls.html#routing-urls",
    title: "Routing urls",
    body: "## Routing urls  "
});

documentTitles["/docs/urls.html#creating-routes"] = "Creating routes";
index.add({
    url: "/docs/urls.html#creating-routes",
    title: "Creating routes",
    body: "### Creating routes  It is now a common practice to use pretty urls. This can easily be done using Atomik's router. The router maps urls to actions and allows you to extract parameters from these urls. An url and its parameters is called a route.  Routes are defined in the *app.routes* configuration key. The action must be specified as a parameter named *action*.      Atomik::set('app.routes', array(        'user/add' =&gt; array(            'action' =&gt; 'user_add'        )     ));  As you see, the route is defined as the array key and its parameters are defined in the sub array. You can add an unlimited number of parameters to the route. There must be at least the *action* parameter for the route to be valid.  The real magic of the routes is the possibility to assign a parameter value with a segment of the uri. This is done by specifying a parameter name prefixed with *:* inside an uri segment (ie. between slashes).  Parameters defined as uri segments can be optional if they are also defined in the parameters list.      Atomik::set('app.routes', array(        'archives/:year/:month' =&gt; array(            'action' =&gt; 'archives',            'month' =&gt; 'all'        )     ));  In this route, the month parameter is optional but not the year. Thus, possible urls are http://example.com/archives/2008 or http://example.com/archives/2008/02. In these case the year parameter will have the *2008* value and the month parameter in the  second example will have *02* as value.  Note that routes are matched in reverse order.  Regexp routes allow you to use regular expressions to match an uri to a route. A regexp route is defined the same way as classical ones but the route is replaced by the regexp which must use the pound sign # as its delimiter.  The uri to match will always be relative (so do not use a slash at the start). To specify parameters inside the route, you must use named subpatterns (see http://php.net/manual/en/regexp.reference.subpatterns.php).      Atomik::set('app.routes', array(        '#archives/(?P&lt;year&gt;[0-9]{4})/(?P&lt;month&gt;[0-9]{2})#' =&gt; array(            'action' =&gt; 'archives',            'month' =&gt; 'all'        )     ));  To provide a name to a route, simply define the *@name* parameter. Both route types (classical and regexp) can be named. Route naming will be useful when  using `Atomik::url()`.      Atomik::set('app.routes', array(        'archives/:year/:month' =&gt; array(            '@name' =&gt; 'archives',            'action' =&gt; 'archives',            'month' =&gt; 'all'        )     ));  "
});

documentTitles["/docs/urls.html#retrieving-route-parameters"] = "Retrieving route parameters";
index.add({
    url: "/docs/urls.html#retrieving-route-parameters",
    title: "Retrieving route parameters",
    body: "### Retrieving route parameters  Once the routing process is done, a configuration key named *request* is available. It contains an associative array with parameters and their value.          $params = Atomik::get('request');     $year = Atomik::get('request.year');      // inside actions and views:      $params = $this['request'];     $year = $this['request.year'];  "
});

documentTitles["/docs/urls.html#file-extensions"] = "File extensions";
index.add({
    url: "/docs/urls.html#file-extensions",
    title: "File extensions",
    body: "## File extensions  Since version 2.2, Atomik can handle file extensions in urls. The file extension is optional.  You can force the file extension to be present by setting *app.force\_uri\_extension* to true.  By default the file extension is the view context. View contexts are discussed in a later chapter. The default view context if no extension is defined is *html*. This can be changed in *app.views.default\_context*.      http://example.com/home         =&gt;	action=home format=html (won't work if app.force_uri_extension is set to true)     http://example.com/home.html    =&gt;	action=home format=html     http://example.com/home.xml     =&gt;	action=home format=xml     http://example.com/home.foo     =&gt;	action=home format=foo  Routes also support file extensions. You can specify a specific extension in your route. The extension can also be a parameter. In this case, if you specify a default value for it, the extension won't be mandatory in the url.      Atomik::set('app.routes', array(        ':category/:article..:format' =&gt; array(        	    'action' =&gt; 'article'        ),        'home..html' =&gt; array(             'action' =&gt; 'home',             'format' =&gt; 'html'        )     ));  (Note that the dot must be escaped in keys using a double dot)  The format parameter is not automatically added in custom routes. If not specified it will default to the value of *app.views.default\_context*.  "
});

documentTitles["/docs/urls.html#building-urls"] = "Building urls";
index.add({
    url: "/docs/urls.html#building-urls",
    title: "Building urls",
    body: "## Building urls  Directly writing url into your code can lead to problems. When using a layout for example, it is hard to know the relative location of the current view, to include stylesheets for example. Some urls also needs lots of concatanation when using parameters and this can make the code less readable.  `Atomik::url()` tries to resolve those problems by providing three things:   - Prepend the base url  - Do not use *index.php* in the url if url rewriting is enabled (or use it if not)  - Handle url parameters  The method works best with relative or absolute urls. It can however also works with full urls.  In this case, the two first points won't be applied.  If null is used as the url, the current action will be used. Named routes can be used using the route name prepended with the @ sign.      $url = Atomik::url('home'); // /index.php?action=home if no url rewriting or /home otherwise     $url = Atomik::url('/user/dashboard'); // /user/dashboard     $url = Atomik::url('http://example.com'); // http://example.com     $url = Atomik::url('@my_route'); // will use the route named my_route  You can add GET parameters to the url using an array as the second argument. You can re-use the current request parameters by using true instead of an array. If you want  the current parameters as well as new ones, use an array and add *\_\_merge\_GET* as value.      $url = Atomik::url('archives', array('year' =&gt; 2008)); // /index.php?action=archives&amp;year=2008 if no url rewriting or /archives?year=2008 otherwise     $url = Atomik::url('archives?year=2008', array('month' =&gt; 02)); // /archives?year=2008&amp;month=02      // if the page has been called with ?year=2008      $url = Atomik::url('archives', true); // /archives?year=2008     $url = Atomik::url('archives', array('__merge_GET', 'month' =&gt; 02)); // /archives?year=2008&amp;month=02  The method also allows you to use embedded parameters. These are parameters in the uri (like in classical routes).  The name of the parameter must be prepended with *:*.      $url = Atomik::url('archives/:year', array('year' =&gt; 2008)); // /index.php?action=archives/2008 if no url rewriting or /archives/2008 otherwise     $url = Atomik::url('archives/:year', array('year' =&gt; 2008, 'month' =&gt; 02)); // /archives/2008?month=02  Using named routes:      $url = Atomik::url('@archives', array('year' =&gt; 2008)); // /index.php?action=archives/2008 if no url rewriting or /archives/2008 otherwise     $url = Atomik::url('@archives', array('year' =&gt; 2008, 'month' =&gt; 02)); // /archives/2008?month=02  When creating urls that point to resources you'll never want them to have the  *index.php* part in them. To prevent that you can use the `Atomik::asset()` method.  It works exactly the same as `Atomik::url()` but will never use *index.php* in the url.  `Atomik::url()` is relative to the application context. For example, if this method is used  inside views from your application, generated urls will be relative to your application's base url.  However, if it is used inside a pluggable application (or plugin) it will be relative to the  plugin's base url. It allows this method to be used everywhere while ensuring that the urls  are relative to the correct base url.  It is however possible to use `Atomik::appUrl()` to always generate urls relative to your application's base url and `Atomik::pluginUrl()` to always generate urls relative to a specific plugin. The plugin name must be provided as the first argument of the latter, remaining arguments being the same as  with `Atomik::url()`.      $url = Atomik::appUrl('home'); // always /home     $url = Atomik::pluginUrl('my_plugin', 'home'); // /my_plugin_base_url/home     $url = Atomik::url('home'); // either /home or /my_plugin_base_url/home depending on the context  For assets, it also exists `Atomik::appAsset()` and `Atomik::pluginAsset()`. The latter, like `Atomik::pluginUrl()`, needs a plugin name as the first argument.  Inside views, you should call any Atomik methods through `$this`:      $url = $this-&gt;url('archives', array('year' =&gt; 2008)); "
});



documentTitles["/docs/actions.html#actions"] = "Actions";
index.add({
    url: "/docs/actions.html#actions",
    title: "Actions",
    body: "# Actions  "
});

documentTitles["/docs/actions.html#introduction"] = "Introduction";
index.add({
    url: "/docs/actions.html#introduction",
    title: "Introduction",
    body: "## Introduction  Without using Atomik, one way of doing things would have been to create a file per page. The page logic (i.e. connecting to a database, handling form data...) would have been at the top of the file followed by the HTML.       &lt;?php 	    if (count($_POST)) { 		    echo 'Form data received!'; 	    }     ?&gt;     &lt;form&gt; 	    &lt;input type=\&quot;text\&quot; name=\&quot;data\&quot; /&gt; 	    &lt;input type=\&quot;submit\&quot; value=\&quot;send\&quot; /&gt;     &lt;/form&gt;  This is BAD!! The application logic and the presentation layer should always be separated as explained [here](http://en.wikipedia.org/wiki/Separation_of_concerns).  Now let's imagine that rather than directly doing both part in the same file we split it. We would have three file: one with the logic, one with the HTML and one that include both.      // page_logic.php 	     &lt;?php     if (count($_POST)) { 	    echo 'Form data received!';     }          // page_html.php          &lt;form&gt; 	    &lt;input type=\&quot;text\&quot; name=\&quot;data\&quot; /&gt; 	    &lt;input type=\&quot;submit\&quot; value=\&quot;send\&quot; /&gt;     &lt;/form&gt;          // page.php          &lt;?php     include 'page_logic.php';     include 'page_html.php';  Now replace the third file (the one with the includes) with Atomik and you'll have the concept behind Atomik. The logic script is named an action and the html a view.  "
});

documentTitles["/docs/actions.html#action-files"] = "Action files";
index.add({
    url: "/docs/actions.html#action-files",
    title: "Action files",
    body: "## Action files  Actions are stored in the *app/actions* directory. Both the action and the  view filename must be the same. Action files must have the *php* extension. If the action or view filename starts with an underscore, the action won't be accessible using an url.  There are no requirements for the content of an action file. It can be anything you want.  So you just do your logic as you used to.  Be aware that actions run in their own scope and not in the global scope as you might think.  Variables declared in the action are forwarded to the view. If you want to keep some variables private (i.e. which will only be available in your action) prefixed them with an underscore.      &lt;?php     $myPublicVariable = 'value';     $_myPrivateVariable = 'secret';  You shouldn't use echo or write any HTML code inside an action. As said before, the goal of an action is to separate the logic from the presentation.  If you would like to exit the application, avoid using exit() and prefer `Atomik::end()` so Atomik can smoothly exit your application.  You can use folders to organize your actions. In this case, views must follow the same directory structure. You can create an *index* action (*index.php*) inside a folder and it will be use as the folder's default page. Views follow the same principle.      app/actions/users.php           &lt;- will be used if url = /users     app/actions/users/index.php     &lt;- will be used if url = /users AND if app/actions/users.php does not exist     app/actions/users/messages.php  &lt;- will be used if url = /users/messages whatever the default page is  "
});

documentTitles["/docs/actions.html#actions-and-http-methods"] = "Actions and HTTP methods";
index.add({
    url: "/docs/actions.html#actions-and-http-methods",
    title: "Actions and HTTP methods",
    body: "## Actions and HTTP methods  Atomik allows you to create multiple files for one action, each of them targeting a specific HTTP method. This enables RESTful websites to be build.  "
});

documentTitles["/docs/actions.html#targeting-http-methods"] = "Targeting HTTP methods";
index.add({
    url: "/docs/actions.html#targeting-http-methods",
    title: "Targeting HTTP methods",
    body: "### Targeting HTTP methods  Method specific action files must be suffixed with the method name. So for example, if you have a *user* action and you would like to target the POST method, you would create a file named *user.post.php*. With the PUT method it would have been *user.put.php*. These files must be located in the actions folder.  You can still create a global action file (in the previous example: *user.php*) which will be executed before any method specific action. Variables from the global action are available in the specific one.  The current http method is available in the *app.http_method* configuration key.  "
});

documentTitles["/docs/actions.html#allowed-methods-and-overriding-the-requests-method"] = "Allowed methods and overriding the request's method";
index.add({
    url: "/docs/actions.html#allowed-methods-and-overriding-the-requests-method",
    title: "Allowed methods and overriding the request's method",
    body: "### Allowed methods and overriding the request's method  Allowed HTTP methods are defined in *app.allowed_http_methods*. By default, all methods available in the protocol are listed but you may want to reduce that list.  Some clients does not handle well HTTP methods. Thus, it is possible to override the request's method using a route parameter (which can be a GET parameter). The default parameter name is *_method*. This can be changed in *app.http_method_param*. It can also be disabled by setting false instead of a string.  "
});

documentTitles["/docs/actions.html#redirections-and-404-errors"] = "Redirections and 404 errors";
index.add({
    url: "/docs/actions.html#redirections-and-404-errors",
    title: "Redirections and 404 errors",
    body: "## Redirections and 404 errors  To redirect the user to another page you can use the `Atomik::redirect()` method. It takes as argument the url to redirect to. By default, this url will first be process using  `Atomik::url()`. This behaviour can be disabled by passing false as the second argument. The response HTTP code can also be specified as the third argument.      $this-&gt;redirect('home');     $this-&gt;redirect('home', true, 303); // 303 http code  Triggering 404 errors is even simpler. Just call the `Atomik::trigger404()` method.      $this-&gt;trigger404();  "
});

documentTitles["/docs/actions.html#includes"] = "Includes";
index.add({
    url: "/docs/actions.html#includes",
    title: "Includes",
    body: "## Includes  Includes are php files containing common logic that you include in your actions.  Includes are stored either in *app/includes* or *app/libs*. directories. This can be changed in *atomik.dirs.includes*.  To include a file from one of these directories use the `Atomik::needed()`  method. It takes as first argument the path to the filename you wish to include relative to the previous directories and without the extension.      // includes app/includes/common.php     Atomik::needed('common');  You can use sub directories. To include a file stored at *app/includes/libs/db.php*:      Atomik::needed('libs/db');  `Atomik::needed()` also allows you to include classes using their name. To do so, classes have to follow the PEAR naming convention (http://pear.php.net/manual/en/standards.naming.php) or use PHP 5.3 namespaces.      // app/libraries/Atomik/Db.php     Atomik::needed('Atomik_Db');     Atomik::needed('Atomik\Db');  `Atomik::needed()` is automatically registered as an spl\_autoload handler. This can be modified by setting *false* to the configuration key named *atomik.class\_autoload*.  "
});

documentTitles["/docs/actions.html#calling-actions-programmatically"] = "Calling actions programmatically";
index.add({
    url: "/docs/actions.html#calling-actions-programmatically",
    title: "Calling actions programmatically",
    body: "## Calling actions programmatically  When executing a request, the action and/or the view associated to it are automatically called. You can however call other actions using Atomik's API.  To execute an action use the `Atomik::execute()` method. It takes as first argument the action name.  By default, if a view with the same name is found, it is rendered and the return value of the `execute()` method is the view output.  If no view is found, an empty string is returned. If false is used as second argument,  the return value is an array containing all *public* variables from the action.      $viewOutput = Atomik::execute('myAction');     $variables = Atomik::execute('myAction', false);  Calling an action using `Atomik::execute()` does not mean an action file must exist. However, in this case, a view file with the same name must exist. Otherwise, an exception will be thrown.  Actions executed this way will also be influenced by the HTTP method. You can specify a specific method by appendinf it to the action name. The global action will also be executed.      $viewOutput = Atomik::execute('myAction.post');  "
});



documentTitles["/docs/views.html#views"] = "Views";
index.add({
    url: "/docs/views.html#views",
    title: "Views",
    body: "# Views  "
});

documentTitles["/docs/views.html#views"] = "Views";
index.add({
    url: "/docs/views.html#views",
    title: "Views",
    body: "## Views  Views are stored in the *app/views* directory. The default file extension is *phtml*.  The content of a view file is, as the action file, free. It should mostly be text or  HTML (or any presentation content, such as XML).  PHP can be used to print variables from the action or to provide presentation logic like loops. 	     &lt;html&gt; 	    &lt;head&gt; 		    &lt;title&gt;Example&lt;/title&gt; 	    &lt;/head&gt; 	    &lt;body&gt; 		    &lt;?php echo $myPublicVariable; ?&gt; 	    &lt;/body&gt;     &lt;/html&gt;  "
});

documentTitles["/docs/views.html#layout"] = "Layout";
index.add({
    url: "/docs/views.html#layout",
    title: "Layout",
    body: "## Layout  It is common in websites that all pages share the same layout. Atomik allows you to define a layout that will be used with all views.  The layout will be rendered after the view has been rendered. The output of the view will be pass to the layout as a variable named `$contentForLayout`.  Layouts are rendered the same way as views.  Layouts can be placed in the *app/views* or *app/layouts* directories. The file extension is the same as the one for views.  The layout name to use has to be defined in the *app.layout* configuration key. If the value is false (which is the default), no layout will be used.  The layout can be disabled at runtime by calling `Atomik::disableLayout()`. It can later be re-enabled by passing false as argument to the same method.      // app/views/_layout.phtml      &lt;html&gt; 	    &lt;head&gt; 		    &lt;title&gt;My website&lt;/title&gt; 	    &lt;/head&gt; 	    &lt;body&gt; 		    &lt;h1&gt;My website&lt;/h1&gt; 		    &lt;div id=\&quot;content\&quot;&gt; 			    &lt;?php echo $contentForLayout; ?&gt; 		    &lt;/div&gt; 	    &lt;/body&gt;     &lt;/html&gt;          // app/config.php          Atomik::set('app.layout', '_layout');  Mutliple layouts can also be used. Just use an array instead of a string in the configuration key.  Layouts will be rendered in reverse order (the first one in the array wrap the second, the second  the third, ...).  "
});

documentTitles["/docs/views.html#view-contexts"] = "View contexts";
index.add({
    url: "/docs/views.html#view-contexts",
    title: "View contexts",
    body: "## View contexts  It is sometimes needed to return content in different formats. Rather than creating multiple actions  doing the same thing, Atomik allows you to create a view for each content type. This is called view  contexts. The correct view is rendered depending on the current context.  The context is defined using a route parameter. By default it is called *format*. This can be changed in *app.views.context\_param*. As specified in the urls chapter, the format parameter is by default the file extension. Which means that using an url like index.xml will result in using the xml context.  The default view context is *html* but it can be changed in *atomik.views.default\_context*.  To create a view for a context just suffix the view name with the context name like an extension.  For example, let's say we have an *article* view. The filename for the xml context would be  *article.xml.phtml*. Some context may not need any prefix like the *html* one.  Depending on the view context, the layout can be disabled and the response content-type can be changed.  The file prefix can also be specified. All of this is done in *app.views.contexts*. 		 Creating a custom view context:      Atomik::set('app.views.contexts.rdf', array(    // the context name, ie. the file extension in the url 	    'suffix'        =&gt; 'rdf',                   // the view's file extension suffix (set to false for no suffix) 	    'layout'        =&gt; false,                   // disables the layout 	    'content_type'  =&gt; 'application/xml+rdf'    // the response's content type     ));  Now you can call an url like http://example.com/article.rdf. In this case the view filename would be *article.rdf.phtml*, the layout would be disabled and the response content type would be *application/xml+rdf*.  If a view context is not defined under *app.views.contexts*, the file prefix will be the context name, the layout won't be disabled and the response content type will be *text/html*.  By default, four contexts are defined: html, ajax, xml and json. The ajax context is the same  as html but with the layout disabled. The last two disable the layout and set the appropriate  content type.  "
});

documentTitles["/docs/views.html#controlling-views"] = "Controlling views";
index.add({
    url: "/docs/views.html#controlling-views",
    title: "Controlling views",
    body: "## Controlling views  "
});

documentTitles["/docs/views.html#views-filename-extension"] = "View's filename extension";
index.add({
    url: "/docs/views.html#views-filename-extension",
    title: "View's filename extension",
    body: "### View's filename extension  The default filename's extension for views is *phtml* as said before. This can be change using the configuration key named *app.views.file\_extension*.  "
});

documentTitles["/docs/views.html#do-not-render-the-view-from-the-action"] = "Do not render the view from the action";
index.add({
    url: "/docs/views.html#do-not-render-the-view-from-the-action",
    title: "Do not render the view from the action",
    body: "### Do not render the view from the action  While the action is executing, you may want to avoid rendering the associated view. This can easily be done by calling `Atomik::noRender()` from your action.  "
});

documentTitles["/docs/views.html#modify-the-associated-view-from-the-action"] = "Modify the associated view from the action";
index.add({
    url: "/docs/views.html#modify-the-associated-view-from-the-action",
    title: "Modify the associated view from the action",
    body: "### Modify the associated view from the action  While the action is executing, you may want to render a different view. In this case, you can use `Atomik::setView()` from your action. It takes as unique argument a view name.  "
});

documentTitles["/docs/views.html#using-a-custom-rendering-engine"] = "Using a custom rendering engine";
index.add({
    url: "/docs/views.html#using-a-custom-rendering-engine",
    title: "Using a custom rendering engine",
    body: "### Using a custom rendering engine  The default rendering process only uses php's include function. You may however want to use a  template engine for example. This is possible by specifying a callback in *app.views.engine*.  The callback will receive two parameters: the first one will be the filename and the second an  array containing the view variables.      function myCustomRenderingEngine($filename, $vars)     { 	    // your custom engine 	    return $renderedContent;     }      Atomik::set('app.views.engine', 'myCustomRenderingEngine');  The custom rendering engine will be used whenever `Atomik::render()`, `Atomik::renderFile()` or `Atomik::renderLayout()` is used.  "
});

documentTitles["/docs/views.html#rendering-views-programmatically"] = "Rendering views programmatically";
index.add({
    url: "/docs/views.html#rendering-views-programmatically",
    title: "Rendering views programmatically",
    body: "## Rendering views programmatically  When executing a request, the action and/or the view associated to it are automatically called. You can however render other views using Atomik's API.  The most useful use of this it to render partial views, small part of presentation code that is reusable.  To render a view use the `Atomik::render()` method.  It takes as first argument the view name and optionally as second argument an array of key/value pairs representing variables.  The method returns the view output.      $viewOutput = Atomik::render('myView');     $viewOutput = Atomik::render('myView', array('var' =&gt; 'value'));  It is also possible to render any file using `Atomik::renderFile()`. It takes as first parameter a filename. Variables can also be passed like with `Atomik::render()`.  You can also render contextual views by adding the file extension prefix to the view name.  "
});



documentTitles["/docs/helpers.html#helpers"] = "Helpers";
index.add({
    url: "/docs/helpers.html#helpers",
    title: "Helpers",
    body: "# Helpers  Helpers are small utility functions, accessible through the `Atomik` object. They are loaded on demand.  "
});

documentTitles["/docs/helpers.html#creating-helpers"] = "Creating helpers";
index.add({
    url: "/docs/helpers.html#creating-helpers",
    title: "Creating helpers",
    body: "## Creating helpers  Helpers in Atomik are stored in *app/helpers*. For example, a `format_date()` helper would  be stored in *app/helpers/format\_date.php*. The helpers directory can be changed using *atomik.dirs.helpers*.  You can then define your helper in two ways: as a function or as a class. If you're using a function,  just create one with the same name as the helper.      function format_date($date)     {         // do the formating         return $date;     }  You can also use a class which can be pretty useful for more complex cases. The class name is a  camel case version (ie. without any underscores or spaces, all words starting with an upper case)  of the helper name suffixed with *Helper*. In our example, it would be  `FormatDateHelper`. This class also needs to have a method named like the  helper name but in camel case and starting with a lower case. In this case, it would be  `formatDate()`.      class FormatDateHelper     {         public function formatDate($date)         {             // do the formating             return $date;         }     }  "
});

documentTitles["/docs/helpers.html#using-helpers"] = "Using helpers";
index.add({
    url: "/docs/helpers.html#using-helpers",
    title: "Using helpers",
    body: "## Using helpers  Helpers are callable from any action or view file. They are accessible as methods of `$this`.      &lt;span class=\&quot;date\&quot;&gt;&lt;?php echo $this-&gt;format_date('01-01-2009') ?&gt;&lt;/span&gt;  ## Registering helpers  You can also registers helper using the `Atomik::registerHelper()` function:      Atomik::registerHelper('say_hello', function() {         echo 'hello';     }); "
});



documentTitles["/docs/utilities.html#utilities"] = "Utilities";
index.add({
    url: "/docs/utilities.html#utilities",
    title: "Utilities",
    body: "# Utilities  All these utilities are helpers bundled with Atomik.  "
});

documentTitles["/docs/utilities.html#escaping-text"] = "Escaping text";
index.add({
    url: "/docs/utilities.html#escaping-text",
    title: "Escaping text",
    body: "## Escaping text  It is a common (and very good) practice to escape data when outputting it on the page. The `escape()` helper is dedicated to this purpose.      echo $this-&gt;escape('my text');  This helper relies on other functions to escape data. It simply executes them one after an other  and returns the result. You can for example, execute the `htmlspecialchars()` function followed by `nl2br()`.  The functions to execute are grouped under profiles. Thus, you can create multiple escaping profiles  depending on the data you need to escape. Profiles are defined in the *app.escaping*  configuration key. To specify which functions to execute in a profile, you can use a string or an  array of strings. Functions will be executed in the order they appear in the array. The default profile is called *default*.  The profile is specified as the last argument of the method. 	     // creating profiles     Atomik::set('app.escaping', array( 	    'default' =&gt; array('htmlspecialchars', 'nl2br'), 	    'url' =&gt; 'urlencode'     ));      // equivalent of nl2br(htmlspecialchars('my text'))     echo $this-&gt;escape('my text');      // equivalent of urlencode('my url param')     echo $this-&gt;escape('my url param', 'url');  "
});

documentTitles["/docs/utilities.html#friendly-urls"] = "Friendly urls";
index.add({
    url: "/docs/utilities.html#friendly-urls",
    title: "Friendly urls",
    body: "## Friendly urls  Having a router without a way to make friendly urls wouldn't be a complete feature.  The `linkify()` helper transforms any string to a url friendly version.      echo $this-&gt;friendlify('My text in the url');     // will echo my-text-in-the-url   "
});

documentTitles["/docs/utilities.html#filtering-and-validating-data"] = "Filtering and validating data";
index.add({
    url: "/docs/utilities.html#filtering-and-validating-data",
    title: "Filtering and validating data",
    body: "## Filtering and validating data  Filtering and validating user input is a very important task and Atomik had to provide a helper for this purpose. This helper is *filter()* and it heavily relies on PHP's filter extension.  PHP's filter extension is built-in since version 5.2 and its documentation is available  at &lt;http://php.net/filter&gt;.  You can find a good documentation (better than the official one) about available filters on the w3schools website at &lt;http://www.w3schools.com/php/php_ref_filter.asp&gt;  To understand and use `filter()` you must know how to use PHP's filter extension. However, Atomik's method adds some features.  `filter()` has the same arguments as `filter_var()`. However, you can also use a regular expression as filter in the second argument. The regexp must use slashes as delimiters. You can also define custom filters in the *app.filters.callbacks* configuration key and use the callback name as filter.      // using a php filter     $result = $this-&gt;filter('me@example.com', FILTER_VALIDATE_EMAIL);     $result = $this-&gt;filter('me@example.com', 'validate_email'); // using the filter name instead of its id     $result = $this-&gt;filter('example.com', FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);      // using a regexp     $result = $this-&gt;filter('12478', '/\d+/');      // using a callback     Atomik::set('app.filters.callbacks.my_custom_filter', 'myFilterFunction');     $result = $this-&gt;filter($data, 'my_custom_filter');  It will return false if an error occured, or the value otherwise.  The method can also be used to filter arrays in the same way as `filter_var_array()`. It works exactly the same as this function but it also adds more features.  The method will return false if any of the item failed to validate. This can be turned off by passing false as the fourth argument.  While the second argument is named a definition in the PHP's extension, Atomik called it rules.  Rules are arrays where keys are field's name and their value a filter or an array  (like with ̀filter_var_array()`). You can pass a rule as the second argument.  Rules can also be defined in the *app.filters.rules* configuration key.  You can then use their name instead of an array as the second argument.  The method also adds the notion of required fields. If you set the *required* key to true in  the field's array, the validation will fail if the field is missing or empty.   When a field is empty but not required, its value will be null. This can be changed by setting  the *default* key in the field's array.  The *filter* key in the field's array follows the same rule as the filter parameter described previously.  Thus it can be a filter's id or name, a regexp or a custom filter's name.  The helper also supports multi-dimension array as input.  Finally, support for validation messages has also been added. When filtering the array, if any values  failed validating, a message will be created. Messages can then be retreived in the  *app.filters.messages* configuration key. There ar two default messages configured in  *app.filters.default\_message* and *app.filters.required\_message*. The former will be used when a field failed to validate while the later when the required condition has not been met.  When setting the two message keys, you can use *%s* which will be replaced with the field's name.  As field names aren't usually pretty, you can use the *label* key in the field's array to define  the text which will be used to replace *%s*.  You can override the failed to validate message for each field by setting the *message*  key in the field's array.      // using a custom message when a field is missing or empty     Atomik::set('app.filters.required_message', 'You forgot to fill the %s field!');      // the data to validate     $dataToFilter = array( 	    'username' =&gt; 'peter', 	    'email' =&gt; 'peter@example.com'     );      // our rules     $fields = array( 	    'username' =&gt; array( 		    'filter' =&gt; FILTER_SANITIZE_STRING, 		    'required' =&gt; true 	    ), 	    'email' =&gt; array( 		    'filter' =&gt; 'validate_email', 		    'required' =&gt; true, 		    'message' =&gt; 'You must provide a valid email address' // custom message when the field failed to validate 	    )     );      if (($filteredData = $this-&gt;filter($dataToFilter, $fields)) === false) { 	    // failed validation, showing messages 	    Atomik::flash(Atomik::get('app.filters.messages'), 'error'); 	    return;     }      // success     // do something with $filteredData "
});



documentTitles["/docs/using-plugins.html#using-plugins"] = "Using plugins";
index.add({
    url: "/docs/using-plugins.html#using-plugins",
    title: "Using plugins",
    body: "# Using plugins  "
});

documentTitles["/docs/using-plugins.html#installing-a-plugin"] = "Installing a plugin";
index.add({
    url: "/docs/using-plugins.html#installing-a-plugin",
    title: "Installing a plugin",
    body: "## Installing a plugin  Plugins are stored in the *app/plugins* directory.  Simply copy the plugin file or folder into this directory.  "
});

documentTitles["/docs/using-plugins.html#activating-and-configuring-a-plugin"] = "Activating and configuring a plugin";
index.add({
    url: "/docs/using-plugins.html#activating-and-configuring-a-plugin",
    title: "Activating and configuring a plugin",
    body: "## Activating and configuring a plugin  Plugins are not automatically activated. To do so, it's needed to add an entry in the *plugins* configuration key.      Atomik::set('plugins', array(         'Db',         'Cache'     ));  Some plugins need custom configuration which can be specified in the plugins config key.      Atomik::set('plugins', array(         'Db' =&gt; array(             'dsn'      =&gt; 'mysql:host=localhost;dbname=atomik',             'username' =&gt; 'atomik',             'password' =&gt; 'atomik'         ),         'Cache'     ));  "
});

documentTitles["/docs/using-plugins.html#pluggable-applications"] = "Pluggable applications";
index.add({
    url: "/docs/using-plugins.html#pluggable-applications",
    title: "Pluggable applications",
    body: "## Pluggable applications  Pluggable applications are a great new thing introduced in version 2.2. It allows any plugin to act as a complete application. It can have its own actions, views, layouts, configuration... Let's say you need a blog, just drop in the Blog plugin and you're done!  Pluggable applications are then connected to an uri. When this uri is accessed, the application starts.  "
});

documentTitles["/docs/using-plugins.html#activating-and-accessing-pluggable-applications"] = "Activating and accessing pluggable applications";
index.add({
    url: "/docs/using-plugins.html#activating-and-accessing-pluggable-applications",
    title: "Activating and accessing pluggable applications",
    body: "### Activating and accessing pluggable applications  As these applications are plugins, activating them is as simple as dropping them in the *app/plugins* folder and adding their name to the *plugins* key.  The application is then available at /pluginName. In the previous example it would be /blog.  Most pluggable applications should provide a configuration key to modify the default uri.  In the case of the Blog plugin, let say it's *route*.  While the key is named route, the way to specify an uri here is not the same as with routes:  it's simply an uri. We'll call it a pattern. If you want to trigger an application from /app  the pattern would be */app*. However, accessing /app/index would not trigger the application! To enable this you have to use the \* wildcard at the end of the pattern so that all children also triggers the app.  The final pattern would be */app/\**.      Atomik::set('plugins.Blog', array(         'route' =&gt; '/my-blog/*'     ));  If a plugin does not have a configuration key to modify the route, this can be done by calling `Atomik::registerPluggableApplication()` from the bootstrap file. This method takes as first argument the plugin name and as second the pattern.      Atomik::registerPluggableApplication('MyPluggableApp', '/my-app/*')  When available, use the plugin configuration as it could override any predefined pattern. You can connect any application to the root of your application using */**.  "
});

documentTitles["/docs/using-plugins.html#overrides"] = "Overrides";
index.add({
    url: "/docs/using-plugins.html#overrides",
    title: "Overrides",
    body: "### Overrides  Using pluggable applications is great! They do everything for you. However, you'll sometimes want to customize these applications. Atomik provides an easy way to do that: overrides.  With overrides you will be able to replace any action, view, layout or helper from a pluggable application.  Overrides are stored in *app/overrides*. In this directory, create a folder named after the plugin. This folder can then contain the classic Atomik folders: *actions*,  *views*, *helpers* and *layouts*.  For example, to override the *index* view from the Blog plugin, you would create the file  *app/overrides/Blog/views/index.phtml*.  Some plugins may allow your actions and views from your *app* folder to be accessible from the application. This is not considered overrides as plugins have priority in this case. But it can be a nice way to add features to a pluggable application. You cannot enable this yourself, only plugin can do it, see the plugin documentation.  "
});



documentTitles["/docs/session.html#session"] = "Session";
index.add({
    url: "/docs/session.html#session",
    title: "Session",
    body: "# Session  &lt;div class=\&quot;note\&quot;&gt;These features need the Session and Flash plugins which are bundled with Atomik&lt;/div&gt;  "
});

documentTitles["/docs/session.html#starting-and-accessing-the-session"] = "Starting and accessing the session";
index.add({
    url: "/docs/session.html#starting-and-accessing-the-session",
    title: "Starting and accessing the session",
    body: "## Starting and accessing the session  You'll need to register the Session plugin:      Atomik::add('plugins', array(         'Session'     ));  By default, Atomik will automatically starts the session. This can be turned off using *autoload* in the plugin's configuration.  The session is available as the *session* key in the global store. Of course, it still remains available as the `$_SESSION` super-global variable.      echo Atomik::get('session.username');  "
});

documentTitles["/docs/session.html#flash-messages"] = "Flash messages";
index.add({
    url: "/docs/session.html#flash-messages",
    title: "Flash messages",
    body: "## Flash messages  Flash messages are messages which are stored in the session and are available only once. This allows to pass error or success messages from one page to another.   To create a flash message call the `flash()` helper. It takes as first parameter a message or an array of messages. Messages can also have labels.  For example *error* or *success*. To specify a label, use the second argument.  The default label is *default*.      $this-&gt;flash('The action has completed successfully');     $this-&gt;flash('The action has failed', 'error'); // with a label     $this-&gt;flash(array('message1', 'message2'), 'error');  Flash messages can then be retreived using the *flash\_messages* key:      foreach (Atomik::get('flash_messages') as $label =&gt; $messages) { 	    foreach ($messages as $message) { 		    // ... 	    }     }      foreach (Atomik::get('flash_messages.my_label') as $message) { 	    // ...     }  "
});



documentTitles["/docs/error-log-debug.html#error-handling-logging-and-debugging"] = "Error handling, logging and debugging";
index.add({
    url: "/docs/error-log-debug.html#error-handling-logging-and-debugging",
    title: "Error handling, logging and debugging",
    body: "# Error handling, logging and debugging  "
});

documentTitles["/docs/error-log-debug.html#handling-errors"] = "Handling errors";
index.add({
    url: "/docs/error-log-debug.html#handling-errors",
    title: "Handling errors",
    body: "## Handling errors  &lt;div class=\&quot;note\&quot;&gt;You will need the Errors plugin which is bundled with Atomik&lt;/div&gt;  By default, Atomik won't catch any exceptions or errors, PHP's normal behavior prevail.  However, the Errors plugin enables error catching so you can display an error page to the  user or error reports while developing.  The plugin will display a page when a 404 error is triggered. The template to render can be specified using *404\_view*, the default one being *errors/404*:      Atomik::set('plugins.Errors', array(         '404_view' =&gt; 'unknown_page'     ));  The *catch\_errors* configuration key must be set to true for Atomik to catch errors. Errors and exceptions are treated the same way. If an error template exists (specified in *error_view* and by default *errors/error*) it will be rendered otherwise an error report is displayed.      Atomik::set('plugins.Errors', array(         'error_view' =&gt; 'my_error_template',         'catch_errors' =&gt; true     ));  By default, uncatched errors are silently droped. You can instead let the exception be thrown using *throw\_errors*.      Atomik::set('plugins.Errors', array(         'throw_errors' =&gt; true     )); 	 "
});

documentTitles["/docs/error-log-debug.html#logging"] = "Logging";
index.add({
    url: "/docs/error-log-debug.html#logging",
    title: "Logging",
    body: "## Logging  &lt;div class=\&quot;note\&quot;&gt;You will need the Logger plugin which is bundled with Atomik as well as Monolog which you'll need to install&lt;/div&gt;  The Logger plugin provides a simple way of logging messages. It provides the ̀log()` helper which takes two arguments, the second one being optional: the message and the level (default is `LOG_ERR` = 3).      $this-&gt;log('an error has occured!', LOG_ERR);  As shown in the example, you should use PHP's LOG\_* constants.  The helper will simply fire an event named *Logger::Log*. Listeners will get two arguments, the message and the level.      function my_logger($message, $level) { 	    echo 'LOG: ' . $message;     }     Atomik::listenEvent('Logger::Log', 'my_logger');  Atomik provides a default logger which will save messages to a text file. To register this logger, set the config key named *register\_default* to true.  The filename is defined in *filename*. You can also define from which level messages should be saved by setting *level* to the minimum level. The default level is `LOG_WARNING` (4).      Atomik::set('plugins.Logger', array(         'register_default' =&gt; true,         'filename' =&gt; 'log.txt'     ));  Finally, you can define the template of the string that will be added to the log file in  *message\_template*. You can use *%date%*, *%level%* and *%message%* which will be replaced  by the appropriate string.  "
});

documentTitles["/docs/error-log-debug.html#debugging"] = "Debugging";
index.add({
    url: "/docs/error-log-debug.html#debugging",
    title: "Debugging",
    body: "## Debugging  Atomik's only provides a simple helper named `debug()` which  is an alias for `var_dump()`. However the method output can be hidden by modifying the *atomik.debug* configuration key.  Also, if *atomik.debug* is true, the error reporting level will be set to the maximum.      Atomik::debug($myVar);     Atomik::set('atomik.debug', false);     Atomik::debug($myVar2); // no output     Atomik::debug($myVar2, true); // use true to force the output even if debug set to false  "
});

documentTitles["/docs/error-log-debug.html#debug-bar"] = "Debug Bar";
index.add({
    url: "/docs/error-log-debug.html#debug-bar",
    title: "Debug Bar",
    body: "## Debug Bar  Atomik provides a plugin to easily integrate [PHP DebugBar](http://phpdebugbar.com). The skeleton application comes with the debug bar thus you don't need to do the following steps.  You'll need to install PHP Debug Bar by yourself. If you are using the skeleton, this can be done as follow:   - add the requirement in the *composer.json* file (`\&quot;maximebf/debugbar\&quot;: \&quot;1.*\&quot;`)  - run `$ composer.phar update`  Activate the plugin in the config and enable debug mode:      Atomik::add('plugins', 'DebugBar');     Atomik::set('atomik.debug', true);  Render the debug bar in your layout:      &lt;html&gt;         &lt;head&gt;             ...             &lt;?php if ($this['atomik.debug']) echo $this-&gt;renderDebugBarHead(); ?&gt;         &lt;/head&gt;         &lt;body&gt;             ...             &lt;?php if ($this['atomik.debug']) echo $this-&gt;renderDebugBar(); ?&gt;         &lt;/body&gt;     &lt;/html&gt;  Be aware that the debug bar includes jQuery and FontAwesome. The debug bar needs at least jQuery to run properly. If you are using jQuery in your project, you can disable debug bar's own version using:      // this only includes FontAwesome     // set to false to include none of them     Atomik::set('plugins.DebugBar.include_vendors', 'css');"
});



documentTitles["/docs/database.html#database"] = "Database";
index.add({
    url: "/docs/database.html#database",
    title: "Database",
    body: "# Database  &lt;div class=\&quot;note\&quot;&gt;You will need the Db plugin which is bundled with Atomik&lt;/div&gt;  The database plugin is a thin layer on top of [PDO](http://fr2.php.net/manual/en/book.pdo.php).  "
});

documentTitles["/docs/database.html#connecting"] = "Connecting";
index.add({
    url: "/docs/database.html#connecting",
    title: "Connecting",
    body: "## Connecting  There are 3 configuration options:   - *dsn*  - *username*  - *password*  Activate the plugin:      Atomik::set('plugins.Db', array(         'dsn' =&gt; 'mysql:host=localhost;dbname=example',         'username' =&gt; 'root',         'password' =&gt; 'rootpassword'     ));  This creates a `PDO` instance which is stored under the *db* key in the global store.      $db = Atomik::get('db');      // or in actions:      $db = $this['db'];  You can then use this object as you would with a normal PDO instance:      $stmt = $this['db']-&gt;prepare('insert into posts (title, content) values (?, ?)');     $stmt-&gt;execute(array('my new post', 'lorem ipsum ...'));  "
});

documentTitles["/docs/database.html#querying-data"] = "Querying data";
index.add({
    url: "/docs/database.html#querying-data",
    title: "Querying data",
    body: "## Querying data  Some useful methods are added to quickly query the database. The `select()` function takes as first argument the table name. By default it will execute a SELECT * statement.      $posts = $db-&gt;select('posts');  The second argument is a where statement. It can either be an SQL string (without the WHERE keyword) or an array of column/value mapping:      $clothes = $db-&gt;select('products', \&quot;category = 'clothes'\&quot;);     // or:     $clothes = $db-&gt;select('products', array('category' =&gt; 'clothes'));  To select only one row, use `selectOne()`.  To select the value of the first column of the first line, use `selectValue` which needs a column name as the second argument.      $postTitle = $db-&gt;selectValue('posts', 'title', array('id' =&gt; 1));  Finally, you can count using `count()`:      $nbClothes = $db-&gt;count('products', array('category' =&gt; 'clothes'));  "
});

documentTitles["/docs/database.html#manipulating-data"] = "Manipulating data";
index.add({
    url: "/docs/database.html#manipulating-data",
    title: "Manipulating data",
    body: "## Manipulating data  Insert data using `insert()` which takes as first argument the table name and as second an array where keys are columns names. It returns the ̀PDOStatement` object that was executed (in case you want to get the last inserted id for example).      $data = array(         'title' =&gt; 'post title',         'content' =&gt; 'lorem ipsum...'     );     $db-&gt;insert('posts', $data);  Updating data is very similar using the `update()` function. It can take as third parameter a where clause like in `select()`.      $data = array('title' =&gt; 'modified title');     $db-&gt;update('posts', $data, array('id' =&gt; 1));  Finally, deleting data is as easy. The second argument can be a where clause (if not specified, all rows will be deleted).      $db-&gt;delete('posts', array('id' =&gt; 1)); "
});



documentTitles["/docs/scripts.html#scripts"] = "Scripts";
index.add({
    url: "/docs/scripts.html#scripts",
    title: "Scripts",
    body: "# Scripts  &lt;div class=\&quot;note\&quot;&gt;You will need the Console plugin which is bundled with Atomik&lt;/div&gt;  The Console plugin allows Atomik to be used in a terminal. It allows other plugins to provide custom commands and to create scripts to better administer your application.  It is built on top of [ConsoleKit](https://github.com/maximebf/ConsoleKit) which you'll need to install.  To call your application from the command line, use the following command  	php index.php [command] [args]  Where *index.php* is Atomik's core file.  "
});

documentTitles["/docs/scripts.html#creating-custom-scripts"] = "Creating custom scripts";
index.add({
    url: "/docs/scripts.html#creating-custom-scripts",
    title: "Creating custom scripts",
    body: "## Creating custom scripts  You can create ConsoleKit Commands inside the *app/scripts* folder (which can be changed using the *scripts\_dir* config key).  Let's create a script in *app/scripts/CleanupDbCommand.php*      &lt;?php      class CleanupDbCommand extends ConsoleKit\Command     {         public function execute(array $args, array $opts)         {             $this-&gt;writeln(sprintf(\&quot;cleaning %s\&quot;, $args[0]));         }     }  To call this script use the following command:      $ php index.php cleanup-db dbname  "
});

documentTitles["/docs/scripts.html#registering-commands"] = "Registering commands";
index.add({
    url: "/docs/scripts.html#registering-commands",
    title: "Registering commands",
    body: "## Registering commands  Instead of using files, you can manually register commands using ̀Console::register()`:      Atomik\Console::register('cleanup-db', function($args, $opts, $console) {         // code     });  "
});

documentTitles["/docs/scripts.html#built-in-commands"] = "Built-in commands";
index.add({
    url: "/docs/scripts.html#built-in-commands",
    title: "Built-in commands",
    body: "## Built-in commands  The plugin provides one built-in command to generate new actions and views.  Just specify a name and the action file and the view file will be generated.  You can generate multiple pages by separating them by a space      php index.php generate home     php index.php generate photos about "
});



documentTitles["/docs/translations.html#translations"] = "Translations";
index.add({
    url: "/docs/translations.html#translations",
    title: "Translations",
    body: "# Translations  &lt;div class=\&quot;note\&quot;&gt;You will need the Translations plugin which is bundled with Atomik&lt;/div&gt;  This plugin is gettext-like. You write your application in its default language and then provide translations for each part of text.      "
});

documentTitles["/docs/translations.html#creating-language-files"] = "Creating language files";
index.add({
    url: "/docs/translations.html#creating-language-files",
    title: "Creating language files",
    body: "## Creating language files  A language file provides translation from one language to another.  They are stored in the *app/languages* directory. This can be changed using the *dir* configuration key. Files be named after the first part of the locale. For example, if the file provide translation to French, it has to be named *fr.php* (because the locale is fr-fr).  In the language file you must defined messages using the  `Translations::setMessages()` method. The messages is made of the string of the original language and the translated one.      &lt;?php     Atomik\Translations::setMessages(array(         'hello' =&gt; 'bonjour',         'how are you?' =&gt; 'comment ca va?'     ));  "
});

documentTitles["/docs/translations.html#detecting-the-user-language"] = "Detecting the user language";
index.add({
    url: "/docs/translations.html#detecting-the-user-language",
    title: "Detecting the user language",
    body: "## Detecting the user language  By default, the plugin will autodetect the language using HTTP headers. This can be turned off by setting false to the *autodetect* configuration key.  If the language cannot be detected, it will fall back on the default language defined in the *language* configuration key.  You can also set the language manually using `Translations::set()`.      Atomik\Translations::set('fr');  The current language is available from the global store under the *app.language* key.      $currentLanguage = Atomik::get('app.language');  "
});

documentTitles["/docs/translations.html#translating-strings"] = "Translating strings";
index.add({
    url: "/docs/translations.html#translating-strings",
    title: "Translating strings",
    body: "## Translating strings  To enable translation for a string use the `Translations::translate()` method.      Atomik\Translations::set('fr');     echo Atomik\Translations::translate('hello'); // will echo bonjour     echo Atomik\Translations::translate('how are you?'); // will echo comment ca va?  The method is also available as an helper:      echo $this-&gt;translate('hello');     echo $this-&gt;_('hello'); // alias  A shortcut function is also defined: `__()`.      Atomik\Translations::set('fr');     echo __('hello'); // will echo bonjour     echo __('how are you?'); // will echo comment ca va?  This method can also be use like the vsprintf() function. It can replace patterns in the string by values provided as an array as the second argument.      echo __('hello %s', array('Peter'));  "
});



documentTitles["/docs/controllers.html#controller"] = "Controller";
index.add({
    url: "/docs/controllers.html#controller",
    title: "Controller",
    body: "# Controller  &lt;div class=\&quot;note\&quot;&gt;You will need the Controller plugin which is bundled with Atomik&lt;/div&gt;  Atomik action files do not follow any conventions. However, some of you may have used MVC frameworks where the business logic is coded in controllers. Controllers are classes where their methods are actions.  This plugin adds support for controllers to Atomik. Once activated you must use controllers in your actions. It is not possible to mix between the classic way and the controller way.  "
});

documentTitles["/docs/controllers.html#differences-with-the-classic-atomik-way"] = "Differences with the classic Atomik way";
index.add({
    url: "/docs/controllers.html#differences-with-the-classic-atomik-way",
    title: "Differences with the classic Atomik way",
    body: "## Differences with the classic Atomik way  There are two major differences which are views and the router.  Each controller have multiple actions (methods) and each action has its own view. While having for example one file for your controller in the *actions* directory you'll need many view files. Thus, instead of saving your views directly in the *views* directory you will have to save them in a folder named after your controller.  When using the router, the *action* parameter is mandatory. This plugin adds another mandatory parameter named *controller*. This parameter refers to the controller name whereas the *action* parameter refers to a method of the controller class.  The default route will use the last segment of the uri as the action name and the rest as the controller name.      // ArchivesController::view()     Atomik::set('app.routes', array( 	    'archives/:year/:month' =&gt; array( 		    'controller' =&gt; 'archives', 		    'action' =&gt; 'view' 	    )     ));  The default controller name is *index* and the default action name is *index*.  "
});

documentTitles["/docs/controllers.html#creating-controllers"] = "Creating controllers";
index.add({
    url: "/docs/controllers.html#creating-controllers",
    title: "Creating controllers",
    body: "## Creating controllers  "
});

documentTitles["/docs/controllers.html#creating-simple-controllers"] = "Creating simple controllers";
index.add({
    url: "/docs/controllers.html#creating-simple-controllers",
    title: "Creating simple controllers",
    body: "### Creating simple controllers  As said before, a controller is a class. It must inherits from `Atomik\Controller\Controller` and respect a naming convention. Your class has to be named using the controller's name starting by an upper case letter  suffixed with *Controller*.   Controller classes will be loaded, as any other classes, with the autoloader. Thus, your files must be named after your controller class.  So for example, with a controller named *users*, it must be saved in *app/actions/UsersController.php* and the class name will be *UsersController*.   If the action file is located in a sub folder, the class name has to follow the PSR-0 convention. For example, if the file is *app/actions/Auth/UsersController.php* the class name will be `Auth\UsersController`.  Then add public methods to your class. All public methods which does not start with an underscore will  be callable as an action.      class UsersController extends Atomik\Controller\Controller     { 	    public function index() 	    { 	    } 	 	    public function login() 	    { 	    }     }  The associated views must be located in the *app/views/users*. In our example, it would be *app/views/user/index.phtml* and *app/views/user/login.phtml*.  You can then use the following urls: &lt;http://example.com/user&gt; or &lt;http://example.com/user/login&gt;.  In classic actions, all defined variables were accessible from the view. This is not possible  anymore when using methods for scoping reasons. To forward variables to the view, simply define  class properties or return an array from your action method.  In *app/actions/UsersController.php*:      class UsersController extends Atomik\Controller\Controller     {         public $title = 'Users';  	    public function index() 	    { 		    return array('username' =&gt; 'peter'); 	    }     }      In *app/views/user/index.phtml*:      &lt;h1&gt;&lt;?php echo $title ?&gt;&lt;/h1&gt;     hello &lt;php echo $username ?&gt;  "
});

documentTitles["/docs/controllers.html#controller-utilities"] = "Controller utilities";
index.add({
    url: "/docs/controllers.html#controller-utilities",
    title: "Controller utilities",
    body: "### Controller utilities  First of all, you can define two methods `preDispatch()` and `postDispatch()` that will be called before and after each action. You can also define an `init()` method which will be called after the constructor.  Route parameters will be automatically mapped to method arguments.      Atomik::set('app.routes', array( 	    'archives/:year/:month' =&gt; array( 		    'controller' =&gt; 'archives', 		    'action' =&gt; 'view' 	    )     ));          // --------------------      class ArchivesController extends Atomik\Controller\Controller     { 	    public function view($year, $month) 	    { 	    }     } 				 				 The `$year` and `$month` argument will be taken from the route parameters. The order is not important.  "
});

documentTitles["/docs/controllers.html#using-controllers-in-pluggable-apps"] = "Using controllers in Pluggable Apps";
index.add({
    url: "/docs/controllers.html#using-controllers-in-pluggable-apps",
    title: "Using controllers in Pluggable Apps",
    body: "## Using controllers in Pluggable Apps  The plugin will be disabled when a pluggable application starts. It can be re-enabled using      $config = array();     Atomik\Controller\Plugin::start($config); "
});



documentTitles["/docs/events.html#events"] = "Events";
index.add({
    url: "/docs/events.html#events",
    title: "Events",
    body: "# Events  Events are one of the most important concept in Atomik. Callbacks can be registered to listen to any events. When an event is fired, all listening callbacks are called.   Events are implicitely declared when they're fired.  "
});

documentTitles["/docs/events.html#listening-to-events"] = "Listening to events";
index.add({
    url: "/docs/events.html#listening-to-events",
    title: "Listening to events",
    body: "## Listening to events  Atomik provides the `listenEvent()` method. It takes as first argument an event name and as second a callback. See &lt;http://php.net/callback&gt; for more information on callbacks.      Atomik::listenEvent('myEvent', function() { 	    // ...     });      Atomik::listenEvent('myArgEvent', function($arg1, $arg2) { 	    // ...     });  Listeners also have priorities. The priority is a number, smaller numbers have a higher priority. The priority is specified when registering the listener.      Atomik::listenEvent('myEvent', 'myEventCallback', 10);     Atomik::listenEvent('myEvent', 'myEventCallback2', 5); // will be called first  Multiple listeners can have the same priority. If you dim your listener more important and want it  to be called before other listeners of the same priority, you can use true as the fourth parameter.      Atomik::listenEvent('myEvent', 'myEventCallback', 10);     Atomik::listenEvent('myEvent', 'myEventCallback2', 10, true); // will be called first  "
});

documentTitles["/docs/events.html#firing-events"] = "Firing events";
index.add({
    url: "/docs/events.html#firing-events",
    title: "Firing events",
    body: "## Firing events  Events are fired using the `fireEvent()` method provided by Atomik.  It takes as first argument the event name and optionally as second an array of arguments for callbacks.      Atomik::fireEvent('myEvent');     Atomik::fireEvent('myArgEvent', array('arg1Value', 'arg2Value'));  This method returns an array with results from each callbacks. A string can also be returned when passing true as the third parameter. The string will be the concatanation of all results.      $results = Atomik::fireEvent('myEvent'); // array     $string = Atomik::fireEvent('myStringEvent', array(), true); // string  "
});

documentTitles["/docs/events.html#events-naming-convention"] = "Events naming convention";
index.add({
    url: "/docs/events.html#events-naming-convention",
    title: "Events naming convention",
    body: "## Events naming convention  While events name can be anything you want, Atomik uses a naming convention for its own events.  Events are composed using *Atomik* or a plugin name, followed by the method from which the event was fired and optionnally the event name. Each part is separated using \&quot;:\&quot; twice and should start with an upper case.  The method `Atomik::dispatch()` fires an event named *Atomik::Dispatch::Start*.  "
});



documentTitles["/docs/developing-plugins.html#developing-plugins"] = "Developing plugins";
index.add({
    url: "/docs/developing-plugins.html#developing-plugins",
    title: "Developing plugins",
    body: "# Developing plugins  "
});

documentTitles["/docs/developing-plugins.html#the-plugin-file"] = "The plugin file";
index.add({
    url: "/docs/developing-plugins.html#the-plugin-file",
    title: "The plugin file",
    body: "## The plugin file  A plugin is made of one file named the same way. For example the Db plugin is in the file *Db.php*. Plugin's file must always start with an uppercase letter.  Plugins are loaded at the beginning of a request, just after the configuration.  The content of the file is free or it can be a class.  To build more complex plugins you can instead of a file create a folder named after your plugin. Your PHP file goes into that folder and must be named *Plugin.php*.  When using folders, it is possible to add a sub folder named *libs* which will automatically be added to php's include_path.  A folder must be used when creating pluggable applications.  "
});

documentTitles["/docs/developing-plugins.html#configuration"] = "Configuration";
index.add({
    url: "/docs/developing-plugins.html#configuration",
    title: "Configuration",
    body: "## Configuration  As said in the \&quot;Using plugins\&quot; section, plugins can have custom configuration. To retrieve this configuration a `$config` variable is automatically available. It contains the array used in the configuration.      // In the configuration file:          Atomik::set('plugins', array(         'MyPlugin' =&gt; array(                'name' =&gt; 'Peter'         )     ));          // In the plugin file:          echo 'hello ' . $config['name'];  "
});

documentTitles["/docs/developing-plugins.html#using-a-class"] = "Using a class";
index.add({
    url: "/docs/developing-plugins.html#using-a-class",
    title: "Using a class",
    body: "## Using a class  For better application design it is advice to use a class to define your plugin. When loading a plugin, it will look for a class named like the plugin.  If this class has a static `start()` method, it will be called when the plugin is loaded with the plugin's custom configuration as argument.      class Db     {         public static function start($config)         {             // $config['name'] == 'Peter'         }     }  It is a good thing to always provide a default configuration. This can be done by merging a default configuration array with the user's configuration.  The class can contain static methods that will be automatically registered as callback on events. These methods have to start by \&quot;on\&quot; followed by the event name without the double \&quot;:\&quot;.      class Db     {         public static onAtomikDispatchStart()         {             // listener for Atomik::Dispatch::Start         }     }  You can prevent automatic callback registration by returning false in the start method.  "
});

documentTitles["/docs/developing-plugins.html#pluggable-applications"] = "Pluggable applications";
index.add({
    url: "/docs/developing-plugins.html#pluggable-applications",
    title: "Pluggable applications",
    body: "## Pluggable applications          Pluggable applications are really simple to create. Create a normal plugin using a folder.  Create your *Plugin.php* file. Call `Atomik::registerPluggableApplication()` when the plugin starts using the plugin name as first parameter (and eventually the pattern to  trigger the application as the second). Create standard Atomik folders inside your plugin  folder: *actions*, *views*, *helpers* and *layouts* and code your application normally.      class PluggApp     {         public static $config = array(             'route' =&gt; '/pluggapp/*'         );              public static start($config)         {             self::$config = array_merge(self::$config, $config);             Atomik::registerPluggableApplication('PluggApp', $config['route']);         }     }  A pluggable application can also have a file named *Application.php* at the root of the plugin folder. This file act the same way as the *bootstrap.php* file. It will be called before the pluggable application is dispatched.  If Atomik detects the *Application.php* file, a *Plugin.php* file is not necessary and the pluggable application will automatically be registered.  A pluggable application behaves as a normal Atomik application and all features are available.  The configuration will be reseted before the dispatch occurs. These applications can provide  their own config in their *Application.php* file like their own routes, default action...  The *pre\_dispatch.php* and *post\_dispatch.php* files can also be used.  Note that every url will be relative to the pluggable application's root. That is to say you  do not have to care of the route used to trigger your application. For this to work properly,  read carefully the next section.  `Atomik::registerPluggableApplication()` as more options which are described in the API reference.  "
});

documentTitles["/docs/developing-plugins.html#assets-and-urls"] = "Assets and urls";
index.add({
    url: "/docs/developing-plugins.html#assets-and-urls",
    title: "Assets and urls",
    body: "## Assets and urls  When using the default *.htaccess* file, plugins can have an *assets* folder which is accessible from the Web. Of course, to use this folder, the plugin must come as a folder.  You can use `Atomik::asset()` like with a normal application. However in the case of plugins,  asset's filename will be prepended with a template defined in *atomik.plugin\_assets\_tpl*.  The default is *app/plugins/%s/assets*. The *%s* sign will be replaced with the plugin name.      echo Atomik::asset('css/styles.css');     echo Atomik::pluginAsset('MyPlugin', 'css/styles.css');     // will output app/plugins/MyPlugin/assets/css/styles.css      Atomik::set('atomik.plugin_assets_tpl', 'plugins/%s/assets');     echo Atomik::asset('css/styles.css');     echo Atomik::pluginAsset('MyPlugin', 'css/styles.css');     // will output plugins/MyPlugin/assets/css/styles.css  It is not adviced to change the plugin's assets folder name as some plugins may not work  with your installation.  "
});

documentTitles["/docs/developing-plugins.html#loading-plugins-programmaticaly"] = "Loading plugins programmaticaly";
index.add({
    url: "/docs/developing-plugins.html#loading-plugins-programmaticaly",
    title: "Loading plugins programmaticaly",
    body: "## Loading plugins programmaticaly  It is of course possible to load plugins at runtime. Atomik provides a bunch of loading methods  so it's simpler for plugins to load plugins they depend on or to create custom plugins.  The most common method is `Atomik::loadPlugin()` which will load a plugin and use the user plugin's configuration (from the plugins key) if one is available.  If a plugin is not available, loading it will throw an exception. To prevent that you can use `Atomik::loadPluginIfAvailable()`.      Atomik::loadPlugin('Db');  You can also load plugins by specifying custom configuration. This is done using  ̀Atomik::loadCustomPlugin()`.      Atomik::loadCustomPlugin('Db', array('dbname' =&gt; 'test'));      // load plugins from a custom directory     Atomik::loadCustomPlugin('MyPlugin', array(), array('dirs' =&gt; '/custom/plugins/directory'));      // using a custom plugin class name (in this case the class name will be MyPluginCustomPlugin)     Atomik::loadCustomPlugin('MyPlugin', array(), array('classNameTemplate' =&gt; '%CustomPlugin'));      // do not call the start() method when loading plugins     Atomik::loadCustomPlugin('MyPlugin', array(), array('callStart' =&gt; false));  `Atomik::loadCustomPluginIfAvailable()` is also available.  Be aware that some plugins may need to listen to some specific events. If you register a plugin too late, the events may have already occured, making the plugin malfunction.  You can check if a plugin is already loaded using `Atomik::isPluginLoaded()` or if it's available using `Atomik::isPluginAvailable()`.  Finally, you can retreive all loaded plugins using `Atomik::getLoadedPlugins()`.  "
});


