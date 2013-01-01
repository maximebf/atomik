
# Actions

## Introduction

Without using Atomik, one way of doing things would have been to create a file
per page. The page logic (i.e. connecting to a database, handling form data...) would
have been at the top of the file followed by the HTML. 

    <?php
	    if (count($_POST)) {
		    echo 'Form data received!';
	    }
    ?>
    <form>
	    <input type="text" name="data" />
	    <input type="submit" value="send" />
    </form>

This is BAD!! The application logic and the presentation layer should always be separated
as explained [here](http://en.wikipedia.org/wiki/Separation_of_concerns).

Now let's imagine that rather than directly doing both part in the same file we split it.
We would have three file: one with the logic, one with the HTML and one that include both.

    // page_logic.php
	
    <?php
    if (count($_POST)) {
	    echo 'Form data received!';
    }
    
    // page_html.php
    
    <form>
	    <input type="text" name="data" />
	    <input type="submit" value="send" />
    </form>
    
    // page.php
    
    <?php
    include 'page_logic.php';
    include 'page_html.php';

Now replace the third file (the one with the includes) with Atomik and you'll have the concept
behind Atomik. The logic script is named an action and the html a view.

## Action files

Actions are stored in the *app/actions* directory. Both the action and the 
view filename must be the same. Action files must have the *php* extension.
If the action or view filename starts with an underscore, the action won't be accessible using an url.

There are no requirements for the content of an action file. It can be anything you want. 
So you just do your logic as you used to.

Be aware that actions run in their own scope and not in the global scope as you
might think.

Variables declared in the action are forwarded to the view. If you want to keep some
variables private (i.e. which will only be available in your action) prefixed them
with an underscore.

    <?php
    $myPublicVariable = 'value';
    $_myPrivateVariable = 'secret';

You shouldn't use echo or write any HTML code inside an action.
As said before, the goal of an action is to separate the logic from the presentation.

If you would like to exit the application, avoid using exit() and prefer
`Atomik::end()` so Atomik can smoothly exit your application.

You can use folders to organize your actions. In this case, views must follow the same
directory structure. You can create an *index* action (*index.php*)
inside a folder and it will be use as the folder's default page. Views follow the same principle.

    app/actions/users.php           <- will be used if url = /users
    app/actions/users/index.php     <- will be used if url = /users AND if app/actions/users.php does not exist
    app/actions/users/messages.php  <- will be used if url = /users/messages whatever the default page is

## Actions and HTTP methods

Atomik allows you to create multiple files for one action, each of them targeting a specific HTTP
method. This enables RESTful websites to be build.

### Targeting HTTP methods

Method specific action files must be suffixed with the method name. So for example, if you have
a *user* action and you would like to target the POST method, you would create a
file named *user.post.php*. With the PUT method it would have been
*user.put.php*. These files must be located in the actions folder.

You can still create a global action file (in the previous example: *user.php*)
which will be executed before any method specific action. Variables from the global action are
available in the specific one.

The current http method is available in the *app.http_method* configuration key.

### Allowed methods and overriding the request's method

Allowed HTTP methods are defined in *app.allowed_http_methods*. By default,
all methods available in the protocol are listed but you may want to reduce that list.

Some clients does not handle well HTTP methods. Thus, it is possible
to override the request's method using a route parameter (which can be a GET parameter).
The default parameter name is *_method*. This can be changed in
*app.http_method_param*. It can also be disabled by setting false instead of
a string.

## Redirections and 404 errors

To redirect the user to another page you can use the `Atomik::redirect()` method.
It takes as argument the url to redirect to. By default, this url will first be process using 
`Atomik::url()`. This behaviour can be disabled by passing false as the second argument.
The response HTTP code can also be specified as the third argument.

    $this->redirect('home');
    $this->redirect('home', true, 303); // 303 http code

Triggering 404 errors is even simpler. Just call the `Atomik::trigger404()` method.

    $this->trigger404();

## Includes

Includes are php files containing common logic that you include in your actions.

Includes are stored either in *app/includes* or *app/libs*.
directories. This can be changed in *atomik.dirs.includes*.

To include a file from one of these directories use the `Atomik::needed()` 
method. It takes as first argument the path to the filename you wish to include relative to the
previous directories and without the extension.

    // includes app/includes/common.php
    Atomik::needed('common');

You can use sub directories. To include a file stored at *app/includes/libs/db.php*:

    Atomik::needed('libs/db');

`Atomik::needed()` also allows you to include classes using
their name. To do so, classes have to follow the PEAR naming convention
(http://pear.php.net/manual/en/standards.naming.php) or use PHP 5.3 namespaces.

    // app/libraries/Atomik/Db.php
    Atomik::needed('Atomik_Db');
    Atomik::needed('Atomik\Db');

`Atomik::needed()` is automatically registered as an spl\_autoload handler.
This can be modified by setting *false* to the configuration key named *atomik.class\_autoload*.

## Calling actions programmatically

When executing a request, the action and/or the view associated to it are
automatically called. You can however call other actions using Atomik's API.

To execute an action use the `Atomik::execute()` method. It takes
as first argument the action name.

By default, if a view with the same name is found, it is rendered and the return value
of the `execute()` method is the view output.

If no view is found, an empty string is returned. If false is used as second argument, 
the return value is an array containing all *public* variables from the action.

    $viewOutput = Atomik::execute('myAction');
    $variables = Atomik::execute('myAction', false);

Calling an action using `Atomik::execute()` does not mean an action file
must exist. However, in this case, a view file with the same name must exist. Otherwise,
an exception will be thrown.

Actions executed this way will also be influenced by the HTTP method. You can specify a specific method
by appendinf it to the action name. The global action will also be executed.

    $viewOutput = Atomik::execute('myAction.post');

