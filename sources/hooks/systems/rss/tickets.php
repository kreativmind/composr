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
 * @package    tickets
 */

/**
 * Hook class.
 */
class Hook_rss_tickets
{
    /**
     * Run function for RSS hooks.
     *
     * @param  string $_filters A list of categories we accept from
     * @param  TIME $cutoff Cutoff time, before which we do not show results from
     * @param  string $prefix Prefix that represents the template set we use
     * @set    RSS_ ATOM_
     * @param  string $date_string The standard format of date to use for the syndication type represented in the prefix
     * @param  integer $max The maximum number of entries to return, ordering by date
     * @return ?array A pair: The main syndication section, and a title (null: error)
     */
    public function run($_filters, $cutoff, $prefix, $date_string, $max)
    {
        if (!addon_installed('tickets')) {
            return null;
        }

        if (!has_actual_page_access(get_member(), 'tickets')) {
            return null;
        }

        if (is_guest()) {
            return null;
        }

        require_code('tickets');
        require_code('tickets2');

        $ticket_types = selectcode_to_idlist_using_callback($_filters, '', null, null, null, null, false);
        if (count($ticket_types) != 0) {
            $rows = array();
            foreach ($ticket_types as $ticket_type_id) {
                if (!has_category_access(get_member(), 'tickets', strval($ticket_type_id))) {
                    continue;
                }
                $rows = array_merge($rows, get_tickets(get_member(), $ticket_type_id, false, false, true));
            }
        } else {
            $rows = get_tickets(get_member(), null, false, false, true);
        }

        require_code('feedback');

        $ticket_type_rows = collapse_2d_complexity('id', 'ticket_type_name', $GLOBALS['SITE_DB']->query_select('ticket_types', array('id', 'ticket_type_name')));

        $content = new Tempcode();
        foreach ($rows as $i => $row) {
            if ($i == $max) {
                break;
            }

            if ($row['lasttime'] < $cutoff) {
                continue;
            }

            $ticket_id = extract_topic_identifier($row['description']);
            $ticket_type_id = $GLOBALS['SITE_DB']->query_select_value_if_there('tickets', 'ticket_type', array('ticket_id' => $ticket_id));

            $author = $row['firstusername'];
            $date = date($date_string, $row['firsttime']);
            $edit_date = date($date_string, $row['lasttime']);

            $title = xmlentities($row['firsttitle']);
            $summary = xmlentities($row['firstpost']->evaluate());

            $category = '';
            $category_raw = '';
            if ((!is_null($ticket_type_id)) && (isset($ticket_type_rows[$ticket_type_id]))) {
                $category = get_translated_text($ticket_type_rows[$ticket_type_id]);
                $category_raw = strval($ticket_type_id);
            }

            $view_url = build_url(array('page' => 'tickets', 'type' => 'ticket', 'id' => $ticket_id), get_module_zone('tickets'), null, false, false, true);

            if (($prefix == 'RSS_') && (get_option('is_on_comments') == '1')) {
                $if_comments = do_template('RSS_ENTRY_COMMENTS', array('_GUID' => '32c536b95de70994d0a13cfed18aa6ec', 'COMMENT_URL' => $view_url, 'ID' => strval($ticket_id)));
            } else {
                $if_comments = new Tempcode();
            }

            $content->attach(do_template($prefix . 'ENTRY', array('VIEW_URL' => $view_url, 'SUMMARY' => $summary, 'EDIT_DATE' => $edit_date, 'IF_COMMENTS' => $if_comments, 'TITLE' => $title, 'CATEGORY_RAW' => $category_raw, 'CATEGORY' => $category, 'AUTHOR' => $author, 'ID' => $ticket_id, 'NEWS' => '', 'DATE' => $date), null, false, null, '.xml', 'xml'));
        }

        require_lang('tickets');
        return array($content, do_lang('SUPPORT_TICKETS'));
    }
}
