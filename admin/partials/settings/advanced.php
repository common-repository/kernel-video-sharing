<?php

/**
 * KVS Plugin settings page view: Feed setting section
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin/partials
 */
?>

<div class="wrap">
<?php
	settings_errors( 'kvs_messages' );
?>
<form method="post" action="options.php" id="kvs-settings-form">
    <?php settings_fields( 'kvs-settings-group-advanced' ); ?>
    <?php do_settings_sections( 'kvs-settings-group-advanced' ); ?>

    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e( 'Last added video ID', 'kvs' ); ?></th>
        <td>
            <input type="text" name="kvs_feed_last_id" value="<?php echo esc_attr(get_option( 'kvs_feed_last_id' )); ?>" />
	        <p class="description">This field is automatically populated by plugin, in most cases no need to touch it</p>
		</td>
        </tr>
	    <tr valign="top">
        <th scope="row"><?php _e( 'Log level', 'kvs' ); ?></th>
        <td>
            <?php 
			$selected = get_option('kvs_log_level') ?: Kvs_Logger::get_log_level();
            ?>
            <select name="kvs_log_level">
				<?php
				foreach(Kvs_Logger::LOG_LEVELS as $indx=>$val) {
					echo '<option value="' . esc_attr($indx) . '"';
					echo ( $indx === $selected ) ? ' selected>' : '>';
					echo esc_html($indx);
                    echo '</option>';
				} ?>
			</select>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Latest log', 'kvs' ); ?></th>
        <td>
            <input type="text" value="<?php echo esc_attr(KVS_LOGFILE);?>" class="width-full margin-bottom-10" readonly="true" disabled="true" />
            <textarea id="kvs-log" class="width-full" style="white-space: pre; overflow: auto;" readonly="true"><?php echo esc_html(Kvs_Logger::get_log_content());?></textarea>
            <a class="button secondary refresh-log" onclick="window.location.reload();">
                <i class="fa fa-fw fa-sync"></i> <?php _e( 'Refresh log', 'kvs' );?>
            </a>
            <a class="button secondary clear-log" onclick="if(confirm('<?php _e( 'Are you sure you want to delete all plugin logs files?', 'kvs' );?>')){jQuery('#kvs-clear-log').submit();}">
                <?php _e( 'Clear plugin logs', 'kvs' );?>
            </a>
		</td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>

<form method="post" id="kvs-import-form" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="kvs_import_full">
    <h3>Full import / update</h3>
    <p>You can manually import all videos from KVS. Please be careful, import starts immediately, and it may take several minutes to process all videos.</p>
    <p>Please make sure your settings are fully set as needed to have proper import.</p>
<?php
    wp_nonce_field( 'kvs_import_full', 'kvs-nonce' );
    submit_button( __( 'Run full import now', 'kvs' ), 'secondary' );
?>
</form>

<form method="post" id="kvs-clear-log" action="<?php echo esc_attr( admin_url( 'admin-post.php' ) ); ?>">
    <input type="hidden" name="action" value="kvs_clear_log">
    <?php wp_nonce_field( 'kvs_import_full', 'kvs-nonce' );?>
</form>

</div>
