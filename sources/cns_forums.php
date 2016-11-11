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
 * @package    core_cns
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__cns_forums()
{
    global $USER_ACCESS_CACHE;
    $USER_ACCESS_CACHE = array();

    global $FORUM_GROUPINGS_TITLES_CACHE;
    $FORUM_GROUPINGS_TITLES_CACHE = null;

    global $FORUM_TREE_SECURE_CACHE;
    $FORUM_TREE_SECURE_CACHE = mixed();

    global $ALL_FORUMS_STRUCT_CACHE;
    $ALL_FORUMS_STRUCT_CACHE = null;
}

/**
 * Render a forum box.
 *
 * @param  array $row Forum row
 * @param  ID_TEXT $zone Zone to link through to
 * @param  boolean $give_context Whether to include context (i.e. say WHAT this is, not just show the actual content)
 * @param  boolean $include_breadcrumbs Whether to include breadcrumbs (if there are any)
 * @param  ?AUTO_LINK $root Virtual root to use (null: none)
 * @param  ID_TEXT $guid Overridden GUID to send to templates (blank: none)
 * @return Tempcode The forum box
 */
function render_forum_box($row, $zone = '_SEARCH', $give_context = true, $include_breadcrumbs = true, $root = null, $guid = '')
{
    if (is_null($row)) { // Should never happen, but we need to be defensive
        return new Tempcode();
    }

    require_lang('cns');

    $map = array('page' => 'forumview');
    if ($row['id'] != db_get_first_id()) {
        $map['id'] = $row['id'];
    }
    if (!is_null($root)) {
        $map['keep_forum_root'] = $root;
    }
    $url = build_url($map, get_module_zone('forumview'));

    $title = $give_context ? do_lang('CONTENT_IS_OF_TYPE', do_lang('FORUM'), $row['f_name']) : $row['f_name'];

    $breadcrumbs = mixed();
    if ($include_breadcrumbs) {
        $breadcrumbs = breadcrumb_segments_to_tempcode(cns_forum_breadcrumbs($row['id'], null, null, true, is_null($root) ? get_param_integer('keep_forum_root', null) : $root));
    }

    $just_forum_row = db_map_restrict($row, array('id', 'f_description'));

    $summary = get_translated_tempcode('f_forums', $just_forum_row, 'f_description', $GLOBALS['FORUM_DB']);

    $num_topics = $row['f_cache_num_topics'];
    $num_posts = $row['f_cache_num_posts'];

    $entry_details = new Tempcode();
    $entry_details->attach(do_lang_tempcode('FORUM_NUM_TOPICS', escape_html(integer_format($num_topics))));
    $entry_details->attach(do_lang_tempcode('LIST_SEP'));
    $entry_details->attach(do_lang_tempcode('FORUM_NUM_POSTS', escape_html(integer_format($num_posts))));

    return do_template('SIMPLE_PREVIEW_BOX', array(
        '_GUID' => ($guid != '') ? $guid : 'f61cd0ea4c2ac496da958a36f118495d',
        'ID' => strval($row['id']),
        'TITLE' => $title,
        'TITLE_PLAIN' => $row['f_name'],
        'SUMMARY' => $summary,
        'URL' => $url,
        'ENTRY_DETAILS' => protect_from_escaping($entry_details),
        'BREADCRUMBS' => $breadcrumbs,
        'FRACTIONAL_EDIT_FIELD_NAME' => $give_context ? null : 'name',
        'FRACTIONAL_EDIT_FIELD_URL' => $give_context ? null : '_SEARCH:admin_cns_forums:__edit_category:' . strval($row['id']),
        'RESOURCE_TYPE' => 'forum',
    ));
}

/**
 * Get SQL clause to limit a query to accessible forums.
 *
 * @param  ID_TEXT $field Field name.
 * @return string SQL clause.
 */
function get_forum_access_sql($field)
{
    $groups = _get_where_clause_groups(get_member());

    if (is_null($groups)) {
        return '1=1';
    }

    $perhaps = $GLOBALS['FORUM_DB']->query('SELECT DISTINCT category_name FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'group_category_access WHERE (' . $groups . ') AND ' . db_string_equal_to('module_the_name', 'forums') . ' UNION ALL SELECT DISTINCT category_name FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'member_category_access WHERE (member_id=' . strval((integer)get_member()) . ' AND active_until>' . strval(time()) . ') AND ' . db_string_equal_to('module_the_name', 'forums'), null, null, false, true);
    if (count($perhaps) == 0) {
        return '0=1';
    }

    $forums = $GLOBALS['FORUM_DB']->query_select('f_forums', array('id'));

    $or_list = '';
    foreach ($perhaps as $row) {
        if ($or_list != '') {
            $or_list .= ' OR ';
        }
        $or_list .= $field . '=' . strval((integer)$row['category_name']);
    }

    $perhaps2 = array_flip(array_map('intval', collapse_1d_complexity('category_name', $perhaps)));
    $not_list = '1=1';
    foreach ($forums as $forum) {
        if (!isset($perhaps2[$forum['id']])) {
            if ($not_list != '') {
                $not_list .= ' AND ';
            }
            $not_list .= $field . '<>' . strval($forum['id']);
        }
    }

    if (strlen($not_list) < strlen($or_list)) {
        return $not_list;
    }
    return '(' . $or_list . ')';
}

