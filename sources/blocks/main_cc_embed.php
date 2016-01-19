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
 * Block class.
 */
class Block_main_cc_embed
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array('select', 'param', 'filter', 'template_set', 'display_type', 'sorting', 'sort', 'max', 'start', 'pagination', 'root', 'as_guest');
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = '(preg_match(\'#<\w+>#\',(array_key_exists(\'filter\',$map)?$map[\'filter\']:\'\'))!=0)?null:array(array_key_exists(\'as_guest\',$map)?($map[\'as_guest\']==\'1\'):false,get_param_integer($block_id.\'_max\',array_key_exists(\'max\',$map)?intval($map[\'max\']):30),get_param_integer($block_id.\'_start\',array_key_exists(\'start\',$map)?intval($map[\'start\']):0),((array_key_exists(\'pagination\',$map)?$map[\'pagination\']:\'0\')==\'1\'),((array_key_exists(\'root\',$map)) && ($map[\'root\']!=\'\'))?intval($map[\'root\']):null,((array_key_exists(\'sorting\',$map)?$map[\'sorting\']:\'0\')==\'1\'),array_key_exists(\'select\',$map)?$map[\'select\']:\'\',get_param_string($block_id.\'_order\',array_key_exists(\'sort\',$map)?$map[\'sort\']:\'\'),array_key_exists(\'display_type\',$map)?$map[\'display_type\']:null,array_key_exists(\'template_set\',$map)?$map[\'template_set\']:\'\',array_key_exists(\'filter\',$map)?$map[\'filter\']:\'\',array_key_exists(\'param\',$map)?$map[\'param\']:db_get_first_id())';
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_PERMISSIVE_GROUPS;
        if (addon_installed('content_privacy')) {
            $info['special_cache_flags'] |= CACHE_AGAINST_MEMBER;
        }
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 60 * 2;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        $category_id = array_key_exists('param', $map) ? intval($map['param']) : db_get_first_id();
        $filter = array_key_exists('filter', $map) ? $map['filter'] : '';
        $do_sorting = ((array_key_exists('sorting', $map) ? $map['sorting'] : '0') == '1');

        require_lang('catalogues');
        require_code('catalogues');
        require_code('feedback');
        require_css('catalogues');

        // Selectcode
        $select = mixed();
        if ((!is_null($map)) && (array_key_exists('select', $map)) && ($map['select'] != '')) {
            require_code('selectcode');
            $select = selectcode_to_sqlfragment($map['select'], 'r.id', 'catalogue_categories', 'cc_parent_id', 'cc_id', 'id');
        }

        // Pick up details about category
        $categories = $GLOBALS['SITE_DB']->query_select('catalogue_categories', array('*'), array('id' => $category_id), '', 1);
        if (!array_key_exists(0, $categories)) {
            return do_lang_tempcode('MISSING_RESOURCE', 'catalogue_category');
        }
        $category = $categories[0];

        // Pick up details about catalogue
        $catalogue_name = $category['c_name'];
        $catalogue = load_catalogue_row($catalogue_name);

        $block_id = get_block_id($map);

        $sort = get_param_string($block_id . '_order', array_key_exists('sort', $map) ? $map['sort'] : '');
        if ($sort == '') {
            $sort = mixed();
        }
        $max = get_param_integer($block_id . '_max', array_key_exists('max', $map) ? intval($map['max']) : 30);
        $start = get_param_integer($block_id . '_start', array_key_exists('start', $map) ? intval($map['start']) : 0);
        $do_pagination = ((array_key_exists('pagination', $map) ? $map['pagination'] : '0') == '1');
        $root = ((array_key_exists('root', $map)) && ($map['root'] != '')) ? intval($map['root']) : get_param_integer('keep_catalogue_' . $catalogue_name . '_root', null);

        // Display type?
        $tpl_set = array_key_exists('template_set', $map) ? $map['template_set'] : $catalogue_name;
        $_display_type = ((array_key_exists('display_type', $map)) && ($map['display_type'] != '')) ? $map['display_type'] : null;
        $display_type = mixed();
        if (!is_null($_display_type)) {
            if (is_numeric($_display_type)) {
                $display_type = intval($_display_type);
            } else {
                switch ($_display_type) {
                    case 'FIELDMAPS':
                        $display_type = C_DT_FIELDMAPS;
                        break;
                    case 'TITLELIST':
                        $display_type = C_DT_TITLELIST;
                        break;
                    case 'TABULAR':
                        $display_type = C_DT_TABULAR;
                        break;
                    case 'GRID':
                        $display_type = C_DT_GRID;
                        break;
                }
            }
        } else {
            $display_type = $catalogue['c_display_type'];
        }

        // Get entries
        $as_guest = array_key_exists('as_guest', $map) ? ($map['as_guest'] == '1') : false;
        $viewing_member_id = $as_guest ? $GLOBALS['FORUM_DRIVER']->get_guest_id() : mixed();
        list($entry_buildup, $sorting, , $max_rows) = get_catalogue_category_entry_buildup(is_null($select) ? $category_id : null, $catalogue_name, $catalogue, 'CATEGORY', $tpl_set, $max, $start, $select, $root, $display_type, true, null, $filter, $sort, $block_id . '_order', $viewing_member_id);

        // Sorting and pagination
        if (!$do_sorting) {
            $sorting = new Tempcode();
        }
        $pagination = new Tempcode();
        if ($do_pagination) {
            require_code('templates_pagination');
            $pagination = pagination(do_lang_tempcode('ENTRIES'), $start, $block_id . '_start', $max, $block_id . '_max', $max_rows);
        }

        $display_type_str = '';
        switch ($display_type) {
            case C_DT_FIELDMAPS:
                $display_type_str = 'FIELDMAPS';
                break;
            case C_DT_TITLELIST:
                $display_type_str = 'TITLELIST';
                break;
            case C_DT_TABULAR:
                $display_type_str = 'TABULAR';
                break;
            case C_DT_GRID:
                $display_type_str = 'GRID';
                break;
        }

        $cart_link = new Tempcode();
        $is_ecommerce = is_ecommerce_catalogue($catalogue_name, $catalogue);
        if ($is_ecommerce) {
            if (get_forum_type() != 'cns') {
                warn_exit(do_lang_tempcode('NO_CNS'));
            }

            require_code('shopping');
            require_lang('shopping');

            $cart_link = show_cart_link();
        }

        // Render
        return do_template('CATALOGUE_' . $tpl_set . '_CATEGORY_EMBED', array(
            '_GUID' => 'dfdsfdsfsd3ffsdfsd',
            'BLOCK_PARAMS' => block_params_arr_to_str($map),
            'DISPLAY_TYPE' => $display_type_str,
            'ROOT' => is_null($root) ? '' : strval($root),
            'CATALOGUE' => $catalogue_name,
            'ENTRIES' => $entry_buildup,
            'SORTING' => $sorting,
            'PAGINATION' => $pagination,

            'CART_LINK' => $cart_link,

            'START' => strval($start),
            'MAX' => strval($max),
            'START_PARAM' => $block_id . '_start',
            'MAX_PARAM' => $block_id . '_max',
        ), null, false, 'CATALOGUE_DEFAULT_CATEGORY_EMBED');
    }
}
