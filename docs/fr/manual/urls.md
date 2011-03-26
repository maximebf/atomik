
# URLs

## Appeler une action

Atomik a mis en place un mécanisme d'url très simple : quelque soit la page où l'on est,
l'url doit toujours pointer vers un seul script d'Atomik, par défaut : *index.php*

L'url peut contenir un paramètre de type GET qui va définir quelle action doit être déclencher.
Ce paramètre peut-être modifié dans la configuration d'Atomik (à l'aide de la clé *atomik/trigger*)
mais par défaut son nom est *action*. 

La valeur de ce paramètre doit uniquement contenir le nom de l'action, sans extension.
Par exemple, si on a le fichier *home.php* dans le répertoire *app/actions*
et/ou un fichier *home.phtml* dans le répertoire *app/views*,
on doit obligatoirement utiliser le paramètre *home* pour appeler cette action.
Ainsi, l'url devra donc ressembler à ceci : http://example.com/index.php?action=home.

Un fichier d'action ou de vue doit obligatoirement exister pour chaque action qui peut-être appelée.

Si le paramètre de l'action n'a pas été trouvé dans la requête, Atomik utilisera alors l'action par défaut
définie dans le fichier de configuration (la clé *app/default_action*).
Par défaut, cette clé a pour valeur *index*.

Atomik vous permet d'utiliser l'url rewriting pour pouvoir rendre votre url plus "propre". Si vous utilisez Apache, vous avez la possibilité de copier
ce code dans un fichier *.htaccess* dans le même répertoire qu'Atomik.
                        
    RewriteEngine on
    RewriteRule ^app/plugins/(.+)/assets - [L]
    RewriteRule ^app/ - [L,F]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?action=$1 [L,QSA]
                        
Ce code devrait à présent vous donner l'accès au dossier *app* depuis le web
mais autorisera aussi l'accès au répertoire *assets* fournit par les plugins.

Lorsque vous utilisez l'url rewriting, vous avez accès aux pages voulues en ajoutant directement le nom de l'action dans l'url.
Par exemple : http://example.com/home

Parfois, il arrive qu'Atomik ne peut pas détecter l'activation ou non de l'url rewriting. Dans ce cas, la méthode *Atomik::url()*
renverra systématiquement *index.php*. Pour empêcher cela, il suffit de modifier la clé de configuration *atomik/url\_rewriting*
avec la valeur : true.

L'action que vous voulez appeler peut-être trouvée dans la clé de configuration *request\_uri*.
La base de l'url (le chemin avant l'affichage de la racine du site, dans la plupart des cas le nom de domaine) 
peut-être obtenue à l'aide de la clé *atomik/base\_url*.

Lorsque vous utilisez l'application avec des plugins (voir le chapitre des plugins), la clé *request_uri* pourra
alors devenir relative par rapport au chemin du plugin. Pour retrouver l'url dans sa totalité, vous pouvez utiliser
*full\_request\_uri*.

## Le routage des url

### Création des itinéraires

Il est courant d'utiliser d'utiliser l'url rewriting afin de rendre les urls moins compliquées, "plus propre". 
Ceci peut être facilement effectué grâce au routeur d'Atomik. Le routeur fera le lien entre l'url et l'action 
et vous facilitera l'accès aux paramètres de cette url. Une url et ses paramètres est appelés un itinéraire.

Les itinéraires sont définis dans la clé de configuration *app/routes*.
Les actions doivent être spécifiées comme un paramètre appelé *action*.

Vous ne pouvez pas mettre en place d'itinéraire si vous utilisez un tableau comme premier paramètre de *Atomik::set()*.
Sinon, le tableau deviendra alors dimensionnel et l'itinéraire ne pourra aboutir.

    Atomik::set('app/routes', array(
       'user/add' => array(
           'action' => 'user_add'
       )
    ));

Comme vous le voyez, l'itinéraire est bien défini comme un tableau de clés et ses paramètres sont bien définis
dans le sous-tableau. Vous avez la possibilité d'ajouter un nombre infini de paramètres pour créer votre itinéraire. 
Vous devrez tout de même faire attention à ce que votre itinéraire soit valide en définissant bien 
vos paramètres pour vos actions.

