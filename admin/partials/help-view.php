<?php

/**
 * KVS Plugin help page view
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin/partials
 */

$section = 'basic';
if( !empty( $_GET['section'] ) ) {
    $section = preg_replace( '/[^a-z]+/', '', $_GET['section'] );
    if( !file_exists( KVS_DIRPATH . 'admin/partials/help/' . $section . '.php' ) ) {
        $section = 'basic';
    }
}

include KVS_DIRPATH . 'admin/partials/help/header.php';

include KVS_DIRPATH . 'admin/partials/help/' . $section . '.php';
?>
