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
class KVS_Elementor_Dynamic_Tags_Numeric extends \Elementor\Core\DynamicTags\Tag {
    
    public function get_name() {
        return 'KVS_Elementor_Dynamic_Tags_Numeric';
    }

    public function get_categories() {
        return array( 'number', 'text' );
    }

    public function get_group() {
        return array( 'post' );
    }

    public function get_title() {
        return __( 'KVS video property', 'kvs' );
    }

    protected function _register_controls() {
        $this->add_control(
            'property',
            array(
                'label' => __( 'Property', 'text-domain' ),
                'type' => 'select',
                'default' => 'kvs-video-id',
				'options' => array(
					'kvs-video-id' => __( 'Video ID', 'kvs' ),
					'kvs-video-rating' => __( 'Video rating (number)', 'kvs' ),
					'kvs-video-rating-percent' => __( 'Video rating (%)', 'kvs' ),
					'kvs-video-votes' => __( 'Video votes amount', 'kvs' ),
					'kvs-video-popularity' => __( 'Video popularity (views)', 'kvs' ),
					'kvs-video-duration' => __( 'Video duration (seconds)', 'kvs' ),
					'kvs-video-duration-time' => __( 'Video duration (h:m:s)', 'kvs' ),
				)
            )
        );
    }

    public function render() {
        $property_selected = $this->get_settings( 'property' );
        if( empty( $property_selected ) ) {
            return;
        }
        
        $meta_name = $property_selected;
        switch ($property_selected) {
            case 'kvs-video-duration-time':
                $meta_name = 'kvs-video-duration'; break;
        }

        $value = get_post_meta( get_the_ID(), $meta_name, true );
        
        switch ($property_selected) {
            case 'kvs-video-rating-percent':
                $value = str_replace('%', '', $value); break;
            case 'kvs-video-duration-time':
                $value = gmdate("H:i:s", $value); break;
        }

        echo esc_html($value);
    }
}