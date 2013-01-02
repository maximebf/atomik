# Atomik Framework

[![Build Status](https://travis-ci.org/maximebf/atomik.png?branch=master)](http://travis-ci.org/maximebf/atomik)

Atomik is an Open-Source micro framework for PHP 5.3+. Atomik is built for 
small web applications that do not need heavy frameworks but still want powerful 
features. It is build with the KISS principle in mind as well as speed and security. 

Atomik is also the perfect introduction for beginners to the world of web development frameworks.

Here's a list of some Atomik features:

 - Very small footprint
 - Open Source (MIT License)
 - Very simple to use
 - Easy to use router for pretty URLs
 - Powerful templating: helpers, layouts, content types...
 - Flash messages
 - Errors handling
 - Intuitive architecture for beginners
 - Respect good programming practices
 - Plugins and pluggable applications
 - Highly extensible
 - Uses existing libraries

## Installation

The easiest way to install Atomik is using [Composer](http://getcomposer.org/)
and the [Atomik Skeleton Application](https://github.com/maximebf/atomik-skeleton):

    php composer.phar create-project atomik/skeleton .

## Core Principles

Atomik provides, on top of a simple directory structure and a dispatch mechanism,
all common tasks needed to create a website.

### Simplicity and the KISS principle

KISS (Keep It Simple Stupid) is a principle which goal is to keep things the
simplest possible. Atomik has been build from the beginning with this idea in 
mind.

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

Atomik is being highly extensible. Nearly
all functions (execute an action, render a view...) can be overridden. This
is made possible through an event system. Before and after each methods, 
and sometimes during, events are fired. Multiple callbacks can be registered for
each events which allow to modify the way Atomik acts.

Plugins can also become full standalone applications! This allows
you to quickly build on top of existing features and thus don't repeating yourself.

Atomik respects the convention over configuration principle. However it hasn't forget
the configuration aspect and even its core features can be modified.

