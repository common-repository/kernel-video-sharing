<?php

/**
 * KVS shortcodes class
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/includes
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs_SC {

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 * @param      string    $feed_url       KVS feed full URL.
	 */
	public function __construct( $feed_url = null ) {
        
	}

    public function kvs_player_shortcode( $atts ){
        global $wpdb;
        
        extract(shortcode_atts(array(
            'id' => 0,
        ), $atts) );
        
        $kvs_id = (int)$atts['id'];
         $post_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta pm
INNER JOIN $wpdb->posts p ON pm.post_id=p.ID
WHERE p.post_status='publish' AND pm.meta_key='kvs-video-id' AND pm.meta_value='$kvs_id' LIMIT 1", 0, 0);
        
        if ( empty($post_id) ) {
            return '';
        }
        
        $file_url = get_post_meta( $post_id, 'kvs-video-file-url', true );

        $kvs_path = get_option( 'kvs_library_path' );
        if ($kvs_path) {
            $return_string = '<div class="kvs-player" style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%;"><iframe style="position: absolute; width: 100%; height: 100%" src="' . rtrim($kvs_path, '/ ') . '/embed/' . $kvs_id . '" frameborder="0" allowfullscreen></iframe></div>';
        } else {
            $return_string = '[CRITICAL] No KVS URL is defined';
        }
        
        return $return_string;
    }
    
    public static function kvs_player_shortcode_constructor( $video_id = 0 ){
        return '[kvs_player id='.$video_id.']';
    }
    
}
