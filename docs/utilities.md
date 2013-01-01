
# Utilities

All these utilities are helpers bundled with Atomik.

## Escaping text

It is a common (and very good) practice to escape data when outputting it on the page.
The `escape()` helper is dedicated to this purpose.

    echo $this->escape('my text');

This helper relies on other functions to escape data. It simply executes them one after an other 
and returns the result. You can for example, execute the `htmlspecialchars()` function followed by `nl2br()`.

The functions to execute are grouped under profiles. Thus, you can create multiple escaping profiles 
depending on the data you need to escape. Profiles are defined in the *app.escaping* 
configuration key. To specify which functions to execute in a profile, you can use a string or an 
array of strings. Functions will be executed in the order they appear in the array.
The default profile is called *default*.

The profile is specified as the last argument of the method.
	
    // creating profiles
    Atomik::set('app.escaping', array(
	    'default' => array('htmlspecialchars', 'nl2br'),
	    'url' => 'urlencode'
    ));

    // equivalent of nl2br(htmlspecialchars('my text'))
    echo $this->escape('my text');

    // equivalent of urlencode('my url param')
    echo $this->escape('my url param', 'url');

## Friendly urls

Having a router without a way to make friendly urls wouldn't be a complete feature. 
The `linkify()` helper transforms any string to a url friendly version.

    echo $this->friendlify('My text in the url');
    // will echo my-text-in-the-url


## Filtering and validating data

Filtering and validating user input is a very important task and Atomik had to provide a helper for
this purpose. This helper is *filter()* and it heavily relies on PHP's filter extension.

PHP's filter extension is built-in since version 5.2 and its documentation is available 
at <http://php.net/filter>.

You can find a good documentation (better than the official one) about available filters on the w3schools
website at <http://www.w3schools.com/php/php_ref_filter.asp>

To understand and use `filter()` you must know how to use PHP's filter
extension. However, Atomik's method adds some features.

`filter()` has the same arguments as `filter_var()`.
However, you can also use a regular expression as filter in the second argument. The regexp must use
slashes as delimiters. You can also define custom filters in the *app.filters.callbacks*
configuration key and use the callback name as filter.

    // using a php filter
    $result = $this->filter('me@example.com', FILTER_VALIDATE_EMAIL);
    $result = $this->filter('me@example.com', 'validate_email'); // using the filter name instead of its id
    $result = $this->filter('example.com', FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);

    // using a regexp
    $result = $this->filter('12478', '/\d+/');

    // using a callback
    Atomik::set('app.filters.callbacks.my_custom_filter', 'myFilterFunction');
    $result = $this->filter($data, 'my_custom_filter');

It will return false if an error occured, or the value otherwise.

The method can also be used to filter arrays in the same way as `filter_var_array()`. It works
exactly the same as this function but it also adds more features.

The method will return false if any of the item failed to validate. This can be turned off by passing
false as the fourth argument.

While the second argument is named a definition in the PHP's extension, Atomik called it rules. 
Rules are arrays where keys are field's name and their value a filter or an array 
(like with ̀filter_var_array()`). You can pass a rule as the second argument. 
Rules can also be defined in the *app.filters.rules* configuration key. 
You can then use their name instead of an array as the second argument.

The method also adds the notion of required fields. If you set the *required* key to true in 
the field's array, the validation will fail if the field is missing or empty. 

When a field is empty but not required, its value will be null. This can be changed by setting 
the *default* key in the field's array.

The *filter* key in the field's array follows the same rule as the filter parameter described previously. 
Thus it can be a filter's id or name, a regexp or a custom filter's name.

The helper also supports multi-dimension array as input.

Finally, support for validation messages has also been added. When filtering the array, if any values 
failed validating, a message will be created. Messages can then be retreived in the 
*app.filters.messages* configuration key. There ar two default messages configured in 
*app.filters.default\_message* and *app.filters.required\_message*.
The former will be used when a field failed to validate while the later when the required condition has not been met.

When setting the two message keys, you can use *%s* which will be replaced with the field's name. 
As field names aren't usually pretty, you can use the *label* key in the field's array to define 
the text which will be used to replace *%s*.

You can override the failed to validate message for each field by setting the *message* 
key in the field's array.

    // using a custom message when a field is missing or empty
    Atomik::set('app.filters.required_message', 'You forgot to fill the %s field!');

    // the data to validate
    $dataToFilter = array(
	    'username' => 'peter',
	    'email' => 'peter@example.com'
    );

    // our rules
    $fields = array(
	    'username' => array(
		    'filter' => FILTER_SANITIZE_STRING,
		    'required' => true
	    ),
	    'email' => array(
		    'filter' => 'validate_email',
		    'required' => true,
		    'message' => 'You must provide a valid email address' // custom message when the field failed to validate
	    )
    );

    if (($filteredData = $this->filter($dataToFilter, $fields)) === false) {
	    // failed validation, showing messages
	    Atomik::flash(A('app.filters.messages'), 'error');
	    return;
    }

    // success
    // do something with $filteredData
