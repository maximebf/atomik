
# Configuration

Lors du changement de version d'Atomik entre 2.1 et 2.2, il y a eu une nette cassure de compatibilité.
Beaucoup de clés ont été modifiées. Pour plus d'information, rendez-vous dans l'annexe A. La façon dont le fichier
d'amorçage est inclue a aussi été changée. L'ancien fichier d'amorçage a maintenant été remplacé par un fichier de configuration.

## Le fichier de configuration

Atomik fournit un fichier de configuration par défaut pour tous (Atomik préférant le respect des conventions
à la place du principe de configuration). Cependant, vous pouvez le réécrire et placer la configuration des plugins
ou la votre à l'intérieur.

Trois formats de fichiers sont désormais compatibles : PHP (par défaut), INI ou JSON.
La format choisit dépent de l'extension de ce fichier (entre .php, .ini ou .json, en minuscule).

Le fichier est par défaut placé dans le répertoire app et appelé *config*.
Il peut être changé dans *atomik/files/config*. Attention : ne pas spécifier d'extension.

Lorsque vous utilisez PHP, le script peut retourner un tableau qui pourra être utilisé avec la méthode *Atomik::set()*.
Vous pouvez alors utiliser directement les accesseurs dans le fichier.

    return array(
        'ma_cle' => 'ma valeur',
        'plugins' => array(
            'Db' => array(
                'dsn' => 'mysql:host=localhost',
                'username' => 'root'
            )
        ),
        'atomik/files' => array(
            'pre_dispatch' = 'pre.php'
            'post_dispatch' = 'post.php'
        )
    );

Lorsque vous utilisez INI, vous pouvez utiliser des points dans la clé pour spécifier des clés multidimensionnelles. De plus, vous pouvez utiliser les slashs
dans les clés JSON ou simplement utiliser les objets enfants.

Les catégories des fichiers INI peuvent être traitées comme clés parentes et dimensionnées.

    my_key = my value

    [plugins]
    Db.dsn = mysql:host=localhost
    Db.username = root

    [atomik.files]
    pre_dispatch = pre.php
    post_dispatch = post.php

Lorsque vous utilisez JSON, les données doivent être contenues dans un objet.

    {
	    "ma_cle": "ma valeur",

	    "plugins" : {
		    "Db": {
			    "dsn": "mysql:host=localhost",
			    "username": "root"
		    }
	    },
	
	    "atomik/files": {
		    "pre_dispatch": "pre.php",
		    "post_dispatch": "post.php"
	    }
    }

## L'amorçage

Une fois la configuration chargée, Atomik va mettre en place son environnement et charger ses plugins. Puis,
il essayera de charger le fichier d'amorçage. Il peut être utilisé pour préparer l'application, charger des bibliothèques
additionnelles, ou encore des plugins.

Le fichier doit impérativement être appelé avant *bootstrap.php* et placé dans le répertoire *app*.
Dans ce fichier, on peut utiliser les accesseurs (la méthode *set()* bien entendu) pour définir
les clés de configuration.

Le nom de ce fichier peut être changé en utilisant la clé de configuration *atomik/files/bootstrap*.

## Personnaliser la structure des dossiers

Comme il a été dit dans le précédant chapitre concernant la procédure d'installation, la structure des dossiers peut être personnalisée.
Cela peut être fait en modifiant les entrées dans la clé de configuration *atomik/dirs*

Chaque clé dans le tableau de répertoire représente un type de répertoire. Cette valeur peut-être une chaîne de caractères pour
un simple chemin ou un tableau dans le cas de multiples chemins.

## Avant et après l'envoie des fichiers

Atomik vous autorise à créer deux fichiers : *pre\_dispatch.php* et *post\_dispatch.php*
dans le répertoire  *app*. Ces fichiers seront appelés respectivement avant et après
le processus d'expédition.

Le nom de ces fichiers peut-être changés en utilisant les clés de configuration : *atomik/files/pre\_dispatch*
et *atomik/files/post\_dispatch*

## Personnaliser vos pages d'erreurs

Lorsque qu'une erreur est soulevée, Atomik affichera alors un rapport d'erreur. Vous avez alors la possibilité de modifier cette page.
Pour cela, créer un fichier *error.php* dans le répertoire *app*..

Vous êtes alors libre d'y mettre se que vous voulez. Prenez garde car la disposition n'est pas appliquée sur cette page. Vous pouvez alors
accéder aux variables appelées *$exception* qui contient toutes les exceptions rejetées.

Il est tout aussi possible de personnaliser les erreurs 404. Pour cela, créez simplement un fichier *404.php* dans le 
répertoire *app*. Comme la page d'erreur, le modèle ne sera pas appliqué.

Les méta-données de ses fichiers peuvent être changées en utilisant les clés de configuration *atomik/files/error* et 
*atomik/files/404*.

## Configuration avancée

Il est parfois intéressant de créer des moutures qui possèdent leurs propres configurations. Par défaut, lorsque vous
allez inclure la classe Atomik, elle sera automatiquement démarrée dans le processus d'expédition.  Ceci peut être désactiver
en plaçant la constante ATOMIK\_AUTORUN à false. Vous pouvez ensuite personnaliser votre fichier de configuration
en utilisant les accesseurs et finalement lancer Atomik en utilisant *Atomik::run()*.

    <?php
    define('ATOMIK_AUTORUN', false);
    require 'Atomik.php';

    // ma configuration
    Atomik::set(array(
	    // .. custom config
    ));

    // lancons Atomik
    Atomik::run();

