
# Les vues et leurs organisations

## Les vues

Les vues sont placées dans le répertoire *app/views*. Leurs extensions
sont par défaut *phtml*.

Le contenu de ces fichiers est libre, tout comme les actions. Atomik recommande tout de même,
pour respecter la séparation entre actions et vues, d'y placer un maximum de texte ou d'HTML
(ou encore un autre langage tel que le XML).

PHP peut tout de même être utilisé pour afficher des variables ou encore pour l'utilisation
de boucles.

    <html>
	    <head>
		    <title>Exemple</title>
	    </head>
	    <body>
		    <?php echo $myPublicVariable; ?>
	    </body>
    </html>

Si votre configuration le permet, il est préférable d'utiliser les shorts tag dans la vue pour avoir une meilleur lisibilité.

## Les modèles de vues

Lorsque vous créez vos vues, vous aurez parfois besoin de réaliser plusieurs fois la même chose, ou quelque chos qui s'y 
rapproche fortement, comme exemple le formatage d'une date. Ce type de modèle sert exactement à cela : il s'agit d'une 
fonction qui sera éxecutée dans votre vue, format\_date() par exemple.

### Créer des modèles de vue

Les modèles de vue, dans Atomik sont placés dans le répertoire *app/helpers*. 
Notre modèle : *app/helpers* devra donc être situé dans *app/helpers/format\_date.php*.
Cet emplacement peut-être changé en utilisant la clé *atomik/dirs/helpers*.

Il y a deux voies pour définir un modèle de vue : comme une fonction ou comme une classe. Si vous utilisez la fonction,
il suffit simplement de mettre le même nom de fichier que du helper. Dans le cas de notre modèle *format\_date*,
le nom de la fonction doit être format_date().
Mais si vous voulez plutôt opter pour une classe, car ce que vous voulez réaliser est plus complexe. Alors, il faut que le nom
de la classe soit nommé sans underscore et en commençant par une majuscule, puis mettre un *Helper* en suffixe. 
Dans notre exemple, ce sera *FormatDateHelper*. Cette clase aura alors besoin d'une méthode appelée comme le nom de la classe, 
sans le helper, et sans la majuscule du début. Dans notre cas, notre méthode s'appelera *formatDate()*.

    function format_date($date)
    {
	    // coder le formatge
	    return $date;
    }

    class FormatDateHelper
    {
	    public function formatDate($date)
	    {
		    // coder le formatge
		    return $date;
	    }
    }

### Utiliser le modèle

Les modèles de vues peuvent être appelés à partir de n'importe quel fichier qui peut-être affiché.
Ceci pour dire que les vues, les layouts (ou modèles) ainsi que les fichiers d'affichage utiliseront *Atomik::renderFile*.
Ils peuvent avoir accès aux méthodes à l'aide de l'instance $this. Les modèles de vues sont automatiquement inclus.

Les modèles de vues peuvent être utilisés dans les actions, mais ils ont été créés dans le but de l'utiliser dans les vues.

    <span class="date"><?php echo $this->format_date('01-01-2009') ?></span>

## Layout

Il est courant, dans les sites web, que toutes les pages partagent un même layout (appelé aussi modèle, patron, ...). 
Atomik vous permet alors de définir un layout qui sera utiliser automatiquement dans toutes les vues.

Le layout sera mise en place une fois que la vue aura été terminée. La sortie de la vue passera donc par
une variable appelée : $contentForLayout. 
Les layouts peuvent être créés en suivant la même méthode que les vues.

Les layouts peuvent être placés dans les répertoires *app/views* ou *app/layouts*
L'extension de ce fichier est le même que celui des vues.

Le nom du layout utilisé peut-être défini dans la clé de configuration *app/layout*. Si la valeur est
false (par défaut), il ne sera pas chargé.

Le layout peut-être desactivé pendant l'exécution du sccript à l'aide de la méthode *Atomik::disableLayout()*.
Il pourra alors être réactivé en passant false à l'argument de cette même méthode.

    // app/view/_layout.phtml
    
    <html>
	    <head>
		    <title>Mon site web</title>
	    </head>
	    <body>
		    <h1>Mon site web</h1>
		    <div id="contenu">
			    <?= $contenuDuLayout; ?>
		    </div>
	    </body>
    </html>
    
    // app/config.php
    
    Atomik::set('app/layout', '_layout');

Plusieur layouts peuvent être utilisés. Pour cela, il suffit de placer un tableau dans la clé de configuration à la 
place d'une chaine de caractères. Les layouts seront alors afficher dans l'ordre inverse (le premier envelopera le 
deuxième qui enveloppera le troisième, ...)

## Les contextes de vues

Parfois, on a besoin d'afficher des contenus de différents formats. Plutôt que de créer de multiples actions qui 
font la même chose, Atomik intégre une fonctionnalité permettant de créer des vues pour chaque type de contenu. 
Ceci est appelé les contextes de vue. La vue affichée dépendra alors du contexte.

