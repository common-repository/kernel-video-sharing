<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/includes
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs_Deactivator {

	public static function deactivate() {
		delete_transient( 'kvs-admin-notice' );
        wp_clear_scheduled_hook( 'kvs_cron_update_hook' );
        wp_clear_scheduled_hook( 'kvs_cron_delete_hook' );
	}

}
