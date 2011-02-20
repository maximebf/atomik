
# Ajax

The Ajax plugin allows you to call your actions using a javascript's XmlHttpRequest and sending back
json or the view without the layout.

For the plugin to be activated, the request must have the *X-Requested-With* HTTP header.
Nearly all javascript frameworks add this header.

When this header is detected, the layout will be automatically disabled. This can be avoided by setting
the *disable\_layout* configuration key to false.

It is also possible to return action variables as JSON instead of the view content. For that, you need
to add action names in the allowed key.

    Atomik::set('plugins/Ajax', array(
	    'allowed' => array(
		    'my-action'
	    );
    ));

This behaviour can be set as the default one by setting true to *allow\_all*.
In this case, variables will always be returned. It is then possible to disable this behaviour on a per
action basis using the *restricted* configuration key.

You can check if the current request is using AJAX by calling *AjaxPlugin::isEnabled()*.

Flash messages are also returned as an HTTP header. It is named *Flash-messages* and contains
an array encoded in javascript.

