
# Démarrage rapide
	
Dans ce tutoriel, vous allez apprendre à créer une application simple en utilisant Atomik. 
Cela ne va pas vous prendre beaucoup de temps, environ 15 minutes.

Notre blog va lister des messages et nous permettre d'en créer des nouveaux.
Il va utiliser une base de données pour stocker nos messages. Nous allons donc utiliser un plugin.

# Création et configuration de la base de donnée

Comme nous l'avons dit précédamment, notre blog aura besoin d'une base de données. D'abord, créons la base de données avec le code SQL suivant:

    CREATE TABLE posts (
	    id 				INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	    publish_date 	DATETIME NOT NULL,
	    title 			VARCHAR(200) NOT NULL,
	    content 		TEXT NOT NULL
    );

    CREATE TABLE comments (
	    id 				INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	    post_id 		INTEGER NOT NULL,
	    publish_date 	DATETIME NOT NULL,
	    content 		TEXT NOT NULL
    );

Maintenant, il faut configurer le plugin Db pour qu'il ait accès à cette base de données.
Nous utilisons la méthode *Atomik::set()*. Cela vous permet de définir des clés dans la configuration globale.
Dans le dossier *app/config.php*, ajoutez les lignes suivantes:

    Atomik::set('plugins/Db', array(
	    'dsn' 		=> 'mysql:host=localhost;dbname=blog',
	    'username' 	=> 'root',
	    'password' 	=> ''
    ));

Vous pouvez voir qu'on défini la clé *plugins/Db* comme un array contenant les informations de connexion.
Modifiez l'hôte, le nom de la base de données, l'identifiant et le mot de passe.

C'est tout ce que nous avons besoin pour se connecter à la base de données.

# Listé les messages

Avec Atomik une page est faite de deux fichiers : la première est dédiée à la logique applicative, on appelle cela communément une action.
La seconde est appelé la vue et contient la présentation, dans la plupart des cas, en HTML.

Les actions se trouvent dans le dossier *app/actions* et les vues, quant à eux, se trouvent dans le dossier *app/views*.
Par exemple, pour une page nommée *home*, nous aurions besoin d'un fichier *home.php* dans le dossier des actions
et un fichier *home.phtml* dans le dossier des vues.

Les deux fichiers ne sont pas obligatoire lors de la création d'une page. Au moins l'un d'eux doit exister.

La page par défault est nommée index. Nous allons lister les messages sur celle-ci. Nous allons d'abord avoir besoin de récupérer les messages de la base de données.
Dans le fichier action (*app/actions/index.php*), ajoutez les lignes suivantes:

    <?php
    $posts = Atomik_Db::findAll('posts');

C'est tout ce qu'on a besoin pour récupérer tous les messages de la base de données! La méthode *Atomik_Db::findAll()* 
prend comme premier argument un nom de table. Ici nous avons spécifié *posts*. Cela retourne un objet *PDOStatement*
avec toutes les lignes retournées.

Les variables définies dans l'action sont automatiquement disponibles dans la vue correspondante. Ainsi, nous pouvons parcourir la variable 
$posts pour lister nos messages.

    <h1>Blog</h1>
    <ul>
	    <?php foreach ($posts as $post): ?>
		    <li><?php echo $post['title'] ?></li>
	    <php? endforeach; ?>
    </ul>
    <a href="<?php echo Atomik::url('add') ?>">Add a new post</a>

Comme vous pouvez le constater cette liste utilise également la synthaxe alternative. 
C'est un conseil quand vous ajoutez du PHP dans les vues pour les rendre plus claires.

L'ajout de la page va être descrit dans le chapitre suivant.

Maintenant, rendez-vous ici: http://localhost. Ne vous inquiétez pas si rien ne s'affiche, 
nous n'avons pas encore créé de message.

# Créé de nouveaux messages

Nous allons créer une nouvelle page nommée *add*. C'est parti pour la création de la vue
(*app/views/add.phtml*)!

    <h1>New post</h1>
    <form action="" method="post">
	    <dl>
		    <dt><label for="title">Title:</label></dt>
		    <dd><input type="text" name="title" /></dd>
		    <dt><label for="content">Content:</label></dt>
		    <dd><textarea name="content" rows="10" cols="100"></textarea></dd>
		    <dt></dt>
		    <dd><input type="submit" /></dd>
	    </dl>
    </form>

Comme vous pouvez le voir, c'est un formulaire HTML très simple. We avons désormais besoin de 
manipuler les données du formulaire. Cela va prendre place dans le fichier action.

