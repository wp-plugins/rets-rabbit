<?php
add_action('admin_post_retsrabbit-search', 'retsrabbit_handle_search'); // If the user is logged in
add_action('admin_post_nopriv_retsrabbit-search', 'retsrabbit_handle_search'); // If the user in not logged in

function retsrabbit_handle_search() {
    $params = array();
    if(isset($_POST)) {
        //pull out the rets-specific search fields. Each
        //one will have "rets:" prepended to the field name
        foreach($_POST as $key => $value){
            if(stripos($key, 'rets:') !== false) {
                $new_key = str_replace('rets:', '', $key);
                $val = sanitize_text_field($value);
                //even though we don't send blank values to the RR API, save them anyway so
                //we can populate the search form correctly on the results page
                $params[$new_key] = $val;
            }
        }

        $search_page_id = get_option('rr-search-results-page');

        $limit = ((isset($_POST['limit']) && $_POST['limit'] != '') ? $_POST['limit'] : get_option('rr-results-per-page', 10));
        $results_page = (isset($_POST['results_page']) ? $_POST['results_page'] : get_permalink($search_page_id));
        $orderby = ((isset($_POST['orderby']) && $_POST['orderby'] != '') ? $_POST['orderby'] : "");
        $sort_order = ((isset($_POST['sort_order']) && $_POST['sort_order'] != '') ? $_POST['sort_order'] : "");

        if(sizeof($params) > 0) {
            set_transient('rr-search-query', null);
            $data = array(
                'params'         => $params,
                'limit'          => $limit,
                'result_page'    => $results_page,
                'orderby'        => $orderby,
                'sort_order'     => $sort_order,
                'page'           => 1
            );
            set_transient('rr-search-query', $data);

            //we save the search parameters and redirect to the results page
            //the results page actually hits the RR API and runs the search

            wp_redirect($results_page);
            exit;
        }
    }
}
?>
