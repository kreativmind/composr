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
 * @package    core
 */

/**
 * Initialise state variables for the special page type being requested.
 *
 * @param  ID_TEXT $special_page_type The special page type.
 * @set    query templates tree lang
 */
function initialise_special_page_types($special_page_type)
{
    disable_php_memory_limit();

    if ($special_page_type == 'templates') {
        global $RECORD_TEMPLATES_USED;
        $RECORD_TEMPLATES_USED = true;
    } elseif ($special_page_type == 'tree') {
        global $RECORD_TEMPLATES_TREE;
        $RECORD_TEMPLATES_TREE = true;
    } elseif (substr($special_page_type, 0, 12) == 'lang_content') {
        global $RECORD_LANG_STRINGS_CONTENT;
        /** A marker indicating whether all referenced content language strings need to be collected, so that the contextual editor knows what was used to generate the screen.
         *
         * @global boolean $RECORD_LANG_STRINGS_CONTENT
         */
        $RECORD_LANG_STRINGS_CONTENT = true;
    } elseif (substr($special_page_type, 0, 4) == 'lang') {
        global $RECORD_LANG_STRINGS;
        $RECORD_LANG_STRINGS = true;
    } elseif ($special_page_type == 'theme_images') {
        global $RECORD_THEME_IMAGES_CACHE;
        $RECORD_THEME_IMAGES_CACHE = true;
    } elseif ($special_page_type == 'ide_linkage') {
        global $RECORD_TEMPLATES_USED;
        $RECORD_TEMPLATES_USED = true;
    }
}

/**
 * Handle special page type output.
 *
 * @param  ID_TEXT $special_page_type The special page type.
 * @set    query templates tree lang
 * @param  Tempcode $out The normal script Tempcode output
 * @param  string $out_evaluated The normal script evaluated output
 */
