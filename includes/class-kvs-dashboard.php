<?php

/**
 * KVS dashboard widget class
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/includes
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs_DB {

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 * @param      string    $feed_url       KVS feed full URL.
	 */
	public function __construct() {
        
	}

	/**
	 * Primary WordPress dashboard widget for KVS plugin
	 *
	 * @since    1.0.0
	 */
    public function kvs_dashboard_widget(){
        global $kernel_video_sharing;
        
        if ( empty($kernel_video_sharing->reader->get_feed_url()) ) {
            $this->kvs_dashboard_widget_title_icon( 'red' );
            
            $link = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings' );
            echo '<p align="center">';
            echo '<button class="button button-primary button-large" onclick="document.location=\'' . esc_js($link) . '\'">';
            echo __( 'Configure settings to activate', 'kvs' );
            echo '</button>';
            echo '</p>';
            
            return;
        }
        
        $this->kvs_dashboard_widget_title_icon( 'green' );
        
        echo '<div class="main">';
        
        echo '<ul><li><i class="fas fa-film"></i> <a href="edit.php?post_type=kvs_video">';
        $count = wp_count_posts( 'kvs_video' )->publish;
        esc_html(printf( __( '%d videos in the library', 'my-text-domain' ), $count ));
        echo '</a></li></ul>';
        
        $cron_period = Kvs_Cron::get_cron_update_period();
        $cron_schedules = Kvs_Cron::kvs_cron_schedules();
        if ( empty($cron_schedules[$cron_period]) ) {
            $cron_period = 'manual';
        }
        
        $last_run = (int)get_option( 'kvs_feed_last_run', 0 );
        $next_run = wp_next_scheduled( 'kvs_cron_update_hook' );
        
        if ( $cron_period === 'manual' || 
                ( $last_run > 0 && $last_run < time()-86400 ) ) {
            echo '<div class="content-box-yellow">';
            
			echo '<p>';
            _e( 'Automatic video synchronization is turned off.', 'kvs' );
			echo '</p>';
            
            $link = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings' );
			echo '<p>';
            echo '<button class="button button-secondary button-small" onclick="document.location=\'' . esc_js($link) . '\'">';
            echo __( 'Check settings', 'kvs' );
            echo '</button>';
            echo '</p>';
            
			echo '</div>';
        } else {
            if ( $last_run ) {
                $time_passed = human_readable_duration( gmdate( 'H:i:s', time() - $last_run ) );
                echo '<p>' . esc_html(sprintf( __( 'Last execution was %s ago.', 'kvs' ), $time_passed )) . '</p>';
                echo '<p>' . esc_html(sprintf( __( 'Synchronization is scheduled to run %s.', 'kvs' ), $cron_schedules[$cron_period]['display'] )) . '</p>';
            } else {
                echo '<p>' . esc_html(__( 'Automatic video synchronization never happened.', 'kvs' )) . '</p>';
            }
            
            $next_run_p = $next_run - time();
            if ( $next_run_p<=60 ) {
                $next_run_r = __( 'less than a minute', 'kvs' );
            } elseif ( $next_run_p < 60*60 ) {
                $next_run_r = human_readable_duration( gmdate( 'i:s', $next_run_p ) );
            } else {
                $next_run_r = human_readable_duration( gmdate( 'H:i:s', $next_run_p ) );
            }
            echo '<p>' . esc_html(sprintf( __( 'Next run in %s.', 'kvs' ), $next_run_r )) . '</p>';
        }
        
        echo '</div>';
    }

	/**
	 * Adding connection status icon to the WordPress dashboard widget title
	 *
	 * @param string $color
	 *
	 * @since    1.0.0
	 */
    private function kvs_dashboard_widget_title_icon( $color = 'green' ) {
?><script>
    var kvs_state = document.createElement("I");
    kvs_state.className = "fa fa-fw fa-circle <?php echo esc_attr($color);?>";
    document.getElementById('kvs_dashboard_widget').children[0].children[0].appendChild(kvs_state);
</script><?php
    }
}
