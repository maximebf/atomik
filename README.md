# Atomik Framework

[![Build Status](https://secure.travis-ci.org/maximebf/atomik.png)](http://travis-ci.org/maximebf/atomik)

Atomik is a free, open-source, micro framework for PHP5. Atomik is built for 
small web applications that do not need heavy frameworks but still want powerful 
features. It is build with the KISS principle in mind as well as speed and security. 
Atomik is also an ideal introduction for beginners to the world of web development frameworks.

## Installation

The easiest way to install Atomik is using [Composer](http://getcomposer.org/)
with the following requirement:

    {
        "require": {
            "maximebf/atomik": ">=3.0.0"
        }
    }

## Core Principles

Atomik provides, on top of a simple directory structure and a dispatch mechanism,
all common tasks needed to create a website. But the main point of Atomik is to
extend it through plugins. You build the framework you want with the features
you need.

### Simplicity and the KISS principle

KISS (Keep It Simple Stupid) is a principle which goal is to keep things the
simplest possible. Atomik has been build from the beginning with this idea in 
mind. It simply works and is nearly bug free. Just unzip your downloaded package
and start working!

### Ready to run

Atomik comes with methods for all common tasks involved in creating a website. 
From escaping output to pretty urls (goind through filtering data, flash messages...),
everything is bundled into a coherent and comprehensive API.

### Actions and Views

Atomik is not an MVC framework according to the definition of MVC (it can 
however become one with plugins...). Still  it follows the same idea.
The application logic, called actions, and the presentation layer, called 
views, are divided into two different files. The action is executed
and then the view is rendered. Both of them is what make a web page.

How you code actions and views is your business! Atomik only provides the
dispatch mechanism. The only other thing it does is forward variables defined
in the action to the view.

### Extensibility and Plugins

As said before, one of the main point of Atomik is being highly extensible. Nearly
all actions (execute an action, render a view...) can be overridden. This
is made possible through an event system. Before and after each methods, 
and sometimes during, events are fired. Multiple callbacks can be registered for
each events which allow to modify the way Atomik acts.

Since version 2.2, plugins can also become full standalone applications! This allows
you to quickly build on top of existing features and thus don't repeating yourself.

Atomik respects the convention over configuration principle. However it hasn't forget
the configuration aspect and even its core features can be modified. 
For example, if you want to drop the logic and presentation separation 
(however highly discourage) and do everything in a single file, you can.

