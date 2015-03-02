<?php
/*
Plugin Name: Rets Rabbit
Plugin URI: http://retsrabbit.com/wordpress
Description: Plugin to integrate the real estate cloud service, Rets Rabbit, with Wordpress.
Version: 0.1
Author: Patrick Pohler
Author URI: http://www.anecka.com
*/

function add_retsrabbit_query_vars( $vars ){
    $vars = array_merge($vars, array("rr_page", "rr_limit", "mls_id"));
    return $vars;
}

function rr_fixpaginationampersand($link) {
    return str_replace('#038;', '&', $link);
}

/*
function rr_checkfordetailpagetitle($title, $sep) {
    $detail_page_id = get_option('rr-detail-page');
    if(get_the_ID() == $detail_page_id) {
        if($mls_id = get_query_var('mls_id')) {
            return $title."| WOOT";
        }
    }

    return $title;
}
*/

add_filter('paginate_links', 'rr_fixpaginationampersand');

add_filter( 'query_vars', 'add_retsrabbit_query_vars' );

require_once(dirname(__FILE__).'/rr_adapter.php');
require_once(dirname(__FILE__).'/rr_shortcodes.php');
require_once(dirname(__FILE__).'/rr_actions.php');
include_once(dirname(__FILE__).'/settings.php');



?>
