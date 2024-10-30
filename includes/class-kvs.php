<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 * Also is used to define internationalization and admin-specific hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/includes
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Kvs_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * KVS log files writer.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Kvs_Logger    KVS log files writer object.
	 */
	public $logger;

	/**
	 * KVS data feed reader.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Kvs_Reader    KVS data feed reader object.
	 */
	public $reader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if( defined( 'KVS_VERSION' ) ) {
			$this->version = KVS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'kvs';

		$this->load_dependencies();
		$this->set_locale();
        
        $this->logger = new Kvs_Logger();
        $this->reader = new Kvs_Reader();
        
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Kvs_Loader. Orchestrates the hooks of the plugin.
	 * - Kvs_i18n. Defines internationalization functionality.
	 * - Kvs_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kvs-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kvs-i18n.php';

		/**
		 * KVS logs writer.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kvs-logger.php';

		/**
		 * KVS feed reader.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kvs-reader.php';

		/**
		 * KVS cron tasks.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kvs-cron.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-kvs-admin.php';

		/**
		 * KVS dashboard widget.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kvs-dashboard.php';

		/**
		 * KVS shortcodes.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kvs-shortcode.php';

		$this->loader = new Kvs_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Kvs_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Kvs_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		global $kernel_video_sharing_admin;

		$kernel_video_sharing_admin = new Kvs_Admin( $this->get_plugin_name(), $this->get_version() );
        $kernel_video_sharing_cron = new Kvs_Cron();
        $kernel_video_sharing_shortcode = new Kvs_SC();

		$this->loader->add_action( 'init', $this, 'register_post_type' );
        $this->loader->add_action( 'init', $this, 'elementor_extensions' );
		$this->loader->add_filter( 'admin_init', $this, 'register_kvs_settings' );
        $this->loader->add_filter( 'all_admin_notices', $kernel_video_sharing_admin, 'kvs_top_info_panel' );

		$this->loader->add_action( 'admin_enqueue_scripts', $kernel_video_sharing_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $kernel_video_sharing_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_notices', $kernel_video_sharing_admin, 'kvs_admin_notice_activation' );
		$this->loader->add_action( 'admin_notices', $kernel_video_sharing_admin, 'kvs_admin_notice_import' );
		$this->loader->add_action( 'admin_menu', $kernel_video_sharing_admin, 'kvs_add_menus' );

		$this->loader->add_filter( 'cron_schedules', $kernel_video_sharing_cron, 'kvs_cron_schedules' );
        $this->loader->add_action( 'kvs_cron_update_hook', $kernel_video_sharing_cron, 'kvs_cron_update_exec' );
        $this->loader->add_action( 'kvs_cron_delete_hook', $kernel_video_sharing_cron, 'kvs_cron_delete_exec' );
        $this->loader->add_action( 'kvs_cron_full_hook', $kernel_video_sharing_cron, 'kvs_cron_full_exec' );

        $this->loader->add_filter( 'manage_kvs_video_posts_columns', $kernel_video_sharing_admin, 'kvs_editor_columns' );
        $this->loader->add_action( 'manage_kvs_video_posts_custom_column', $kernel_video_sharing_admin, 'kvs_editor_columns_data', 10, 2 );
        $this->loader->add_filter( 'manage_edit-kvs_video_sortable_columns', $kernel_video_sharing_admin, 'kvs_editor_columns_sort' );
        
		$this->loader->add_action( 'admin_head-edit.php', $kernel_video_sharing_admin, 'kvs_add_import_videos_button' );
        $this->loader->add_action( 'admin_post_kvs_update_meta', $kernel_video_sharing_admin, 'kvs_update_meta' );
        $this->loader->add_action( 'admin_post_kvs_import', $kernel_video_sharing_admin, 'kvs_do_import' );
        $this->loader->add_action( 'admin_post_kvs_delete', $kernel_video_sharing_admin, 'kvs_do_delete' );
        $this->loader->add_action( 'admin_post_kvs_import_full', $kernel_video_sharing_admin, 'kvs_do_full_import' );
        $this->loader->add_action( 'admin_post_kvs_clear_log', $kernel_video_sharing_admin, 'kvs_do_clear_log' );

		$this->loader->add_filter( 'plugin_action_links_kvs/kvs.php', $this, 'plugins_page_settings_link' );
        
        $this->loader->add_action( 'wp_dashboard_setup', $kernel_video_sharing_admin, 'kvs_add_dashboard_widget' );
        
        $this->loader->add_shortcode( 'kvs_player', $kernel_video_sharing_shortcode, 'kvs_player_shortcode' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
        // Main locader run
		$this->loader->run();
        
        // Cron scheduler run - get new videos
        if( wp_next_scheduled( 'kvs_cron_update_hook' ) === false ) {
            $period = Kvs_Cron::get_cron_update_period();
            $schedules = Kvs_Cron::kvs_cron_schedules();
            if( isSet( $schedules[$period] ) ) {
                wp_schedule_event( time(), $period, 'kvs_cron_update_hook' );
            }
        }
        
        // Cron scheduler run - delete outdated videos
        if( wp_next_scheduled( 'kvs_cron_delete_hook' ) === false ) {
            $period = Kvs_Cron::get_cron_delete_period();
            $schedules = Kvs_Cron::kvs_cron_schedules();
            if( isSet( $schedules[$period] ) ) {
                wp_schedule_event( time(), $period, 'kvs_cron_delete_hook' );
            }
        }
        
        // Cron scheduler run - full videos update
        if( wp_next_scheduled( 'kvs_cron_full_hook' ) === false ) {
            $period = Kvs_Cron::get_cron_full_period();
            $schedules = Kvs_Cron::kvs_cron_schedules();
            if( isSet( $schedules[$period] ) ) {
                wp_schedule_event( time(), $period, 'kvs_cron_full_hook' );
            }
        }
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Kvs_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register global plugin settings
	 *
	 * @since     1.0.0
	 */
	public function register_kvs_settings() {
		global $kernel_video_sharing_admin;

		if( empty( $kernel_video_sharing_admin ) ) {
			$kernel_video_sharing_admin = new Kvs_Admin( $this->get_plugin_name(), $this->get_version() );
		}
		register_setting( 'kvs-settings-group', 'kvs_feed_url', array($kernel_video_sharing_admin, 'kvs_feed_url_check' ) );
        register_setting( 'kvs-settings-group', 'kvs_library_path' );
        
		register_setting( 'kvs-settings-group', 'kvs_update_period', array($kernel_video_sharing_admin, 'kvs_cron_update_period_change' ) );
		register_setting( 'kvs-settings-group', 'kvs_update_limit' );
		register_setting( 'kvs-settings-group', 'kvs_delete_period', array($kernel_video_sharing_admin, 'kvs_cron_delete_period_change' ) );
        register_setting( 'kvs-settings-group', 'kvs_full_period', array($kernel_video_sharing_admin, 'kvs_cron_full_period_change' ) );
		

		register_setting( 'kvs-settings-group-rules', 'kvs_video_locale' );
		register_setting( 'kvs-settings-group-rules', 'kvs_video_screenshot' );
		register_setting( 'kvs-settings-group-rules', 'kvs_video_filter_by' );
		register_setting( 'kvs-settings-group-rules', 'kvs_video_filter_category' );
		register_setting( 'kvs-settings-group-rules', 'kvs_video_filter_source' );
        
		register_setting( 'kvs-settings-group-post', 'kvs_post_type' );
		register_setting( 'kvs-settings-group-post', 'kvs_post_status' );
		register_setting( 'kvs-settings-group-post', 'kvs_post_date' );
        register_setting( 'kvs-settings-group-post', 'kvs_post_import_featured_image' );
        register_setting( 'kvs-settings-group-post', 'kvs_post_body_template' );
		register_setting( 'kvs-settings-group-post', 'kvs_taxonomy_category' );
		register_setting( 'kvs-settings-group-post', 'kvs_taxonomy_tag' );
		register_setting( 'kvs-settings-group-post', 'kvs_taxonomy_model' );
		register_setting( 'kvs-settings-group-post', 'kvs_taxonomy_source' );
		register_setting( 'kvs-settings-group-post', 'kvs_custom1_name' );
		register_setting( 'kvs-settings-group-post', 'kvs_custom1_value' );
		register_setting( 'kvs-settings-group-post', 'kvs_custom2_name' );
		register_setting( 'kvs-settings-group-post', 'kvs_custom2_value' );
		register_setting( 'kvs-settings-group-post', 'kvs_custom3_name' );
		register_setting( 'kvs-settings-group-post', 'kvs_custom3_value' );

		register_setting( 'kvs-settings-group-advanced', 'kvs_feed_last_id' );
		register_setting( 'kvs-settings-group-advanced', 'kvs_log_level' );
	}

	/**
	 * Add "settings" link to the KVS Plugin on the plugins list page.
	 *
	 * @since     1.0.0
	 * @return    array    Plugin links.
	 */
	public function plugins_page_settings_link( $links ) {
		$link = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings' );
		$settings_link = '<a href="' . $link . '"><i class="fa fa-cog"></i> ' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}


	/**
	 * Register custom post type and taxonomies for KVS Videos
	 *
	 * @since     1.0.0
	 */
	public function register_post_type() {
        // Register taxonomies for that list of post types
        $post_types = array(
            'kvs_video'
        );
        if( !empty( get_option( 'kvs_post_type' ) ) ) {
            $post_types[] = get_option( 'kvs_post_type' );
        }
        
		/**
		 * Post Type: KVS Videos.
		 */
		$labels = array(
			'name' => __( 'KVS Videos', 'kvs' ),
			'singular_name' => __( 'KVS Video', 'kvs' ),
			'menu_name' => __( 'KVS Videos', 'kvs' ),
			'all_items' => __( 'All KVS Videos', 'kvs' ),
			'add_new' => __( 'Add new', 'kvs' ),
			'add_new_item' => __( 'Add new video', 'kvs' ),
			'edit_item' => __( 'Edit video', 'kvs' ),
			'new_item' => __( 'New video', 'kvs' ),
			'view_item' => __( 'View video', 'kvs' ),
			'view_items' => __( 'View videos', 'kvs' ),
			'search_items' => __( 'Search videos', 'kvs' ),
			'not_found' => __( empty( get_option( 'kvs_post_type' ) ) ? 'No videos found' : 'KVS videos are set to be imported into another post type', 'kvs' ),
			'not_found_in_trash' => __( 'No videos found in trash', 'kvs' ),
			'parent' => __( 'Parent video:', 'kvs' ),
			'featured_image' => __( 'Video screenshot', 'kvs' ),
			'set_featured_image' => __( 'Set video screenshot', 'kvs' ),
			'remove_featured_image' => __( 'Remove video screenshot', 'kvs' ),
			'use_featured_image' => __( 'Use as video screenshot', 'kvs' ),
			'archives' => __( 'Video archives', 'kvs' ),
			'insert_into_item' => __( 'Insert into video', 'kvs' ),
			'uploaded_to_this_item' => __( 'Upload to this video', 'kvs' ),
			'filter_items_list' => __( 'Filter videos list', 'kvs' ),
			'items_list_navigation' => __( 'Videos list navigation', 'kvs' ),
			'items_list' => __( 'Videos list', 'kvs' ),
			'attributes' => __( 'Videos attributes', 'kvs' ),
			'name_admin_bar' => __( 'KVS Video', 'kvs' ),
			'item_published' => __( 'Video published', 'kvs' ),
			'item_published_privately' => __( 'Video published privately.', 'kvs' ),
			'item_reverted_to_draft' => __( 'Video reverted to draft.', 'kvs' ),
			'item_scheduled' => __( 'Video scheduled', 'kvs' ),
			'item_updated' => __( 'Video updated.', 'kvs' ),
			'parent_item_colon' => __( 'Parent video:', 'kvs' ),
		);
        $icon = "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' width='12pt' height='13pt' viewBox='0 0 12 13' version='1.1'><g id='surface1'><path style=' stroke:none;fill-rule:nonzero;fill:white;fill-opacity:1;' d='M 5.933594 13 L 2.941406 11.355469 L 2.941406 1.644531 L 5.933594 0 L 10.023438 2.246094 L 3.207031 6.292969 L 3.207031 6.324219 L 10.222656 10.628906 Z M 0.015625 9.742188 L 0.015625 3.257812 L 2.609375 1.835938 L 2.609375 11.164062 Z M 0.015625 9.742188 '/><path style=' stroke:none;fill-rule:nonzero;fill:white;fill-opacity:1;' d='M 7.332031 4.207031 L 10.355469 2.402344 L 11.851562 3.226562 L 11.851562 9.773438 L 10.554688 10.46875 L 7.332031 8.476562 Z M 7.332031 4.207031 '/></g></svg>";
        $taxonomies_list = array();
        if( !empty( get_option( 'kvs_taxonomy_category' ) ) ) {
            $taxonomies_list[] = get_option( 'kvs_taxonomy_category' );
        }
        if( !empty( get_option( 'kvs_taxonomy_tag' ) ) ) {
            $taxonomies_list[] = get_option( 'kvs_taxonomy_tag' );
        }
        if( !empty( get_option( 'kvs_taxonomy_model' ) ) ) {
            $taxonomies_list[] = get_option( 'kvs_taxonomy_model' );
        }
        if( !empty( get_option( 'kvs_taxonomy_source' ) ) ) {
            $taxonomies_list[] = get_option( 'kvs_taxonomy_source' );
        }
		$args = array(
			'label' => __( 'KVS Videos', 'kvs' ),
			'labels' => $labels,
			'description' => 'Kernel Video Sharing videos imported from the feed',
			'public' => true,
			'publicly_queryable' => false,
			'show_ui' => true,
			'show_in_rest' => true,
			'rest_base' => '',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'has_archive' => false,
			'show_in_menu' => true,
            'menu_icon' => 'data:image/svg+xml;base64,' . base64_encode($icon),
			'show_in_nav_menus' => false,
			'delete_with_user' => false,
			'exclude_from_search' => true,
			'capability_type' => 'post',
			'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap' => true,
			'hierarchical' => false,
			'query_var' => false,
			'supports' => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
			'taxonomies' => $taxonomies_list,
			'show_in_graphql' => false,
		);
		register_post_type( 'kvs_video', $args );

		/**
		 * Taxonomy: Video categories.
		 */
        
		$labels = array(
			'name' => __( 'Video categories', 'kvs' ),
			'singular_name' => __( 'Video category', 'kvs' ),
			'menu_name' => __( 'Categories', 'kvs' ),
			'all_items' => __( 'All categories', 'kvs' ),
			'edit_item' => __( 'Edit category', 'kvs' ),
			'view_item' => __( 'View category', 'kvs' ),
			'update_item' => __( 'Update category name', 'kvs' ),
			'add_new_item' => __( 'Add new category', 'kvs' ),
			'new_item_name' => __( 'New category name', 'kvs' ),
			'parent_item' => __( 'Parent category', 'kvs' ),
			'parent_item_colon' => __( 'Parent category:', 'kvs' ),
			'search_items' => __( 'Search categories', 'kvs' ),
			'popular_items' => __( 'Popular categories', 'kvs' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'kvs' ),
			'add_or_remove_items' => __( 'Add or remove categories', 'kvs' ),
			'choose_from_most_used' => __( 'Choose from the most used categories', 'kvs' ),
			'not_found' => __( 'No categories found', 'kvs' ),
			'no_terms' => __( 'No categories', 'kvs' ),
			'items_list_navigation' => __( 'Categories list navigation', 'kvs' ),
			'items_list' => __( 'Video categories list', 'kvs' ),
			'back_to_items' => __( 'Back to categories', 'kvs' ),
		);
		$args = array(
			'label' => __( 'Video categories', 'kvs' ),
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'hierarchical' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'query_var' => false,
			'rewrite' => [ 'slug' => 'kvs_category', 'with_front' => true, ],
			'show_admin_column' => true,
			'show_in_rest' => true,
			'rest_base' => 'kvs_category',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'show_in_quick_edit' => true,
			'show_in_graphql' => false,
			'default_term' => ['name' => 'Undefined'],
			'capabilities' => array(
				'assign_terms' => 'manage_options',
				'edit_terms'   => 'do_not_allow',
				'manage_terms' => 'manage_options',
			),
		);
        if( get_option( 'kvs_taxonomy_category' ) === 'kvs_category' ) {
            register_taxonomy( 'kvs_category', $post_types, $args );
            foreach( $post_types as $pt ) {
                register_taxonomy_for_object_type( 'kvs_category', $pt );
            }
        }
	
		/**
		 * Taxonomy: Video tags.
		 */
	
		$labels = array(
			'name' => __( 'Video tags', 'kvs' ),
			'singular_name' => __( 'Video tag', 'kvs' ),
			'menu_name' => __( 'Tags', 'kvs' ),
			'all_items' => __( 'All tags', 'kvs' ),
			'edit_item' => __( 'Edit tag', 'kvs' ),
			'view_item' => __( 'View tag', 'kvs' ),
			'update_item' => __( 'Update tag name', 'kvs' ),
			'add_new_item' => __( 'Add new tag', 'kvs' ),
			'new_item_name' => __( 'New tag name', 'kvs' ),
			'parent_item' => __( 'Parent tag', 'kvs' ),
			'parent_item_colon' => __( 'Parent tag:', 'kvs' ),
			'search_items' => __( 'Search tags', 'kvs' ),
			'popular_items' => __( 'Popular tags', 'kvs' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'kvs' ),
			'add_or_remove_items' => __( 'Add or remove tags', 'kvs' ),
			'choose_from_most_used' => __( 'Choose from the most used tags', 'kvs' ),
			'not_found' => __( 'No tags found', 'kvs' ),
			'no_terms' => __( 'No tags', 'kvs' ),
			'items_list_navigation' => __( 'Tags list navigation', 'kvs' ),
			'items_list' => __( 'KVS tags list', 'kvs' ),
			'back_to_items' => __( 'Back to tags', 'kvs' ),
		);
		$args = array(
			'label' => __( 'Video tags', 'kvs' ),
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => false,
			'hierarchical' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'query_var' => false,
			'rewrite' => [ 'slug' => 'kvs_tag', 'with_front' => true, ],
			'show_admin_column' => true,
			'show_in_rest' => true,
			'rest_base' => 'kvs_tag',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'show_in_quick_edit' => true,
			'show_in_graphql' => false,
			'capabilities' => array(
				'assign_terms' => 'manage_options',
				'edit_terms'   => 'do_not_allow',
				'manage_terms' => 'manage_options',
			),
		);
        if( get_option( 'kvs_taxonomy_tag' ) === 'kvs_tag' ) {
    		register_taxonomy( 'kvs_tag', $post_types, $args );
            foreach( $post_types as $pt ) {
                register_taxonomy_for_object_type( 'kvs_tag', $pt );
            }
        }
	
		/**
		 * Taxonomy: Video models.
		 */
	
		$labels = array(
			'name' => __( 'Video models', 'kvs' ),
			'singular_name' => __( 'Video model', 'kvs' ),
			'menu_name' => __( 'Models', 'kvs' ),
			'all_items' => __( 'All models', 'kvs' ),
			'edit_item' => __( 'Edit model', 'kvs' ),
			'view_item' => __( 'View model', 'kvs' ),
			'update_item' => __( 'Update model name', 'kvs' ),
			'add_new_item' => __( 'Add new model', 'kvs' ),
			'new_item_name' => __( 'New model name', 'kvs' ),
			'parent_item' => __( 'Parent model', 'kvs' ),
			'parent_item_colon' => __( 'Parent model:', 'kvs' ),
			'search_items' => __( 'Search models', 'kvs' ),
			'popular_items' => __( 'Popular models', 'kvs' ),
			'separate_items_with_commas' => __( 'Separate models with commas', 'kvs' ),
			'add_or_remove_items' => __( 'Add or remove models', 'kvs' ),
			'choose_from_most_used' => __( 'Choose from the most used models', 'kvs' ),
			'not_found' => __( 'No models found', 'kvs' ),
			'no_terms' => __( 'No models', 'kvs' ),
			'items_list_navigation' => __( 'Models list navigation', 'kvs' ),
			'items_list' => __( 'KVS models list', 'kvs' ),
			'back_to_items' => __( 'Back to models', 'kvs' ),
		);
		$args = array(
			'label' => __( 'Video models', 'kvs' ),
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => false,
			'hierarchical' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'query_var' => false,
			'rewrite' => [ 'slug' => 'kvs_model', 'with_front' => true, ],
			'show_admin_column' => true,
			'show_in_rest' => true,
			'rest_base' => 'kvs_model',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'show_in_quick_edit' => true,
			'show_in_graphql' => false,
			'capabilities' => array(
				'assign_terms' => 'manage_options',
				'edit_terms'   => 'do_not_allow',
				'manage_terms' => 'manage_options',
			),
		);
        if( get_option( 'kvs_taxonomy_model' ) === 'kvs_model' ) {
    		register_taxonomy( 'kvs_model', $post_types, $args );
            foreach( $post_types as $pt ) {
                register_taxonomy_for_object_type( 'kvs_model', $pt );
            }
        }
	
		/**
		 * Taxonomy: Video content sources.
		 */
	
		$labels = array(
			'name' => __( 'Video content sources', 'kvs' ),
			'singular_name' => __( 'Video content source', 'kvs' ),
			'menu_name' => __( 'Content sources', 'kvs' ),
			'all_items' => __( 'All content sources', 'kvs' ),
			'edit_item' => __( 'Edit content source', 'kvs' ),
			'view_item' => __( 'View content source', 'kvs' ),
			'update_item' => __( 'Update content source name', 'kvs' ),
			'add_new_item' => __( 'Add new content source', 'kvs' ),
			'new_item_name' => __( 'New content source name', 'kvs' ),
			'parent_item' => __( 'Parent content source', 'kvs' ),
			'parent_item_colon' => __( 'Parent content source:', 'kvs' ),
			'search_items' => __( 'Search content sources', 'kvs' ),
			'popular_items' => __( 'Popular content sources', 'kvs' ),
			'separate_items_with_commas' => __( 'Separate content sources with commas', 'kvs' ),
			'add_or_remove_items' => __( 'Add or remove content sources', 'kvs' ),
			'choose_from_most_used' => __( 'Choose from the most used content sources', 'kvs' ),
			'not_found' => __( 'No content sources found', 'kvs' ),
			'no_terms' => __( 'No content sources', 'kvs' ),
			'items_list_navigation' => __( 'Content sources list navigation', 'kvs' ),
			'items_list' => __( 'KVS content sources list', 'kvs' ),
			'back_to_items' => __( 'Back to content sources', 'kvs' ),
		);
		$args = array(
			'label' => __( 'Video content sources', 'kvs' ),
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => false,
			'hierarchical' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'query_var' => false,
			'rewrite' => [ 'slug' => 'kvs_source', 'with_front' => true, ],
			'show_admin_column' => true,
			'show_in_rest' => true,
			'rest_base' => 'kvs_source',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'show_in_quick_edit' => true,
			'show_in_graphql' => false,
			'capabilities' => array(
				'assign_terms' => 'manage_options',
				'edit_terms'   => 'do_not_allow',
				'manage_terms' => 'manage_options',
			),
		);
        if( get_option( 'kvs_taxonomy_source' ) === 'kvs_source' ) {
    		register_taxonomy( 'kvs_source', $post_types, $args );
            foreach( $post_types as $pt ) {
                register_taxonomy_for_object_type( 'kvs_source', $pt );
            }
        }
	}

    
	/**
	 * Check if Elementor plugin is active
	 *
	 * @since     1.0.2
	 */
    public function elementor_extensions() {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if( is_plugin_active( 'elementor/elementor.php' ) ) {
            include_once KVS_DIRPATH . 'includes/elementor/dynamic-tags-numeric.php';
            include_once KVS_DIRPATH . 'includes/elementor/dynamic-tags.php';
            
            add_action( 'elementor/dynamic_tags/register_tags', function( $dynamic_tags ) {
                $dynamic_tags->register_tag( 'KVS_Elementor_Dynamic_Tags_Numeric' );
                $dynamic_tags->register_tag( 'KVS_Elementor_Dynamic_Tags_Screenshot' );
                $dynamic_tags->register_tag( 'KVS_Elementor_Dynamic_Tags_Link' );
                $dynamic_tags->register_tag( 'KVS_Elementor_Dynamic_Tags_VideoURL' );
            } );
        }
    }
}
