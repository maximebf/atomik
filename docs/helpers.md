
# Helpers

Helpers are small utility functions, accessible through the `Atomik` object. They are
loaded on demand.

## Creating helpers

Helpers in Atomik are stored in *app/helpers*. For example, a `format_date()` helper would 
be stored in *app/helpers/format\_date.php*. The helpers directory can be changed using
*atomik.dirs.helpers*.

You can then define your helper in two ways: as a function or as a class. If you're using a function, 
just create one with the same name as the helper.

    function format_date($date)
    {
        // do the formating
        return $date;
    }

You can also use a class which can be pretty useful for more complex cases. The class name is a 
camel case version (ie. without any underscores or spaces, all words starting with an upper case) 
of the helper name suffixed with *Helper*. In our example, it would be 
`FormatDateHelper`. This class also needs to have a method named like the 
helper name but in camel case and starting with a lower case. In this case, it would be 
`formatDate()`.

    class FormatDateHelper
    {
        public function formatDate($date)
        {
            // do the formating
            return $date;
        }
    }

## Using helpers

Helpers are callable from any action or view file.
They are accessible as methods of `$this`.

    <span class="date"><?php echo $this->format_date('01-01-2009') ?></span>

## Registering helpers

You can also registers helper using the `Atomik::registerHelper()` function:

    Atomik::registerHelper('say_hello', function() {
        echo 'hello';
    });
