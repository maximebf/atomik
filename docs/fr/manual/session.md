
# Session

## Démarrer et utiliser une session

Par défaut, Atomik démarre automatiquement les sessions. Si besoin, vous pouvez les désactiver en midifiant
la configuration *atomik/start\_session*.

*Atomik::flash()* ne peut marcher si les sessions ne sont pas activés.

Vous pouvez accéder à une session depuis la méthode *Atomik::get()* avec 
le mot clef "session". Bien sûr, vous pouvez toujours utiliser la super-global $_SESSION.

    echo Atomik::get('session/nom');

## Messages flash

Les messages de type flash sont des messages stockés dans une session et accessible seulement une fois.
Ils sont particuliérement utiles pour passer messages d'erreurs où de réussite d'une page à une autre.


Pour créer un message flash, il suffit d'appeler la méthode *Atomik::flash()*, prenant en premier paramètre
un message ou un array de messages. Ces messages peuvent aussi avoir des labels, par exemple  *error* ou  *succes*.
Pour spécifier un label, il suffit de renseigner le second paramètre (facultatif, initialisé à *default* par défaut).

    Atomik::flash('L'action s'est déroulée avec succès');
    Atomik::flash('Une erreur est survenue', 'error'); // avec un label
    Atomik::flash(array('message1', 'message2'), 'error');

Les messages flash peuvent être retrouvés grâce au sélecteur flash. La valeur d'indexation est le nom du label utilisé (default...).
Utilisez *all* pour retrouver tous les messages. Cela retournera un array contenant en clef d'indexation le label utilisé et le message associé.
Si vous souhaitez retrouvez seulement les messages d'un seul label, c'est un array de messages qui sera renvoyé.

    foreach (Atomik::get('flash:all) as $label => $messages) {
	    foreach ($messages as $message) {
		    // ...
	    }
    }

    foreach (Atomik::get('flash:my_label) as $message) {
	    // ...
    }