/**
 * Organise a list of forum rows into a tree structure.
 *
 * @param  array $all_forums The list of all forum rows (be aware that this will get modified for performance reasons).
 * @param  AUTO_LINK $forum_id The forum row that we are taking as the root of our current recursion.
 * @return array The child list of $forum_id.
 */
function cns_organise_into_tree(&$all_forums, $forum_id)
{
    $children = array();
    $all_forums_copy = $all_forums;
    foreach ($all_forums_copy as $i => $forum) {
        if ($forum['f_parent_forum'] == $forum_id) {
            $forum['children'] = cns_organise_into_tree($all_forums, $forum['id']);
            $children[$forum['id']] = $forum;
            unset($all_forums[$i]);
        }
    }
    return $children;
}

/**
 * Gets a list of subordinate forums of a certain forum.
 *
 * @param  AUTO_LINK $forum_id The ID of the forum we are finding subordinate forums of.
 * @param  ?string $create_or_list The field name to use in the OR list (null: do not make an OR list, return an array).
 * @param  ?array $tree The forum tree structure (null: unknown, it will be found using cns_organise_into_tree).
 * @param  boolean $ignore_permissions Whether to ignore permissions in this.
 * @return mixed The list (is either a true list, or an OR list).
 */
function cns_get_all_subordinate_forums($forum_id, $create_or_list = null, $tree = null, $ignore_permissions = false)
{
    if (is_null($forum_id)) {
        if (is_null($create_or_list)) {
            return array($forum_id);
        } else {
            return '(' . $create_or_list . ' IS NULL)';
        }
    }

    if (is_null($tree)) {
        global $ALL_FORUMS_STRUCT_CACHE;
        if (is_null($ALL_FORUMS_STRUCT_CACHE)) {
            $max_forum_detail = intval(get_option('max_forum_detail'));
            $huge_forums = $GLOBALS['FORUM_DB']->query_select_value('f_forums', 'COUNT(*)') > $max_forum_detail;
            if ($huge_forums) {
                $max_forum_inspect = intval(get_option('max_forum_inspect'));

                $all_descendant = $GLOBALS['FORUM_DB']->query('SELECT id,f_parent_forum FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_forums WHERE id=' . strval($forum_id) . ' OR f_parent_forum=' . strval($forum_id), $max_forum_inspect);
                if (count($all_descendant) == $max_forum_inspect) { // Too many
                    if (is_null($create_or_list)) {
                        return array($forum_id);
                    } else {
                        return '(' . $create_or_list . '=' . strval($forum_id) . ')';
                    }
                }
                $tree = cns_organise_into_tree($all_descendant, $forum_id);
            } else {
                $ALL_FORUMS_STRUCT_CACHE = $GLOBALS['FORUM_DB']->query_select('f_forums');
                $all_forum_struct_copy = $ALL_FORUMS_STRUCT_CACHE;
                $tree = cns_organise_into_tree($all_forum_struct_copy, $forum_id);
            }
        } else {
            $all_forum_struct_copy = $ALL_FORUMS_STRUCT_CACHE;
            $tree = cns_organise_into_tree($all_forum_struct_copy, $forum_id);
        }
    }

    $subordinates = array();
    foreach ($tree as $subordinate) {
        $subordinates = $subordinates + cns_get_all_subordinate_forums($subordinate['id'], null, $subordinate['children'], $ignore_permissions);
    }
    load_up_all_module_category_permissions(get_member(), 'forums');
    if (($ignore_permissions) || (has_category_access(get_member(), 'forums', strval($forum_id)))) {
        $subordinates[$forum_id] = $forum_id;
    }

    if (!is_null($create_or_list)) {
        $or_list = '';
        foreach ($subordinates as $subordinate) {
            if ($or_list != '') {
                $or_list .= ' OR ';
            }
            $or_list .= $create_or_list . '=' . strval($subordinate);
        }
        if ($or_list == '') {
            return $or_list;
        }
        return '(' . $or_list . ')';
    }

    return $subordinates;
}

/* Interesting function, not currently needed
function cns_is_up_to_date_on_forum($forum_id, $member_id = null)
{
    $_last_topic = $GLOBALS['FORUM_DB']->query_select('f_forums', array('f_cache_last_time', 'f_cache_last_topic_id'), array('id' => $forum_id));
    if (!array_key_exists(0, $_last_topic)) {
        return false; // Data error, but let's just trip past
    }
    $topic_last_time = $_last_topic[0]['f_cache_last_time'];
    $topic_id = $_last_topic[0]['f_cache_last_topic_id'];
    return cns_has_read_topic($topic_id, $topic_last_time, $member_id);
}
*/