Ces contextes sont définis en utilisant les paramètres de routage. Par défaut, il est appelé *format*. Il peut être changé
dans *app/views/context\_param*. Comme il a été dit dans le chapitre portant sur les urls, le format des paramètres
est par défaut l'extension du fichier. Ce qui signifie que l'utilisation d'une url comme index.xml résultera de 
l'utilisation du format xml.

Le contexte par défaut de la vue est le *html* mais il peut-être changé dans *atomik/views/default\_context*.

Pour créer une vue pour un contexte donné, il suffit de mettre en suffixe le nom du contexte sur le nom de la vue, 
comme une extension. Par exemple, nous avons une vue *article*. Pour avoir le contexte xml, on devra mettre *article.xml.phtml*.
Certain contexte n'ont pas besoin de suffixe comme *html*.

En fonction du contexte de la vue, le layout peut-être désactiver et l'asociation contenu-type peut-être modifée. 
Le préfixe du fichier peut aussi être spécifié. Tout ceci peut-être effectué dans *app/views/contexts*.

    Atomik::set('app/views/contexts/rdf', array(    // le nom du contexte, par exemple le nom du fichier dans l'url
	    'prefix'        => 'rdf',                   // le préfixe de l'extension de la vue (mettre false pour ne pas en avoir)
	    'layout'        => false,                   // Si vous voulez activer le layout
	    'content-type'  => 'application/xml+rdf'    // le type de contenu
    ));

Maintenant, vou pouvez appeler une url comme http://example.com/article.rdf. Dans ce cas le nom de la vue
sera *article.rdf.phtml*, le layout devra être desactivé et le type de contenu devra être
*application/xml+rdf*.

Si un contexte de vue n'est pas défini dans *app/views/contexts*, le préfixe du fichier sera alors le nom du contexte,
le layout devra être désactivé et le type de réponse sera  *text/html*.

Par défaut, il y a quatres contextes définis : html, ajax, xml et json. Le context ajax est le même que celui d'html avec
le layout désactivé. Les deux derniers lorsque le layout est activé, doivent mettre le type de contenu aproprié.

## Les controlleurs de vues

### L'extensions des fichiers de vues

Par défaut, l'extension des fichiers de vues est *phtml* comme il a déjà été dit. Ceci peut être changé
Ceci peut être changé en utilisant la clé de configuration *app/views/file_extension*.

### Ne pas afficher une vue

Tant que l'action est en cours d'éxécution, vous avez la possibilité d'annuler l'affichage de la vue.
Pour cela, il suffit d'appeler la méthode *Atomik::noRender()* depuis votre fichier d'action.

### Modifier une vue correpondante à une action

Tant que l'action est en cours d'éxécution, vous avez la possibilité de changer de vue correpondante à tout moment.
Dans ce cas, vous pouvez utiliser *Atomik::setView()* dans votre action. Elle prends un unique argument : 
le nom de la vue.

### Utilisation d'un moteur de rendu personnalisé

Le moteur de rendu par défaut est seulement utilisé par les fonctions incluses par PHP. Vous pouvez cependant utiliser votre moteur de template.
Pour cela, vous devez spécifier un callback dans la clé de configuration *app/views/engine*

Le fonction calback recevra 2 paramètres : le nom du fichier et un tableau associatif contenant les variables spécifiques de la vue.

    function myCustomRenderingEngine($filename, $vars)
    {
	    // Votre propre moteur de rendu personnalisé
	    return $renderedContent;
    }

    Atomik::set('app/views/engine', 'myCustomRenderingEngine');

Votre moteur de rendu personnalisé sera utilisé à chaque fois que *Atomik::render()*,
*Atomik::renderFile()* ou *Atomik::renderLayout()* sera utilisées.

## Personnaliser automatiquement vos vues

Lorsque vous éxécutez une requète, l'action et/ou la vue associée est automatiquementt
appelées. Vous pouvez cependant modifier la vue pour une autre en utilisant l'API d'Atomik.

L'utilité principal d'utiliser ceci pour afficher vos vues est que la partie du code
peut-être réutilisée.

Pour permuter une vue, utilisez la méthode *Atomik::render()*.

Elle prend en argument le nom de la vue, et optionnellement, un tableau associatif de clé/valeur
repésentant les variables.

    $viewOutput = Atomik::render('maVue');
    $viewOutput = Atomik::render('maVue', array('var' => 'value'));

Il est possible d'afficher un quelconque fichier avec la méthode *Atomik::renderFile()*.
Celle-ci prend un paramètre : le nom du fichier. Les variables peuvent aussi être passées comme avec *Atomik::render()*.

Vous pouvez également afficher une vue contextuelle en ajoutant l'extension du fichier au nom de la vue.

