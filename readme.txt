=== Custom Content Type Manager ===
Contributors: fireproofsocks
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FABHDKPU7P6LN
Tags: cms, content management, custom post types, custom content types, custom fields, images, image fields, ecommerce, modx
Requires at least: 3.3.0
Tested up to: 4.2.2
Stable tag: 0.9.8.6
Version: 0.9.8.6

Break out of your blog!  Create custom fields for dropdowns, images, and more!  This plugin gives Wordpress true CMS functionality.

== Description ==

http://www.youtube.com/watch?v=rbRHrdKwo5A

The Custom Content Type Manager (CCTM) allows users to create custom content types (also known as post types) and standardized custom fields for each, including dropdowns, checkboxes, and images and more! This gives WordPress CMS functionality: Break out of your Blog!

You can select multiple images, posts, or media items to be stored in a single field making it easy for you to store a gallery of images or long lists of values.  This plugin also lets you export and import your content definitions, making it easy to ensure a similar structure between multiple sites.

This plugin was written in part for the book [WordPress 3 Plugin Development Essentials](http://www.packtpub.com/wordpress-3-plugin-development-essentials/book), published by Packt.

= Links =

Please use the following links for support and discussion:

* Please sign up for the CCTM [Mailing List](http://eepurl.com/dlfHg)
* Participate in the [Forum](http://wordpress.org/tags/custom-content-type-manager?forum_id=10)
* File [Bug reports or Feature Requests](https://github.com/craftsmancoding/custom-content-type-manager/issues).
* Read the [Official documentation](https://github.com/craftsmancoding/custom-content-type-manager/wiki)

= Requirements =

* WordPress 3.3.0 or greater
* PHP 5.3 or greater
* MySQL 4.1.2 or greater (5.x recommended)


== Installation ==

This plugin uses the standard installation procedure: install the plugin's folder inside of `wp-content/plugins` (make sure the folder is named *custom-content-type-manager*).

Here is a typical use-case verbosely for the record:

1. Install this plugin using the traditional WordPress plugin installation, or upload this plugin's folder to the `/wp-content/plugins/` directory (ensure that the directory is named *custom-content-type-manager*).
1. Activate the plugin through the 'Plugins' menu in the WordPress manager.
1. Upon activation you can adjust the plugin settings by clicking the newly created "Custom Content Types" menu item, or click this plugin's "Settings" link on the Plugins page.
1. After clicking the Settings link, you will see a list of content types -- there are two built-in types listed: post and page. To test this plugin, try adding a new content type named "movie" by clicking the "Add Custom Content Type" button at the top of the page.
1. There are a *lot* of options when setting up a new content type, but all the necessary ones are shown on the first page.  Pay attention to the "Name", "Show Admin User Interface", and "Public" settings. "Show Admin User Interface" *must* be checked in order for you to be able to create or edit new instances of your custom content type. 
1. Save the new content by clicking the "Create New Content Type" button.
1. Your content type should now be listed under on the main Custom Content Types Manager settings page. Activate your new content type by clicking the blue "Activate" link.
1. Once you have activated the content type, you should see a new menu item in the left-hand admin menu. E.g. "Movies" in our example.
1. Try adding some custom fields to your new content type by clicking on the "Manage Custom Fields" link on the settings page.
1. You can add as many custom fields as you want by clicking the "Add Custom Field" button at the top of the page, e.g. try adding a "plot_summary" field using a "textarea" input type, and try adding a "rating" dropdown. 
1. When you are finished configuring your custom fields, click the "Save Changes" button.
1. Now try adding a new instance of your content type ("Movies" in this example). Click the link in the left-hand admin menu to add a movie.
1. Your new "Movie" post will have the custom fields you defined.
1. If you have added any media custom fields, be sure to upload some images using the WordPress "Media" menu in the left-hand menu.

Please note: if you are upgrading from version 0.8.7 or before, you must *completely* uninstall and remove the previous version! This will not delete any of your content, but you should take some notes about the exact names of your content types before doing this.  Sorry, I know it's a pain, but I had to correct for limitations in the data structure.  See [this Wiki page](http://code.google.com/p/wordpress-custom-content-type-manager/wiki/DeletePostType) for more information.


== Frequently Asked Questions ==

Please see the online [FAQ](https://github.com/craftsmancoding/custom-content-type-manager/wiki/FAQ) for the most current information.


== Screenshots ==


== Changelog ==

As of May 2015, this plugin has been shifted over to [Github](https://github.com/craftsmancoding/custom-content-type-manager) due to the shutdown of Google Code.  Active development will occur in Github; the SVN repo will be only updated perfunctorily per WordPress requirements when there is a new tagged version.

The most recent SVN version of the code is located at:

	https://downloads.wordpress.org/plugin/custom-content-type-manager.zip

The most recent Git version of the code is located at:

	https://github.com/craftsmancoding/custom-content-type-manager

= 0.9.8.6 =

* Fixed error renaming custom fields.


== Requirements ==

* WordPress 3.3 or greater.
* PHP 5.2.6 or greater
* MySQL 4.1.2 or greater

These requirements are tested during WordPress initialization; the plugin will not load if these requirements are not met. Error messaging will fail if the user is using a version of WordPress older than version 2.0.11. 


== About ==

This plugin was written in part for the book [WordPress 3 Plugin Development Essentials](http://www.packtpub.com/wordpress-3-plugin-development-essentials/book) published by Packt. The architecture for this plugin was inspired by [MODX](http://modx.com/).  You can compare the 2 systems side by side in my book [MODX and WordPress](https://leanpub.com/modx-vs-wordpress)

== Future TO-DO == 

Please see the [Issues page](https://github.com/craftsmancoding/custom-content-type-manager/issues) for active development.  See also the [depcrecated page](http://code.google.com/p/wordpress-custom-content-type-manager/issues/list) at Google Code.  All Google Code projects will become read-only on [August 24, 2015](http://google-opensource.blogspot.com/2015/03/farewell-to-google-code.html).

If you are eager to see a particular feature implemented in a future release, please share your feedback at the official [Issues page](https://github.com/craftsmancoding/custom-content-type-manager/issues)




== Upgrade Notice ==

= 0.9.8.6 =

* Fixed error renaming custom fields. All users should upgrade.
