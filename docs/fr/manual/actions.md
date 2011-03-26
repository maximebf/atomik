
# Actions

## Introduction à la séparation logique/présentation

Sans utiliser Atomik, une des façon de faire les plus utilisée pour créer une application web est de créer un fichier
par page. La logique de la page (comprendre la connexion à la base de données, le traitement des formulaires, ...)
doit être fait au début de chaque fichier.

    <?php
	    if (count($_POST)) {
		    echo 'Les données du formulaires ont bien été recu';
	    }
    ?>
    <form>
	    <input type="text" name="data" />
	    <input type="submit" value="send" />
    </form>

Cette technique est très mauvaise ! En effet, la logique et le présentation doivent être toujours séparées.

Maintenant, imaginons que nous vouillons désormais séparer l'unique fichier pour qu'il respecte les couches d'abstractions. Nous allons donc devoir le diviser.
Nous aurons donc trois fichiers : un comportant la logique, l'autre l'HTML, et enfin une dernier qui inclura ces deux fichiers.

    // page_logic.php

    < ?php
    if (count($_POST)) {
	    echo 'Les données du formulaires ont bien été recu';
    }

    // page_html.php

    <form>
	    <input type="text" name="data" />
	    <input type="submit" value="send" />
    </form>
    
    // page.php

    < ?php
    include 'page_logic.php';
    include 'page_html.php';

Maintenant, il suffit d'imaginer que Atomik joue le rôle du troisième fichier (celui avec les deux includes). Avec cette exemple, vous pouvez facilement comprendre
le concept qui est derrière Atomik. Les parties logiques sont appelées actions et le html est appelé vue.

## Les fichiers d'actions

Les actions doivent être situées dans le répertoire *app/actions*. Les deux fichiers, action et vue,
doit avoir le même nom. De plus, les actions doivent avoir l'extension *php*.
Si le nom d'une action ou d'une vue commence par un underscore, alors l'action ne sera pas disponible par l'url.

Il n'y a aucune exigence concernant votre fichier action. Il peut contenir tout ce que bon vous semble.

Soyez conscient que les actions sont éxécutées dans leurs propre contexte et non dans le contexte global comme
vous pourrez le croire.

Les variables déclarées dans les actions sont directement expédiées dans la vue. Si vous voulez conserver des variables privées,
(lorsque vous ne devez pas les afficher), vous devrez ajouter un underscore au début du nom de la variable.

    <?php
    $myPublicVariable = 'value';
    $_myPrivateVariable = 'secret';

Vous ne devez pas avoir à utiliser la fonction echo ou du quelconque code HTML à l'intérieur du fichier d'action.
Comme il a été dit avant, le fichier d'action n'est pas concu pour cela, c'est le rôle de la vue.

Si vous voulez quitter votre application, au lieu d'utiliser exit(), préférer
*Atomik::end()* pour qu'Atomik puisse quitter votre application "proprement".

Vous pouvez utiliser des dossiers pour organiser vos actions. Dans ce cas, les vues doivent aussi avoir la même
structure de dossiers. Vous pouvez créer une action *index* (*index.php*)
à l'intérieur du dossier. Ce sera alors la page d'accueil de ce dossier, ou module. Les vues suivent aussi ce principe.

    app/actions/users.php           <- sera utiliser si url = /users
    app/actions/users/index.php     <- sera utiliser si url = /users ET SI app/actions/users.php n'existe pas
    app/actions/users/messages.php  <- sera utiliser si url = /users/messages quelque soit la page par défaut

## Les actions et les méthodes HTTP

Atomik vous offre la possibilité de créer plusieurs fichiers pour une seule action, chacun peut être ciblé à l'aide d'une url spécifique.
Cela permet au site web d'être plus facile à construire.

### Cibler une méthode HTTP

Une méthode spécifiant une action doit être ajouter à la fin du nom de la méthode. Par exemple,
si vous avez une action *user* et que vous voulez qu'elle cible une méthode de type POST, vous devrez créer
fichier nommé *user.post.php*. Avec la méthode put, il aurait été *user.put.php*. 
Tous ces fichiers doivent être situés dans le dossier des actions.

Vous pouvez créer des fichiers d'actions globales (dans le précédant exemple : *user.php*)
tant que vous n'avez pas encore éxécuté une méthode d'action spécifique. Les variables provenant de l'action globale
sont disponible dans ce fichier spécifique.

La méthode http actuelle est disponible dans la clé de configuration *app/http\_method*

### Autoriser les méthodes et réécrire leurs requêtes

L'autorisation des méthodes HTTP est définie dans la clé *app/allowed\_http\_methods*. Par défaut, toutes les méthodes disponible
dans l'application sont listées, mais vous pouvez si vous le souhaité réduire cette liste.

Beaucoup de clients ne gèrent pas correctement les méthodes HTTP (Par exemple Flex). Pour cela, 
il est possible de réécrire les requètes des méthodes utilisant un initinéraire comme paramètre (qui peut être un paramètre GET).
Le nom du paamètre par défaut est *\_method*. Il peut être changé dans 
*app/http\_method\_param*. Il est aussi possible de les désactiver en plaçant
false à la place de la chaîne de caractère.

## Appeler une action automatiquement

Lorsque vous éxécutez une requète, l'action et/ou la vue associée sont automatiquement appelées.
Vous pouvez cependant appeler une autre action en utilisant l'API d'Atomik.

Pour éxécuter une action, il faut utiliser la méthode *Atomik::execute()*. Celle-ci
prend comme premier argument le nom de l'action.

Par défaut, si une vue avec le même nom est trouvée, elle sera affichée et la valeur de retour
de la méthode  *execute()* sera la sortie de cette vue.

Si la vue n'est pas trouvée, une chaîne de caractère vide sera retournée. Si false est utilisé comme second argument,
la valeur de retour retournée sera un tableau contenant toutes les variables *public* provenant de l'action.

    $viewOutput = Atomik::execute('myAction');
    $variables = Atomik::execute('myAction', false);

Pour apeler une action, l'utilisation de *Atomik::execute()* ne signifie pas que le fichier d'action doit
exister. Cependant, dans ce cas, un fichier de vue avec le même nom doit exister. Sinon, c'est la valeur false
qui est retournée.

Des actions éxécutées par cette méthode seront influencées par les méthodes HTTP. Vous pouvez spécifier des méthodes
en utilisant le nom de la méthode avec le nom de l'action. Une action globale sera alors éxécutée.

    $viewOutput = Atomik::execute('myAction.post');
			
		
	
