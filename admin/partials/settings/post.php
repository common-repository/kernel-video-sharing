<?php

/**
 * KVS Plugin settings page view: Post creation setting section
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
    
    $post_types = get_post_types( array(
        'public' => true,
        'capability_type' => 'post'
        ), 'objects');
    foreach( $post_types as $slug=>$obj ) {
        if( substr($slug, 0, 4) === 'kvs_'  || $slug == 'attachment') {
            unset( $post_types[$slug] );
        }
    }

    $taxonomies = get_taxonomies( array( 'public'=>true ), 'objects');
    foreach( $taxonomies as $slug=>$obj ) {
        if( substr($slug, 0, 4) === 'kvs_' ) {
            unset( $taxonomies[$slug] );
        }
    }
?>
<form method="post" action="options.php" id="kvs-settings-form">
    <?php settings_fields( 'kvs-settings-group-post' ); ?>
    <?php do_settings_sections( 'kvs-settings-group-post' ); ?>

	<h3>Posts</h3>
	<table class="form-table">
	    <tr valign="top">
        <th scope="row"><?php _e( 'Create posts of type', 'kvs' ); ?></th>
        <td>
            <select name="kvs_post_type">
                <option value=""><?php _e( 'KVS Videos', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_post_type' );
				foreach( $post_types as $slug=>$obj ) {
					echo '<option value="' . esc_attr($slug) . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo esc_html($obj->label);
                    echo '</option>';
				} ?>
			</select>
	        &nbsp;&nbsp;
            <label>
				<?php
				$checked = get_option( 'kvs_post_import_featured_image' );
                ?>
                <input type="checkbox" name="kvs_post_import_featured_image" value="import"<?php echo $checked ? ' checked="checked"' : ''; ?>>
                <?php _e('Import screenshots as featured images', 'kvs'); ?>
            </label>
	        <p class="description">Choose which post type you want KVS videos to be imported to</p>
		</td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e( 'Post status', 'kvs' ); ?></th>
        <td>
            <select name="kvs_post_status">
                <option value="published" <?php if( get_option( 'kvs_post_status' ) == 'published' ){echo ' selected';} ?>><?php _e( 'Published', 'kvs' ); ?></option>
                <option value="pending" <?php if( get_option( 'kvs_post_status' ) == 'pending' ){echo ' selected';} ?>><?php _e( 'Pending Review', 'kvs' ); ?></option>
                <option value="draft" <?php if( get_option( 'kvs_post_status' ) == 'draft' ){echo ' selected';} ?>><?php _e( 'Draft', 'kvs' ); ?></option>
			</select>
	        <p class="description">Status of the created posts</p>
		</td>
        </tr>

		<tr valign="top">
        <th scope="row"><?php _e( 'Publishing date', 'kvs' ); ?></th>
        <td>
            <select name="kvs_post_date">
                <option value="feed" <?php if( get_option( 'kvs_post_date' ) == 'feed' ){echo ' selected';} ?>><?php _e( 'Take from feed', 'kvs' ); ?></option>
                <option value="now" <?php if( get_option( 'kvs_post_date' ) == 'now' ){echo ' selected';} ?>><?php _e( 'Current time', 'kvs' ); ?></option>
			</select>
	        <p class="description">Publishing date of the created posts</p>
		</td>
        </tr>
        
        <tr valign="top">
        <th scope="row"><?php _e( 'Post body template', 'kvs' ); ?></th>
        <td>
	        <div class="textarea">
                <textarea name="kvs_post_body_template" id="kvs_post_body_template" class="width-wide" rows="10"><?php echo esc_html(get_option( 'kvs_post_body_template' )); ?></textarea>
		        <p class="description">Specify post body template for all new posts</p>
	        </div>
            <div class="hint">
                <h3>Template elements available:</h3>
                <ul>
                    <li><code>{%id%}</code> - KVS video ID</li>
                    <li><code>{%title%}</code> - video title</li>
                    <li><code>{%description%}</code> - video description</li>
                    <li><code>{%date%}</code> - video publishing date</li>
                    <li><code>{%popularity%}</code> - video impressions amount</li>
                    <li><code>{%rating%}</code> - video rating (number 0.0 - 5.0)</li>
                    <li><code>{%rating_percent%}</code> - video rating (percent 0 - 100%)</li>
                    <li><code>{%votes%}</code> - votes amount</li>
                    <li><code>{%duration%}</code> - video duration <i>h:m:s</i></li>
                    <li><code>{%link%}</code> - KVS video page URL</li>
                </ul>
            </div>
            <div class="hint">
                <h3>Post template sample:</h3>
                <code class="sample">{%description%}<br/>
Rating: {%rating_percent%}<br/>
[kvs_player id={%id%}]</code>
            </div>
		</td>
        </tr>
	</table>

	<h3>Categorization</h3>
	<table class="form-table">
	    <th scope="row"><?php _e( 'Categories taxonomy', 'kvs' ); ?></th>
        <td>
            <select name="kvs_taxonomy_category" class="kvs_taxonomies_select">
                <option value="">&#9940; <?php _e( 'Do not import categories', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_taxonomy_category' );
				foreach( $taxonomies as $slug=>$obj ) {
					echo '<option value="' . esc_attr($slug) . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo esc_html($obj->label);
                    echo '</option>';
				} ?>
                <option value="kvs_category" class="kvs"<?php if($selected==='kvs_category') {echo ' selected';}?>>
                    &#11088; <?php _e( 'Create custom categories taxonomy', 'kvs' ); ?>
                </option>
			</select>
	        <p class="description">Select if you want to import video categories into Wordpress taxonomy</p>
		</td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Tags taxonomy', 'kvs' ); ?></th>
        <td>
            <select name="kvs_taxonomy_tag" class="kvs_taxonomies_select">
                <option value="">&#9940; <?php _e( 'Do not import tags', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_taxonomy_tag' );
				foreach( $taxonomies as $slug=>$obj ) {
					echo '<option value="' . esc_attr($slug) . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo esc_html($obj->label);
                    echo '</option>';
				} ?>
                <option value="kvs_tag" class="kvs"<?php if($selected==='kvs_tag') {echo ' selected';}?>>
                    &#11088; <?php _e( 'Create custom tags taxonomy', 'kvs' ); ?>
                </option>
			</select>
	        <p class="description">Select if you want to import video tags into Wordpress taxonomy</p>
		</td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Models taxonomy', 'kvs' ); ?></th>
        <td>
            <select name="kvs_taxonomy_model" class="kvs_taxonomies_select">
                <option value="">&#9940; <?php _e( 'Do not import models', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_taxonomy_model' );
				foreach( $taxonomies as $slug=>$obj ) {
					echo '<option value="' . esc_attr($slug) . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo esc_html($obj->label);
                    echo '</option>';
				} ?>
                <option value="kvs_model" class="kvs"<?php if($selected==='kvs_model') {echo ' selected';}?>>
                    &#11088; <?php _e( 'Create custom models taxonomy', 'kvs' ); ?>
                </option>
			</select>
	        <p class="description">Select if you want to import video models into Wordpress taxonomy</p>
		</td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e( 'Content sources taxonomy', 'kvs' ); ?></th>
        <td>
            <select name="kvs_taxonomy_source" class="kvs_taxonomies_select">
                <option value="">&#9940; <?php _e( 'Do not import content sources', 'kvs' ); ?></option>
				<?php
				$selected = get_option( 'kvs_taxonomy_source' );
				foreach( $taxonomies as $slug=>$obj ) {
					echo '<option value="' . esc_attr($slug) . '"';
					echo ( $slug === $selected ) ? ' selected>' : '>';
					echo esc_html($obj->label);
                    echo '</option>';
				} ?>
                <option value="kvs_source" class="kvs"<?php if($selected==='kvs_source') {echo ' selected';}?>>
                    &#11088; <?php _e( 'Create custom content sources taxonomy', 'kvs' ); ?>
                </option>
			</select>
	        <p class="description">Select if you want to import video content sources into Wordpress taxonomy</p>
		</td>
        </tr>
    </table>

	<h3>Custom fields</h3>
	<table class="form-table">
		<tr valign="top">
        <th scope="row"><?php _e( 'Custom 1', 'kvs' ); ?></th>
        <td>
            <input type="text" name="kvs_custom1_name" value="<?php echo esc_attr( get_option( 'kvs_custom1_name' )); ?>" />
	        =
	        <input type="text" name="kvs_custom1_value" value="<?php echo esc_attr( get_option( 'kvs_custom1_value' )); ?>" />
	        <p class="description">Specify custom field name and value (see <b>Template elements</b> above)</p>
        </td>
        </tr>
		<tr valign="top">
        <th scope="row"><?php _e( 'Custom 2', 'kvs' ); ?></th>
        <td>
            <input type="text" name="kvs_custom2_name" value="<?php echo esc_attr( get_option( 'kvs_custom2_name' )); ?>" />
	        =
	        <input type="text" name="kvs_custom2_value" value="<?php echo esc_attr( get_option( 'kvs_custom2_value' )); ?>" />
	        <p class="description">Specify custom field name and value (see <b>Template elements</b> above)</p>
        </td>
        </tr>
		<tr valign="top">
        <th scope="row"><?php _e( 'Custom 3', 'kvs' ); ?></th>
        <td>
            <input type="text" name="kvs_custom3_name" value="<?php echo esc_attr( get_option( 'kvs_custom3_name' )); ?>" />
	        =
	        <input type="text" name="kvs_custom3_value" value="<?php echo esc_attr( get_option( 'kvs_custom3_value' )); ?>" />
	        <p class="description">Specify custom field name and value (see <b>Template elements</b> above)</p>
        </td>
        </tr>
	</table>
    <?php submit_button( __( 'Save Changes', 'kvs' ) ); ?>
</form>
</div>