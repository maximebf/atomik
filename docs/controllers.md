
# Controller

<div class="note">You will need the Controller plugin which is bundled with Atomik</div>

Atomik action files do not follow any conventions. However, some of you may have used MVC frameworks
where the business logic is coded in controllers. Controllers are classes where their methods are
actions.

This plugin adds support for controllers to Atomik. Once activated you must use controllers in your
actions. It is not possible to mix between the classic way and the controller way.

## Differences with the classic Atomik way

There are two major differences which are views and the router.

Each controller have multiple actions (methods) and each action has its own view. While having
for example one file for your controller in the *actions* directory you'll need many view
files. Thus, instead of saving your views directly in the *views* directory you will have to save
them in a folder named after your controller.

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

As said before, a controller is a class. It must inherits from `Atomik\Controller\Controller` and respect
a naming convention. Your class has to be named using the controller's name starting by an upper case letter 
suffixed with *Controller*. 

Controller classes will be loaded, as any other classes, with the autoloader. Thus, your files must be
named after your controller class.

So for example, with a controller named *users*, it must be saved in *app/actions/UsersController.php* and
the class name will be *UsersController*. 

If the action file is located in a sub folder, the class name has to follow the PSR-0 convention.
For example, if the file is *app/actions/Auth/UsersController.php* the class name will be `Auth\UsersController`.

Then add public methods to your class. All public methods which does not start with an underscore will 
be callable as an action.

    class UsersController extends Atomik\Controller\Controller
    {
	    public function index()
	    {
	    }
	
	    public function login()
	    {
	    }
    }

The associated views must be located in the *app/views/users*.
In our example, it would be *app/views/user/index.phtml* and *app/views/user/login.phtml*.

You can then use the following urls: <http://example.com/user> or <http://example.com/user/login>.

In classic actions, all defined variables were accessible from the view. This is not possible 
anymore when using methods for scoping reasons. To forward variables to the view, simply define 
class properties or return an array from your action method.

In *app/actions/UsersController.php*:

    class UsersController extends Atomik\Controller\Controller
    {
        public $title = 'Users';

	    public function index()
	    {
		    return array('username' => 'peter');
	    }
    }
    
In *app/views/user/index.phtml*:

    <h1><?php echo $title ?></h1>
    hello <php echo $username ?>

### Controller utilities

First of all, you can define two methods `preDispatch()` and `postDispatch()`
that will be called before and after each action. You can also define an `init()`
method which will be called after the constructor.

Route parameters will be automatically mapped to method arguments.

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

## Using controllers in Pluggable Apps

The plugin will be disabled when a pluggable application starts. It can be re-enabled using

    $config = array();
    Atomik\Controller\Plugin::start($config);
