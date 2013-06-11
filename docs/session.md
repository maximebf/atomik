# Session

<div class="note">These features need the Session and Flash plugins which are bundled with Atomik</div>

## Starting and accessing the session

You'll need to register the Session plugin:

    Atomik::add('plugins', array(
        'Session'
    ));

By default, Atomik will automatically starts the session. This can be turned off
using *autoload* in the plugin's configuration.

The session is available as the *session* key in the global store. Of course,
it still remains available as the `$_SESSION` super-global variable.

    echo Atomik::get('session.username');

## Flash messages

Flash messages are messages which are stored in the session and are available only once.
This allows to pass error or success messages from one page to another. 

To create a flash message call the `flash()` helper. It takes as
first parameter a message or an array of messages. Messages can also have labels. 
For example *error* or *success*. To specify a label, use the second argument. 
The default label is *default*.

    $this->flash('The action has completed successfully');
    $this->flash('The action has failed', 'error'); // with a label
    $this->flash(array('message1', 'message2'), 'error');

Flash messages can then be retreived using the *flash\_messages* key:

    foreach (Atomik::get('flash_messages') as $label => $messages) {
	    foreach ($messages as $message) {
		    // ...
	    }
    }

    foreach (Atomik::get('flash_messages.my_label') as $message) {
	    // ...
    }

