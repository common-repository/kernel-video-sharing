=== Kernel Video Sharing Integration ===
Contributors: kvsteam
Donate link: https://www.kernel-video-sharing.com/
Tags: video, player, tube, videoplayer
Requires at least: 3.0.1
Tested up to: 6.4.3
Stable tag: 1.0.9
Requires PHP: 7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Kernel Video Sharing plugin for WordPress

== Description ==

This plugin provides integration of Kernel Video Sharing video content into Wordpress.

WordPress is a popular solution for basic sites, but video websites on this platform face many difficulties, for
example:

* all video files are stored on a single server, there is no multi-server support, some of the disadvantages that follow from this:
* you cannot add new servers to increase the total amount of stored videos.
* you cannot load balance videos between several servers to cope with the increased load on high traffic.
* you cannot move videos between servers.
* you cannot send less important countries to a slower server, and more important countries to a faster server.
* it is impossible to limit less important countries in streaming speed to save bandwidth.
* there is no possibility of video conversion, all files must be uploaded ready-made, no multi-server conversion support.
* there is no way to have different video formats and qualities.
* there is no possibility of displaying timeline screenshots when fast-forwarding a video.
* there is no way to maintain the entire base of video files under control, or make an audit of video files.
* there is no way to control the speed of video files depending on their bitrate to save bandwidth.
* extremely weak advertising features, for example:
* very basic advertising opportunities or are absent at all.
* most of the advertising is possible only through the templates to be set.

All these and many other disadvantages can be easily solved using KVS. You can install KVS aside to WordPress
installation and then maintain the entire video database in it - add, administer, delete videos with all meta
information, while KVS will be responsible for converting, storing, managing videos files, as well as for displaying
the player and all advertisements inside the player. This plugin will then automatically pull videos from KVS into
WordPress and create new posts with them.

KVS also provides extremely feature rich import and grabber API that can be used to automate importing video content
into KVS with highly customizable settings.

== Installation ==

NOTE: This plugin requires KVS Ultimate to be installed on your server!

This plugin should be installed from Wordpress plugins repository, or manually via unpacking ZIP into plugins directory.
Read plugin help section in WP admin to learn how to use it for your benefits.

== Changelog ==

= 1.0 =
* Initial version

= 1.0.7 =
* Consmetical changes

= 1.0.8 =
* Added support for post status (draft, published, pending).
* Added support for up to 3 custom fields to be populated with KVS data.

= 1.0.9 =
* Added support for post publishing date (either take from feed, or current date).
* Fixed bug with sending parameters in feed URL.