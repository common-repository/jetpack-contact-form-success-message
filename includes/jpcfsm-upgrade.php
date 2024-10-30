<?php

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) || !defined( 'ABSPATH' ) ) {
    die;
}

$formatted = get_option('jpsm_message');
$filter_all = '1' == get_option('jpsm_filter_all');

$settings = array();

if ( $formatted ) {
    $settings['message'] = $formatted;
}

if ( $filter_all ) {
    $settings['strip_content'] = true;
}

if ( $settings ) {
    update_option ( 'jpcfsm_settings', $settings );
}

delete_option ( 'jpsm_message' );
delete_option ( 'jpsm_filter_all' );