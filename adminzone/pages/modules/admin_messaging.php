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
 * @package    staff_messaging
 */

/**
 * Module page class.
 */
class Module_admin_messaging
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
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
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        if ((get_forum_type() == 'cns') && (!running_script('upgrader'))) {
            $moderator_groups = $GLOBALS['FORUM_DRIVER']->get_moderator_groups();
            $staff_access = array();
            foreach ($moderator_groups as $id) {
                $staff_access[$id] = 5;
            }
            cns_require_all_forum_stuff();
            require_code('cns_forums_action');
            require_code('cns_forums_action2');
            $GLOBALS['CNS_DRIVER'] = $GLOBALS['FORUM_DRIVER'];
            require_lang('messaging');
            cns_make_forum(do_lang('MESSAGING_FORUM_NAME'), '', db_get_first_id() + 1, $staff_access, db_get_first_id());
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions.
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user).
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name).
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled).
     */
    public function get_entry_points($check_perms = true, $member_id = null, $support_crosslinks = true, $be_deferential = false)
    {
        return array(
            'browse' => array('CONTACT_US_MESSAGING', 'menu/adminzone/audit/messaging'),
        );
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        $type = get_param_string('type', 'browse');

        require_lang('messaging');

        set_helper_panel_tutorial('tut_support_desk');

        if ($type == 'view') {
            breadcrumb_set_parents(array(array('_SELF:_SELF:browse', do_lang_tempcode('CONTACT_US_MESSAGING'))));
            breadcrumb_set_self(do_lang_tempcode('MESSAGE'));
        }

        if ($type == 'take') {
            $id = get_param_string('id', false, INPUT_FILTER_GET_COMPLEX);
            $message_type = get_param_string('message_type');
            breadcrumb_set_parents(array(array('_SELF:_SELF:browse', do_lang_tempcode('CONTACT_US_MESSAGING')), array('_SELF:_SELF:view:' . $id . ':message_type=' . $message_type, do_lang_tempcode('MESSAGE'))));
            breadcrumb_set_self(do_lang_tempcode('_TAKE_RESPONSIBILITY'));
        }

        $this->title = get_screen_title('CONTACT_US_MESSAGING');

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        if (get_forum_type() == 'none') {
            warn_exit(do_lang_tempcode('NO_FORUM_INSTALLED'));
        }

        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            return $this->choose_message();
        }
        if ($type == 'view') {
            return $this->view_message();
        }
        if ($type == 'take') {
            return $this->take_responsibility();
        }

        return new Tempcode();
    }

    /**
     * Choose a message.
     *
     * @return Tempcode The message choose screen.
     */
    public function choose_message()
    {
        $fields = new Tempcode();

        $start = get_param_integer('start', 0);
        $max = get_param_integer('max', 30);

        require_code('templates_results_table');

        $max_rows = 0;
        $rows = $GLOBALS['FORUM_DRIVER']->show_forum_topics(get_option('messaging_forum_name'), $max, $start, $max_rows);
        if ($rows !== null) {
            foreach ($rows as $i => $row) {
                $name = $row['firsttitle'];
                if (trim($name) == '') {
                    $name = do_lang('UNKNOWN');
                }
                $looking_at = $row['title'];
                if ($row['description'] != '') {
                    $looking_at = $row['description'];
                }
                $id = substr($looking_at, strrpos($looking_at, '_') + 1);
                $message_type = substr($looking_at, strpos($looking_at, '#') + 1, strrpos($looking_at, '_') - strpos($looking_at, '#') - 1);
                if ($message_type == '') {
                    continue;
                }
                $url = build_url(array('page' => '_SELF', 'type' => 'view', 'id' => $id, 'message_type' => $message_type), '_SELF');

                $fields->attach(results_entry(array(hyperlink($url, $name, false, true), get_timezoned_date_time($row['firsttime']), $message_type), true));
            }
        }

        $fields_title = results_field_title(array(do_lang_tempcode('TITLE'), do_lang_tempcode('DATE'), do_lang_tempcode('TYPE')));
        $results_table = results_table('messages', $start, 'start', $max, 'max', $max_rows, $fields_title, $fields, null, null, null, null, paragraph(do_lang_tempcode('SELECT_A_MESSAGE')));

        $tpl = do_template('RESULTS_TABLE_SCREEN', array('_GUID' => '6ced89e25a12a45deb6cf10bd42869ee', 'TITLE' => $this->title, 'RESULTS_TABLE' => $results_table));

        require_code('templates_internalise_screen');
        return internalise_own_screen($tpl);
    }

    /**
     * View a message.
     *
     * @return Tempcode The message view screen.
     */
    public function view_message()
    {
        $id = get_param_string('id', false, INPUT_FILTER_GET_COMPLEX);
        $message_type = get_param_string('message_type');

        require_css('messaging');
        require_javascript('checking');

        $take_responsibility_url = build_url(array('page' => '_SELF', 'type' => 'take', 'id' => $id, 'message_type' => $message_type), '_SELF');
        $responsible = null;

        $forum = get_option('messaging_forum_name');

        // Filter/read comments
        require_code('feedback');
        actualise_post_comment(true, $message_type, $id, build_url(array('page' => '_SELF', 'type' => 'view', 'id' => $id), '_SELF', null, false, false, true), null, $forum);
        $count = 0;
        $_comments = $GLOBALS['FORUM_DRIVER']->get_forum_topic_posts($GLOBALS['FORUM_DRIVER']->find_topic_id_for_topic_identifier($forum, $message_type . '_' . $id, do_lang('COMMENT')), $count);
        if ((is_array($_comments)) && (array_key_exists(0, $_comments))) {
            $message_title = $_comments[0]['title'];
            $message = $_comments[0]['message'];
            if (isset($_comments[0]['message_comcode'])) {
                push_lax_comcode(true);
                $message = comcode_to_tempcode(str_replace('[/staff_note]', '', str_replace('[staff_note]', '', $_comments[0]['message_comcode'])), $GLOBALS['FORUM_DRIVER']->get_guest_id());
                pop_lax_comcode();
            }
            $by = $_comments[0]['username'];

            foreach ($_comments as $i => $comment) {
                if (is_object($comment['message'])) {
                    $comment['message'] = $comment['message']->evaluate();
                }
                if (substr($comment['message'], 0, strlen(do_lang('AUTO_SPACER_STUB'))) == do_lang('AUTO_SPACER_STUB')) {
                    $matches = array();
                    if (preg_match('#' . str_replace('\\{1\\}', '(.+)', preg_quote(do_lang('AUTO_SPACER_TAKE_RESPONSIBILITY'))) . '#', $comment['message'], $matches) != 0) {
                        $responsible = $matches[1];
                    }
                    $_comments[$i] = null;
                }
            }
            $_comments[0] = null;
        } else {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $comment_details = get_comments($message_type, true, $id, false, $forum, null, $_comments, true);

        // Find who's read this
        $whos_read = array();
        if (get_forum_type() == 'cns') {
            // Read - who has, and when
            $topic_id = $GLOBALS['FORUM_DRIVER']->find_topic_id_for_topic_identifier($forum, $message_type . '_' . $id, do_lang('COMMENT'));
            $rows = $GLOBALS['FORUM_DB']->query_select('f_read_logs', array('l_member_id', 'l_time'), array('l_topic_id' => $topic_id));
            foreach ($rows as $row) {
                if (is_guest($row['l_member_id'])) {
                    continue;
                }

                $username = $GLOBALS['FORUM_DRIVER']->get_username($row['l_member_id']);
                $member_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($row['l_member_id'], true);
                $date = get_timezoned_date_time($row['l_time']);
                $whos_read[] = array('USERNAME' => $username, 'MEMBER_ID' => strval($row['l_member_id']), 'MEMBER_URL' => $member_url, 'DATE' => $date);
            }
        }

        return do_template('MESSAGING_MESSAGE_SCREEN', array(
            '_GUID' => '61561f1a333b88370ceb66dbbcc0ea4c',
            'TITLE' => $this->title,
            'MESSAGE_TITLE' => $message_title,
            'MESSAGE' => $message,
            'BY' => $by,
            'WHOS_READ' => $whos_read,
            'COMMENT_DETAILS' => $comment_details,
            'TAKE_RESPONSIBILITY_URL' => $take_responsibility_url,
            'RESPONSIBLE' => $responsible,
        ));
    }

    /**
     * Take responsibility for handling a message.
     *
     * @return Tempcode Success message.
     */
    public function take_responsibility()
    {
        $id = get_param_string('id');
        $message_type = get_param_string('message_type');

        // Save as responsibility taken
        $forum = get_option('messaging_forum_name');
        $username = $GLOBALS['FORUM_DRIVER']->get_username(get_member());
        $displayname = $GLOBALS['FORUM_DRIVER']->get_username(get_member(), true);
        $GLOBALS['FORUM_DRIVER']->make_post_forum_topic(
            $forum,
            $message_type . '_' . $id,
            get_member(),
            '',
            do_lang('AUTO_SPACER_TAKE_RESPONSIBILITY', $username, $displayname),
            '',
            do_lang('COMMENT')
        );

        // Redirect them back to view screen
        $url = build_url(array('page' => '_SELF', 'type' => 'view', 'id' => $id, 'message_type' => $message_type), '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }
}
