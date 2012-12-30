
# Translations

<div class="note">You will need the Translations plugin which is bundled with Atomik</div>

This plugin is gettext-like. You write your application in its default language and then
provide translations for each part of text.
    
## Creating language files

A language file provides translation from one language to another.

They are stored in the *app/languages* directory. This can
be changed using the *dir* configuration key. Files be named after
the first part of the locale. For example, if the file provide translation to French, it has to be
named *fr.php* (because the locale is fr-fr).

In the language file you must defined messages using the 
`Translations::setMessages()` method. The messages is made of
the string of the original language and the translated one.

    <?php
    Atomik\Translations::setMessages(array(
        'hello' => 'bonjour',
        'how are you?' => 'comment ca va?'
    ));

## Detecting the user language

By default, the plugin will autodetect the language using HTTP headers. This can be turned off
by setting false to the *autodetect* configuration key.

If the language cannot be detected, it will fall back on the default language defined in the
*language* configuration key.

You can also set the language manually using `Translations::set()`.

    Atomik\Translations::set('fr');

The current language is available from the global store under the *app.language* key.

    $currentLanguage = Atomik::get('app.language');

## Translating strings

To enable translation for a string use the `Translations::translate()` method.

    Atomik\Translations::set('fr');
    echo Atomik\Translations::translate('hello'); // will echo bonjour
    echo Atomik\Translations::translate('how are you?'); // will echo comment ca va?

The method is also available as an helper:

    echo $this->translate('hello');
    echo $this->_('hello'); // alias

A shortcut function is also defined: `__()`.

    Atomik\Translations::set('fr');
    echo __('hello'); // will echo bonjour
    echo __('how are you?'); // will echo comment ca va?

This method can also be use like the vsprintf() function. It can replace
patterns in the string by values provided as an array as the second argument.

    echo __('hello %s', array('Peter'));

