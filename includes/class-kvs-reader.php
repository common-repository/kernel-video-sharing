<?php

/**
 * KVS feed reader class
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/includes
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs_Reader {

	/**
	 * Feed URL that will be called
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    
	 */
	protected $url;

	/**
	 * Feed meta lifetime in seconds
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      int    
	 */
	const META_LIFETIME = 86400; // 1 day

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 * @param      string    $feed_url       KVS feed full URL.
	 */
	public function __construct( $feed_url = null ) {
		$this->url = $feed_url;
        if( empty($this->url) ) {
            $this->url = get_option( 'kvs_feed_url', '' );
        }
	}

	/**
	 * Retrieve the KVS feed URL.
	 *
	 * @since     1.0.0
	 * @return    string    KVS feed URL.
	 */
	public function get_feed_url() {
		return $this->url;
	}
    
    
	/**
	 * Retrieve the params array for CURL requests.
	 *
	 * @since     1.0.0
	 * @return    array    CURL params.
	 */
	public function get_curl_params() {
		return array(
            'user-agent' => 'KVS WordPress plugin v' . KVS_VERSION,
            'stream' => false,
            'filename' => '',
            'decompress' => true,
        );
	}
    
    
	/**
	 * Retrieve the KVS feed meatadata from WP options storage.
	 *
	 * @since     1.0.0
	 * @return    array                  KVS feed meta array.
	 */
	public function get_feed_meta() {
        global $kernel_video_sharing;
        
        $meta_arr = get_option('kvs_feed_meta');
        $meta_update = get_option('kvs_feed_meta_update_time');
        
        if( empty($meta_arr) 
                || $meta_update < (time() - Self::META_LIFETIME) ) {
            if( empty($meta_arr) ) {
                $kernel_video_sharing->logger->log(
                    'Feed meta is empty in local sorage!',
                    'DEBUG'
                );
            } elseif( $meta_update < (time() - Self::META_LIFETIME) ) {
                $kernel_video_sharing->logger->log( 
                    'Feed meta is outdated, last update: ' . date( 'd.m.Y H:i:s', $meta_update ),
                    'DEBUG'
                );
            }
            
            $meta_arr = $this->update_feed_meta();
        }
        
        if( !empty($meta_arr['screenshots']) ) {
            return $meta_arr;
        }

		return array();
	}
    
    
	/**
	 * Retrieve the KVS feed meatadata.
	 *
	 * @since     1.0.0
	 * @param     string    $feed_url    KVS feed full URL.
	 * @return    array                  KVS feed meta array.
	 */
	public function update_feed_meta( $feed_url = null ) {
        global $kernel_video_sharing;
        
		if( empty($feed_url) ) {
			$feed_url = $this->url;
		}
		if( empty($feed_url) ) {
			return array();
		}

		$feed_url .= (strpos($feed_url, '?') === false ? '?' : '&');
        try {
            $feed_url .= 'action=get_meta';
            $feed_url .= '&meta[]=categories';
            $feed_url .= '&meta[]=tags';
            $feed_url .= '&meta[]=models';
            $feed_url .= '&meta[]=content_sources';
            $kernel_video_sharing->logger->log( 'Retrieving videos feed: ' . $feed_url, 'DEBUG' );
            
            $curl = new Wp_Http_Curl();
            $result = $curl->request( $feed_url, $this->get_curl_params() );
            if( !is_wp_error($result) ) {
                $r_data = json_decode($result['body'], true);
                if( isSet( $r_data['screenshots'] ) ) {
                    if( !empty( $r_data['locales'] ) ) {
                        $locales = array();
                        foreach($r_data['locales'] as $loc) {
                            $locales[ $loc['code'] ] = $loc['title'];
                        }
                        $r_data['locales'] = $locales;
                    }
                    
                    update_option( 'kvs_feed_meta', $r_data );
                    update_option( 'kvs_feed_meta_update_time', time() );
                    
                    $kernel_video_sharing->logger->log( 'Feed meta parsed successfully', 'DEBUG' );
                    
                    return $r_data;
                } else {
                    $kernel_video_sharing->logger->log( 'Empty feed meta returned', 'ERROR' );
                }
            } else {
                $kernel_video_sharing->logger->log(
                    'Error retrieving feed meta: ' . $result->get_error_message(),
                    'ERROR'
                );
            }
        } catch(Exception $e) {
            $kernel_video_sharing->logger->log(
                'Error retrieving feed meta: ' . $e->getMessage(),
                'ERROR'
            );
            return array();
        }

		return array();
	}
    
    
	/**
	 * Retrieve the KVS feed .
	 *
	 * @since     1.0.0
	 * @param     string    $feed_url    KVS feed full URL.
	 * @return    array                  KVS feed meta array.
	 */
	public function get_feed( $start = null, $limit = null, $format = null, $locale = null ) {
        global $kernel_video_sharing;
        
		if( empty($feed_url) ) {
			$feed_url = $this->url;
		}
		if( empty($feed_url) ) {
			return array();
		}
        
        $meta = $this->get_feed_meta();

		$feed_url .= (strpos($feed_url, '?') === false ? '?' : '&');
        $feed_url .= 'feed_format=json&sorting=video_id+asc';
        if( !empty($start) ) {
            $feed_url .= '&start=' . (int)$start;
        }
        if( !empty($limit) ) {
            $feed_url .= '&limit=' . (int)$limit;
        }
        if( !empty($format) && in_array( $format, $meta['screenshots'] ) ) {
            $feed_url .= '&screenshot_format=' . $format;
        }
        if( !empty($locale) && !empty($meta['locales'][ $locale ]) ) {
            $feed_url .= '&locale=' . $locale;
        }
        $kernel_video_sharing->logger->log( 'Retrieving videos feed: ' . $feed_url, 'DEBUG' );
        
        try {
            $curl = new Wp_Http_Curl();
            $result = $curl->request( $feed_url, $this->get_curl_params() );
            if( !is_wp_error( $result ) ) {
                $r_data = json_decode($result['body'], true);
                if( isSet( $r_data[0]['id'] ) ) {
                    $kernel_video_sharing->logger->log( count( $r_data ) . ' videos in the feed', 'DEBUG' );
                    return $r_data;
                } else {
                    $kernel_video_sharing->logger->log( 'Empty videos feed returned', 'ERROR' );
                }
            } else {
                $kernel_video_sharing->logger->log( 'Error retrieving videos feed: ' . $result->get_error_message(), 'ERROR' );
            }
        } catch(Exception $e) {
            $kernel_video_sharing->logger->log( 'Error retrieving videos feed: ' . $e->getMessage(), 'ERROR' );
            return array();
        }

		return array();
	}
    
    
	/**
	 * Retrieve deleted videos from the feed.
	 *
	 * @since     1.0.0
	 * @param     string    $days        Period to check for deleted videos
	 * @return    array                  KVS feed meta array.
	 */
	public function get_deleted_ids( $days = null ) {
        global $kernel_video_sharing;
        
        $feed_url = $this->url;
		if( empty($feed_url) ) {
			return array();
		}
        
		$feed_url .= (strpos($feed_url, '?') === false ? '?' : '&');
        $feed_url .= 'action=get_deleted_ids';
        if( !empty($days) && is_numeric($days) ) {
            $feed_url .= '&days=' . (int)$days;
        }
        $kernel_video_sharing->logger->log( 'Retrieving deleted videos: ' . $feed_url, 'DEBUG' );
        
        try {
            $curl = new Wp_Http_Curl();
            $result = $curl->request( $feed_url, $this->get_curl_params() );
            if( !is_wp_error( $result ) ) {
                $deleted = explode("\n", $result['body']);
                $arr = array_filter($deleted, function($k){return is_numeric($k);});
                $kernel_video_sharing->logger->log( count($arr) . ' videos marked as deleted', 'DEBUG' );
                return $arr;
            } else {
                $kernel_video_sharing->logger->log( 'Error retrieving deleted videos: ' . $result->get_error_message(), 'ERROR' );
            }
        } catch(Exception $e) {
            $kernel_video_sharing->logger->log( 'Error retrieving deleted videos: ' . $e->getMessage(), 'ERROR' );
            return array();
        }

		return array();
    }
}
