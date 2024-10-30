=== DX Contact Form Success Page for Jetpack ===
Contributors: devrix
Tags: jetpack, jet pack, message, contact form, contact, form, success, email
Requires at least: 4.4.19
Tested up to: 5.5.1
Stable tag: 0.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Author URI: https://devrix.com

Replaces the default success message showing after successfully submitting a contact form, with your custom message.

== Description ==

DX Contact Form Success Page for Jetpack replaces the default success message showing after successfully submitting a contact form, with your custom message.

This little plugin can be handy for telling your clients about the deadlines to reply, how long it is going to take to get a reply to their contact, etc..

You can also filter entire post/page content to display only the success message, in case of course the contact form is sent.

You can style the message better, there is a filter <code>jpcfsm_message_content_loaded</code> you can use for this regard. Assuming for example you want to wrap the message in a green box with a black border, this code can help you achieve it easily without the need to use extra plugins or style thrugh the content editor:

Add to your child theme's functions file:

<pre>
add_filter('jpcfsm_message_content_loaded', function($message) {
	return '&lt;div style="background:green;border:1px solid #555"&gt;' . $message . '&lt;/div&gt;';
});
</pre>

For support threads please use the support section of this plugin here on the forums. If you want to send out a private note or idea or something, then you can contact me <a href="http://devrix.com/contact/">here</a>

Thank you!

== Installation ==

* Install and activate the plugin:

1. Upload the plugin to your plugins directory, or use the WordPress installer.
2. Activate the plugin through the \'Plugins\' menu in WordPress.

== Frequently Asked Questions ==

= I am getting the default Jetpack success message, what is wrong? =

You must set a custom message in the plugin settings first in order for the plugin to use it to filter the default Jetpack message.

== Changelog ==
= 0.4.4 =
* Sanitized
* Tested up to WordPress 5.5.1 version

= 0.4.3 =
* Code refactor and change plugin name

= 0.4.2 =
* Fixed Error having the whole content filtered when no form is sent yet (if filter content setting is on)

= 0.4 =
* Fixed self calls with new Self, which is supported in PHP >= 5.5 only
* Removed PHP version check: the plugin should work with any version.

= 0.3 =
* See https://wordpress.org/support/topic/needs-an-update-16/
* Fixed - https://wordpress.org/support/topic/cyrillic-messages-support/
* i18n for admin

= 0.2 =
* Improved settings page.
* Added ability to filter entire post/page content and show only the success message when the form is sent.

= 0.1 =
* Initial release.

== Other Notes ==

TODO: Format the message with contact form submitted user data