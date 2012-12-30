
# Events

Events are one of the most important concept in Atomik.
Callbacks can be registered to listen to any events. When an event
is fired, all listening callbacks are called. 

Events are implicitely declared when they're fired.

## Listening to events

Atomik provides the `listenEvent()` method.
It takes as first argument an event name and as second a callback.
See <http://php.net/callback> for more information on callbacks.

    Atomik::listenEvent('myEvent', function() {
	    // ...
    });

    Atomik::listenEvent('myArgEvent', function($arg1, $arg2) {
	    // ...
    });

Listeners also have priorities. The priority is a number, smaller numbers have a higher priority.
The priority is specified when registering the listener.

    Atomik::listenEvent('myEvent', 'myEventCallback', 10);
    Atomik::listenEvent('myEvent', 'myEventCallback2', 5); // will be called first

Multiple listeners can have the same priority. If you dim your listener more important and want it 
to be called before other listeners of the same priority, you can use true as the fourth parameter.

    Atomik::listenEvent('myEvent', 'myEventCallback', 10);
    Atomik::listenEvent('myEvent', 'myEventCallback2', 10, true); // will be called first

## Firing events

Events are fired using the `fireEvent()` method provided by Atomik. 
It takes as first argument the event name and
optionally as second an array of arguments for callbacks.

    Atomik::fireEvent('myEvent');
    Atomik::fireEvent('myArgEvent', array('arg1Value', 'arg2Value'));

This method returns an array with results from each callbacks. A string can also be returned
when passing true as the third parameter. The string will be the concatanation of all results.

    $results = Atomik::fireEvent('myEvent'); // array
    $string = Atomik::fireEvent('myStringEvent', array(), true); // string

## Events naming convention

While events name can be anything you want, Atomik uses a naming convention for its own events.

Events are composed using *Atomik* or a plugin name, followed by the method from which the
event was fired and optionnally the event name. Each part is separated using ":" twice and should
start with an upper case.

The method `Atomik::dispatch()` fires an event named *Atomik::Dispatch::Start*.

