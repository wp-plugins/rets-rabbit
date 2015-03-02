<?php
class retsrabbit_shortcodes {

    static function listings($atts, $content = null, $tag = null) {

        global $wp_query;
        $template = (isset($atts['template']) ? $atts['template'] : 'listings.php');
        $limit = intVal(isset($atts['limit']) ? $atts['limit'] : get_option('rr-results-per-page', 10));
        $num_photos = intVal(isset($atts['num_photos']) ? $atts['num_photos'] : -1);
        $paginate = (isset($atts['paginate']) ? $atts['paginate'] === 'true' : true);
        $orderby = (isset($atts['orderby']) ? $atts['orderby'] : "");
        $sort_order = (isset($atts['sort_order']) ? $atts['sort_order'] : "");

        $params = json_decode($atts['params'], true);

        $rr_adapter = new rr_adapter();
        $results = $rr_adapter->run_search($params, $limit, $num_photos, $orderby, $sort_order);
        //$results = $response->results;
        return $rr_adapter->parse($results, $template, $paginate, $limit);
    }

    static function listing($atts, $content = null, $tag) {

        global $wp_query;
        $template = (isset($atts['template']) ? $atts['template'] : 'detail.php');
        $num_photos = (isset($atts['num_photos']) ? $atts['num_photos'] : -1);

        if(!$mls_id = get_query_var('mls_id'))
            $mls_id = $atts['mls_id'];

        $rr_adapter = new rr_adapter();
        $result = $rr_adapter->get_listing($mls_id, $num_photos);
        return $rr_adapter->parse_single($result, $template);
    }

    static function search_form($atts, $content = null, $tag = null) {
        global $wp_query;
        $template = (isset($atts['template']) ? $atts['template'] : 'search-form.php');
        $rr_adapter = new rr_adapter();
        $query_params = get_transient('rr-search-query');
        return $rr_adapter->generate_form($template, $query_params);
    }

    static function search_results($atts, $content = null, $tag = null) {
        global $wp_query;
        $template = (isset($atts['template']) ? $atts['template'] : 'results.php');
        $limit = (isset($atts['limit']) ? $atts['limit'] : get_option('rr-results-per-page', 10));
        $num_photos = (isset($atts['num_photos']) ? $atts['num_photos'] : 1);
        $paginate = (isset($atts['paginate']) ? $atts['paginate'] === 'true' : true);

        $query_params = get_transient('rr-search-query');
        $params = $query_params['params'];
        $orderby = $query_params['orderby'];
        $sort_order = $query_params['sort_order'];

        if($params != null && sizeof($params) > 0) {
            $rr_adapter = new rr_adapter();
            $results = $rr_adapter->run_search($params, $limit, $num_photos, $orderby, $sort_order);

            return $rr_adapter->parse($results, $template, $paginate, $limit);
        } else {
            return "";
        }
    }
}

add_shortcode("retsrabbit-listings", array("retsrabbit_shortcodes", "listings"));

add_shortcode('retsrabbit-listing', array("retsrabbit_shortcodes", "listing"));

add_shortcode("retsrabbit-search-form", array("retsrabbit_shortcodes", "search_form"));

add_shortcode("retsrabbit-search-results", array("retsrabbit_shortcodes", "search_results"));
?>
