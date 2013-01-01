
# Database

<div class="note">You will need the Db plugin which is bundled with Atomik</div>

The database plugin is a thin layer on top of [PDO](http://fr2.php.net/manual/en/book.pdo.php).

## Connecting

There are 3 configuration options:

 - *dsn*
 - *username*
 - *password*

Activate the plugin:

    Atomik::set('plugins.Db', array(
        'dsn' => 'mysql:host=localhost;dbname=example',
        'username' => 'root',
        'password' => 'rootpassword'
    ));

This creates a `PDO` instance which is stored under the *db* key in the global store.

    $db = Atomik::get('db');

    // or in actions:

    $db = $this['db'];

You can then use this object as you would with a normal PDO instance:

    $stmt = $this['db']->prepare('insert into posts (title, content) values (?, ?)');
    $stmt->execute(array('my new post', 'lorem ipsum ...'));

## Querying data

Some useful methods are added to quickly query the database. The `select()` function
takes as first argument the table name. By default it will execute a SELECT * statement.

    $posts = $db->select('posts');

The second argument is a where statement. It can either be an SQL string (without the WHERE
keyword) or an array of column/value mapping:

    $clothes = $db->select('products', "category = 'clothes'");
    // or:
    $clothes = $db->select('products', array('category' => 'clothes'));

To select only one row, use `selectOne()`.

To select the value of the first column of the first line, use `selectValue` which needs
a column name as the second argument.

    $postTitle = $db->selectValue('posts', 'title', array('id' => 1));

Finally, you can count using `count()`:

    $nbClothes = $db->count('products', array('category' => 'clothes'));

## Manipulating data

