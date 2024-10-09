=== Dead Drop Messaging ===

Contributors: justingreerbbi
Donate link: https://justin-greer.com
Tags: messaging, communication, private, encrytped, secure, mobile application
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Dead Drop Messaging is a plugin that allows users to send private messages securely using the Dead Drop Messaging mobile application.
This plugin allows groups to create and manage thier own internal secure messaging system without the use of other public messaging systems.

The benifit of this system is that no one other than your memebers will have access to your communication. You manage all your data locally
and without the use of 3rd party systems that could be compromised.

This plugin offers Point to Point passthru to users of the Dead Drop Messaging Mobile Application.

**Features:**

1. Self Contained API to communicate with Dead Drop Messaging members that are configured to use your server.
2. Full passthru of encrypted messages. The system does not store any messages in plain text nor does the system store Point to Point encyrption keys.
3. Oauth2 Authentication Protocol using the Password Grant Type.
4. Rolling AES256 keys for more secururity.

**Requirements**

1. A Secure Server running the latest version of WordPress.
2. A Secure domain using HTTPS protocol (no exceptions).

== Installation ==

1. Upload the `dead-drop-messaging` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin settings from the 'Settings' menu.

== Frequently Asked Questions ==

= How do I send a message? =

Navigate to the 'Messages' section in your WordPress dashboard and click 'New Message'.

= Is the messaging secure? =

Yes, all messages are encrypted and stored securely.

== Screenshots ==

1. Screenshot of the messaging interface.
2. Screenshot of the settings page.

== Changelog ==

= 1.0 =

== Upgrade Notice ==

= 1.0 =
Initial release.

== Arbitrary section ==

You may provide arbitrary sections and subsections as needed.

== A brief Markdown Example ==

Ordered list:

1. Item 1
2. Item 2
3. Item 3

Unordered list:

-   Item 1
-   Item 2
-   Item 3
