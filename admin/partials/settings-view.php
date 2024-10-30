<?php

/**
 * KVS Plugin settings page view
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin/partials
 */

$section = 'feed';
if( !empty( $_GET['section'] ) ) {
    $section = preg_replace( '/[^a-z]+/', '', $_GET['section'] );
    if( !file_exists( KVS_DIRPATH . 'admin/partials/settings/' . $section . '.php' ) ) {
        $section = 'feed';
    }
}

include KVS_DIRPATH . 'admin/partials/settings/_header.php';

include KVS_DIRPATH . 'admin/partials/settings/' . $section . '.php';
?>
