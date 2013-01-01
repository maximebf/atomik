
# Views

## Views

Views are stored in the *app/views* directory. The default file extension is *phtml*.

The content of a view file is, as the action file, free. It should mostly be text or 
HTML (or any presentation content, such as XML).

PHP can be used to print variables from the action or to provide presentation logic like
loops.
	
    <html>
	    <head>
		    <title>Example</title>
	    </head>
	    <body>
		    <?php echo $myPublicVariable; ?>
	    </body>
    </html>

## Layout

It is common in websites that all pages share the same layout. Atomik allows you to define
a layout that will be used with all views.

The layout will be rendered after the view has been rendered. The output of the view will be
pass to the layout as a variable named `$contentForLayout`. 
Layouts are rendered the same way as views.

Layouts can be placed in the *app/views* or *app/layouts* directories.
The file extension is the same as the one for views.

The layout name to use has to be defined in the *app.layout* configuration key. If the
value is false (which is the default), no layout will be used.

The layout can be disabled at runtime by calling `Atomik::disableLayout()`.
It can later be re-enabled by passing false as argument to the same method.

    // app/views/_layout.phtml

    <html>
	    <head>
		    <title>My website</title>
	    </head>
	    <body>
		    <h1>My website</h1>
		    <div id="content">
			    <?php echo $contentForLayout; ?>
		    </div>
	    </body>
    </html>
    
    // app/config.php
    
    Atomik::set('app.layout', '_layout');

Mutliple layouts can also be used. Just use an array instead of a string in the configuration key. 
Layouts will be rendered in reverse order (the first one in the array wrap the second, the second 
the third, ...).

## View contexts

It is sometimes needed to return content in different formats. Rather than creating multiple actions 
doing the same thing, Atomik allows you to create a view for each content type. This is called view 
contexts. The correct view is rendered depending on the current context.

The context is defined using a route parameter. By default it is called *format*. This can be changed in
*app.views.context\_param*. As specified in the urls chapter, the format parameter is by default the
file extension. Which means that using an url like index.xml will result in using the xml context.

The default view context is *html* but it can be changed in *atomik.views.default\_context*.

To create a view for a context just suffix the view name with the context name like an extension. 
For example, let's say we have an *article* view. The filename for the xml context would be 
*article.xml.phtml*. Some context may not need any prefix like the *html* one.

Depending on the view context, the layout can be disabled and the response content-type can be changed. 
The file prefix can also be specified. All of this is done in *app.views.contexts*.
		
Creating a custom view context:

    Atomik::set('app.views.contexts.rdf', array(    // the context name, ie. the file extension in the url
	    'suffix'        => 'rdf',                   // the view's file extension suffix (set to false for no suffix)
	    'layout'        => false,                   // disables the layout
	    'content-type'  => 'application/xml+rdf'    // the response's content type
    ));

Now you can call an url like http://example.com/article.rdf. In this case the view filename
would be *article.rdf.phtml*, the layout would be disabled and the response content type
would be *application/xml+rdf*.

If a view context is not defined under *app.views.contexts*, the file prefix will be the context name,
the layout won't be disabled and the response content type will be *text/html*.

By default, four contexts are defined: html, ajax, xml and json. The ajax context is the same 
as html but with the layout disabled. The last two disable the layout and set the appropriate 
content type.

## Controlling views

### View's filename extension

The default filename's extension for views is *phtml* as said before. This can be change using
the configuration key named *app.views.file\_extension*.

### Do not render the view from the action

While the action is executing, you may want to avoid rendering the associated view. This can easily be done
by calling `Atomik::noRender()` from your action.

### Modify the associated view from the action

While the action is executing, you may want to render a different view. In this case, you can use
`Atomik::setView()` from your action. It takes as unique argument a view name.

### Using a custom rendering engine

The default rendering process only uses php's include function. You may however want to use a 
template engine for example. This is possible by specifying a callback in *app.views.engine*.

The callback will receive two parameters: the first one will be the filename and the second an 
array containing the view variables.

    function myCustomRenderingEngine($filename, $vars)
    {
	    // your custom engine
	    return $renderedContent;
    }

    Atomik::set('app.views.engine', 'myCustomRenderingEngine');

The custom rendering engine will be used whenever `Atomik::render()`,
`Atomik::renderFile()` or `Atomik::renderLayout()` is used.

## Rendering views programmatically

When executing a request, the action and/or the view associated to it are
automatically called. You can however render other views using Atomik's API.

The most useful use of this it to render partial views, small part of presentation
code that is reusable.

To render a view use the `Atomik::render()` method.

It takes as first argument the view name and optionally as second argument
an array of key/value pairs representing variables. 
The method returns the view output.

    $viewOutput = Atomik::render('myView');
    $viewOutput = Atomik::render('myView', array('var' => 'value'));

It is also possible to render any file using `Atomik::renderFile()`. It takes
as first parameter a filename. Variables can also be passed like with `Atomik::render()`.

You can also render contextual views by adding the file extension prefix to the view name.

