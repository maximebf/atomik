
# Db

The Db plugin provides database features over PDO. Thus, it is advice to know
how to use PDO before using it. Check out http://php.net/pdo for more information.

## Connecting to a database

The plugin can automatically create a default instance (which will be named default). To
do so, you must at least define the *dsn* config key (in the plugin configuration).

The username and password key can also be used.

    Atomik::set('plugins/Db', array(
	    'dsn' => 'mysql:host=localhost;dbname=mydb',
	    'username' => 'root',
	    'password' => ''
    ));

The *table\_prefix* key can be used to define a default table prefix.

## Querying the database

### Using PDO-like methods
				
The *Atomik\_Db* class provides three alias methods to PDO methods: *query()*, *exec()* and *prepare()*.
The two last one behave exactly the same way. However *query()* is a little different.

Under the hood it creates a PDO statement using prepare and executes it. Thus, the method also allows
an additional argument which can contains an array to pass to the execute call of the statement.

    $results = Atomik_Db::query('select * from posts');
    $results = Atomik_Db::query('select * from posts where id=?', array(1));

Using other PDO methods with *Atomik\_Db*

    $statement = Atomik_Db::prepare('select * from posts');
    $statement->execute();

    $results = Atomik_Db::exec("insert into posts (content) values ('my new post')");

### Using *find* methods
				
The *Atomik\_Db* class also provides two powerful methods to query the database: *find()* and *findAll()*. 
They are exactly the same but the first one will return only one record whereas the second all of them.

Conditions are specified as an associative array. The keys are database fields.
An sql string can also be used instead of the array.

    // all records from the posts table
    $results = Atomik_Db::findAll('posts');

    // only records from the author 3
    $results = Atomik_Db::findAll('posts', array('author' => 3));
    // or
    $results = Atomik_Db::findAll('posts', 'author = 3');

You can also specify an order by and a limit clause. Respectively as the third and fourth arguments.

    // all records from the posts table ordered by creation_date
    $results = Atomik_Db::findAll('posts', null, 'creation_date ASC');

    // the first 10 records from the posts table
    $results = Atomik_Db::findAll('posts', null, '', '10');

    // the first 10 records from the posts table ordered by creation_date
    $results = Atomik_Db::findAll('posts', null, 'creation_date' , '10');

    // records 10 to 20 from the posts table
    $results = Atomik_Db::findAll('posts', null, '', '10, 10');

Working with the result of find methods

    $posts = Atomik_Db::findAll('posts');
    foreach ($posts as $post) {
	    echo $post['title'];
	    echo $post['content'];
    }

### Counting records

You can perform SELECT COUNT(\*) queries using *Atomik\_Db::count()*.
This method takes the same parameters as *Atomik\_Db::find()*.

    $numberOfPosts = Atomik_Db::count('posts');

## Manipulating data

### Insert

The *Atomik\_Db::insert()* method can be use to insert data into the database. It takes
as first argument the table name and as second an associative array where keys are fields name.

    Atomik_Db::insert('posts', array('title' => 'my first posts', 'content' => 'hello world'));
    // will execute: insert into posts (title, content) values('my first post', 'hello world')

All values are automatically escaped.

### Update

*Atomik_Db_Instance::update()* works pretty much the same. However it takes as the last argument
an array of conditions (the same way as in find methods) or an sql string.

    Atomik_Db::update('posts', array('content' => 'updated hello world'), array('id' => 1));
    // will execute: update posts set content = 'updated hello world' where posts.id = 1

### Delete

Finally, the *Atomik_Db_Instance::delete()* works the same as find methods (without the order by and
limit arguments).

    Atomik_Db::delete('posts', array('id' => 1));
    // will execute: delete from posts where posts.id = 1

    Atomik_Db::delete(array('posts', array('id' => 1)));
    // will execute: delete from posts where posts.id = 1

    Atomik_Db::delete('posts', 'id = 1');
    // will execute: delete from posts where id = 1

## Creating queries using Atomik\_Db\_Query

