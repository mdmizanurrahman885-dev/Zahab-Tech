<?php
/**
 * Removes the gateway settings. Order meta (_zcs_*) is kept because it is part of each order's payment record.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'woocommerce_zcs_online_settings' );