/**
 * Find whether a member may moderate a certain forum.
 *
 * @param  AUTO_LINK $forum_id The ID of the forum.
 * @param  ?MEMBER $member_id The member ID (null: current member).
 * @return boolean The answer.
 */
function cns_may_moderate_forum($forum_id, $member_id = null)
{
    if (is_null($member_id)) {
        $member_id = get_member();
    }

    if (is_null($forum_id)) {
        return has_privilege($member_id, 'moderate_private_topic');
    }

    return has_privilege($member_id, 'edit_midrange_content', 'topics', array('forums', $forum_id));
}

/**
 * Get an OR list of a forums parents, suited for selection from the f_topics table.
 *
 * @param  AUTO_LINK $forum_id The ID of the forum.
 * @param  ?AUTO_LINK $parent_id The ID of the parent forum (-1: get it from the DB) (null: there is no parent, as it is the root forum).
 * @return string The OR list.
 */
function cns_get_forum_parent_or_list($forum_id, $parent_id = -1)
{
    if (is_null($forum_id)) {
        return '';
    }

    if ($parent_id == -1) {
        $parent_id = $GLOBALS['FORUM_DB']->query_select_value('f_forums', 'f_parent_forum', array('id' => $forum_id));
        if (is_null($parent_id)) {
            return '';
        }
    }

    $from_below = cns_get_forum_parent_or_list($parent_id);
    $term = 't_forum_id=' . strval($forum_id);

    return $term . (($from_below != '') ? (' OR ' . $from_below) : '');
}

/**
 * Get breadcrumbs for a forum.
 *
 * @param  ?~mixed $end_point_forum The ID of the forum we are at in our path (null: end of recursion) (false: no forum ID available, this_name and parent_forum must not be null).
 * @param  ?mixed $this_name The name of the given forum as string or Tempcode (null: find it from the DB).
 * @param  ?AUTO_LINK $parent_forum The parent forum of the given forum (null: find it from the DB).
 * @param  boolean $start Whether this is being called as the recursion start of deriving the breadcrumbs (top level call).
 * @param  ?AUTO_LINK $root Virtual root (null: none).
 * @return Tempcode The breadcrumbs.
 * @return array The breadcrumbs.
 */
function cns_forum_breadcrumbs($end_point_forum, $this_name = null, $parent_forum = null, $start = true, $root = null)
{
    if (is_null($end_point_forum)) {
        return array();
    }

    if (is_null($root)) {
        $root = get_param_integer('keep_forum_root', db_get_first_id());
    }

    static $cache = array();
    if (isset($cache[$end_point_forum])) {
        return $cache[$end_point_forum];
    }

    if (is_null($this_name)) {
        $_forum_details = $GLOBALS['FORUM_DB']->query_select('f_forums', array('f_name', 'f_parent_forum'), array('id' => $end_point_forum), '', 1);
        if (!array_key_exists(0, $_forum_details)) {
            //warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html(strval($end_point_forum)), 'forum'));
            return array();
        }
        $forum_details = $_forum_details[0];
        $this_name = $forum_details['f_name'];
        $parent_forum = $forum_details['f_parent_forum'];
    }

    $segments = array();

    if (((!$start) || (has_privilege(get_member(), 'open_virtual_roots'))) && (is_integer($end_point_forum))) {
        $map = array('page' => 'forumview');
        if ($end_point_forum != db_get_first_id()) {
            $map['id'] = $end_point_forum;
        }
        $test = get_param_string('kfs' . strval($end_point_forum), null, true);
        if (($test !== null) && ($test !== '0')) {
            $map['kfs' . strval($end_point_forum)] = $test;
        }
        if ($start) {
            $map['keep_forum_root'] = $end_point_forum;
        }
        $page_link = build_page_link($map, get_module_zone('forumview'));
        $segments[] = array($page_link, $this_name, $start ? do_lang_tempcode('VIRTUAL_ROOT') : new Tempcode());
    } else {
        $segments[] = array('', $this_name);
    }

    if ($end_point_forum !== $root) {
        $out = cns_forum_breadcrumbs($parent_forum, null, null, false, $root);
    } else {
        $out = array();
    }

    $out = array_merge($out, $segments);

    if ($start) {
        $cache[$end_point_forum] = $out;
    }

    return $out;
}

/**
 * Whether a forum supports anonymous posts.
 *
 * @param  ?AUTO_LINK $forum_id The ID of the forum (null: private topics).
 * @return boolean Whether it does.
 */
function cns_forum_allows_anonymous_posts($forum_id)
{
    if (is_null($forum_id)) {
        return (get_option('is_on_anonymous_posts') == '1');
    }
    return ($GLOBALS['FORUM_DB']->query_select_value_if_there('f_forums', 'f_allows_anonymous_posts', array('id' => $forum_id)) === 1);
}