function special_page_types($special_page_type, &$out, $out_evaluated)
{
    global $RECORDED_TEMPLATES_USED;

    if (php_function_allowed('set_time_limit')) {
        set_time_limit(280);
    }

    $middle_spt = new Tempcode();

    if (is_null($out_evaluated)) {
        $out->evaluate(); // False evaluation
    }

    // FUDGE: Yuck. We have to after-the-fact make it wide, and empty lots of internal caching to reset the state.
    $_GET['wide_high'] = '1';
    $_GET['wide'] = '1';
    $GLOBALS['PANELS_CACHE'] = array();
    $GLOBALS['IS_WIDE_HIGH_CACHE'] = 1;
    $GLOBALS['IS_WIDE_CACHE'] = 1;
    $GLOBALS['TEMPCODE_SETGET'] = array();
    $GLOBALS['LOADED_TPL_CACHE'] = array();
    $GLOBALS['HELPER_PANEL_TEXT'] = null;
    $GLOBALS['HELPER_PANEL_TUTORIAL'] = null;

    // CSS
    if (substr($special_page_type, -4) == '.css') {
        $url_map = array(
            'page' => 'admin_themes',
            'type' => 'edit_css',
            'theme' => $GLOBALS['FORUM_DRIVER']->get_theme(),
            'file' => $special_page_type,
            'keep_wide_high' => 1,
        );
        $url = build_url($url_map, get_module_zone('admin_themes'));

        require_code('site2');
        smart_redirect($url->evaluate());
    }

    // Sitemap Editor
    if ($special_page_type == 'sitemap') {
        $url = build_url(array('page' => 'admin_sitemap', 'type' => 'sitemap', 'id' => get_zone_name() . ':' . get_page_name()), get_module_zone('admin_sitemap'));

        require_code('site2');
        smart_redirect($url->evaluate());
    }

    // IDE linkage
    if ($special_page_type == 'ide_linkage') {
        $title = get_screen_title('IDE_LINKAGE');

        $file_links = new Tempcode();

        global $JAVASCRIPTS, $CSSS, $REQUIRED_CODE, $LANGS_REQUESTED;

        /*foreach (array_keys($JAVASCRIPTS) as $name) Already in list of templates
        {
            $txtmte_url = 'txmt://open?url=file://'.$name;
            $file_links->attach(do_template('INDEX_SCREEN_ENTRY', array('_GUID' => 'ef68ed85bfc07b45e1fe2d94bd2672f2', 'URL' = >$txtmte_url, 'NAME' => $name)));
        }*/

        foreach (array_keys($CSSS) as $name) {
            $search = find_template_place($name, get_site_default_lang(), $GLOBALS['FORUM_DRIVER']->get_theme(), '.css', 'css');
            if (!is_null($search)) {
                list($theme, $type, $suffix) = $search;
                $txtmte_url = 'txmt://open?url=file://' . get_file_base() . '/themes/' . $theme . '/' . $type . '/' . $name . $suffix;
                $file_links->attach(do_template('INDEX_SCREEN_ENTRY', array(
                    '_GUID' => 'c3d6bdf723918aae23541d91ebf09f0b',
                    'DISPLAY_STRING' => '(CSS)',
                    'URL' => $txtmte_url,
                    'NAME' => $name . $suffix,
                )));
            }
        }

        foreach (array_keys($REQUIRED_CODE) as $name) {
            $path_a = get_file_base() . '/' . ((strpos($name, '.php') === false) ? ('/sources_custom/' . $name . '.php') : $name);
            $path_b = get_file_base() . '/' . ((strpos($name, '.php') === false) ? ('/sources/' . $name . '.php') : str_replace('_custom', '', $name));

            if (file_exists($path_a)) {
                $txtmte_url = 'txmt://open?url=file://' . $path_a;
                $file_links->attach(do_template('INDEX_SCREEN_ENTRY', array(
                    '_GUID' => '3d99e7c51959f12cb1a935d302e0fac2',
                    'DISPLAY_STRING' => '(PHP)',
                    'URL' => $txtmte_url,
                    'NAME' => $name . (((strpos($name, '.php') === false) ? '.php' : '')),
                )));
            }
            if (file_exists($path_b)) {
                $txtmte_url = 'txmt://open?url=file://' . $path_b;
                $file_links->attach(do_template('INDEX_SCREEN_ENTRY', array(
                    '_GUID' => '6c9fbce894cc841776123781906ebd88',
                    'DISPLAY_STRING' => '(PHP)',
                    'URL' => $txtmte_url,
                    'NAME' => $name . (((strpos($name, '.php') === false) ? '.php' : '')),
                )));
            }
        }

        foreach (array_keys($LANGS_REQUESTED) as $name) {
            if (file_exists(get_file_base() . '/lang_custom/' . fallback_lang() . '/' . $name . '.ini')) {
                $txtmte_url = 'txmt://open?url=file://' . get_file_base() . '/lang_custom/' . fallback_lang() . '/' . $name . '.ini';
                $file_links->attach(do_template('INDEX_SCREEN_ENTRY', array(
                    '_GUID' => 'f04c9d10f87f7a728b8a347992340ee4',
                    'DISPLAY_STRING' => '(Language)',
                    'URL' => $txtmte_url,
                    'NAME' => $name . '.ini',
                )));
            }
            if (file_exists(get_file_base() . '/lang/' . fallback_lang() . '/' . $name . '.ini')) {
                $txtmte_url = 'txmt://open?url=file://' . get_file_base() . '/lang/' . fallback_lang() . '/' . $name . '.ini';
                $file_links->attach(do_template('INDEX_SCREEN_ENTRY', array(
                    '_GUID' => 'b41dfcb41b4fea5a12c25e880f7bccfd',
                    'DISPLAY_STRING' => '(Language)',
                    'URL' => $txtmte_url,
                    'NAME' => $name . '.ini',
                )));
            }
        }

        foreach (array_unique($RECORDED_TEMPLATES_USED) as $name) {
            $search = find_template_place(
                basename($name, '.' . get_file_extension($name)),
                get_site_default_lang(),
                $GLOBALS['FORUM_DRIVER']->get_theme(),
                '.' . get_file_extension($name),
                dirname($name)
            );
            if (!is_null($search)) {
                list($theme, $type) = $search;
                $txtmte_url = 'txmt://open?url=file://' . get_file_base() . '/themes/' . $theme . '/' . $type . '/' . basename($name);
                $file_links->attach(do_template('INDEX_SCREEN_ENTRY', array(
                    '_GUID' => 'c2a5f66b9d6564b30c506afafd59b676',
                    'DISPLAY_STRING' => '(Templates)',
                    'URL' => $txtmte_url,
                    'NAME' => $name . $suffix,
                )));
            }
        }

        $middle_spt = do_template('INDEX_SCREEN', array(
            '_GUID' => '7722ab1c391c86adccde04dbc0ef7ba9',
            'TITLE' => $title,
            'CONTENT' => $file_links,
            'PRE' => do_lang_tempcode('TXMT_PROTOCOL_EXPLAIN'),
            'POST' => '',
        ));
    }

    // Theme images mode
    if ($special_page_type == 'theme_images') {
        $title = get_screen_title('THEME_IMAGE_EDITING');

        $theme_images = new Tempcode();

        global $RECORDED_THEME_IMAGES;
        foreach (array_keys($RECORDED_THEME_IMAGES) as $theme_image_details) {
            list($id, $theme, $lang) = unserialize($theme_image_details);

            $url_map = array(
                'page' => 'admin_themes',
                'type' => 'edit_image',
                'theme' => is_null($theme) ? $GLOBALS['FORUM_DRIVER']->get_theme() : $theme,
                'lang' => $lang,
                'id' => $id,
            );
            $url = build_url($url_map, 'adminzone');

            $image = find_theme_image($id, false, false, $theme, $lang);
            if ($image == '') {
                continue;
            }

            $theme_images->attach(do_template('INDEX_SCREEN_FANCIER_ENTRY', array(
                '_GUID' => '65ea324fb12a488adae780915624a268',
                'IMG' => $image,
                'DESCRIPTION' => '',
                'URL' => $url,
                'NAME' => $id,
                'TARGET' => '_blank',
                'TITLE' => '',
            )));
        }

        $middle_spt = do_template('INDEX_SCREEN_FANCIER_SCREEN', array(
            '_GUID' => 'b16d40ad36f209b1a3559df6f1ebac55',
            'TITLE' => $title,
            'CONTENT' => $theme_images,
            'PRE' => do_lang_tempcode('CONTEXTUAL_EDITING_SCREEN'),
            'POST' => '',
        ));
    }

    // Content translation mode
    if (substr($special_page_type, 0, 12) == 'lang_content') {
        $map_a = get_file_base() . '/lang/langs.ini';
        $map_b = get_custom_file_base() . '/lang_custom/langs.ini';
        if (!file_exists($map_b)) {
            $map_b = $map_a;
        }
        require_code('files');
        $map = better_parse_ini_file($map_b);

        $lang_name = user_lang();
        if (array_key_exists($lang_name, $map)) {
            $lang_name = $map[$lang_name];
        }

        global $RECORDED_LANG_STRINGS_CONTENT;

        require_lang('lang');
        require_code('form_templates');

        $fields = new Tempcode();

        require_code('lang2');

        $names = find_lang_content_names(array_keys($RECORDED_LANG_STRINGS_CONTENT));

        foreach ($RECORDED_LANG_STRINGS_CONTENT as $key => $forum_db) {
            $value_found = get_translated_text($key, $forum_db ? $GLOBALS['FORUM_DB'] : $GLOBALS['SITE_DB']);
            if ($value_found != '') {
                $description = make_string_tempcode(escape_html($value_found));
                if ((get_option('google_translate_api_key') == '') || (user_lang() == get_site_default_lang())) {
                    $actions = new Tempcode();
                } else {
                    require_javascript('translate');
                    $actions = do_template('TRANSLATE_ACTION', array(
                        '_GUID' => '441cd96588b2a4f74e94003643262833',
                        'LANG_FROM' => get_site_default_lang(),
                        'LANG_TO' => user_lang(),
                        'NAME' => 'trans_' . strval($key),
                        'OLD' => $value_found,
                    ));
                }
                $description->attach($actions);
                $fields->attach(form_input_text(is_null($names[$key]) ? ('#' . strval($key)) : $names[$key], $description, 'trans_' . strval($key), $value_found, false));
            }
        }

        if ($fields->is_empty()) {
            inform_exit(do_lang_tempcode('NOTHING_TO_TRANSLATE'));
        }

        $title = get_screen_title('__TRANSLATE_CONTENT', true, array(escape_html($lang_name)));

        $post_url = build_url(array('page' => 'admin_lang', 'type' => '_content', 'contextual' => 1), 'adminzone');
        $hidden = form_input_hidden('redirect', get_self_url(true, true));
        $hidden = form_input_hidden('lang', user_lang());

        $middle_spt = do_template('FORM_SCREEN', array(
            '_GUID' => '0d4dd16b023d0a7960f3eac85f54ddc4',
            'SKIP_WEBSTANDARDS' => true,
            'TITLE' => $title,
            'HIDDEN' => $hidden,
            'FIELDS' => $fields,
            'URL' => $post_url,
            'TEXT' => do_lang_tempcode('CONTEXTUAL_EDITING_SCREEN'),
            'SUBMIT_ICON' => 'buttons__save',
            'SUBMIT_NAME' => do_lang_tempcode('SAVE'),
            'SUPPORT_AUTOSAVE' => true,
        ));
    } // Language mode
    elseif (substr($special_page_type, 0, 4) == 'lang') {
        $map_a = get_file_base() . '/lang/langs.ini';
        $map_b = get_custom_file_base() . '/lang_custom/langs.ini';
        if (!file_exists($map_b)) {
            $map_b = $map_a;
        }
        require_code('files');
        $map = better_parse_ini_file($map_b);

        $lang_name = user_lang();
        if (array_key_exists($lang_name, $map)) {
            $lang_name = $map[$lang_name];
        }

        global $RECORDED_LANG_STRINGS;
        require_lang('lang');
        require_code('form_templates');
        require_code('lang_compile');
        $fields = new Tempcode();
        $descriptions = get_lang_file_section(fallback_lang());
        foreach (array_keys($RECORDED_LANG_STRINGS) as $key) {
            $value_found = do_lang($key, null, null, null, null, false);
            $description = array_key_exists($key, $descriptions) ? make_string_tempcode($descriptions[$key]) : new Tempcode();
            if (!is_null($value_found)) {
                if ((get_option('google_translate_api_key') == '') || (user_lang() == get_site_default_lang())) {
                    $actions = new Tempcode();
                } else {
                    require_javascript('translate');
                    $actions = do_template('TRANSLATE_ACTION', array(
                        '_GUID' => '031eb918cb3bcaf4339130b46f8b1b8a',
                        'LANG_FROM' => get_site_default_lang(),
                        'LANG_TO' => user_lang(),
                        'NAME' => 'l_' . $key,
                        'OLD' => str_replace('\n', "\n", $value_found),
                    ));
                }
                $description->attach($actions);
                $fields->attach(form_input_text($key, $description, 'l_' . $key, str_replace('\n', "\n", $value_found), false));
            }
        }
        $title = get_screen_title('__TRANSLATE_CODE', true, array(escape_html($lang_name)));
        $post_url = build_url(array('page' => 'admin_lang', 'type' => '_code2'), 'adminzone');
        $hidden = form_input_hidden('redirect', get_self_url(true, true));
        $hidden = form_input_hidden('lang', user_lang());
        $middle_spt = do_template('FORM_SCREEN', array(
            '_GUID' => '47a2934eaec30ed5eea635d4c462cee0',
            'SKIP_WEBSTANDARDS' => true,
            'TITLE' => $title,
            'HIDDEN' => $hidden,
            'FIELDS' => $fields,
            'URL' => $post_url,
            'TEXT' => do_lang_tempcode('CONTEXTUAL_EDITING_SCREEN'),
            'SUBMIT_ICON' => 'buttons__save',
            'SUBMIT_NAME' => do_lang_tempcode('SAVE'),
            'SUPPORT_AUTOSAVE' => true,
        ));
    }

    // Template mode?
    if (($special_page_type == 'templates') || ($special_page_type == 'tree')) {
        require_lang('themes');

        global $RECORD_TEMPLATES_USED;
        $RECORD_TEMPLATES_USED = false;
        $templates = new Tempcode();

        if ($special_page_type == 'templates') {
            $title = get_screen_title('TEMPLATES');

            $_RECORDED_TEMPLATES_USED = array_count_values($RECORDED_TEMPLATES_USED);
            ksort($_RECORDED_TEMPLATES_USED);
            foreach ($_RECORDED_TEMPLATES_USED as $name => $count) {
                $edit_url_map = array(
                    'page' => 'admin_themes',
                    'type' => '_edit_templates',
                    'theme' => $GLOBALS['FORUM_DRIVER']->get_theme(),
                    'f0file' => $name,
                );
                $edit_url = build_url($edit_url_map, 'adminzone', null, false, true);

                $templates->attach(do_template('TEMPLATE_LIST_ENTRY', array(
                    '_GUID' => '67fb5ac96a4ab2103ae82f9be7c43e24',
                    'COUNT' => integer_format($count),
                    'NAME' => $name,
                    'EDIT_URL' => $edit_url,
                )));
            }
        } else { // tree
            $title = get_screen_title('TEMPLATE_TREE');

            $hidden = new Tempcode();
            global $CSSS, $JAVASCRIPTS;
            foreach (array_keys($CSSS) as $c) {
                if (substr($c, 0, 8) != 'merged__') {
                    $hidden->attach(form_input_hidden('f' . strval(mt_rand(0, mt_getrandmax())) . 'file', 'css/' . $c . '.css'));
                }
            }
            foreach (array_keys($JAVASCRIPTS) as $c) {
                if (substr($c, 0, 8) != 'merged__') {
                    $hidden->attach(form_input_hidden('f' . strval(mt_rand(0, mt_getrandmax())) . 'file', 'javascript/' . $c . '.js'));
                }
            }

            $edit_url_map = array(
                'page' => 'admin_themes',
                'type' => '_edit_templates',
                'preview_url' => get_self_url(true, false, array('special_page_type' => null)),
                'theme' => $GLOBALS['FORUM_DRIVER']->get_theme(),
            );
            $edit_url = build_url($edit_url_map, 'adminzone', null, false, true);

            $tree = find_template_tree_nice($out->codename, $out->children, $out->fresh);

            $templates = do_template('TEMPLATE_TREE', array(
                '_GUID' => 'ff2a2233b8b4045ba4d8777595ef64c7',
                'HIDDEN' => $hidden,
                'EDIT_URL' => $edit_url,
                'TREE' => $tree,
            ));
        }

        $middle_spt = do_template('TEMPLATE_LIST_SCREEN', array(
            '_GUID' => 'ab859f67dcb635fcb4d1747d3c6a2c17',
            'TITLE' => $title,
            'TEMPLATES' => $templates,
        ));
    }

    // Query mode?
    if ($special_page_type == 'query') {
        require_lang('profiling');

        global $QUERY_LIST;
        $queries = new Tempcode();
        $total_time = 0.0;
        $sort_order = get_param_string('query_sort', 'time');
        switch ($sort_order) {
            case 'time':
                sort_maps_by($QUERY_LIST, 'time');
                break;
            case 'text':
                sort_maps_by($QUERY_LIST, 'text');
                break;
        }
        $QUERY_LIST = array_reverse($QUERY_LIST);
        foreach ($QUERY_LIST as $query) {
            $queries->attach(do_template('QUERY_LOG', array(
                '_GUID' => 'ab88e1e92609136229ad920c30647647',
                'TIME' => float_format($query['time'], 3),
                'ROWS' => is_null($query['rows']) ? '' : integer_format($query['rows']),
                'TEXT' => $query['text']
            )));
            $total_time += $query['time'];
        }
        $title = get_screen_title('VIEW_PAGE_QUERIES');
        $total = count($QUERY_LIST);
        $middle_spt = do_template('QUERY_SCREEN', array(
            '_GUID' => '5f679c8f657b4e4ae94ae2d0ed4843fa',
            'TITLE' => $title,
            'TOTAL' => integer_format($total),
            'TOTAL_TIME' => float_format($total_time, 3),
            'QUERIES' => $queries,
        ));
    }

    $echo = globalise($middle_spt, null, '', true);
    $echo->evaluate_echo();

    exit();
}