*Atomik\_Db\_Query* provides a way to create sql queries without writing a line of sql!

### Building queries

Using *Atomik\_Db\_Query* can be thought as similar to writing sql queries. Methods can
be chained so that the flow of a query is not disrupted.

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->where('id = ?', 1);

*select()* takes as arguments which fields to select.

*from()* takes as argument a table name. This method can be called
several times to query from multiple tables. An alias can be specified as second argument.

*where()* can be used in multiple ways. As shown above, it can be an sql string.
This string can contain positional parameter which can then be specified as method arguments.

The method can also take as first argument an array where keys are field's name. All values will be
automatically escaped. This can be prevented using the *expr()* method of the query
object. It takes as argument the value which won't be escaped.

Multiple call to *where()* can be performed. They will be concatenated using the AND
sql operator. If you want to use OR instead you can use the *orWhere()* method.

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->where('id = 1');

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->where('name = ?', 'peter')->where('password = ?', 'foo');

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->where('join_date = ?', $query->expr('NOW()'));

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->where(array('id' => 1))->orWhere('id = 2');

*Atomik\_Db\_Query* also allows you to perform joins using *join()* and group by using *groupBy()*. 
You can also specify an having clause using *having()* (which works the same as *where()*);

An order by clause can be specified using *orderBy()* which takes a field name as first
argument and optionally the direction (ASC or DESC) as second.

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->orderBy('name');

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->orderBy('name ASC');

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->orderBy('name', 'DESC');

Finally, you can specify a limit clause which takes either a single argument which will be the length or two
arguments where the first one will be the offset and the second the length.

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->limit(10);

    $query = new Atomik_Db_Query();
    $query->select()->from('users')->limit(10, 10);

### Executing the query

The quickest way to execute the query is by simply calling the *execute()* method of the query object.

    $result = $query->execute();

You can also pass query objects to *Atomik\_Db::query()* without the need to specify
the parameters as second argument.

    $result = Atomik_Db::query($query);

You can also get the generated sql using *toSql()* or using the object in a string context (echo for example).

If you're using parameters in your query, they won't be included in the sql string (the question mark, or the key will
be kept). All parameters can be retrieved using *getParams()*.

    $stmt = $pdo->prepare($query->toSql());
    $stmt->execute($query->getParams());

## Using the console and sql scripts

The Console plugin if needed for this section.

The Db plugin offers facilities to manage sql scripts. If you save your scripts in the
*app/sql* folder, they can be executed with a single command.

This command is *db-create*. You can also use *db-create-sql* to preview
the sql that will be executed.

    php index.php db-create-sql
    php index.php db-create

Plugins can also provide their own scripts in their own sql folder.

If you want to only execute the sql from certain plugins, you can specify which one, separated by a space,
in the sql-create command. To only execute scripts from your application, use App as filter.

    php index.php db-create App Blog

## Atomik_Db and Instances

The Db plugin allows you to manage as many connections as you want. Each connection is bound to a manager
class of type *Atomik\_Db\_Instance*.

The *Atomik\_Db* is only a frontend to an instance, each static methods forwarding the call to the 
*Atomik\_Db\_Instance* object. Thus, all static methods are available as non-static in any *Atomik\_Db\_Instance*.

### Managing instances

Each instances managed by *Atomik\_Db* must be named.

You can add instances to *Atomik\_Db* using *addAvailableInstance()*.
The first argument is the name and the second the instance object.

    Atomik_Db::addAvailableInstance('db1', $instance);

You can then check if an instance is available using *isInstanceAvailable()* and
retrieve all instances using *getAvailableInstances()*.

Finally, it is possible to create and register an instance using *createInstance()*.
It takes as first argument the instance name and then the same as argument as *Atomik\_Db\_Instance*'s
constructor. There is a fifth parameter, which is true by default, that, if true, will set the new instance as the
default one.

    $instance = Atomik_Db::createInstance('db1', $dsn, $username, $password);

The default instance can be set using *Atomik_Db::setInstance()*. It takes
as argument an instance name or object. It can be retrieve using *getInstance()*.

