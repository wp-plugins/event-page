=== Event Page Plugin ===
Contributors: mpraetzel
Donate link: http://www.ternstyle.us/donate
Tags: events, event page, shows, date, time, dates, event dates, date and time, post date, post time, post event, event list
Requires at least: 2.8
Tested up to: 3.3.1
Stable tag: 2.7.4

The Event Page Plugin allows you to create a page, category page or post on your wordpress blog that lists all your events. If you'd like to see an event plugin for WordPress with more features (repeating events, post list and calendar views, a color coded legend, Google Calendar and map support and more) please visit: `http://www.ternstyle.us/terncal`

== Description ==

The Event Page Plugin allows you to create a page, category page or post on your wordpress blog that lists all your events sorted in ascending or descending order according to the date and time you specify. Employing pagination you can page through your events. Documentation: `http://www.ternstyle.us/products/plugins/wordpress/wordpress-event-page-plugin`

If you'd like to see an event plugin for WordPress with more features (repeating events, post list and calendar views, a color coded legend, Google Calendar and map support and more) please visit: `http://www.ternstyle.us/terncal`

Features:

* List your events in a Wordpress page or post
* Order your events in ascending or descending order
* Show the next upcoming event
* Pagination to page through events
* Fully customize your list's HTML code.

Resources:

* Homepage for this plugin: `http://www.ternstyle.us/products/plugins/wordpress/wordpress-event-page-plugin`
* Documentation: `http://www.ternstyle.us/products/plugins/wordpress/wordpress-event-page-plugin`
* Working example: `http://blog.ternstyle.us/events`
* A more feature rich plugin: `http://www.ternstyle.us/terncal`

== Installation ==

* Unpack the downloaded zipped file
* Upload the "event-page" folder to your /wp-content/plugins directory
* Log into Wordpress
* Go to the "Plugins" page
* Activate the Event Page Plugin
* You'll need to create a new template file entitled "events.php"
** To do this copy your file entitled page.php and name it events.php.
** Place this code `<?php
/*
Template Name: Events
*/
?>` on the first line of the file.
** Remove the code that prints the post to the page and replace it with this code: `<?php tern_wp_events(); ?>`
** Upload the new file to the server.
* Now you'll need to create a new page which you can title whatever you like.
* Assign this page to the template entitled "Events"
* Remember to alter your Event Page settings to reflect the new name of this page and any other options you may wish to alter.
* That should be it. View the page and you should see the Event Page in its moderately useful glory!

== Features ==

* List your events in a Wordpress page or post
* Order your events in ascending or descending order
* Check to see if there are any upcoming events
* Show the next upcoming event
* Pagination to page through events
* Fully customize your list's HTML code.

== Resources ==

* Homepage for this plugin: `http://www.ternstyle.us/products/plugins/wordpress/wordpress-event-page-plugin`
* Documentation: `http://www.ternstyle.us/products/plugins/wordpress/wordpress-event-page-plugin`
* Working example: `http://blog.ternstyle.us/events`
* A more feature rich plugin: `http://www.ternstyle.us/terncal`

== Frequently Asked Questions ==

= How do I make a post an event? =

When editing a page you'll notice at the bottom of page there is a box entitled "Event Information" fill out the appropriate date and time here. Also, you'll need to place the event in the category that you specified in the Event Page settings.

= How do I display the event meta data like location and url? =

To do this add the following code to your WordPress template files where you'd like the meta data to appear:
`<?php WP_event_page_meta_fields(true); ?>`

== Screenshots ==

1. This is an image of a the working example.
2. This is an image of the administrative settings page for this plugin.
3. This screenshot is of the date & time settings.
4. This is a screenshot of the mark-up editor.