/**
 * Convert a template tree structure into a HTML representation.
 *
 * @param  ID_TEXT $codename The codename of the current template item in the recursion
 * @param  array $children The template tree structure for children
 * @param  boolean $fresh Whether the template tree came from a cache (if so, we can take some liberties with it's presentation)
 * @param  boolean $cache_started As $fresh, except something underneath at any unknown point did come from the cache, so this must have by extension
 * @return string HTML representation
 */
function find_template_tree_nice($codename, $children, $fresh, $cache_started = false)
{
    // Basic node rendering
    if ($codename == '') {
        $source = make_string_tempcode('?');
    } elseif ($codename[0] == ':') {
        $source = make_string_tempcode(substr($codename, 1));
    } elseif ($codename == '(mixed)') {
        $source = make_string_tempcode($codename);
    } else {
        if (strpos($codename, '/') === false ) {
            $file = 'templates/' . $codename . '.tpl';
        } else {
            $file = $codename;
            $codename = basename($codename, '.tpl');
        }

        $guid = mixed();
        foreach ($children as $child) {
            if ($child[0] == ':guid') {
                $guid = substr($child[1][0][0], 1);
            }
        }

        $edit_url_map = array(
            'page' => 'admin_themes',
            'type' => '_edit_templates',
            'f0file' => $file,
            'f0guid' => $guid,
            'preview_url' => get_self_url(true, false, array('special_page_type' => null)),
            'theme' => $GLOBALS['FORUM_DRIVER']->get_theme(),
        );
        $edit_url = build_url($edit_url_map, 'adminzone');

        $source = do_template('TEMPLATE_TREE_ITEM', array(
            '_GUID' => 'be8eb00699631677d459b0f7c5ba60c8',
            'FILE' => $file,
            'EDIT_URL' => $edit_url,
            'CODENAME' => $codename,
            'GUID' => $guid,
            'ID' => strval(mt_rand(0, mt_getrandmax())),
        ));
    }
    $out = $source->evaluate();

    // Now for the children...

    $middle = '';

    // Simplify down
    do {
        $_children = array();
        foreach ($children as $child) {
            if (
                (
                    (count($child[1]) != 0)
                    ||
                    ((strlen($child[0]) != 0) && ($child[0][0] != ':'))
                )
                &&
                (substr($child[0], 0, 5) != ':set:') // Not this as it rebinds all owner template properties, meaning huge template tree repetition
            ) {
                $_children[] = $child;
            }
        }
        $children = $_children;
    } while ((count($children) == 1) && ($children[0][0] == ':container'));

    // Remove duplicates
    $children_unique = array();
    foreach ($children as $child) {
        $children_unique[serialize($child)] = $child;
    }

    foreach ($children_unique as $child) {
        $middle2 = find_template_tree_nice($child[0], $child[1], $child[2], $cache_started || !$fresh);
        if ($middle2 != '') {
            $_middle = do_template('TEMPLATE_TREE_ITEM_WRAP', array('_GUID' => '59f003e298db3b621132649d2e315f9d', 'CONTENT' => $middle2));
            $middle .= $_middle->evaluate();
        }
    }
    if (($middle == '') && ((strlen($codename) == 0) || ($codename[0] == ':'))) {
        return '';
    }
    if ($middle != '') {
        $_out = do_template('TEMPLATE_TREE_NODE', array('_GUID' => 'ff937cbe28f1988af9fc7861ef01ffee', 'ITEMS' => $middle));
        $out .= $_out->evaluate();
    }

    return $out;
}

