<?php

/**
 * KVS log writer class
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/includes
 * @author     Kernel Video Sharing <sales@kernel-video-sharing.com>
 */
class Kvs_Logger {

	/**
	 * Log file name
	 *
	 * @since    1.0.3
	 * @access   protected
	 * @var      string
	 */
	protected $log_file_name;

	/**
	 * Log file pointer resource
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      resource    
	 */
	protected $log_file;

	/**
	 * Logger levels
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    
	 */
	const LOG_LEVELS = array(
        'NONE' => 0,
        'WARNING' => 1,
        'ERROR' => 2,
        'DEBUG' => 3,
    );

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct( ) {
        if ( defined( 'KVS_LOGFILE' ) ) {
            $log_file = KVS_LOGFILE;
        } else {
            define( 'KVS_LOGFILE', KVS_DIRPATH . 'logs/' . date('d-m-Y') . '.log' );
            $log_file = KVS_LOGFILE;
            if( !file_exists( $log_file ) ) {
                wp_mkdir_p( dirname( $log_file ) );
            }
        }

		$this->log_file_name = $log_file;
	}
    
	/**
	 * Optional class destructor.
	 *
	 * @since    1.0.0
	 */
    public function __destruct() {
        if ($this->log_file) {
	        fclose($this->log_file);
        }
    }
    
	/**
	 * Returns current log level slug
	 *
	 * @since     1.0.0
	 * @return     string    Log level slug
	 */
    public static function get_log_level() {
        if ( !defined('KVS_LOG_LEVEL') ) {
            $level = get_option('kvs_log_level') ?: 'NONE';
            if( isSet( self::LOG_LEVELS[$level] ) ) {
                define( 'KVS_LOG_LEVEL', $level );
            } else {
                define( 'KVS_LOG_LEVEL', 'NONE' );
            }
        }
        return KVS_LOG_LEVEL;
    }

	/**
	 * Write line(s) to the log file
	 *
	 * @since     1.0.0
	 * @param     string|array    String or array of string to write to the log
	 */
	public function log( $string = null, $log_level = 'WARNING' ) {
        if( empty( $string ) ) {
            return;
        }
        if( !$this->log_file_name ) {
            return;
        }
        if( !$this->log_file ) {
            $this->log_file = fopen($this->log_file_name, 'a+');
        }
        
        if( empty( self::LOG_LEVELS[$log_level] ) ) {
            return;
        }
        if( self::LOG_LEVELS[$log_level] > self::LOG_LEVELS[self::get_log_level()] ) {
            return;
        }
        
        if( !is_array( $string ) ) {
            $string = [ $string ];
        }
        
        // Microseconds will be added to the time for DEBUG mode
        if( self::get_log_level() === 'DEBUG' ) {
            $d = new DateTime();
            $time = $d->format( 'H:m:i.u' );
        } else {
            $time = date( 'H:m:i' );
        }
        foreach( $string as $row ) {
            fwrite( 
                $this->log_file, 
                $time . " " . $row . "\n"
            );
        }
	}
    
	/**
	 * Return log file content
	 *
	 * @since     1.0.0
     * @return    string    Log file content
	 */
    public static function get_log_content() {
        return file_get_contents( KVS_LOGFILE );
    }
    
    
	/**
	 * Clear current log file
	 *
	 * @since     1.0.0
	 * @param     string    Log file location
	 */
    public function clear_log_content() {
    	if ($this->log_file) {
		    fclose($this->log_file);
	    }
        $dir_path = KVS_DIRPATH . 'logs/';
        if (is_dir($dir_path)) {
            $dh = opendir($dir_path);
            if ($dh) {
                while (($entry = readdir($dh)) !== false) {
					if (substr($entry, -4) == '.log') {
						unlink("$dir_path/$entry");
					}
				}
	        }
        }
    }

}
