<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    catalogues
 */

/**
 * Hook class.
 */
class Hook_content_meta_aware_catalogue_entry
{
    /**
     * Get content type details. Provides information to allow task reporting, randomisation, and add-screen linking, to function.
     *
     * @param  ?ID_TEXT $zone The zone to link through to (null: autodetect).
     * @param  ?ID_TEXT $catalogue_name Catalogue name for entry (null: unknown / N/A).
     * @return ?array Map of award content-type info (null: disabled).
     */
    public function info($zone = null, $catalogue_name = null)
    {
        return array(
            'support_custom_fields' => false,

            'content_type_label' => 'catalogues:CATALOGUE_ENTRY',
            'content_type_universal_label' => 'Catalogue entry',

            'connection' => $GLOBALS['SITE_DB'],
            'table' => 'catalogue_entries',
            'id_field' => 'id',
            'id_field_numeric' => true,
            'parent_category_field' => 'cc_id',
            'parent_category_meta_aware_type' => 'catalogue_category',
            'is_category' => false,
            'is_entry' => true,
            'category_field' => array('c_name', 'cc_id'), // For category permissions
            'category_type' => array('catalogues_catalogue', 'catalogues_category'), // For category permissions
            'parent_spec__table_name' => 'catalogue_categories',
            'parent_spec__parent_name' => 'cc_parent_id',
            'parent_spec__field_name' => 'id',
            'category_is_string' => array(true, false),

            'title_field' => 'CALL: generate_catalogue_entry_title',
            'title_field_dereference' => false,
            'description_field' => null,
            'thumb_field' => 'CALL: generate_catalogue_thumb_field',
            'thumb_field_is_theme_image' => false,
            'alternate_icon_theme_image' => null,

            'view_page_link_pattern' => '_SEARCH:catalogues:entry:_WILD',
            'edit_page_link_pattern' => '_SEARCH:cms_catalogues:_edit:_WILD',
            'view_category_page_link_pattern' => '_SEARCH:catalogues:category:_WILD',
            'add_url' => (function_exists('has_submit_permission') && has_submit_permission('mid', get_member(), get_ip_address(), 'cms_catalogues')) ? (get_module_zone('cms_catalogues') . ':cms_catalogues:add_entry' . (is_null($catalogue_name) ? '' : (':catalogue_name=' . $catalogue_name))) : null,
            'archive_url' => ((!is_null($zone)) ? $zone : get_module_zone('catalogues')) . ':catalogues',

            'support_url_monikers' => true,

            'views_field' => 'ce_views',
            'order_field' => null,
            'submitter_field' => 'ce_submitter',
            'author_field' => null,
            'add_time_field' => 'ce_add_date',
            'edit_time_field' => 'ce_edit_date',
            'date_field' => 'ce_add_date',
            'validated_field' => 'ce_validated',

            'seo_type_code' => 'catalogue_entry',

            'feedback_type_code' => 'catalogues',

            'permissions_type_code' => (get_value('disable_cat_cat_perms') === '1') ? null : 'catalogues_category', // null if has no permissions

            'search_hook' => 'catalogue_entries',
            'rss_hook' => 'catalogues',
            'attachment_hook' => 'catalogue_entry',
            'unvalidated_hook' => 'catalogue_entry',
            'notification_hook' => 'catalogue_entry',
            'sitemap_hook' => 'catalogue_entry',

            'addon_name' => 'catalogues',

            'cms_page' => 'cms_catalogues',
            'module' => 'catalogues',

            'filtercode' => 'catalogues::_catalogues_filtercode',
            'filtercode_protected_fields' => array(), // These are ones even some staff should never know

            'commandr_filesystem_hook' => 'catalogues',
            'commandr_filesystem__is_folder' => false,

            'support_revisions' => false,

            'support_privacy' => true,

            'support_content_reviews' => true,

            'actionlog_regexp' => '\w+_CATALOGUE_ENTRY',
        );
    }

    /**
     * Run function for content hooks. Renders a content box for an award/randomisation.
     *
     * @param  array $row The database row for the content
     * @param  ID_TEXT $zone The zone to display in
     * @param  boolean $give_context Whether to include context (i.e. say WHAT this is, not just show the actual content)
     * @param  boolean $include_breadcrumbs Whether to include breadcrumbs (if there are any)
     * @param  ?ID_TEXT $root Virtual root to use (null: none)
     * @param  boolean $attach_to_url_filter Whether to copy through any filter parameters in the URL, under the basis that they are associated with what this box is browsing
     * @param  ID_TEXT $guid Overridden GUID to send to templates (blank: none)
     * @return Tempcode Results
     */
    public function run($row, $zone, $give_context = true, $include_breadcrumbs = true, $root = null, $attach_to_url_filter = false, $guid = '')
    {
        require_code('catalogues');

        return render_catalogue_entry_box($row, $zone, $give_context, $include_breadcrumbs, is_null($root) ? null : intval($root), $guid);
    }
}

/**
 * Find a catalogue entry title.
 *
 * @param  array $url_parts The URL parts to search from.
 * @param  boolean $resource_fs_style Whether to get the field title using resource-fs style.
 * @return string The field title.
 */
function generate_catalogue_entry_title($url_parts, $resource_fs_style = false)
{
    $catalogue_name = mixed();
    $fields = mixed();

    $unique_key_num = 0;
    if ($resource_fs_style) {
        $catalogue_name = $GLOBALS['SITE_DB']->query_select_value('catalogue_entries', 'c_name', array('id' => intval($url_parts['id'])));
        $fields = $GLOBALS['SITE_DB']->query_select('catalogue_fields', array('*'), array('c_name' => $catalogue_name), 'ORDER BY cf_order,' . $GLOBALS['SITE_DB']->translate_field_ref('cf_name'));
        foreach ($fields as $i => $f) {
            if ($f['cf_type'] == 'codename') {
                $unique_key_num = $i;
                break;
            }
        }
    }

    require_code('catalogues');
    $field_values = get_catalogue_entry_field_values($catalogue_name, intval($url_parts['id']), array($unique_key_num), $fields);
    $field = $field_values[$unique_key_num];
    if (is_null($field)) {
        return uniqid('', true);
    }
    $value = $field['effective_value_pure'];
    return strip_comcode($value);
}

/**
 * Find a catalogue entry thumbnail.
 *
 * @param  array $url_parts The URL parts to search from.
 * @return string The field title.
 */
function generate_catalogue_thumb_field($url_parts)
{
    $unique_key_num = mixed();

    $catalogue_name = $GLOBALS['SITE_DB']->query_select_value('catalogue_entries', 'c_name', array('id' => intval($url_parts['id'])));
    $fields = $GLOBALS['SITE_DB']->query_select('catalogue_fields', array('*'), array('c_name' => $catalogue_name), 'ORDER BY cf_order,' . $GLOBALS['SITE_DB']->translate_field_ref('cf_name'));
    foreach ($fields as $i => $f) {
        if ($f['cf_type'] == 'picture') {
            $unique_key_num = $i;
            break;
        }
    }

    if ($unique_key_num === null) {
        return '';
    }

    require_code('catalogues');
    $field_values = get_catalogue_entry_field_values($catalogue_name, intval($url_parts['id']), array($unique_key_num), $fields);
    $field = $field_values[$unique_key_num];
    if (is_null($field)) {
        return '';
    }
    $value = $field['effective_value_pure'];
    return $value;
}
