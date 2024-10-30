<?php

/**
 * KVS Plugin settings page view: Header & Menu
 *
 * @link       https://www.kernel-video-sharing.com/
 * @since      1.0.0
 *
 * @package    Kvs
 * @subpackage Kvs/admin/partials
 */

$base = admin_url( 'edit.php?post_type=kvs_video&page=kvs-settings' );
$sections = array(
    'feed'     => __( 'Sync settings', 'kvs' ),
    'post'     => __( 'Post creation', 'kvs' ),
    'rules'    => __( 'Import rules', 'kvs' ),
    'advanced' => __( 'Advanced', 'kvs' ),
);
if( empty( $kernel_video_sharing->reader->get_feed_url() ) ) {
    $section = 'feed';
}
?>

<div class="kvs-setting-header">
    <h1><img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIxLjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9ItCh0LvQvtC5XzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAyMzIgNDEuMSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjMyIDQxLjE7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4KCS5zdDB7ZmlsbDojMjc2RkRCO30KCS5zdDF7ZmlsbDojNDM0NDQ4O30KCS5zdDJ7ZmlsbDojM0IzQTNBO30KPC9zdHlsZT4KPGc+Cgk8ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMzE5IC0xNTgzKSI+CgkJPGc+CgkJCTxwYXRoIGlkPSJfeDMzX2c1OGEiIGNsYXNzPSJzdDAiIGQ9Ik0zMzYuOCwxNjI0LjFsLTktNS4ydi0zMC43bDktNS4ybDEyLjMsNy4xbC0yMC41LDEyLjh2MC4xbDIxLjEsMTMuNkwzMzYuOCwxNjI0LjF6CgkJCQkgTTMxOSwxNjEzLjh2LTIwLjVsNy44LTQuNXYyOS41TDMxOSwxNjEzLjh6Ii8+CgkJPC9nPgoJPC9nPgoJPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTMxOSAtMTU4MykiPgoJCTxnPgoJCQk8cGF0aCBpZD0iX3gzM19nNThiIiBjbGFzcz0ic3QxIiBkPSJNMzQxLDE1OTYuM2w5LjEtNS43bDQuNSwyLjZ2MjAuN2wtMy45LDIuMmwtOS43LTYuM0wzNDEsMTU5Ni4zeiIvPgoJCTwvZz4KCTwvZz4KCTxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0zMTkgLTE1ODMpIj4KCQk8Zz4KCQkJPHBhdGggaWQ9Il94MzNfZzU4YyIgY2xhc3M9InN0MiIgZD0iTTM2MywxNTk4aDIuNXY1LjNsNC01LjNoMy4ybC00LjYsNi4ybDUuMSw2LjhIMzcwbC0zLjUtNC43bC0xLDEuM3YzLjRIMzYzVjE1OTh6IE0zNzQsMTU5OAoJCQkJaDcuN3YyLjZoLTUuMXYyLjZoNS4xdjIuNmgtNS4xdjIuNmg1LjF2Mi42SDM3NFYxNTk4eiBNMzgyLjksMTYxMXYtMTNoMy44YzIuMSwwLDMuOCwxLjgsMy44LDMuOWMwLDEuNC0wLjgsMi43LTIsMy40bDIuNSw1LjcKCQkJCWgtMi44bC0yLjMtNS4yaC0wLjV2NS4yTDM4Mi45LDE2MTF6IE0zODUuNCwxNjAzLjJoMS4zYzAuNywwLDEuMy0wLjYsMS4zLTEuM2MwLTAuNy0wLjYtMS4zLTEuMy0xLjNoLTEuM1YxNjAzLjJ6IE00MDAsMTU5OGgyLjUKCQkJCXYxM2gtM2wtNS4xLTguN3Y4LjdoLTIuNXYtMTNoM2w1LjEsOC43TDQwMCwxNTk4eiBNNDAzLjcsMTU5OGg3Ljd2Mi42aC01LjF2Mi42aDUuMXYyLjZoLTUuMXYyLjZoNS4xdjIuNmgtNy43VjE1OTh6CgkJCQkgTTQxMi41LDE1OThoMi41djEwLjRoNC42djIuNmgtNy4yVjE1OTh6IE00MzAuNSwxNjExbC00LjYtMTNoMi43bDMuMiw5LjJsMy4yLTkuMmgyLjdsLTQuNiwxM0g0MzAuNXogTTQzOC42LDE1OThoMi41djEzaC0yLjUKCQkJCVYxNTk4eiBNNDQyLjMsMTU5OGgzLjZjMy41LDAsNi40LDIuOSw2LjQsNi41cy0yLjksNi41LTYuNCw2LjVoLTMuNUw0NDIuMywxNTk4eiBNNDQ1LjksMTYwOC40YzIuMSwwLDMuOC0xLjcsMy44LTMuOQoJCQkJYzAtMi4xLTEuNy0zLjktMy44LTMuOWgtMXY3LjhINDQ1Ljl6IE00NTMuNCwxNTk4aDcuN3YyLjZoLTUuMXYyLjZoNS4xdjIuNmgtNS4xdjIuNmg1LjF2Mi42aC03LjdWMTU5OHogTTQ3NSwxNjA0LjUKCQkJCWMwLDMuNi0yLjksNi41LTYuNCw2LjVjLTMuNSwwLTYuNC0zLTYuNC02LjVjMC0zLjYsMi45LTYuNSw2LjQtNi41QzQ3Mi4xLDE1OTgsNDc1LDE2MDAuOSw0NzUsMTYwNC41eiBNNDY0LjgsMTYwNC41CgkJCQljMCwyLjEsMS43LDMuOSwzLjgsMy45YzAsMCwwLDAsMCwwYzIuMSwwLDMuOC0xLjgsMy44LTMuOXMtMS43LTMuOS0zLjgtMy45QzQ2Ni41LDE2MDAuNiw0NjQuNywxNjAyLjQsNDY0LjgsMTYwNC41CgkJCQlDNDY0LjgsMTYwNC41LDQ2NC44LDE2MDQuNSw0NjQuOCwxNjA0LjVMNDY0LjgsMTYwNC41eiBNNDgxLjIsMTYwMS45YzAtMi4yLDEuNy0zLjksMy44LTMuOWMxLjUsMCwyLjgsMC41LDMuOSwxLjRsLTEuOCwxLjkKCQkJCWMtMC42LTAuNC0xLjMtMC42LTIuMS0wLjZjLTAuNywwLTEuMywwLjYtMS4zLDEuM2MwLDAuMywwLjEsMC42LDAuMywwLjhjMC40LDAuNSwxLjEsMC42LDEuNywwLjZjMi4xLDAuMSwzLjcsMS42LDMuNywzLjgKCQkJCWMwLDIuMS0xLjcsMy45LTMuOCwzLjljLTEuNiwwLTMuMi0wLjYtNC40LTEuOGwxLjgtMS44YzAuNywwLjcsMS42LDEuMSwyLjYsMS4xYzAuNywwLDEuMi0wLjYsMS4yLTEuM2MwLTAuNC0wLjEtMC44LTAuNS0xCgkJCQljLTAuNi0wLjQtMS42LTAuMi0yLjItMC40QzQ4Mi4zLDE2MDUuMiw0ODEuMSwxNjAzLjcsNDgxLjIsMTYwMS45TDQ4MS4yLDE2MDEuOXogTTQ5MC41LDE1OThoMi41djUuMmg0LjZ2LTUuMmgyLjV2MTNoLTIuNXYtNS4yCgkJCQloLTQuNnY1LjJoLTIuNVYxNTk4eiBNNTEwLjMsMTYxMWwtMS4zLTMuNmgtMy45bC0xLjMsMy42aC0yLjdsNC42LTEzaDIuN2w0LjYsMTNINTEwLjN6IE01MDcsMTYwMS44bC0xLDIuOWgyLjFMNTA3LDE2MDEuOHoKCQkJCSBNNTEzLjgsMTYxMXYtMTNoMy44YzIuMSwwLDMuOCwxLjgsMy44LDMuOWMwLDEuNC0wLjgsMi43LTIsMy40bDIuNSw1LjdoLTIuOGwtMi4zLTUuMmgtMC41djUuMkw1MTMuOCwxNjExeiBNNTE2LjMsMTYwMy4yaDEuMwoJCQkJYzAuNywwLDEuMy0wLjYsMS4zLTEuM2MwLTAuNy0wLjYtMS4zLTEuMy0xLjNoLTEuM1YxNjAzLjJ6IE01MjIuOCwxNTk4aDIuNXYxM2gtMi41TDUyMi44LDE1OTh6IE01MzQuNiwxNTk4aDIuNXYxM2gtM2wtNS4xLTguNwoJCQkJdjguN2gtMi41di0xM2gzbDUuMSw4LjdMNTM0LjYsMTU5OHogTTU0OSwxNTk5LjhsLTEuOCwxLjhjLTAuNy0wLjctMS42LTEtMi42LTFjLTIuMSwwLTMuOCwxLjgtMy44LDMuOWMwLDAsMCwwLDAsMAoJCQkJYzAsMi4xLDEuNywzLjksMy44LDMuOWMxLjIsMCwyLjMtMC42LDMtMS42aC0zdi0yLjZoNi4zbDAsMC4yYzAsMy42LTIuOSw2LjUtNi40LDYuNWMtMy41LDAtNi40LTMtNi40LTYuNQoJCQkJYzAtMy42LDIuOC02LjUsNi40LTYuNUM1NDYuNCwxNTk4LDU0Ny45LDE1OTguNyw1NDksMTU5OS44TDU0OSwxNTk5Ljh6Ii8+CgkJPC9nPgoJPC9nPgo8L2c+Cjwvc3ZnPgo=" height="60" alt="<?php _e( 'Kernel Video Sharing plugin settings', 'kvs' ); ?>" /></h1>
	<nav class="kvs-setting-tabs-wrapper hide-if-no-js" aria-label="Secondary menu">
    <?php if( !empty( $kernel_video_sharing->reader->get_feed_url() ) ): ?>
        <?php foreach( $sections as $sec=>$title ): ?>
            <a href="<?php echo esc_attr($base . '&section=' . $sec); ?>" class="kvs-setting-tab<?php if( $sec === $section ) {echo ' active';} ?>" aria-current="true"><?php echo esc_html($title); ?></a>
        <?php endforeach; ?>
    <?php else: ?>
        <?php foreach( $sections as $sec=>$title ): ?>
            <a class="kvs-setting-tab<?php if( $sec === $section ) {echo ' active';} else {echo ' disabled';} ?>" aria-current="true"><?php echo esc_html($title); ?></a>
        <?php endforeach; ?>
    <?php endif; ?>
	</nav>
</div>