/**
 * Takes the output from the scripts, and check the XHTML for conformance, then echoes the page, plus the webstandards checking results.
 *
 * @param  string $out The XHTML to check
 * @param  boolean $display_regardless Display XHTML output regardless of whether there was an error or not
 * @param  integer $preview_mode Whether we are opening up an XHTML-fragment in a preview box (0 means no, 1 means yes, 2 means we are asking for additional manual check information)
 * @set 0 1 2
 * @param  boolean $ret Whether to return Tempcode
 * @return string Returned result (won't return it $ret is false)
 */
function check_xhtml_webstandards($out, $display_regardless = false, $preview_mode = 0, $ret = false)
{
    if ((!$display_regardless) && ($preview_mode == 0)) {
        $hash = md5($out);
        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('webstandards_checked_once', 'hash', array('hash' => $hash));
        if (!is_null($test)) {
            return '';
        }
    }

    require_lang('webstandards');
    require_css('webstandards');
    require_code('obfuscate');
    require_code('webstandards');

    global $EXTRA_CHECK;
    $show = false;
    do {
        $error = check_xhtml(
            $out,
            get_option('webstandards_xhtml') != '1',
            $preview_mode != 0,
            get_option('webstandards_javascript') == '1',
            get_option('webstandards_css') == '1',
            get_option('webstandards_wcag') == '1',
            get_option('webstandards_compat') == '1',
            get_option('webstandards_ext_files') == '1',
            $display_regardless || ($preview_mode == 2)
        );
        $show = (count($error['errors']) != 0) || ($display_regardless);
        if ((!$show) && (get_option('webstandards_ext_files') == '1')) {
            $out = array_pop($EXTRA_CHECK);
        }
    } while ((!$show) && (!is_null($out)) && (get_option('webstandards_ext_files') == '1'));

    if ($show) {
        return display_webstandards_results($out, $error, $preview_mode != 0, $ret);
    } elseif ($preview_mode == 0) {
        $GLOBALS['SITE_DB']->query_insert('webstandards_checked_once', array('hash' => $hash), false, true);
    }
    return '';
}

