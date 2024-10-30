<?php

/**
 * KVS & Elementor integration: adding KVS video properties as a dynamic tag support
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.2
 *
 * @package    Kvs
 * @subpackage Kvs/includes/elementor
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */

use Elementor\Controls_Manager;

class KVS_Elementor_Dynamic_Tags_Screenshot extends \Elementor\Core\DynamicTags\Tag {
    
    public function get_name() {
        return 'KVS_Elementor_Dynamic_Tags_Screenshot';
    }

    public function get_categories() {
        return array( 'image' );
    }

    public function get_group() {
        return array( 'post' );
    }

    public function get_title() {
        return __( 'KVS video screenshot', 'kvs' );
    }

    public function get_content( array $options = [] ) {
        $url = get_post_meta( get_the_ID(), 'kvs-video-screenshot', true );

        return array(
            'url' => $url,
        );
	}
}

class KVS_Elementor_Dynamic_Tags_Link extends \Elementor\Core\DynamicTags\Tag {
    
    public function get_name() {
        return 'KVS_Elementor_Dynamic_Tags_Link';
    }

    public function get_categories() {
        return array( 'url' );
    }

    public function get_group() {
        return array( 'post' );
    }

    public function get_title() {
        return __( 'KVS video link', 'kvs' );
    }

    public function get_content( array $options = [] ) {
        return get_post_meta( get_the_ID(), 'kvs-video-link', true );
	}
}

class KVS_Elementor_Dynamic_Tags_VideoURL extends \Elementor\Core\DynamicTags\Tag {
    
    public function get_name() {
        return 'KVS_Elementor_Dynamic_Tags_VideoURL';
    }

    public function get_categories() {
        return array( 'url' );
    }

    public function get_group() {
        return array( 'post' );
    }

    public function get_title() {
        return __( 'KVS video file URL', 'kvs' );
    }

    public function get_content( array $options = [] ) {
        return get_post_meta( get_the_ID(), 'kvs-video-file-url', true );
	}
}