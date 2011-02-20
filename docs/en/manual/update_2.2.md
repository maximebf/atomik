
# Update from 2.1 to 2.2
	
## Configuration

There is a massive compatibility break between these two versions. 2.2 features a new organization for
configuration keys. Below is a map of all keys that have changed and their new name.

    routes => app/routes
    debug => atomik/debug
    layout => app/layout
    escaping => app/escaping
    filters => app/filters
    base_url => atomik/base_url
    url_rewriting => atomik/url_rewriting
    atomik/default_action => app/default_action
    atomik/disable_layout => app/disable_layout
    atomik/views => app/views

## New features

+ File extension support in routes
+ View contexts
+ Actions targeted to specific HTTP methods
+ View helpers
+ Pluggable applications
+ Methods

