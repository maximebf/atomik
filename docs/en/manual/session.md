
# Session

## Starting and accessing the session

By default, Atomik will automatically starts the session. This can be turned off in the 
*atomik/start\_session* configuration key. 

*Atomik::flash()* will not work if the session is not started.

The session is available as the *session* key in the global store. Of course,
it still remains available as the $_SESSION super-global variable.

    echo Atomik::get('session/username');

## Flash messages

Flash messages are messages which are stored in the session and are available only once.
This allows to pass error or success messages from one page to another. 

To create a flash message call the *Atomik::flash()* method. It takes as
first parameter a message or an array of messages. Messages can also have labels. For example *error* or 
*success*. To specify a label, use the second argument. The default label is *default*.

    Atomik::flash('The action has completed successfully');
    Atomik::flash('The action has failed', 'error'); // with a label
    Atomik::flash(array('message1', 'message2'), 'error');

Flash messages can then be retreived using the flash selector. The value is a label name. Use *all* to
retreive all messages. It will return an array containing as key the label names and the associated
value is an array of messages. If you only retreives messages from one label, an array of messages will be returned.

    foreach (Atomik::get('flash:all) as $label => $messages) {
	    foreach ($messages as $message) {
		    // ...
	    }
    }

    foreach (Atomik::get('flash:my_label) as $message) {
	    // ...
    }