Toute la puissance des itinéraires vient de la possibilité d'attribuer la valeur d'un paramètre avec
un segment de l'url. Cela peut-être réalisé en plaçant le préfixe *:* dans le nom du paramètre.

Les paramètres définies comme des segments peuvent être optionnels tant qu'ils ont
déjà été définis dans la liste de paramètres.

    Atomik::set('app/routes', array(
       'archives/:year/:month' => array(
           'action' => 'archives',
           'month' => 'all'
       )
    ));

Cette itinéraire permet de rendre le paramètre "month" optionnel tandis que "year" ne l'est pas. Ainsi, les possibles urls sont
http://example.com/archives/2008 ou encore http://example.com/archives/2008/02.
Dans chacun des cas, le paramètre "year" a pour valeur *2008* tandis que le paramètre "month" n'est pas
spécifié dans le premier exemple et l'est dans le second ( et a pour valeur *02*).

Les itinéraires peuvent être réversibles.

Depuis Atomik 2.2, les itinéraires peuvent être nommés et les expressions régulières peuvent être utilisées
avec leur notation classique.

Pour définir un nom à un itinéraire, il suffit simplement de définir le paramètre *@name*. Les deux types
d'itinéraires (classique et regexp) peuvent être nommés. La mise en place d'un nom pour les itinéraires peut-être très utile lorsque vous
devez utilisez la méthode *Atomik::url()*.

    Atomik::set('app/routes', array(
       'archives/:year/:month' => array(
           '@name' => 'archives',
           'action' => 'archives',
           'month' => 'all'
       )
    ));

Les itinéraires sous forme de regexp vous autorise à utiliser les expressions régulières pour identifier une url à une route. Un itinéraire sous forme de regexp
peuvent définir les mêmes chemins que celle classique. Un itinéraire est remplacé par une regexp lorsque le signe dièse #
est utilisé comme délimiteur.

