<?php

/**
 * KVS Plugin help page view: Setup support page content
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin/partials
 */

?>

<div class="wrap">
    <h2>Basic setup scenario</h2>
    <p><b>NOTE</b>: you need your KVS installation to be istalled and fully configured before you start using this plugin.</p>

    <h2>Step 1. Configure Exporting feed in KVS</h2>
    <p>
        First, please go to your KVS admin panel and create exporting feed in <b>Videos -> Feeds -> Add exporting feed</b>.
	    In exporting feed editor specify feed <b>Title</b> to any you like, set <b>External ID</b> = <b>wordpress</b>.
    </p>
    <p>
        Under <b>Video filters</b> section you can configure options that may reduce set of videos returned by this feed.
	    For example, you can configure that only videos from a specific category are returned by this feed and only
	    videos from that category will be imported to this WordPress project. From the other point it is not strictly
	    necessary to enable filtering at feed level, as you can also configure category filters in WordPress plugin
	    settings.
    </p>
    <p>
        In <b>Feed data</b> section make sure that <b>Content type</b> is set to <b>Website link</b> option, and also
	    activate options for exposing categories and tags. Other options in this section are subject to your needs,
	    these options basically affect which data will be available in the feed. E.g. if you need video models to be
	    also imported into your WordPress - activate this option as well.
    </p>
    <p>
        After saving feed data, KVS admin panel will show its <b>Feed URL</b> in the Exporting feeds list. Or you can
	    open the created feed for editing and you will see the same URL is printed in <b>Feed access point</b> field.
	    You will need this URL in the next step.
    </p>

    <h2>Step 2. Connect KVS Videos plugin in WordPress to KVS feed</h2>
    <p>
        In your WordPress admin area go to KVS Videos plugin settings. Specify the feed URL you've copied on the step 1
	    into <b>KVS Feed URL</b> field and press <b>Connect KVS feed</b> button. If the feed URL is specified correctly,
	    the settings page will be updated with more settings available.
    </p>
	<p>
        Configure <b>KVS installation URL</b> option that is in most cases is the URL where KVS is installed. For
		example if you have KVS installed at https://domain.com/tube/, then specify this URL in this field.
    </p>
	<p>
        In terms of data sync, this plugin offers 3 levels of syncronization with KVS videos database:
    </p>
	<ol>
		<li>
			<b>Syncing new videos</b>. This is the main sync process. It is expected that your KVS installation will
			have some new videos added manually by you, or automatically by KVS grabbers, or even by users. Then the
			idea of this plugin is to make sure these new videos automatically appear in your Wordpress project as some
			posts. Here you have to decide how often you want this sync to happen, and this depends on how often new
			videos appear in KVS. This setting is solely up to you, but we would recommend to keep it in manual mode
			from the beginning, so that you can first fully configure and verify the whole integration before you
			activate any automation. The limit of videos per run could only be useful if your KVS already has 1000s of
			videos and you plan to sync them all into your new Wordpress installation. Then syncing all videos at a time
			could be a bit heavy for a typical Wordpress server, so you may need to set some limit to split sync into
			multiple smaller iterations. Other than that we do not recommend configuring any limit of videos per
			sync.
		</li>
		<li>
			<b>Processing deleted videos</b>. Since this plugin is designed to auto-sync all videos from KVS to
			Wordpress, if you need to delete some videos you should first delete them in KVS (to avoid them being
			re-imported again on future re-syncs). Then you can also configure automated processing of deleted videos,
			and they will be deleted from your Wordpress shortly after being delete from KVS. Therefore the recommended
			option for this type of sync is every hour.
		</li>
		<li>
			<b>Running full update</b>. This syncronization is designed for situations when you want to re-sync metadata
			of all videos again (titles, descriptions, categories, other taxonomy). In the real world this would
			probably happen if you want to change the way how videos are synced into your Wordpress, so you need to
			re-sync them again after changing related settings. Therefore this sync should be normally set to manual
			mode, and only run when needed from the <b>Advanced</b> section.
		</li>
	</ol>

	<h2>Step 3. Configure how you want to create videos in Wordpress</h2>
	<p>
        This is configured in <b>Post creation</b> section. There are 2 ways you can create posts with videos from KVS
		- and choosing this depends on your existing data; on how you currently use your posts in Wordpress.
    </p>
	<ol>
		<li>
			The first way is to use one of the existing post types (for example, standard Posts) that will be
			auto-created and published by this plugin. In this case you can customize the HTML code structure of every new
			post using the list of supported data tokens. Use this approach if you already have videos published as
			posts, and you want KVS to automate publishing of new videos. Or if you have a new empty project and you
			want to enhance SEO for your videos from KVS.
		</li>
		<li>
			The second way is to use custom KVS video type without automation into standard Posts. Use this approach if
			you want to manually create and publish posts with KVS videos after they are imported from KVS; or if you
			have a non-typical tube site and you want to create other posts featuring videos from KVS.
		</li>
	</ol>
	<p>
		This section also allows you to configure how to import video categorization by selecting some existing
		taxonomies, or by creating specific KVS taxonomies.
		<br/><b style="color: red">IMPORTANT!</b> In order video
		categorization to be available for export, please make sure that you enabled the corresponding options in
		Exporting feed configuration in KVS admin panel (see <b>Step1</b>). No categorization will be exported by default.
	</p>

	<h2>That's it!</h2>
	<p>
		After configuring basic options, you can try pulling some videos from KVS to test your configuration. You can
		do that in <b>All KVS videos</b> list page by pressing <b>Check for new videos</b> button at the top.
	</p>
</div>
