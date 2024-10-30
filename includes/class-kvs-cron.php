<?php

/**
 * KVS cron tasks class
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/includes
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs_Cron {

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 * @param      string    $feed_url       KVS feed full URL.
	 */
	public function __construct( $feed_url = null ) {
        
	}
    
	/**
	 * Retrieve cron run period for inserting and updating videos
	 *
	 * @since     1.0.0
	 * @return    string    schedule interval name.
	 */
	public static function get_cron_update_period() {
		return get_option( 'kvs_update_period' ) ?: 'manual';
	}
    
	/**
	 * Retrieve cron run period for inserting and updating videos
	 *
	 * @since     1.0.0
	 * @return    string    schedule interval name.
	 */
	public static function get_cron_delete_period() {
		return get_option( 'kvs_delete_period' ) ?: 'manual';
	}
    
	/**
	 * Retrieve cron run period for full videos update
	 *
	 * @since     1.0.0
	 * @return    string    schedule interval name.
	 */
	public static function get_cron_full_period() {
		return get_option( 'kvs_full_period' ) ?: 'manual';
	}
    
	/**
	 * Adding cron schedules
	 *
	 * @since 1.0.0
	 * @param   array  $schedules  Cron Schedules.
     * #return  array              Filtered and extended schedules array
	 */
	public static function kvs_cron_schedules( $schedules = array() ) {
		if ( ! isset( $schedules['kvs_1m'] ) ) {
			$schedules['kvs_1m'] = array(
				'interval' => 1 * 60,
				'display'  => __( 'Every minute', 'kvs' ),
			);
		}
		if ( ! isset( $schedules['kvs_15m'] ) ) {
			$schedules['kvs_15m'] = array(
				'interval' => 15 * 60,
				'display'  => __( 'Every 15 minutes', 'kvs' ),
			);
		}
		if ( ! isset( $schedules['kvs_1h'] ) ) {
			$schedules['kvs_1h'] = array(
				'interval' => 60 * 60,
				'display'  => __( 'Every hour', 'kvs' ),
			);
		}
		if ( ! isset( $schedules['kvs_6h'] ) ) {
			$schedules['kvs_6h'] = array(
				'interval' => 6 * 60 * 60,
				'display'  => __( 'Every 6 hours', 'kvs' ),
			);
		}
		if ( ! isset( $schedules['kvs_1d'] ) ) {
			$schedules['kvs_1d'] = array(
				'interval' => 24 * 60 * 60,
				'display'  => __( 'Once a day', 'kvs' ),
			);
		}
		if ( ! isset( $schedules['kvs_7d'] ) ) {
			$schedules['kvs_7d'] = array(
				'interval' => 7 * 24 * 60 * 60,
				'display'  => __( 'Once a week', 'kvs' ),
			);
		}
		return $schedules;
	}
    
    
	/**
	 * Return schedule title bu slug
	 *
	 * @since    1.0.0
	 * @param    string    Schedule slug.
     * #return   string    Schedule name to display.
	 */
	public static function get_schedule_title( $slug ) {
		$schedules = self::kvs_cron_schedules( array() );
        if( !empty( $schedules[$slug] ) ) {
            return $schedules[$slug]['display'];
        }
        return '';
	}
    
    
	/**
	 * Run scheduled actions for updating and adding new videos
	 *
	 * @since 1.0.0
	 */
    public function kvs_cron_update_exec() {
        global $kernel_video_sharing_admin;
        
        $kernel_video_sharing_admin->kvs_do_import( $silent = true, $full_import = false );
    }
    
	/**
	 * Run scheduled actions for full videos update
	 *
	 * @since 1.0.0
	 */
    public function kvs_cron_full_exec() {
        global $kernel_video_sharing_admin;
        
        $kernel_video_sharing_admin->kvs_do_import( $silent = true, $full_import = true );
    }
    
	/**
	 * Run scheduled actions for deleting outdated videos
	 *
	 * @since 1.0.0
	 */
    public function kvs_cron_delete_exec() {
        global $kernel_video_sharing_admin;
        
        $kernel_video_sharing_admin->kvs_do_delete( $silent = true, $full_import = false );
    }
    
}
