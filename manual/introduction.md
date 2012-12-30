
# Introduction

Atomik is a free, open-source, micro framework for PHP 5.3+. Atomik tries not to be
yet another framework. It has been built on the idea that there's no simple yet
complete tools for small to medium websites. 
Atomik is build with the KISS (Keep It Simple Stupid) and DRY (Don't Repeat Yourself)
principles in mind as well as speed and security. It is also an ideal introduction for 
beginners to the world of web development frameworks. 

Here's a list of some Atomik features:
		
+ Very small footprint
+ Flexible licensing (MIT)
+ Very simple to use
+ Contains everything to create a website
+ It simply works
+ Application logic separated from the presentation layer
+ Plugins
+ Highly extensible

The manual is licensed under the Creative Commons Attribution license.

## Requirements

+ HTTP Server. Apache with mod_rewrite is a good choice.
+ PHP 5.3 or greater

## Installation

1. Unzip the downloaded archive

2. Upload or move Atomik files and folders into a directory
   available from the web.

3. Navigate to the url of the folder where you unpacked Atomik.
   Eg: http://localhost/atomik.

4. You should see a *Congratulations, you're ready to roll!* page.

5. Start coding!

## More about the directory structure

Atomik's core file, namely *index.php*, must be situated at the root of your website.
The default directory structure is simple because everything goes into this webroot.

Your application per se goes into the *app* directory. Actions and views have their own directories.

When using the provided Apache *.htaccess* file, the *app* directory is not 
accessible from the web.

If you do not use the provided *.htaccess* file, do not forget to allow *assets*
folders in plugins directories. Such a path can look like *app/plugins/MyPlugin/assets*.

In a production environment, it is always better to remove the application files from the webroot. This is not the default way
of doing things with Atomik as it can be a bit more difficult. However it is very easy to configure. Atomik allows you to edit
the path for each directory. This will be covered in the configuration chapter.

