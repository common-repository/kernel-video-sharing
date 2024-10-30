<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/includes
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs_Activator {

	public static function activate() {
		set_transient( 'kvs-admin-notice', true, 5 );
	}

}
