=== MobStac WordPress Mobile  ===
Contributors: mobstac
Tags: mobile, mobile site, mobile website, iphone, android, blackberry, smartphone site, mobstac, cell phone, cellphone, google, handset, motorola, nokia, opera, opera mini, operamini, palm, plugin, posts, sprint, verizon, windows ce, yahoo
Requires at least: 1.5
Tested up to: 3.3.1
Stable tag: trunk

Renders for mobile visitors a mobile version of your WordPress site, with blazing-fast page loads, multiple themes, 
support for over 5000 mobile devices (not just iPhones and touch phones), analytics, ad network integration, 
and awesome support!

== Description ==

Renders for mobile visitors a mobile version of your WordPress site, with blazing-fast page loads, multiple themes, 
support for over 5000 mobile devices (not just iPhones and touch phones), analytics, ad network integration, 
and awesome support!

== Installation ==
1. Select `MobStac Mobile` from the Plugin directory in WordPress
1. Activate `MobStac Mobile` through the 'Plugins' menu page in WordPress Admin

== Frequently Asked Questions ==

= I have the WP Supercache plugin installed. Is MobStac compatible with it? =

Absolutely, MobStac is compatible and works well with the WP Super Cache plugin as well as others
such as W3 Total Cache. For best results, please configure your caching plugin to turn off caching
for mobile devices since that's handled by MobStac.


== Changelog ==
= 2.75 =
* Javascript insereted by the plugin redirects by regex match of user agent and saves a roundtrip to MobStac servers.

= 2.74 =
* Allowing users to modify MobStac settings.

= 2.73 =
* Renamed screenshots files.

= 2.72 =
* Added screenshots to the trunk.

= 2.71 =
* Added screenshots.
* Hide the API form if the site is created.
* Compatible with WP 3.3.1

= 2.70 =
* Mobsite preview is shown when API Keys match.

= 2.69 =
* Minor fix.

= 2.68 =
* The JS snippet should be inserted only when API Keys match.

= 2.67 =
* Bug fixes.

= 2.66 =
* API Key validation.

= 2.65 =
* Modified the URL for obtaining API Key.

= 2.64 =
* From now on mobile will be created from http://mobstac.com/ .

= 2.63 =
* Minor fix.

= 2.62 =
* Preview site while plugin is installed.

= 2.61 =
* Changed the URL to load the mobsite preview on wordpress dashboard.

= 2.60 =
* Added provision for the user to decide whether he wants to share his e-mail and blog url.

= 2.52 =
* Show a pop-up window when the user tries to access his Mobstac dashboard from the Mobstac Configuration panel.

= 2.51 =
* Show a live mobile preview of the site right inside WP.

= 2.5 =
* 1-click signup process! No need to specify API key and mobile site anymore, plugin directly 
  talks to mobstac.com and configures itself
* Faster mobile redirects

= 2.11 =
* Faster automatic redirection for mobile devices.

= 2.1 =
* Added backward compatibilty for 1.3x series of plugins.

= 2.0 =
* Refactoring of code to better share PHP code between different CMS plugins
* Support for MobStac CMS REST API added. The MobStac framework can query and get information on posts, 
  pages categories, API Version and Wordpress platform version. Plugin notifies MobStac backend on being disabled.

= 1.39 =
* Googlebot-Mobile is redirected to the mobile site as it does not execute JS.

= 1.38 =
* Insert link rel="alternate" pointing at the mobile site. This help with Google search traffic 
  and prevents Google Wireless Transcoder from misinterpreting our mobile site.

= 1.37 =
* Update JavaScript redirect snippet to fix a bug - it was not possible to stay on the desktop site
  for a session: you would get sent back to the mobile site.
  
= 1.36 =
* Compatibility with WordPress 3.1
* Use the new and improved JavaScript redirect snippet (no unnecessary requests to the server anymore)

= 1.35 =
* Compatibility with WordPress 3.0.5
* Send API pings to MobStac only when a post is published

= 1.34 =
* Compatibility with WordPress 3.0.4

= 1.33 =
* Compatibility with WordPress release 3.0.3

= 1.32 =
* Woops, fix missing second argument bug

= 1.31 =
* Get rid of the call to /m/check altogether since JavaScript takes care of the redirect
* Use the mobsite hostname while making a call to /m/autoredirect/

= 1.30 =
* Switch to using the new /m/autoredirect/ URL that is more efficient on the server side

= 1.29 =
* Now showing the MobStac site stats on the Wordpress dashboard

= 1.28 =
* Resolve bugs with referrer tracking
* Don't set the maneref parameter when the referrer is blank
* Revert change that appended parameters to the redirect script

= 1.27 =
* Track original sources for mobile traffic accurately through the maneref parameter

= 1.26 =
* Insert redirect JS snippet at higher position in <head> so that redirects are faster
* Ensure plugin is dormant in the case permalinks have not been configured

= 1.25 =
* Fixed the problem of import of drafts.

= 1.24 =
* Fix compatibility with PHP4 (remove the use of try-catch)

= 1.23 =
* Prefix all function names with mobstac_ so that there are no namespace clashes with other plugins

= 1.22 =
* Warning is displayed on admin dashboard if permalinks are not configured

= 1.21 =
* Fix bug with API key not being set
* Provide a direct link to the API key in the user's MobStac Dashboard 

= 1.20 =
* Added API key to plugin configuration
* Added support for importing older posts into MobStac
* Mobile site URLs now reflect the original post permalinks
* Added support for unicode URLs
* Added a ping mechanism for immediate synchronization of posts as they are published

= 1.11 =
* Fixed the issue where caching systems interfere with redirection
* Path of the mobstac cookie is explicitly set to '/'

= 1.10 =
* Release to the WordPress plugin directory

= 1.05 =
* Latch onto the 'template_redirect' hook instead of 'init'
* Bail out if we're on an admin page - we don't want to mess with that
* Better error handling overall
* Fix code style

= 1.04 =
* Plugin now plants a cookie and redirects based on the cookie value for a session

= 1.03 =
* Bug fix in appending of 'http://" to the redirect URL

= 1.02 =
* Add "http://" to the beginning of the redirect URL
* Catch Errors and Warnings and continue normal execution
* Append version of the plugin to the get parameters

= 1.01 =
* Allow an override of the redirect code by appending msr=0 to the GET parameters

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.10 =
* Release to the WordPress plugin directory

== Screenshots ==
1. Tablet Preview
2. Mobile Preview