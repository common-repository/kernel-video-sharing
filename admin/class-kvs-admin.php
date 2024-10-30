<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		global $kernel_video_sharing;

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'kvs-boot-css', 
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css',
			array(), $this->version, 'all' );
		wp_enqueue_style(
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'css/kvs-admin.css', 
			array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 
			$this->plugin_name, 
			plugin_dir_url( __FILE__ ) . 'js/kvs-admin.js', 
			array( 'jquery' ), $this->version, true);
		wp_enqueue_script( 
			$this->plugin_name . '-fa', 
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/js/all.min.js',
			array(), $this->version, true);
	}


	/**
	 * KVS plugin activation notice
	 *
	 * @since 1.0.0
	 */
	public function kvs_admin_notice_activation() {
		if( get_transient( 'kvs-admin-notice' ) ) {
			echo '<div class="updated notice is-dismissible">';
			echo '<p>'.__( 'Welcome to Kernel Video Sharing plugin.', 'kvs' ).'</p>';
			echo '<p><a href="' . esc_attr(admin_url( 'edit.php?post_type=kvs_video&page=kvs-help' )) . '" class="kvs_configuration_plugin_main">'.__( 'Open Help section to learn about basic usage.', 'kvs' ).'</a></p>';
			echo '</div>';
			delete_transient( 'kvs-admin-notice' );
		}
	}


	/**
	 * KVS plugin status panel on all KVS pages
	 *
	 * @since 1.0.0
	 */
	public function kvs_top_info_panel() {
        global $pagenow, $post_type;
        
        $is_settings = false;
        if( !empty($_GET['page']) && $_GET['page'] === 'kvs-settings' ) {
            $is_settings = true;
        }
        if( $post_type !== 'kvs_video' && !$is_settings ) {
            return;
        }
        
        $cron_period = Kvs_Cron::get_cron_update_period();
        $cron_schedules = Kvs_Cron::kvs_cron_schedules();
        if( empty($cron_schedules[$cron_period]) ) {
            $cron_period = 'manual';
        }
        
        if( $cron_period !== 'manual' ) {
            $last_run = (int)get_option( 'kvs_feed_last_run', 0 );
            $next_run = wp_next_scheduled( 'kvs_cron_update_hook' );
            
            echo '<div class="kvs-info-panel ' . ( $is_settings ? ' no-options' : '' ) . '" id="kvs-info-panel">';
            
            echo '<i class="fa fa-clock fa-fw"></i> ';
            if( $last_run ) {
                $last_run_p = time() - $last_run;
                if( $last_run_p<=60 ) {
                    $last_run_p = __( 'less than a minute', 'kvs' );
                } elseif( $last_run_p < 60*60 ) {
                    $last_run_p = human_readable_duration( gmdate( 'i:s', $last_run_p ) );
                } else {
                    $last_run_p = human_readable_duration( gmdate( 'H:i:s', $last_run_p ) );
                }
                echo esc_html(sprintf( __( 'Last import %s ago.', 'kvs' ), $last_run_p ));
            } else {
                echo __( 'Automatic videos synchronization was not fired yet.', 'kvs' );
            }
            echo '<div class="kvs-drop">';
            
            $updated = (int)get_option( 'kvs_feed_last_update', 0 );
            $inserted = (int)get_option( 'kvs_feed_last_insert', 0 );
            $deleted = (int)get_option( 'kvs_feed_last_delete', 0 );
            if( $updated + $inserted + $deleted > 0 ) {
                echo '<div class="kvs-last-run">';
                echo '<i class="fa fa-exclamation fa-fw"></i> ';
                $actions = array();
                if( $updated ) {
                    $actions[] = sprintf( __( '%d videos was updated', 'kvs' ), $updated );
                }
                if( $inserted ) {
                    $actions[] = sprintf( __( '%d new videos was added', 'kvs' ), $inserted );
                }
                if( $deleted ) {
                    $actions[] = sprintf( __( '%d videos was deleted', 'kvs' ), $deleted );
                }
                echo esc_html(implode(', ', $actions));
                echo '.';
                echo '</div>';
            }
            
            $next_run_p = $next_run - time();
            if( $next_run_p<=60 ) {
                $next_run_r = __( 'less than a minute', 'kvs' );
            } elseif( $next_run_p < 60*60 ) {
                $next_run_r = human_readable_duration( gmdate( 'i:s', $next_run_p ) );
            } else {
                $next_run_r = human_readable_duration( gmdate( 'H:i:s', $next_run_p ) );
            }
            echo '<div class="kvs-next-run">';
            echo '<i class="fa fa-calendar fa-fw"></i> ';
            echo esc_html(sprintf( __( 'Next run in %s.', 'kvs' ), $next_run_r ));
            if( !empty( $cron_schedules[$cron_period]['display'] ) ) {
                echo esc_html(' (' . $cron_schedules[$cron_period]['display'] . ')');
            }
            echo '</div>';
            echo '</div>';

            echo '</div>';
        }
	}


	/**
	 * Adding admin menus
	 *
	 * @since 1.0.0
	 */
	public function kvs_add_menus() {
        global $submenu;
        
		if( file_exists( KVS_DIRPATH . 'admin/partials/dashboard-view.php' ) ) {
    		add_submenu_page( 'edit.php?post_type=kvs_video', __( 'Dashboard', 'kvs'), __( 'Dashboard', 'kvs'), 'manage_options', 'kvs-dashboard', array( $this, 'kvs_dashboard_page' ), 0 );
        }
		if( file_exists( KVS_DIRPATH . 'admin/partials/settings-view.php' ) ) {
    		add_submenu_page( 'edit.php?post_type=kvs_video', __( 'Settings', 'kvs'), __( 'Settings', 'kvs'), 'manage_options', 'kvs-settings', array( $this, 'kvs_settings_page' ) );
        }
		if( file_exists( KVS_DIRPATH . 'admin/partials/help-view.php' ) ) {
    		add_submenu_page( 'edit.php?post_type=kvs_video', __( 'Help', 'kvs'), __( 'Help', 'kvs'), 'manage_options', 'kvs-help', array( $this, 'kvs_help_page' ) );
        }
        $submenu['edit.php?post_type=kvs_video'][] = array(__( 'Visit website', 'kvs') . ' <i class="fas fa-external-link-alt"></i>', 'manage_options', KVS_WEBSITE);
	}


	/**
	 * KVS Dashboard Widget adding
	 *
	 * @since 1.0.0
	 */
	public function kvs_add_dashboard_widget() {
        $title = '<span>';
        $title .= '<img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIxLjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9ItCh0LvQvtC5XzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAyMzIgNDEuMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjMyIDQxLjE7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4KCS5zdDB7ZmlsbDojMjc2RkRCO30KCS5zdDF7ZmlsbDojNDM0NDQ4O30KCS5zdDJ7ZmlsbDojM0IzQTNBO30KPC9zdHlsZT4KPGc+Cgk8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMzE5IC0xNTgzKSI+CgkJPGc+CgkJCTxwYXRoIGlkPSJfeDMzX2c1OGEiIGNsYXNzPSJzdDAiIGQ9Ik0zMzYuOCwxNjI0LjFsLTktNS4ydi0zMC43bDktNS4ybDEyLjMsNy4xbC0yMC41LDEyLjh2MC4xbDIxLjEsMTMuNkwzMzYuOCwxNjI0LjF6CgkJCQkgTTMxOSwxNjEzLjh2LTIwLjVsNy44LTQuNXYyOS41TDMxOSwxNjEzLjh6Ii8+CgkJPC9nPgoJPC9nPgoJPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTMxOSAtMTU4MykiPgoJCTxnPgoJCQk8cGF0aCBpZD0iX3gzM19nNThiIiBjbGFzcz0ic3QxIiBkPSJNMzQxLDE1OTYuM2w5LjEtNS43bDQuNSwyLjZ2MjAuN2wtMy45LDIuMmwtOS43LTYuM0wzNDEsMTU5Ni4zeiIvPgoJCTwvZz4KCTwvZz4KCTxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0zMTkgLTE1ODMpIj4KCQk8Zz4KCQkJPHBhdGggaWQ9Il94MzNfZzU4YyIgY2xhc3M9InN0MiIgZD0iTTM2MywxNTk4aDIuNXY1LjNsNC01LjNoMy4ybC00LjYsNi4ybDUuMSw2LjhIMzcwbC0zLjUtNC43bC0xLDEuM3YzLjRIMzYzVjE1OTh6IE0zNzQsMTU5OAoJCQkJaDcuN3YyLjZoLTUuMXYyLjZoNS4xdjIuNmgtNS4xdjIuNmg1LjF2Mi42SDM3NFYxNTk4eiBNMzgyLjksMTYxMXYtMTNoMy44YzIuMSwwLDMuOCwxLjgsMy44LDMuOWMwLDEuNC0wLjgsMi43LTIsMy40bDIuNSw1LjcKCQkJCWgtMi44bC0yLjMtNS4yaC0wLjV2NS4yTDM4Mi45LDE2MTF6IE0zODUuNCwxNjAzLjJoMS4zYzAuNywwLDEuMy0wLjYsMS4zLTEuM2MwLTAuNy0wLjYtMS4zLTEuMy0xLjNoLTEuM1YxNjAzLjJ6IE00MDAsMTU5OGgyLjUKCQkJCXYxM2gtM2wtNS4xLTguN3Y4LjdoLTIuNXYtMTNoM2w1LjEsOC43TDQwMCwxNTk4eiBNNDAzLjcsMTU5OGg3Ljd2Mi42aC01LjF2Mi42aDUuMXYyLjZoLTUuMXYyLjZoNS4xdjIuNmgtNy43VjE1OTh6CgkJCQkgTTQxMi41LDE1OThoMi41djEwLjRoNC42djIuNmgtNy4yVjE1OTh6IE00MzAuNSwxNjExbC00LjYtMTNoMi43bDMuMiw5LjJsMy4yLTkuMmgyLjdsLTQuNiwxM0g0MzAuNXogTTQzOC42LDE1OThoMi41djEzaC0yLjUKCQkJCVYxNTk4eiBNNDQyLjMsMTU5OGgzLjZjMy41LDAsNi40LDIuOSw2LjQsNi41cy0yLjksNi41LTYuNCw2LjVoLTMuNUw0NDIuMywxNTk4eiBNNDQ1LjksMTYwOC40YzIuMSwwLDMuOC0xLjcsMy44LTMuOQoJCQkJYzAtMi4xLTEuNy0zLjktMy44LTMuOWgtMXY3LjhINDQ1Ljl6IE00NTMuNCwxNTk4aDcuN3YyLjZoLTUuMXYyLjZoNS4xdjIuNmgtNS4xdjIuNmg1LjF2Mi42aC03LjdWMTU5OHogTTQ3NSwxNjA0LjUKCQkJCWMwLDMuNi0yLjksNi41LTYuNCw2LjVjLTMuNSwwLTYuNC0zLTYuNC02LjVjMC0zLjYsMi45LTYuNSw2LjQtNi41QzQ3Mi4xLDE1OTgsNDc1LDE2MDAuOSw0NzUsMTYwNC41eiBNNDY0LjgsMTYwNC41CgkJCQljMCwyLjEsMS43LDMuOSwzLjgsMy45YzAsMCwwLDAsMCwwYzIuMSwwLDMuOC0xLjgsMy44LTMuOXMtMS43LTMuOS0zLjgtMy45QzQ2Ni41LDE2MDAuNiw0NjQuNywxNjAyLjQsNDY0LjgsMTYwNC41CgkJCQlDNDY0LjgsMTYwNC41LDQ2NC44LDE2MDQuNSw0NjQuOCwxNjA0LjVMNDY0LjgsMTYwNC41eiBNNDgxLjIsMTYwMS45YzAtMi4yLDEuNy0zLjksMy44LTMuOWMxLjUsMCwyLjgsMC41LDMuOSwxLjRsLTEuOCwxLjkKCQkJCWMtMC42LTAuNC0xLjMtMC42LTIuMS0wLjZjLTAuNywwLTEuMywwLjYtMS4zLDEuM2MwLDAuMywwLjEsMC42LDAuMywwLjhjMC40LDAuNSwxLjEsMC42LDEuNywwLjZjMi4xLDAuMSwzLjcsMS42LDMuNywzLjgKCQkJCWMwLDIuMS0xLjcsMy45LTMuOCwzLjljLTEuNiwwLTMuMi0wLjYtNC40LTEuOGwxLjgtMS44YzAuNywwLjcsMS42LDEuMSwyLjYsMS4xYzAuNywwLDEuMi0wLjYsMS4yLTEuM2MwLTAuNC0wLjEtMC44LTAuNS0xCgkJCQljLTAuNi0wLjQtMS42LTAuMi0yLjItMC40QzQ4Mi4zLDE2MDUuMiw0ODEuMSwxNjAzLjcsNDgxLjIsMTYwMS45TDQ4MS4yLDE2MDEuOXogTTQ5MC41LDE1OThoMi41djUuMmg0LjZ2LTUuMmgyLjV2MTNoLTIuNXYtNS4yCgkJCQloLTQuNnY1LjJoLTIuNVYxNTk4eiBNNTEwLjMsMTYxMWwtMS4zLTMuNmgtMy45bC0xLjMsMy42aC0yLjdsNC42LTEzaDIuN2w0LjYsMTNINTEwLjN6IE01MDcsMTYwMS44bC0xLDIuOWgyLjFMNTA3LDE2MDEuOHoKCQkJCSBNNTEzLjgsMTYxMXYtMTNoMy44YzIuMSwwLDMuOCwxLjgsMy44LDMuOWMwLDEuNC0wLjgsMi43LTIsMy40bDIuNSw1LjdoLTIuOGwtMi4zLTUuMmgtMC41djUuMkw1MTMuOCwxNjExeiBNNTE2LjMsMTYwMy4yaDEuMwoJCQkJYzAuNywwLDEuMy0wLjYsMS4zLTEuM2MwLTAuNy0wLjYtMS4zLTEuMy0xLjNoLTEuM1YxNjAzLjJ6IE01MjIuOCwxNTk4aDIuNXYxM2gtMi41TDUyMi44LDE1OTh6IE01MzQuNiwxNTk4aDIuNXYxM2gtM2wtNS4xLTguNwoJCQkJdjguN2gtMi41di0xM2gzbDUuMSw4LjdMNTM0LjYsMTU5OHogTTU0OSwxNTk5LjhsLTEuOCwxLjhjLTAuNy0wLjctMS42LTEtMi42LTFjLTIuMSwwLTMuOCwxLjgtMy44LDMuOWMwLDAsMCwwLDAsMAoJCQkJYzAsMi4xLDEuNywzLjksMy44LDMuOWMxLjIsMCwyLjMtMC42LDMtMS42aC0zdi0yLjZoNi4zbDAsMC4yYzAsMy42LTIuOSw2LjUtNi40LDYuNWMtMy41LDAtNi40LTMtNi40LTYuNQoJCQkJYzAtMy42LDIuOC02LjUsNi40LTYuNUM1NDYuNCwxNTk4LDU0Ny45LDE1OTguNyw1NDksMTU5OS44TDU0OSwxNTk5Ljh6Ii8+CgkJPC9nPgoJPC9nPgo8L2c+Cjwvc3ZnPgo=" height="30" style="margin: -10px 0;" alt=" ';
        $title .= __( 'Kernel Video Sharing', 'kvs' );
        $title .= '" /></span>';
        wp_add_dashboard_widget( 
            KVS_PREFIX . '_dashboard_widget', 
            $title,
            array( new Kvs_DB(), 'kvs_dashboard_widget' ),
            null, // control callback
            null, // callback args
            'side',
            'high'
        );
	}

	/**
	 * KVS Dashboard page
	 *
	 * @since 1.0.0
	 */
	public function kvs_dashboard_page() {
		global $kernel_video_sharing;

		if(!current_user_can('manage_options')) {
			wp_die('Unauthorized user');
		}

		include_once KVS_DIRPATH . 'admin/partials/dashboard-view.php';
	}

	/**
	 * KVS settings page
	 *
	 * @since 1.0.0
	 */
	public function kvs_settings_page() {
		global $kernel_video_sharing;

		if(!current_user_can('manage_options')) {
			wp_die('Unauthorized user');
		}

		include_once KVS_DIRPATH . 'admin/partials/settings-view.php';
	}


	/**
	 * KVS help page
	 *
	 * @since 1.0.0
	 */
	public function kvs_help_page() {
		global $kernel_video_sharing;

		if(!current_user_can('manage_options')) {
			wp_die('Unauthorized user');
		}

		include_once KVS_DIRPATH . 'admin/partials/help-view.php';
	}


	/**
	 * Manage admin KVS videos editor columns
	 *
	 * @since 1.0.0
	 * @param     array    Input columns
	 * @return    array    Filtered and extended columns
	 */
    public function kvs_editor_columns( $columns ) {
        // Insert Videos column anfter checkbox, before Title
        $keys = array_keys( $columns );
        $index = array_search( 'cb', $keys );
        $pos = false === $index ? count( $columns ) : $index + 1;

        $columns = array_merge(
            array_slice( $columns, 0, $pos ),
            arraY( 'video' => __( 'Video', 'kvs' ) ),
            array_slice( $columns, $pos )
        );
        
        $columns['last_modified'] = __( 'Last update', 'kvs' );
        $columns['shortcode'] = __( 'Shortcode', 'kvs' );
        
        return $columns;
    }
    
	/**
	 * Fill admin KVS videos editor custom columns
	 *
	 * @since 1.0.0
	 * @param     string    Input columns
	 * @param     int       Input columns
	 */
    public function kvs_editor_columns_data( $column, $post_id ) {
        if( $column == 'video' ) {
            echo '<a href="' . esc_attr(get_post_meta( $post_id, 'kvs-video-link', true )) . '" target="_blank">';
            echo '<img src="' . esc_attr(get_post_meta( $post_id, 'kvs-video-screenshot', true )) . '" height="80" style="max-width:100%;" />';
            echo '</a>';
        }
        if( $column == 'last_modified' ) {
            echo esc_html(get_the_modified_date( get_option('date_format') . ' ' . get_option('time_format'), $post_id ));
        }
        if( $column == 'shortcode' ) {
            echo '<span class="copy2cb" title="' . __( 'Click to copy to clipboard', 'kvs' ) . '">';
            echo esc_html(Kvs_SC::kvs_player_shortcode_constructor( get_post_meta( $post_id, 'kvs-video-id', true ) ));
            echo ' <i class="fas fa-fw fa-clone"></i>';
            echo '</span>';
        }
    }
    
    
	/**
	 * Add sorting to custom columns
	 *
	 * @since 1.0.0
	 * @param     string    Input columns
	 * @param     int       Input columns
	 */
    public function kvs_editor_columns_sort( $columns ) {
        $columns['last_modified'] = 'last_modified';
        
        return $columns;
    }

    
	/**
	 * Add "Manual Import" button to the KVS Videos list header
	 *
	 * @since 1.0.0
	 */
	public function kvs_add_import_videos_button() {
		global $post_type_object, $kernel_video_sharing;

		if( $post_type_object->name === 'kvs_video' &&
			 !empty( $kernel_video_sharing->reader->get_feed_url() ) ) {
?><script type="text/javascript">jQuery(document).ready( function($){
jQuery('.wrap h1').after('<button onclick="jQuery(\'#kvs-delete-form\').submit();" class="page-title-action kvs-import"><i class="fas fa-fw fa-sync"></i> <?php echo __( 'Check for deleted videos', 'kvs' );?></button>');
});</script>
<form method="post" id="kvs-delete-form" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="kvs_delete">
<?php
    wp_nonce_field( 'kvs_delete', 'kvs-nonce' );
?>
</form><script type="text/javascript">jQuery(document).ready( function($){
jQuery('.wrap h1').after('<button onclick="jQuery(\'#kvs-import-form\').submit();" class="page-title-action kvs-import"><i class="fas fa-fw fa-sync"></i> <?php echo __( 'Check for new videos', 'kvs' );?></button>');
});</script>
<form method="post" id="kvs-import-form" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="kvs_import">
<?php
    wp_nonce_field( 'kvs_import', 'kvs-nonce' );
?>
</form>
    <?php
		}
	}


	/**
	 * Check KVS feed URL for validity
	 *
	 * @since    1.0.0
	 * @param     string    Input submitted
	 * @return    string    Input filtered
	 */
	public function kvs_feed_url_check( $val ) {
		global $kernel_video_sharing;

		if( !empty( $val && $val !== $kernel_video_sharing->reader->get_feed_url() ) ) {
			$meta = $kernel_video_sharing->reader->update_feed_meta( $val );
			if( empty($meta) ) {
                delete_option( 'kvs_feed_last_id' );
                delete_option( 'kvs_feed_meta' );
                delete_option( 'kvs_feed_meta_update_time' );
                wp_clear_scheduled_hook( 'kvs_cron_update_hook' );
                wp_clear_scheduled_hook( 'kvs_cron_delete_hook' );
                
				$val = '';
				add_settings_error(
					'kvs_messages',
					'kvs_feed_url_error',
					__( 'Feed URL is invalid', 'kvs' ),
					'error'
	        	);
			}
		}
		return $val;
	}
    
	/**
	 * Check if cron update period was changed
	 *
	 * @since    1.0.0
	 * @param     string    Input submitted
	 * @return    string    Input filtered
	 */
	public function kvs_cron_update_period_change( $val ) {
        global $kernel_video_sharing;
        
		if( $val !== Kvs_Cron::get_cron_update_period() ) {
            wp_clear_scheduled_hook( 'kvs_cron_update_hook' );
            $kernel_video_sharing->logger->log(
                'Scheduled feed update period was changed from "' .
                Kvs_Cron::get_schedule_title( Kvs_Cron::get_cron_update_period() ) .
                '" to "' . Kvs_Cron::get_schedule_title( $val ) . '"', 
                'DEBUG'
            );
		}
		return $val;
	}
    
    
	/**
	 * Check if cron delete period was changed
	 *
	 * @since    1.0.0
	 * @param     string    Input submitted
	 * @return    string    Input filtered
	 */
	public function kvs_cron_delete_period_change( $val ) {
        global $kernel_video_sharing;
        
		if( $val !== Kvs_Cron::get_cron_delete_period() ) {
            wp_clear_scheduled_hook( 'kvs_cron_delete_hook' );
            $kernel_video_sharing->logger->log(
                'Scheduled feed update period was changed to: ' . Kvs_Cron::get_schedule_title( $val ), 
                'DEBUG'
            );
		}
		return $val;
	}
    
    
	/**
	 * Check if cron FULL update period was changed
	 *
	 * @since    1.0.0
	 * @param     string    Input submitted
	 * @return    string    Input filtered
	 */
	public function kvs_cron_full_period_change( $val ) {
        global $kernel_video_sharing;
        
		if( $val !== Kvs_Cron::get_cron_full_period() ) {
            wp_clear_scheduled_hook( 'kvs_cron_full_hook' );
            $kernel_video_sharing->logger->log(
                'Scheduled feed FULL update period was changed from "' .
                Kvs_Cron::get_schedule_title( Kvs_Cron::get_cron_full_period() ) .
                '" to "' . Kvs_Cron::get_schedule_title( $val ) . '"', 
                'DEBUG'
            );
		}
		return $val;
	}
    
    
	/**
	 * Update KVS feed meta
	 *
	 * @since 1.0.0
	 */
    public function kvs_update_meta() {
        global $kernel_video_sharing;
        
        $meta = $kernel_video_sharing->reader->update_feed_meta();
        if( !empty($meta) ) {
            set_transient( 'kvs-meta-notice-success', true, 5 );
        }
        $link = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings&section=rules' );
        wp_redirect( $_SERVER["HTTP_REFERER"] ?? $link, 302, 'WordPress' );
        exit;
    }

	/**
	 * Do import action: add new videos and update old ones
	 *
	 * @since 1.0.0
	 * @param     bool    $silent         Show admin notice after and return headers
	 * @param     bool    $full_import    Ignore last imported Video ID and do full import
	 */
    public function kvs_do_import( $silent = false, $full_import = false ) {
		global $kernel_video_sharing, $wpdb;
        
        $debug = 'Videos ';
        if( $full_import ) {
            $debug .= 'FULL ';
        }
        $debug .= 'import/update started';
        if( $silent ) {
            $debug .= ' in silent mode';
        }
        $kernel_video_sharing->logger->log( $debug, 'DEBUG' );
        
        $taxonomy_category = get_option( 'kvs_taxonomy_category' );
        $taxonomy_tag      = get_option( 'kvs_taxonomy_tag' );
        $taxonomy_model    = get_option( 'kvs_taxonomy_model' );
        $taxonomy_source   = get_option( 'kvs_taxonomy_source' );

        $custom_fields = [];
        for ($i = 1; $i <= 3; $i++) {
            if (!empty(get_option( "kvs_custom{$i}_name" )) && !empty(get_option( "kvs_custom{$i}_value" ))) {
                $custom_fields[get_option( "kvs_custom{$i}_name" )] = get_option( "kvs_custom{$i}_value" );
            }
        }
        
        $kernel_video_sharing->logger->log( 
            'Categories taxonomy: ' . ($taxonomy_category ?: '-') .
            '; Tags taxonomy: ' . ($taxonomy_tag ?: '-') .
            '; Models taxonomy: ' . ($taxonomy_model ?: '-') .
            '; Sources taxonomy: ' . ($taxonomy_source ?: '-'),
            'DEBUG' );

        $filter = null;
        $taxonomy_filter_by = get_option( 'kvs_video_filter_by' );
        switch ($taxonomy_filter_by) {
            case 'categories':
                $filter = get_option( 'kvs_video_filter_category' );
                break;
            case 'content_sources':
                $filter = get_option( 'kvs_video_filter_source' );
                break;
            default:
                $taxonomy_filter_by = '';
        }
        if( !empty( $taxonomy_filter_by ) ) {
            $kernel_video_sharing->logger->log(
                'Filter applied on ' . $taxonomy_filter_by . ': ' . implode(', ', $filter), 
                'DEBUG'
            );
        }
        
        $format = get_option( 'kvs_video_screenshot' );
        $locale = get_option( 'kvs_video_locale' );
        $kernel_video_sharing->logger->log( 'Feed locale set: ' . $locale, 'DEBUG' );
        
        $limit = 0;
        if( !$full_import ) {
            $limit = (int)get_option( 'kvs_update_limit' );
            $kernel_video_sharing->logger->log( 'Batch limit set: ' . $limit, 'DEBUG' );
        }
        
        $last_id = 0;
        if( !$full_import ) {
            $last_id = (int)get_option( 'kvs_feed_last_id' );
            $kernel_video_sharing->logger->log( 'Last video ID: ' . $last_id, 'DEBUG' );
        }
        
        $import_featured_image = !empty( get_option( 'kvs_post_import_featured_image' ) );
        
        $data = $kernel_video_sharing->reader->get_feed( $last_id+1, $limit, $format, $locale );
        if( !empty($data) ) {
            // Index videos in the feed
            $ids = array();
            foreach($data as $n=>$row) {
                $last_id = $row['id'];
                
                if( !empty( $taxonomy_filter_by ) ) {
                    if( $taxonomy_filter_by == 'content_source' ) {
                        if( !in_array( $row['content_source'], $filter ) ) {
                            unset( $data[$n] );
                            continue;
                        }
                    }
                    if( $taxonomy_filter_by == 'categories' ) {
                        if( !array_intersect( $row['categories'], $filter ) ) {
                            unset( $data[$n] );
                            continue;
                        }
                    }
                }
                
                $ids[ (int)$row['id'] ] = $n;
            }
            if( !empty( $taxonomy_filter_by ) ) {
                $kernel_video_sharing->logger->log( count( $ids ) . ' videos to process after filtering', 'DEBUG' );
            }
            
            if( !empty( $ids ) ) {
                // Find videos that are already in the WP DB
                // 
                // Warning: 
                // We do not check post status here to prevent post recreation 
                // in case of corresponding post was already trashed before
                $posts = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta
                    WHERE meta_key = 'kvs-video-id' 
                    AND meta_value IN (" . implode( ',', array_keys($ids) ) . ")", ARRAY_N);
                $kernel_video_sharing->logger->log( count( $posts ) . ' corresponding posts found', 'DEBUG' );

                // Mark videos from the feed to UPDATE
                // Rest records will be created as a new posts
                foreach($posts as $post) {
                    $indx = $ids[(int)($post[1])];
                    $data[$indx]['wp_post_id'] = (int)($post[0]);
                }

                // Process videos (inser and update)
                foreach($data as $row) {
                    // Filling base post properties and custom fields
                    $video = array(
                        'post_type'     => get_option( 'kvs_post_type' ) ?: 'kvs_video', 
                        'post_title'    => $row['title'],
                        'post_content'  => $row['description'],
                        'post_date'     => get_option( 'kvs_post_date' ) == 'now' ? date('Y-m-d H:i:s') : $row['post_date'],
                        'post_date_gmt' => get_gmt_from_date( get_option( 'kvs_post_date' ) == 'now' ? date('Y-m-d H:i:s') : $row['post_date'] ),
                        'meta_input'    => array(
                            'kvs-video-id' => $row['id'],
                            'kvs-video-rating' => $row['rating'],
                            'kvs-video-rating-percent' => $row['rating_percent'],
                            'kvs-video-votes' => $row['votes'],
                            'kvs-video-popularity' => $row['popularity'],
                            'kvs-video-link' => $row['link'],
                            'kvs-video-file-url' => $row['file_url'], // ToDo: do not import in future
                            'kvs-video-duration' => $row['duration'],
                        ),
                    );

                    $replacements = array(
                        '{%id%}'             => $row['id'],
                        '{%title%}'          => $row['title'],
                        '{%description%}'    => $row['description'],
                        '{%date%}'           => get_option( 'kvs_post_date' ) == 'now' ? date('Y-m-d H:i:s') : $row['post_date'],
                        '{%popularity%}'     => $row['popularity'],
                        '{%rating%}'         => $row['rating'],
                        '{%rating_percent%}' => $row['rating_percent'],
                        '{%votes%}'          => $row['votes'],
                        '{%duration%}'       => $row['duration'],
                        '{%link%}'           => $row['link'],
                    );
                    $contentTemplate = get_option( 'kvs_post_body_template' ) ?: '{%description%}';
                    $video['post_content'] = strtr( $contentTemplate, $replacements);

                    foreach ($custom_fields as $custom_field_name => $custom_field_value) {
                        $video['meta_input'][$custom_field_name] = strtr($custom_field_value, $replacements);
                    }
                    
                    if( !empty( $row['screenshot_main'] ) && 
                        !empty( $row['screenshots'][ $row['screenshot_main']-1 ] ) ) {
                        $video['meta_input']['kvs-video-screenshot'] = $row['screenshots'][ $row['screenshot_main']-1 ];
                    } else {
                        $video['meta_input']['kvs-video-screenshot'] = reset( $row['screenshots'] );
                    }

                    $post_id = null;
                    $featured_image_id = null;
                    // Checking if we need to update post or create a new one
                    if( !empty( $row['wp_post_id'] ) ) {
                        $video['ID'] = $row['wp_post_id'];
                        $post_id = wp_update_post( $video, $wp_error = true );
                        if( !is_wp_error( $post_id ) ) {
                            $featured_image_id = get_post_thumbnail_id( $post_id );
                            
                            $kernel_video_sharing->logger->log( 
                                'Updating post #' . $row['wp_post_id'] . 
                                ' for video #' . $row['id'],
                                'DEBUG'
                            );
                        } else {
                            $kernel_video_sharing->logger->log(
                                'Error updating post #' . $row['wp_post_id'] . 
                                ' for video #' . $row['id'] . ': ' . 
                                $post_id->get_error_message(),
                                'ERROR'
                            );
                        }
                    } else {
                        $video['post_status'] = get_option( 'kvs_post_status' ) == 'draft' ? 'draft' : (get_option( 'kvs_post_status' ) == 'pending' ? 'pending' : 'publish');
                        $video['post_author'] = 1;  // ToDo: Try to find corresponding WP user by $row['user']
                        $post_id = wp_insert_post( $video, $wp_error = true );
                        if( !is_wp_error( $post_id ) ) {
                            $kernel_video_sharing->logger->log( 
                                'New post #' . $post_id . 
                                ' added for video #' . $row['id'],
                                'DEBUG'
                            );
                        } else {
                            $kernel_video_sharing->logger->log(
                                'Error inserting new post for video #' . $row['id'] . ': ' . 
                                $post_id->get_error_message(),
                                'ERROR'
                            );
                        }
                    }

                    // Update taxonomies if post was successfully created or updated
                    if( !is_wp_error( $post_id ) && !empty( $post_id ) ) {
                        $debug = array();
                        
                        if( $import_featured_image ) {
                            if( $this->kvs_generate_featured_image( 
                                $post_id,
                                $featured_image_id,
                                $video['post_title'],
                                $video['meta_input']['kvs-video-screenshot']
                            ) ) {
                                $debug[] = 'Featured image imported from ' . 
                                           $video['meta_input']['kvs-video-screenshot'];
                            } else {
                                $debug[] = 'Error importing featured image from ' . 
                                           $video['meta_input']['kvs-video-screenshot'];
                            }
                        }
                        
                        if( !empty( $taxonomy_category ) ) {
                            $row['categories'] = isSet( $row['categories'] ) ? $row['categories'] : null;
                            wp_set_object_terms( $post_id, $row['categories'], $taxonomy_category );
                            $debug[] = 'Catogories: ' . implode(', ', $row['categories']);
                        }
                        if( !empty( $taxonomy_tag ) ) {
                            $row['tags'] = isSet( $row['tags'] ) ? $row['tags'] : null;
                            wp_set_object_terms( $post_id, $row['tags'], $taxonomy_tag );
                            $debug[] = 'Tags: ' . implode(', ', $row['tags']);
                        }
                        if( !empty( $taxonomy_model ) ) {
                            $row['models'] = isSet( $row['models'] ) ? $row['models'] : null;
                            wp_set_object_terms( $post_id, $row['models'], $taxonomy_model );
                            $debug[] = 'Models: ' . implode(', ', $row['models']);
                        }
                        if( !empty( $taxonomy_source ) ) {
                            $row['content_source'] = isSet( $row['content_source'] ) ? $row['content_source'] : null;
                            wp_set_object_terms( $post_id, $row['content_source'], $taxonomy_source );
                            $debug[] = 'Source: ' . $row['content_source'];
                        }
                        if( !empty( $debug ) ) {
                            $kernel_video_sharing->logger->log(
                                'Taxonomies set for the post #' . $post_id . ': ' . 
                                implode( '; ', $debug ),
                                'DEBUG'
                            );
                        }
                    }

                }
                update_option( 'kvs_feed_last_update', count($posts) );
                update_option( 'kvs_feed_last_insert', count($data)-count($posts) );
            } else {
                if(!$silent) {
                    set_transient( 'kvs-import-notice-empty', true, 5 );
                }
                
                update_option( 'kvs_feed_last_update', 0 );
                update_option( 'kvs_feed_last_insert', 0 );
            }
            
            update_option( 'kvs_feed_last_id', $last_id );
            
            if(!$silent) {
                set_transient( 'kvs-import-notice-success', true, 5 );
            }
        } else {
            if(!$silent) {
                set_transient( 'kvs-import-notice-empty', true, 5 );
            }
            
            update_option( 'kvs_feed_last_update', 0 );
            update_option( 'kvs_feed_last_insert', 0 );
        }
        
        update_option( 'kvs_feed_last_run', time() );
        $kernel_video_sharing->logger->log( 'Feed processing finished', 'DEBUG' );
        
        if(!$silent) {
            $post_type_object = get_post_type_object( 'kvs_video' );
            $link = admin_url( $post_type_object->_edit_link );
            wp_redirect( $_SERVER["HTTP_REFERER"] ?? $link, 302, 'WordPress' );
            exit;
        }
    }
    
	/**
	 * Save screenshot image locally and attach to the post
	 *
	 * @since 1.0.3
	 * @param     int       $post_id       Post ID to attach featured image
	 * @param     int       $attach_id     Attachment ID for featured image (on post updating)
	 * @param     string    $title         featured image Title
	 * @param     string    $image_url     Screenshot image location
	 * @param     string    $file_name     File name for featured image
	 */
    public function kvs_generate_featured_image( $post_id, $attach_id, $title, $image_url, $file_name = null  ){
		global $kernel_video_sharing;
        
        if( empty( $image_url ) ) {
    		$kernel_video_sharing->logger->log( 'Empty screenshot URL provided', 'DEBUG' );
            return;
        }
        
        $image_data = wp_remote_retrieve_body( wp_remote_get( $image_url ) );
        if( empty( $image_data ) ) {
            $image_data = file_get_contents( $image_url );
        }
        if( empty( $image_data ) ) {
    		$kernel_video_sharing->logger->log( 'Empty screenshot file content', 'DEBUG' );
            return;
        }
        
        $upload_dir = wp_upload_dir();
        if( empty( $file_name ) ) {
            $file_name = $post_id . '-screenshot-' . basename( $image_url );
        }
        
        if( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $file_name;
        } else {
            $file = $upload_dir['basedir'] . '/' . $file_name;
        }
        $res = file_put_contents( $file, $image_data );
        if( empty( $res ) ) {
    		$kernel_video_sharing->logger->log(
                'Error writing featured image file contents to: ' . $file,
                'DEBUG'
            );
            return;
        }

        $wp_filetype = wp_check_filetype( $file_name, null );
        if( empty( $wp_filetype['type'] ) ) {
    		$kernel_video_sharing->logger->log(
                'Unsupported featured image file type',
                'DEBUG'
            );
            return;
        }
        
        if( empty( $title ) ) {
            $title = sanitize_file_name( $file_name );
        }

        if( empty( $attach_id ) ) {
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => $title,
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        }
        
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        
        return set_post_thumbnail( $post_id, $attach_id );
    }

	/**
	 * Do import action: delete old videos
	 *
	 * @since 1.0.0
	 * @param     bool    $silent         Show admin notice after and return headers
	 * @param     bool    $full_import    Ignore last imported Video ID and do full import
	 */
    public function kvs_do_delete( $silent = false, $full_import = false ) {
		global $kernel_video_sharing, $wpdb;
        
        update_option( 'kvs_feed_last_delete', 0 );
        $days = $full_import ? 0 : 30;
        
        $deleted = $kernel_video_sharing->reader->get_deleted_ids( $days );
        if( !empty( $deleted ) ) {
            // Find videos in the WP DB by KVS IDs
            $posts = $wpdb->get_results("SELECT pm.post_id, pm.meta_value 
                FROM $wpdb->postmeta pm
                    INNER JOIN $wpdb->posts p ON pm.post_id = p.ID
                WHERE (p.post_status = 'publish' or p.post_status = 'future')
                AND pm.meta_key = 'kvs-video-id' 
                AND pm.meta_value IN (" . implode( ',', $deleted ) . ")", ARRAY_N);

            if( !empty($posts) ) {
                $del_log = array();
                foreach($posts as $post) {
                    $del_post = wp_delete_post( $post[0], $force_delete = false );
                    if( $del_post ) {
                        $del_log[] = $del_post->ID;
                    }
                }
                $kernel_video_sharing->logger->log(
                    'Deleted ' . count( $del_log ) . ' videos: ' . implode(', ', $del_log), 
                    'DEBUG'
                );
                update_option( 'kvs_feed_last_delete', count( $posts ) );
            }
            update_option( 'kvs_feed_last_update', 0 );
            update_option( 'kvs_feed_last_insert', 0 );
            if ( !$silent ) {
                set_transient( 'kvs-import-notice-success', true, 5 );
            }
        }

        if(!$silent) {
            $post_type_object = get_post_type_object( 'kvs_video' );
            $link = admin_url( $post_type_object->_edit_link );
            wp_redirect( $_SERVER["HTTP_REFERER"] ?? $link, 302, 'WordPress' );
            exit;
        }
    }

	/**
	 * Full import action
	 *
	 * @since 1.0.0
	 */
    public function kvs_do_full_import() {
        global $kernel_video_sharing;
        
        $name = wp_get_current_user()->user_login;
		$kernel_video_sharing->logger->log( 'Manual FULL import started by ' . $name, 'DEBUG' );
        $this->kvs_do_delete( $silent = true, $full_import = true );
        $this->kvs_do_import( $silent = false, $full_import = true );
    }

	/**
	 * Clear log action
	 *
	 * @since 1.0.0
	 */
    public function kvs_do_clear_log() {
        global $kernel_video_sharing;
        
        $kernel_video_sharing->logger->clear_log_content();
        
        $link = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings&section=advanced' );
        wp_redirect( $_SERVER["HTTP_REFERER"] ?? $link, 302, 'WordPress' );
        exit;
    }
    

	/**
	 * Videos import notice
	 *
	 * @since 1.0.0
	 */
	public function kvs_admin_notice_import() {
		if( get_transient( 'kvs-import-notice-success' ) ) {
			echo '<div class="updated notice is-dismissible">';
            $updated = (int)get_option( 'kvs_feed_last_update', 0 );
            $inserted = (int)get_option( 'kvs_feed_last_insert', 0 );
            $deleted = (int)get_option( 'kvs_feed_last_delete', 0 );
            echo '<p>';
            if( $updated + $inserted + $deleted > 0 ) {
                $actions = array();
                if( $updated ) {
                    $actions[] = sprintf( __( '%d videos updated', 'kvs' ), $updated );
                }
                if( $inserted ) {
                    $actions[] = sprintf( __( '%d new videos added', 'kvs' ), $inserted );
                }
                if( $deleted ) {
                    $actions[] = sprintf( __( '%d videos deleted', 'kvs' ), $deleted );
                }
                echo esc_html(implode(', ', $actions));
                echo '.';
            } else {
                _e( 'Videos successfuly updated.', 'kvs' );
            }
            echo '</p>';
			echo '<p><a href="' . esc_attr((get_option( 'kvs_post_type' ) == 'attachment' ? admin_url( 'upload.php' ) : (get_option( 'kvs_post_type' ) == 'post' ? admin_url( 'edit.php' ) : admin_url( 'edit.php?post_type=kvs_video' )))) . '">'.__( 'Manage videos', 'kvs' ) . '</a></p>';
			echo '</div>';
			delete_transient( 'kvs-import-notice-success' );
		}
		if( get_transient( 'kvs-import-notice-empty' ) ) {
            if( (int)get_option( 'kvs_feed_last_id' ) > 0 ) {
    			echo '<div class="notice is-dismissible">';
    			echo '<p>' . __( 'No new videos found in KVS feed.', 'kvs' ) . '</p>';
            	echo '</div>';
            } else {
    			echo '<div class="notice notice-warning is-dismissible">';
                echo '<p>' . __( 'No videos found in KVS feed.', 'kvs' ) . '</p>';
        		echo '<p><a href="' . esc_attr(admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings' )) . '">' . __( ' Please check your KVS feed settings', 'kvs' ) . '</a></p>';
            	echo '</div>';
            }
            delete_transient( 'kvs-import-notice-empty' );
		}
		if( get_transient( 'kvs-meta-notice-success' ) ) {
			echo '<div class="updated notice is-dismissible">';
			echo '<p>' . __( 'KVS feed metadata successfully updated.', 'kvs' ) . '</p>';
			echo '</div>';
			delete_transient( 'kvs-meta-notice-success' );
		}
	}


}