/**
 * Show results of running a webstandards checking function.
 *
 * @param  string $out The data that was checked
 * @param  array $error Error information
 * @param  boolean $preview_mode Whether we are opening up an XHTML-fragment in a preview box
 * @param  boolean $ret Whether to return Tempcode
 * @return string Returned result (won't return it $ret is false)
 */
function display_webstandards_results($out, $error, $preview_mode = false, $ret = false)
{
    global $KEEP_MARKERS, $SHOW_EDIT_LINKS;
    $KEEP_MARKERS = false;
    $SHOW_EDIT_LINKS = false;

    global $XHTML_SPIT_OUT;
    $XHTML_SPIT_OUT = true;

    if (php_function_allowed('set_time_limit')) {
        set_time_limit(280);
    }

    require_css('webstandards');

    ob_start();

    $title = get_screen_title('WEBSTANDARDS_ERROR');

    // Escape and colourfy
    $i = 0;

    // Output header
    if (count($_POST) == 0) {
        if (get_param_integer('keep_markers', 0) == 1) {
            $messy_url = new Tempcode();
        } else {
            $messy_url = build_url(array('page' => '_SELF', 'special_page_type' => 'code', 'keep_markers' => 1), '_SELF', null, true);
        }
        $ignore_url = build_url(array('page' => '_SELF', 'keep_webstandards_check' => 0), '_SELF', null, true);
        $ignore_url_2 = build_url(array('page' => '_SELF', 'webstandards_check' => 0), '_SELF', null, true);
    } else {
        $messy_url = new Tempcode();
        $ignore_url = new Tempcode();
        $ignore_url_2 = new Tempcode();
    }
    $error_lines = array();
    $return_url = new Tempcode();
    if (count($error['errors']) != 0) {
        $errorst = new Tempcode();
        foreach ($error['errors'] as $j => $_error) {
            $errorst->attach(do_template('WEBSTANDARDS_ERROR', array(
                '_GUID' => '2239470f4b9bd38fcb570689cecaedd2',
                'I' => strval($j),
                'LINE' => integer_format($_error['line']),
                'POS' => integer_format($_error['pos']),
                'ERROR' => $_error['error'],
            )));
            $error_lines[$_error['line']] = 1;
        }
        $errors = $errorst->evaluate();
        $echo = do_template('WEBSTANDARDS_ERROR_SCREEN', array(
            '_GUID' => 'db6c362632471e7c856380d32da91054',
            'MSG' => do_lang_tempcode('NEXT_ITEM_BACK'),
            'RETURN_URL' => $return_url,
            'TITLE' => $title,
            'IGNORE_URL_2' => $ignore_url_2,
            'IGNORE_URL' => $ignore_url,
            'MESSY_URL' => $messy_url,
            'ERRORS' => $errorst,
            'RET' => $ret,
        ));
        unset($errorst);
        $echo->evaluate_echo();
    } else {
        $echo = do_template('WEBSTANDARDS_SCREEN', array(
            '_GUID' => 'd8de848803287e4c592418d57450b7db',
            'MSG' => do_lang_tempcode('NEXT_ITEM_BACK'),
            'RETURN_URL' => $return_url,
            'TITLE' => get_screen_title('VIEWING_SOURCE'),
            'MESSY_URL' => $messy_url,
            'RET' => $ret,
        ));
        $echo->evaluate_echo();
    }

    $level_ranges = $error['level_ranges'];
    $tag_ranges = $error['tag_ranges'];
    $value_ranges = $error['value_ranges'];

    $current_range = 0;
    $current_tag = 0;
    $current_value = 0;
    $number = 1;
    $in_at = false;

    for ($i = 0; $i < strlen($out); ++$i) {
        if (isset($level_ranges[$current_range])) {
            $level = $level_ranges[$current_range][0];
            $start = $level_ranges[$current_range][1];
            if ($start == 0) {
                $start = 1; // Hack for when error starts before a line, messing up our output
            }

            if ($i == $start) { // Add in a font tag
                $x = 8;
                if ($level % $x == 0) {
                    $colour = 'teal';
                }
                if ($level % $x == 1) {
                    $colour = 'blue';
                }
                if ($level % $x == 2) {
                    $colour = 'purple';
                }
                if ($level % $x == 3) {
                    $colour = 'gray';
                }
                if ($level % $x == 4) {
                    $colour = 'red';
                }
                if ($level % $x == 5) {
                    $colour = 'maroon';
                }
                if ($level % $x == 6) {
                    $colour = 'navy';
                }
                if ($level % $x == 7) {
                    $colour = 'olive';
                }
                $previous = ($i == 0) ? '' : $out[$i - 1];
                $string = new Tempcode();
                if (($previous == ' ') || ($previous == "\n") || ($previous == "\r")) {
                    $string->attach(str_pad('', $level * 3 * 6, '&nbsp;'));
                }
                $string->attach(do_template('WEBSTANDARDS_TAG_START', array('_GUID' => '3a4c99283d32006143fc688ce8f2cadc', 'COLOUR' => $colour)));
                $string->evaluate_echo();
            }
        }

        if (isset($tag_ranges[$current_tag])) {
            $start = $tag_ranges[$current_tag][0];

            if ($i == $start) { // Add in a strong tag
                $string = do_template('WEBSTANDARDS_TAG_NAME_START');
                $string->evaluate_echo();
            }
        }

        if (isset($value_ranges[$current_value])) {
            $start = $value_ranges[$current_value][0];

            if ($i == $start) { // Add in a em tag
                $in_at = true;
                $string = do_template('WEBSTANDARDS_ATTRIBUTE_START');
                $string->evaluate_echo();
            }
        }

        $char = $out[$i];

        if (($char == "\n") || ($i == 0)) {
            if ($number > 1) {
                $escaped_code = do_template('WEBSTANDARDS_LINE_END');
                $escaped_code->evaluate_echo();
            }
            if (preg_match('#^\s*\n#', substr($out, $i + 1)) != 0) {
                // Do not show blank lines
                ++$number;
                continue;
            }
            if (isset($error_lines[$number])) {
                $markers = new Tempcode();
                foreach ($error['errors'] as $j => $_error) {
                    if ($number == $_error['line']) {
                        $markers->attach(do_template('WEBSTANDARDS_MARKER', array('_GUID' => '4b1898d5f1e0f56d18a47561659da3bb', 'I' => strval($j), 'ERROR' => $_error['error'])));
                    }
                }
                $escaped_code = do_template('WEBSTANDARDS_LINE_ERROR', array('_GUID' => '2ffa5c26090d3d814206e3a9e46c7b4e', 'MARKERS' => $markers, 'NUMBER' => integer_format($number)));
                $escaped_code->evaluate_echo();
            } else {
                $escaped_code = do_template('WEBSTANDARDS_LINE_START', array('_GUID' => '4994f4748c3cd0cbf4e9278ca0e9b1fc', 'NUMBER' => integer_format($number)));
                $escaped_code->evaluate_echo();
            }
            ++$number;
        }

        // Marker
        $end_markers = new Tempcode();
        if (isset($error_lines[$number])) {
            foreach ($error['errors'] as $_error) {
                if ($i == $_error['global_pos']) {
                    $_text = do_template('WEBSTANDARDS_MARKER_START');
                    $_text->evaluate_echo();
                    if ($char == "\r" || ($char == "\n")) {
                        $__text = '!' . do_lang('HERE') . '!';
                        if (function_exists('ocp_mark_as_escaped')) {
                            ocp_mark_as_escaped($__text);
                        }
                        echo $__text;
                    }

                    $end_markers->attach(do_template('WEBSTANDARDS_MARKER_END'));
                }
            }
        }

        // Escaping
        if ($char == '&') {
            $char = '&amp;';
        }
        elseif ($char == '<') {
            $char = '&lt;';
        }
        elseif ($char == '>') {
            $char = '&gt;';
        }
        elseif ($char == '"') {
            $char = '&quot;';
        }
        elseif ($char == '\'') {
            $char = '&#039;';
        }
        if ((is_null($level_ranges)) && ($char == ' ')) {
            $char = '&nbsp;';
        }
        if ((is_null($level_ranges)) && ($char == "\t")) {
            $char = '&nbsp;&nbsp;&nbsp;';
        }
        //if ($char == ' ') $char = '&nbsp;';
        if (function_exists('ocp_mark_as_escaped')) {
            ocp_mark_as_escaped($char);
        }
        echo $char;

        // Marker
        $end_markers->evaluate_echo();

        if (isset($value_ranges[$current_value])) {
            $end = $value_ranges[$current_value][1];

            if ($i == $end - 1) {
                if ($in_at) {
                    $text = do_template('WEBSTANDARDS_ATTRIBUTE_END');
                    $text->evaluate_echo();
                }
                $in_at = false;
                ++$current_value;
            }
        }

        if (isset($level_ranges[$current_range])) {
            $end = $level_ranges[$current_range][2];

            if ($i == $end - 1) {
                $string = do_template('WEBSTANDARDS_TAG_END');
                $string->evaluate_echo();
                ++$current_range;
                while ((isset($level_ranges[$current_range])) && ($level_ranges[$current_range][1] <= $i)) {
                    ++$current_range;
                }
            }
        }

        if (isset($tag_ranges[$current_tag])) {
            $end = $tag_ranges[$current_tag][1];

            if ($i == $end - 1) {
                $string = do_template('WEBSTANDARDS_TAG_NAME_END');
                $string->evaluate_echo();
                ++$current_tag;
            }
        }
    }

    if ($number > 1) {
        $escaped_code = do_template('WEBSTANDARDS_LINE_END');
        $escaped_code->evaluate_echo();
    }

    $echo = do_template('WEBSTANDARDS_SCREEN_END', array('_GUID' => '739514a06ae65252293fc62b1c7cec40', 'RET' => $ret));
    $echo->evaluate_echo();
    if (!$ret) {
        $echo = globalise(make_string_tempcode(ob_get_contents()), null, '', true);
        ob_end_clean();
        $echo->evaluate_echo();
        exit();
    }
    $out = ob_get_clean();
    return $out;
}

/**
 * Attach a message showing memory usage.
 *
 * @param  Tempcode $messages_bottom Where to place the message.
 */
function attach_message_memory_usage(&$messages_bottom)
{
    if (function_exists('memory_get_peak_usage')) {
        $memory_usage = memory_get_peak_usage();
    } else {
        $memory_usage = memory_get_usage();
    }
    $messages_bottom->attach(do_template('MESSAGE', array(
        '_GUID' => 'd605c0d111742a8cd2d4ef270a1e5fe1',
        'TYPE' => 'inform',
        'MESSAGE' => do_lang_tempcode('MEMORY_USAGE', escape_html(float_format(floatval($memory_usage) / 1024.0 / 1024.0, 2))),
    )));
}
