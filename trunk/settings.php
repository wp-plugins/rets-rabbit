<?php
add_action('admin_menu', 'retsrabbit_admin_menu');


function retsrabbit_admin_menu() {
    //add_options_page('Rets Rabbit Plugin Options', 'Rets Rabbit', 'manage_options', 'retsrabbit', 'retsrabbit_account_options');
    add_menu_page('Rets Rabbit Settings', 'Rets Rabbit', 'manage_options', 'retsrabbit', 'retsrabbit_account_options');

    add_submenu_page('retsrabbit', 'Rets Rabbit Metadata', 'Metadata', 'manage_options', 'retsrabbit-metadata', 'retsrabbit_metadata');

    add_action('admin_init', 'retsrabbit_register_settings');
}

function retsrabbit_register_settings() {
    register_setting('rr-settings-group', 'rr-client-id');
    register_setting('rr-settings-group', 'rr-client-secret');
    register_setting('rr-settings-group', 'rr-templates');
    register_setting('rr-settings-group', 'rr-detail-page');
    register_setting('rr-settings-group', 'rr-search-results-page');
    register_setting('rr-settings-group', 'rr-results-per-page');
}

function retsrabbit_account_options() {
    if( !current_user_can('manage_options') ) {
        wp_die( __('You do not have permissions to access this page.'));
    }

    $pages = get_pages();

    $search_page_id = get_option('rr-search-results-page');
    $detail_page_id = get_option('rr-detail-page');
?>

    <h2>Rets Rabbit Settings</h2>
    <form method="post" action="options.php">
        <?php
        settings_fields('rr-settings-group');
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Client Id</th>
                <td><input type="text" class="regular-text" name="rr-client-id" value="<?php echo get_option('rr-client-id'); ?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Client Secret</th>
                <td><input type="text" class="regular-text" name="rr-client-secret" value="<?php echo get_option('rr-client-secret'); ?>"></td>
            </tr>
            <tr valign="top">
                <th scope="row">Template Location</th>
                <td><input type="text" class="regular-text" name="rr-templates" value="<?php echo get_option('rr-templates', 'wp-content/plugins/retsrabbit/template/'); ?>"></td>
            </tr>
        </table>
        <hr>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Detail Page</th>
                <td>
                    <select name="rr-detail-page">
                        <option value="">None</option>
                        <?php foreach($pages as $page) :?>
                            <option <? if ($page->ID == $detail_page_id) : ?> selected <? endif; ?> value="<?= $page->ID ?>"><?= $page->post_title ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Search Results Page</th>
                <td>
                    <select name="rr-search-results-page">
                        <option value="">None</option>
                        <?php foreach($pages as $page) :?>
                            <option <? if ($page->ID == $search_page_id) : ?> selected <? endif; ?> value="<?= $page->ID ?>"><?= $page->post_title ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Default Results Per Page</th>
                <td>
                    <input type="text" name="rr-results-per-page" value="<?= get_option('rr-results-per-page', 10) ?>">
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
<?php

}

function retsrabbit_metadata() {
    $rr_adapter = new rr_adapter();
    $table_data = array();

    $types = $rr_adapter->metadata();

    if(sizeof($types) == 0)
    {
        $table_data[] = array("<p><h2>No metadata has been found! Please check your Rets Rabbit credentials on the Rets Rabbit Settings Page.</h2></p>");
    }

    foreach ($types as $type) {

        if($type->Resource == "Property") {

            $table_data[] = array("<h2><em>resource</em> {$type->Resource}</h2>&nbsp;&nbsp;&nbsp;[<a class='hide-table' href='#".$type->Resource."'>show/hide</a>]");

            //classes
            foreach ($type->Data as $class) {
                $table_data[] = array("<h3><em>class</em> {$class->ClassName} : {$class->Description}</h3>&nbsp;&nbsp;&nbsp;<!--[<a class='hide-rows' href='#' data-row='".$class->ClassName."'>show/hide</a>]-->");

                $fields = $class->Fields;
                if($fields && sizeof($fields) > 0) {
                    foreach ($fields as $field) {
                        $table_data[] = array("<span class='".$class->ClassName."'><em>field</em> {$field->SystemName} ({$field->DataType}) : {$field->LongName}");

                        if(isset($field->LookupValues)) {
                            $values = $field->LookupValues;
                            foreach($values as $value) {
                                $table_data[] = array("<span class='".$class->ClassName."' style='margin-left:20px;'><em>value</em> {$value->LongValue}");
                            }
                        }
                    }
                }
            }
            //objects (images)
            $object_types = $type->Objects;
            foreach ($object_types as $type) {
                $table_data[] = array("<em>object</em> <h3>{$type->ObjectType}</h3> <strong>described as \"{$type->Description}\"</strong>");
            }
        }

    }
    echo("<table>");
    foreach($table_data as $row) {
        echo("<tr><td>{$row[0]}</td></tr>");
    }
    echo("</table>");

    ?>
    <!--
    <script type="text/javascript">
    jQuery(function() {
        jQuery("table").children('tbody').hide();

        jQuery(".hide-table").click(function() {
            jQuery(this).closest('table').children('tbody').toggle(800);
        });

        jQuery('.hide-rows').click(function() {
            //$(this).closest('tbody').children('tr:not(:first)').toggle(300);
            var class_id = $(this).attr("data-row");
            jQuery('.' + class_id).closest('tr').toggle(800);
        });
    });
    </script>
-->
    <?php
}
?>
