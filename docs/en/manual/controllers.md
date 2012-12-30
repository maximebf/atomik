
# Controller

<div class="note">You will need the Controller plugin which is bundled with Atomik</div>

Atomik action files do not follow any conventions. However, some of you may have used MVC frameworks
where the business logic is coded in controllers. Controllers are classes where their methods are
actions.

This plugin adds support for controllers to Atomik. Once activated you must use controllers in your
actions. It is not possible to mix between the classic way and the controller way.

The plugin will be disabled when a pluggable application starts. It can be enabled using

    Atomik\Controller::$disable = false;

## Differences with the classic Atomik way

There are two major differences which are views and the router.

Each controller have multiple actions (methods) and each action has its own view. While having
for example one file named *user.php* in the *actions* directory which
contains the *UserController* class (we'll come to that later), you'll need many view
files. Thus, instead of saving your views directly in the *views* directory you will have to save
them in a folder named after your action.

When using the router, the *action* parameter is mandatory. This plugin adds another mandatory
parameter named *controller*. This parameter refers to the controller name whereas the *action*
parameter refers to a method of the controller class.

The default route will use the last segment of the uri as the action name and the rest as the controller name.

    // ArchivesController::view()
    Atomik::set('app.routes', array(
	    'archives/:year/:month' => array(
		    'controller' => 'archives',
		    'action' => 'view'
	    )
    ));

The default controller name is *index* and the default action name is *index*.

## Creating controllers

### Creating simple controllers

As said before, a controller is a class. The only condition is in the naming convention. Your class has to be named
using the controller name starting by an upper case letter suffixed with *Controller*. So for example,
with a controller named *user* (saved in *app/actions/user.php*), the class name will be *UserController*. 

If the action file is located in a sub folder, the class name as to follow the PSR-0 convention.
For example, if the file is *app/actions/auth/user.php* the class name will be *Auth\UserController*.

Then add public methods to your class. All public methods which does not start with an underscore will 
be callable as an action.

Don't forget to also create the view associated to each action.

    class UserController
    {
	    public function index()
	    {
	    }
	
	    public function login()
	    {
	    }
    }

Also create two view files: *app/views/user/index.phtml* and *app/views/user/login.phtml*.
Note that they are saved under the *app/views/user* directory where the last folder is the 
controller name.

You can then use the following urls: <http://example.com/user> or <http://example.com/user/login>.

In classic actions, all defined variables where accessible from the view. This is not possible 
anymore when using methods for scoping reasons. To forward variables to the view, simply define 
class properties.

    // app/actions/user.php

    class UserController
    {
	    public function index()
	    {
		    $this->username = 'peter';
	    }
    }
    
    // app/views/user/index.phtml

    hello <php echo $username ?>

### Creating controllers by subclassing Atomik\Controller\Controller

Subclassing Atomik\Controller\Controller when creating a controller class brings some nice features.

First of all, you can define two methods `_before()` and ̀_after()`
that will be called before and after each action.

Secondly, route parameters will be automatically mapped to method arguments.

    Atomik::set('app.routes', array(
	    'archives/:year/:month' => array(
		    'controller' => 'archives',
		    'action' => 'view'
	    )
    ));
    
    // --------------------

    class ArchivesController extends Atomik\Controller\Controller
    {
	    public function view($year, $month)
	    {
	    }
    }
				
				
The `$year` and `$month` argument will be taken from the route parameters.
The order is not important.
