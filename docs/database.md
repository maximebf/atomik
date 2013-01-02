
# Database

<div class="note">You will need the Db plugin which is bundled with Atomik</div>

The database plugin is a thin layer on top of [PDO](http://fr2.php.net/manual/en/book.pdo.php).

## Connecting

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

Insert data using `insert()` which takes as first argument the table name and as second
an array where keys are columns names. It returns the ̀PDOStatement` object that was
executed (in case you want to get the last inserted id for example).

    $data = array(
        'title' => 'post title',
        'content' => 'lorem ipsum...'
    );
    $db->insert('posts', $data);

Updating data is very similar using the `update()` function. It can take as third parameter
a where clause like in `select()`.

    $data = array('title' => 'modified title');
    $db->update('posts', $data, array('id' => 1));

Finally, deleting data is as easy. The second argument can be a where clause (if not specified,
all rows will be deleted).

    $db->delete('posts', array('id' => 1));
