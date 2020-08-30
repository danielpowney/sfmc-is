=== JavaScript Beacon for Interaction Studio ===
Contributors: dpowney
Tags: RTIM, tracking, personalisation, contextual bandit, recommendations
Requires at least: 4.0
Tested up to: 5.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Integrates the Interaction Studio JavaScript beacon onto your website.

== Description ==

Integrates the Interaction Studio JavaScript beacon onto your website.

* 2 integration methods: Synchronous and Asyncrhronous
* Tells Interaction Studio who the current user is
* Works with Gutenberg editor
* Add a page data variable on your site for easier sitemap configuration

Sample page data JavaScript variable:
`
window.IS_PAGE_DATA = {
	"pageType" : "single",
    "postType" : "post",
	"postId" : 1,
	"postThumbnail" : "",
	"postTitle" : "Hello world!"
};
`

Also contains a filter to optionally push user data to Interaction Studio via _aaq.push() calls

**Follow this plugin on [GitHub](https://github.com/danielpowney/sfmc-is)**

== Screenshots ==
1. Plugin settings

== Installation ==

1. Install and activate the plugin.
2. Go to plugin settings page to configure the JavaScript beacon integration

== Changelog ==

= 1.1 =
* Tweak: Removed hybrid integration option
* New: Added page data variable option for easier sitemap configuration
* Fix: Do not load scripts if account id or dataset id are not set

= 1.0 (17/08/2020) =
 * Initial
