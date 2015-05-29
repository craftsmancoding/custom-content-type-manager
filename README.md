# Custom Content Type Manager

The Custom Content Type Manager (or CCTM) plugin allows WordPress 3.x users to create, extend, and manage custom content types (a.k.a. post types) and their fields like a true CMS. You can define custom fields for any content type, including checkboxes, textareas, and dropdowns. Developers can also create their own types of fields.

Custom fields can be added to any post type so that each time you create a new post, page, book, or movie post, a standard set of user-defined fields will be there. Custom field can hold any type of information: dropdown lists, checkboxes, WYSIWYG, and even images.

This is the Github home for plugin for WordPress.  The code was ported to Github after Google Code announced that it will be shutting down in 2015.  The legacy code-base is still visible at https://code.google.com/p/wordpress-custom-content-type-manager/

-----------------------------

# For Developers

## Dev Environment

This project relies on several tools to manage dependencies and testing resources.

- [Composer](https://getcomposer.org/) for PHP dependencies
- [Bower](http://bower.io/) for JS libraries, CSS, and other assets
- [Karma](https://karma-runner.github.io/) a test runner for JS
- [Karma-Jasmine](https://github.com/karma-runner/karma-jasmine) an adapter for the Jasmine framework.

````
curl -sS https://getcomposer.org/installer | php
npm install bower
npm install karma
npm install karma-jasmine@2_0 --save-dev
````


## API

Inside the WordPress manager, the CCTM establishes a mini REST API.  For simplicity, we rely on the built-in `ajaxurl` endpoint.  Setting the `action` parameter to `cctm` will cause the request to be routed to the CCTM's API endpoint.


````
var data = {
    'action': 'cctm',
    '_verb': 'get',
    '_resource': 'fields'
};

// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
$.post(ajaxurl, data, function(response) {
    var obj = jQuery.parseJSON(response);
    alert('Got this from the server: ' + obj.hash);
});
````

## Extraordinary Packages

Writing this plugin would have driven me insane without the help of the hard working coders who went before:

- http://fractal.thephpleague.com/  ??
- https://github.com/ventoviro/windwalker-renderer
- https://github.com/thephpleague/flysystem
- https://github.com/webmozart/json
- http://validator.particle-php.com/en/latest/

? https://libraries.io/packagist/mcustiel%2Fphp-simple-config/0.3

https://github.com/neomerx/json-api

http://jsonapi.org/
https://github.com/mgonto/restangular
https://github.com/WP-API/WP-API

## TODO (before refactor is complete)

Fieldtype class: fetch all classes implementing interface
Posttypes controller + model
API responses for update + delete etc.
Filesystem Factor... function where you pass local dir, get a Flysystem object
Localization


## Rants

get_post_type_object is NOT the converse of the register_post_type function.  It's maddeningly close, but register_post_type uses "supports" and "capabilities"... so you have to use post_type_supports and get_post_type_capabilities to fill in some of the gaps. 