Notre action doit seulement être éxécutée quand il y a des données POST. Atomik vous permet de créer
des fichier action pour des méthodes HTTP spécifiques. Pour ce faire, ajoutez au nom de l'action suivi d'un point le nom 
de la méthode HTTP en minuscule. Notre fichier action va donc se nommer *app/actions/add.post.php*.

La première chose que nous avons besoin de faire est de filtrer les données. C'est toujours une étape importante 
lorsqu'il s'agit de données POST pour des raisons de sécurités. Nous allons utiliser *Atomik::filter()*.

Cette méthode fonctionne dans deux sens: elle peut filtrer une valeur scalaire ou elle peut filtrer un array tout entier.
Nous allons bien évidemment l'utiliser plus tard quand nous allons flitrer l'array $_POST.

Pour filtrer un array, la méthode a besoin de règles. La règle est un array répertoriant les clés
autorisées dans les données d'entrée. Pour chaque clé, nous pouvons utiliser un filtre et définir si il est requis.
Le filtre par défault est de sécuriser les chaînes de caractères (FILTER_SANITIZE_STRING) et nous allons utiliser celle-ci.
Nous allons seulement définir les champs comme requis.

    $rule = array(
	    'title' => array('required' => true),
	    'content' => array('required' => true)
    );

Maintenant nous pouvons filtrer les données en utilisant ces règles. Si la validation échoue, la méthode va retourner
*false*. Dans ce cas, cela générera quelques messages d'erreurs stockés dans *app/filters/messages*.
Nous pouvons utiliser *Atomik::flash()* pour les stocker.

    if (($data = Atomik::filter($_POST, $rule)) === false) {
	    Atomik::flash(A('app/filters/messages'), 'error');
	    return;
    }

Vous pouvez remarquer que nous utilisons la fonction A() qui est un alliase de
*Atomik::get()*.

Maintenant que nos données ont été validé, nous allons les insérer dans la base de données.
Nous allons utiliser la méthode *Atomik_Db::insert()*.

    $data['publish_date'] = date('Y-m-d h:i:s');
    Atomik_Db::insert('posts', $data);
		
		
Utiliser date() pourrait causer une erreur si la *timezone* n'est pas définie
dans le *php.ini*. Cela peut être résolu en appelant la fonction
date\_default\_timezone_set() lors de l'exécution.

Remarquez que nous définissons la *publish\_date* avant d'insérer les données.

Enfin, nous allons ajouter un *flash message* annonçant que l'opération a été réussi.
Nous allons aussi rediriger l'utilisateur à la page index.

    Atomik::flash('Post successfully added!', 'success');
    Atomik::redirect('index');

Vous pouvez retrouver l'action complète ci-dessous

    <?php

    $rule = array(
	    'title' => array('required' => true),
	    'content' => array('required' => true)
    );

    if (($data = Atomik::filter($_POST, $rule)) === false) {
	    Atomik::flash(A('app/filters/messages'), 'error');
	    return;
    }

    $data['publish_date'] = date('Y-m-d h:i:s');
    Atomik_Db::insert('posts', $data);

    Atomik::flash('Post successfully added!', 'success');
    Atomik::redirect('index');

# Visualiser un message

Nous allons maintenant créer un page appelé *view* pour visualiser un seul message.

La page va avoir besoin d'un paramètre GET nommé *id* qui doit contenir l'id
du message. Allons créer le fichier action (*app/actions/view.php*)
avec ces simples lignes:

    <?php

    if (!Atomik::has('request/id')) {
	    Atomik::flash('Missing id parameter', 'error');
	    Atomik::redirect('index');
    }

    $post = Atomik_Db::find('posts', array('id' => A('request/id')));
		

D'abors nous vérifions si le paramètre id est défini. Si ce n 'est pas le cas, nous créons un *flash message* et redirigeons 
l'utilisateur à la page index. Sinon, nous allons chercher le message demandé dans la base de données.

La vue (*app/views/view.phtml*) est aussi très simple:

    <h1><?php echo $post['title'] ?></h1>
    <p>
	    Publiée le <?php echo $post['publish_date'] ?>
    </p>
    <p>
	    <?php echo $post['content'] ?>
    </p>

Pour terminer, nous allons modifier la vue de l'index pour ajouter un lien sur les titre des messages. Remplacez
la ligne où le titre du message est écrit par:

    <li>
	    <a href="<?php echo Atomik::url('view', array('id' => $post['id'])) ?>"><?php echo $post['title'] ?></a>
    </li>

# Fin

Bien joué! Le tutoriel est terminé. Vous pouvez en apprendre davantage dans les chapitres suivants. 

