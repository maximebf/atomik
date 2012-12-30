
# The global store

Atomik provides a global store where anything can be saved for the time of a request.
This global store acts like an associative array with key/value pairs.
It's mainly used to store the configuration.

## Accessing the store

Accessors are methods provided by the Atomik class that allow you to 
access the global store. They are six of them: *get*, *set*, *add*, *prepend*,
*has* and *delete*.

The `get()` method allows you to retrieve the value
associated to the key passed as first argument. If a second argument is 
specified it will be use as a default value in the case where the key is
not found.

    echo Atomik::get('key');
    echo Atomik::get('keyThatDoesntExist', 'defaultValue');

There's also a `Atomik::getRef()` method to obtain a
reference to the value. However this method do not have a default value parameter
and it will return null if the key is not found.

The `set()` method allows you to define a key and its
associated value. It will overwrite any existing value.

This accessor can also take an array as argument to set multiple key/value pairs
at once. This array will be merged with the store.

    Atomik::set('key', 'value');

    Atomik::set(array(
	    'key1' => 'value1',
	    'key2' => 'value2'
    ));

The `add()` method works like the `set()`
method but rather than replacing values when they already exists, adds them. For
example if the key points to an array, the value will be added to this array as a new
item. If the key points to a value which is not an array, it will be transformed to
one.

`prepend()` is exactly the same but adds the value at the beginning
of the array.

    Atomik::set('key1', array('item1'));
    Atomik::add('key1', 'item2');
    Atomik::add('key1', array('item3', 'item4'));
    $array = Atomik::get('key1'); // array('item1', 'item2', 'item3', 'item4')

The `has()` and `delete()`
methods only take a key as argument. The first one checks if the key exists
and the second deletes the key and its value. The method also returns the value
which had the deleted key or false if the key didn't exist.

    if (Atomik::has('key')) {
	    Atomik::delete('key');
    }

## Nested arrays

Dots can be used to access nested arrays. Each segment in the key has to point to a nested
array unless it's the last one.

    Atomik::set('users', array(
	    'paul' => array(
		    'id' => 1,
		    'age' => 20
	    ),
	    'peter' => array(
		    'id' => 2,
		    'age' => 33
	    )
    ));
    
    $paul = Atomik::get('users.paul'); // returns an array
    $paulAge = Atomik::get('users.paul.age'); // returns 20
    $peterId = Atomik::get('users.peter.id'); // returns 2
    
    Atomik::set('users.sofia', array(
	    'id' => 3,
	    'age' => 25
    ));
    
    $sofiaAge = Atomik::get('users.sofia.age');

You can also use paths in sub arrays when setting some values.

    Atomik::set(array(
	    'users' => array(
		    'paul.age' => 22,
		    'paul.friends' => array(
			    'peter.age' => 20
		    )
	    )
    ));
    
    echo Atomik::get('users.paul.age'); // 22
    
    var_export(Atomik::get('users.paul.friends'));
    array(
	    'peter' => array(
		    'age' => 20
	    )
    )

`Atomik::dimensionizeArray()` can be used to *dimensionize* any array.

When using an array, be aware that it will be *dimensionized* before being
merged. This is done using `Atomik::dimensionizeArray()`. It can be avoided
using false as the third argument of `set()`.

To avoid a key to be dimensionized, you can escape dots using double dots:

    Atomik::set('routes', array(
        '/users..json' => array('action' => 'users', 'format' => 'json')
    ));

## Accessing the global store from actions and views

You should always access the global store through `$this` from actions
and views. Get values using the same syntax as with arrays:

    $this['users'] = array(
        'paul' => array(
            'id' => 1,
            'age' => 20
        )
    );
    if (isset($this['users.paul'])) {
        $age = $this['users.paul.age'];
        unset($this['users.paul']);
    }

Accessors are also available as methods of ̀$this`:

    $age = $this->get('users.paul.age', 21);

## Using accessors with any array

Accessors can be used with any array. You need to pass as argument an array
(the position of the argument depends on the method). See the API guide for 
more information. Still, here's an example:

    $array = array();
    Atomik::set('key', 'value', $array);
    echo Atomik::get('key', null, $array);
