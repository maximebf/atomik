# Get started

In this tutorial, you will learn how to create a simple blogging application in a few minutes.
Our blog will list posts and allow you to create new ones which will be stored in a database.
We will use a few plugins in the process.

Requirements:

 - PHP 5.3
 - A webserver (eg. Apache)
 - [Sqlite](http://www.sqlite.org/)

Install Atomik using the skeleton app as explained in the Installation section.
Atomik provides a skeleton application to get you started quickly. 

All future paths reference in this tutorial will be relative to your installation folder.
This path should be accessible from a web browser.

## The database

As said before, our blog application will need a database. To keep it simple, we'll use Sqlite
which is a very simple database engine that stores data into a single file.

Here is the database schema for this tutorial. Save it in *schema.sql*:

    CREATE TABLE posts (
        id       INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
        title    VARCHAR(200) NOT NULL,
        content  TEXT NOT NULL
    );

Now let's create the database file:

    $ sqlite3 -init schema.sql example.db
    sqlite> .exit

Will now need to configure the Db plugin so it can access the database. This plugin
simply creates a new `PDO` instance to which it adds some useful methods.

Open the *app/config.php* file. You'll see that we use the `Atomik::set()` method
to define configuration options. This method is one of many that allow you to
manipulate the global store, a place where you can store data shared accross your app.

In the plugins section, (line 5) add the following lines:

    // ...
    'plugins' => array(
        // ...
        'Db' => array(
            'dsn' => 'sqlite:example.db'
        )
    ),
    // ...

The Db plugins creates a `PDO` instance accessible through the *db* key in the global store.
This is all we need to connect to the database.

## Listing posts
    
A page in Atomik is made of two files: the first one is dedicated to the business logic, 
it is called an action. The second one is called a view and holds the presentation code, 
which in most cases is HTML.

Actions are located in the *app/actions* folder and views in the *app/views*
folder. For example, for a page named home, we would need a *home.php* file in the actions
folder and/or a *home.phtml* file in the views folder.

<div class="note">Both files are not mandatory when creating a page. At least one of them must exists.</div>

The default page is named *index*. We will list posts on this one. We'll first need to retrieve 
posts from the database. In the action file (*app/actions/index.php*), add the following lines:

    <?php
    $posts = $this['db']->select('posts');

Here we access the global store through `$this` which represent the `Atomik` object. The `select()`
function from the Db plugin creates an SQL SELECT statement.

Variables defined in the action are automatically available in the view. Thus, we can iterate 
through the `$posts` variable to list our posts.

    <h1>Blog</h1>
    <ul>
       <?php foreach ($posts as $post): ?>
            <li><?= $this->escape($post['title']) ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="<?= $this->url('add') ?>">Add a new post</a>

(Atomik automatically converts short tags to the long form)

It is a good practice to escape content before outputting it which is the goal of the `escape()` helper.
The `url()` helper generates urls from action names.

Helpers are utility functions available through `$this` in actions and views. Atomik comes bundled with
a few of them to help you get started quickly.

Now, navigate to [http://localhost](http://localhost). Don't worry if nothing shows up, we havn't created
any post yet.

## Creating new posts

We're going to create a new page called *add*. Let's start by creating the view
(*app/views/add.phtml*).

    <h1>New post</h1>
    <?= $form = $this->form($this->url('add')) ?>
      <p>
        <label for="title">Title:</label>
        <?= $form->input('title') ?>
      </p>
      <p>
        <label for="content">Content:</label>
        <?= $form->textarea('content') ?>
      </p>
      <?= $form->buttons('Add post') ?>
    </form>

In this view, we've used the `form()` helper which returns an object with functions to easily
render common HTML inputs.

Our action should only be executed when there's POST data. Atomik allows you to create
action files for specific HTTP methods. To do so, add a suffix to the action name with a dot followed 
by the HTTP method in lower case. Our action file will thus be named *app/actions/add.post.php*.

As a side note if you're new to web programming, the HTTP protocol defines multiple verbs (or methods).
Each one carry a meaning and eventually some data. The most common one is GET. It is the method
your browser uses to request a page. To send some data from the browser to the server, the browser
will perform a POST request.

The first thing we need to do is filter the data. This is always an important step when dealing with
data from an outside source as it is a common vectore of attack. We're going to use the `filter()` helper.

This helper is built on top of PHP's [filter extension](http://fr2.php.net/manual/en/book.filter.php).

This method works in two ways: it can filter a single value or it can filter an entire array.
We're obviously going to use the latter as we're going to filter the `$_POST` array.

To filter an array, the method needs a set of rules: an array listing the requirements for each
fields in the input data. For each field, we can use a filter and define if it's required.
The default filter is to sanitize strings (`FILTER_SANITIZE_STRING`) and we'll use that one.
We're only going to set fields as required.

    $fields = array(
        'title' => array('required' => true),
        'content' => array('required' => true)
    );

If the validation fails, the method will return false. In this case it will generate some error 
messages stored in *app.filters.messages*. We can then use the `flash()` helper to display them 
to the user.

    if (($data = $this->filter($_POST, $fields)) === false) {
        $this->flash($this['app.filters.messages'], 'error');
        return;
    }

Now that our data has been validated we're going to insert them in the database.
We'll use the `insert()` method from the Db plugin.

    $this['db']->insert('posts', $data);

Finally, we'll add a flash message announcing that the operation has been successful.
We'll also redirect the user to the index page.

    $this->flash('Post successfully added!', 'success');
    $this->redirect('index');

Below is the complete action:

    <?php
    
    $fields = array(
        'title' => array('required' => true),
        'content' => array('required' => true)
    );
    
    if (($data = $this->filter($_POST, $fields)) === false) {
        $this->flash($this['app.filters.messages'], 'error');
        return;
    }
    
    $this['db']->insert('posts', $data);
    
    $this->flash('Post successfully added!', 'success');
    $this->redirect('index');


## Viewing a post

We are now going to create a page named *view* to show a single post.

The page will need a request parameter named *id* which must contain the id
of a post. Let's create the action file (*app/actions/view.php*)
with these simple lines:

    <?php
    
    if (!isset($this['request.id'])) {
        $this->flash('Missing id parameter', 'error');
        $this->redirect('index');
    }
    
    $post = $this['db']->selectOne('posts', array('id' => $this['request.id']));

First we check if the id parameter is set. The *request* array from the global store
contains all the parameters given through the URL and the `$_GET` array.

The view (*app/views/view.phtml*) is also very simple:

    <h1><?= $this->escape($post['title']) ?></h1>
    <p>
        Published the <?= $post['publish_date'] ?>
    </p>
    <p>
        <?= $this->escape($post['content']) ?>
    </p>

Finally, we're going to modify the index view to add a link on post titles. Replace
the line where the post title is echoed with:

    <li>
        <a href="<?= $this->url('view', array('id' => $post['id'])) ?>">
            <?= $this->escape($post['title']) ?>
        </a>
    </li>
