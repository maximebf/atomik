
## Sélecteurs

Le système de sélecteurs est semblable à l'utilisation des sélecteurs CSS en javascript via un arbre DOM.

Les sélecteurs propre à Atomik vous permettent de sélectionner n'importe quel type de donnée ou d'objet.

Vous avez déjà rencontrer les sélecteurs quand vous avez découvert les accesseurs.
Les sélecteurs sont disponible via la méthode *Atomik::get()*.

Les namespaces sont utilisés pour différencier les sélecteurs. Un namespace est un mot suivit
de *:* au début du sélecteur.

    Atomik::flash('mon message', 'label');
    $messages = A('flash:label');

Les Plugins peuvent fournir leurs propres sélecteurs avec leurs namespaces.

Pour creer un namespace utilisez *Atomik::registerSelector()*
prenant en premier paramètres le préfixe du namespace voulu (namespace sans les deux points) et en second le nom de la fonction à appeler (callback).
Appelé, le callback prendra en paramètre celui utilisé pour *Atomik::get()*.

    function mon_selecteur($string) {
	    return strtoupper($string);
    }
    Atomik::registerSelector('up', 'mon_selecteur');
    echo A('up:hello world'); // HELLO WORLD