Les urls doivent toujours être relative (n'utilisez donc pas de slash au début). Pour spécifier un paramètre dans un itinéraire,
vous devez utiliser le sous-modèle de nom (voir 
http://php.net/manual/en/regexp.reference.subpatterns.php).

    Atomik::set('app/routes', array(
       '#archives/(?P<year>[0-9]{4})/(?P<month>[0-9]{2})#' => array(
           '@name' => 'archives',
           'action' => 'archives',
           'month' => 'all'
       )
    ));

### Récupérer les paramètres de l'itinéraire

Lorque le processus est terminé, la clé de configuration nommée *request* est disponible. Elle contient
un tableau associatif avec les paramètres et leurs valeurs.

    $params = Atomik::get('request');
    $year = Atomik::get('request/year');

## Les extensions des fichiers

Depuis la version 2.2, Atomik permet d'afficher les extensions dans les urls. Ceci est facultatif.
Vous pouvez forcer l'extension en paramétrant la configuration *app/force_uri_extension* à : true.

Par défaut, l'extension du fichier est celui de la vue. Ceci sera abordé dans un autre chapitre.
L'extension de la vue par défaut est *html*. Elle peut être changée dans le fichier
*app/views/default_context*.

    http://example.com/home         =>      action=home format=html (ne fonctionne pas si app/force_uri_extension est à true)
    http://example.com/home.html    =>      action=home format=html
    http://example.com/home.xml     =>      action=home format=xml
    http://example.com/home.foo     =>      action=home format=foo

Les itinéraires peuvent aussi avoir leurs extensions. Vous pouvez spécifier une extension dans votre itinéraire. L'extension
peut aussi être un paramètre. Dans ce cas, si vous spécifier une valeur par défaut, l'extension
ne sera pas obligatoire dans l'url.

    Atomik::set('app/routes', array(
       ':category/:article.:format' => array(
                'action' => 'article'
       ),
       'home.html' => array(
            'action' => 'home',
            'format' => 'html'
       )
    ));

Le paramètre format n'est pas automatiquement ajouté dans l'itinéraire personnalisé. S'il n'est pas spécifié,
il aura alors pour valeur celle de *app/views/default_context*.

## Construire des urls avec Atomik::url()

Écrire directement l'url dans votre code peut amener de multiples problèmes dans le futur. Lorsque vous utilisez un layout,
il est difficile de savoir la position relative de la vue correspondante, à inclure dans la feuille de style par exemple. 
Beaucoup d'urls ont souvent besoin de concaténation lorsqu'ils utilisent des paramètres et cela rend tout de suite le code 
moins lisible.

*Atomik::url()* tente de résoudre ce problème en proposant 3 choses : 
                        
Ne pas utiliser de *index.php*  dans l'url si l'url rewriting est activé (ou utilisez le s'il ne l'est pas).

Cette méthode fonctionne aussi bien avec les urls relatives ou absolues. ependant, on peut aussi travailler avec des 
urls entières. Dans ce cas, les deux premiers points ne doivent pas être appliqués.

Si la valeur NULL est utilisée comme url, l'action en cours sera utilisée. La mise en place des noms des itinéraires peut être fait en utilisant
le préfixe @.

    $url = Atomik::url('home'); // /index.php?action=home if no url rewriting or /home otherwise
    $url = Atomik::url('/user/dashboard'); // /user/dashboard
    $url = Atomik::url('http://example.com'); // http://example.com
    $url = Atomik::url('@my_route'); // will use the route named my_route

Vous pouvez ajouter dans paramètres de type GET dans l'url en utilisant un tableau dans le second paramètre. Vous pouvez
réutiliser les paramètres de la requête en cours en utilisant la valeur *true* à la place d'un tableaux. Si vous voulez
utiliser les paramètres actuels avec de nouvelles, utilisez un tableau et ajouter la valeur *__merge_GET*.

    $url = Atomik::url('archives', array('year' => 2008)); // /index.php?action=archives&year=2008 if no url rewriting or /archives?year=2008 otherwise
    $url = Atomik::url('archives?year=2008', array('month' => 02)); // /archives?year=2008&month=02

    // if the page has been called with ?year=2008

    $url = Atomik::url('archives', true); // /archives?year=2008
    $url = Atomik::url('archives', array('__merge_GET', 'month' => 02)); // /archives?year=2008&month=02

La méthode permet également d'utiliser les paramètres embarqués. Ce sont des paramètres dans l'url (comme dans un classique itinéraire)
Le nom de ses paramètres doit être préfixer d'un *:*.

    $url = Atomik::url('archives/:year', array('year' => 2008)); // /index.php?action=archives/2008 if no url rewriting or /archives/2008 otherwise
    $url = Atomik::url('archives/:year', array('year' => 2008, 'month' => 02)); // /archives/2008?month=02

    $url = Atomik::url('@archives', array('year' => 2008)); // /index.php?action=archives/2008 if no url rewriting or /archives/2008 otherwise
    $url = Atomik::url('@archives', array('year' => 2008, 'month' => 02)); // /archives/2008?month=02

Lorsque vous créez des urls comme points de ressources (modifier), vous ne voudrez jamais les avoir le *index.php* dedans.
Pour éviter cela, vous pouvez utiliser la méthode *Atomik::asset()*.
Celle-ci fonctionne de la même façon que
*Atomik::url()* mais n'utilisera jamais *index.php* dans l'url.

Le fonctionnement de *Atomik::url()* est en fonction du contexte de l'application. Par exemple, si la méthode est utilisée
à l'intérieur des vues de votre application, l'url généré sera relative à la base de l'url de votre application. Cependant, si la méthode est utilisé
à l'intérieur d'un plugin, il sera relatif à la base de l'url du plugin. Tout ceci permet à la méthode
d'être utilisée partout, tout en assurant la relativité des urls.

Il est cependant possible d'utiliser la méthode *Atomik::appUrl()* pour générer constamment des url relative dans la base d'url
de votre application et la méthode *Atomik::pluginUrl()* pour générer des urls relatives à un plugin spécifique. Le nom du plugin
doit être équipé du premier argument du dernier argument devenu le même que celui de *Atomik::url()*.

    $url = Atomik::appUrl('home'); // always /home
    $url = Atomik::pluginUrl('my_plugin', 'home'); // /my_plugin_base_url/home
    $url = Atomik::url('home'); // either /home or /my_plugin_base_url/home depending on the context

En complément de ce chapitre, on peux citer deux méthodes supplémentaires : *Atomik::appAsset()* et *Atomik::pluginAsset()*.
La dernière, tout comme *Atomik::pluginUrl()* a besoin d'un plugin pour le premier argument.

