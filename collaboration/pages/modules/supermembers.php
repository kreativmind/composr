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
 * @package    supermember_directory
 */

/**
 * Module page class.
 */
class Module_supermembers
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
        $info['update_require_upgrade'] = 1;
        $info['locked'] = false;
        return $info;
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
            '!' => array('SUPER_MEMBERS', 'menu/collaboration/supermembers'),
        );
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        if ($upgrade_from == 2) {
            set_option('supermembers_text', '[html]' . get_option('supermembers_text') . '[/html]');
            return;
        }
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

        require_lang('supermembers');

        $this->title = get_screen_title('SUPER_MEMBERS');

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        if (addon_installed('authors')) {
            require_lang('authors');
        }
        if (addon_installed('points')) {
            require_lang('points');
        }

        $message = get_option('supermembers_text');
        if (has_actual_page_access(get_member(), 'admin_config')) {
            if ($message != '') {
                $message .= ' [semihtml]<span class="associated_link"><a href="{$PAGE_LINK*,_SEARCH:admin_config:category:SECURITY#group_SUPER_MEMBERS}">' . do_lang('EDIT') . '</a></span>[/semihtml]'; // XHTMLXHTML
            }
        }
        $text = comcode_to_tempcode($message, null, true);

        $supermember_groups = collapse_1d_complexity('group_id', $GLOBALS['SITE_DB']->query_select('group_zone_access', array('group_id'), array('zone_name' => get_zone_name())));
        $supermember_groups = array_merge($supermember_groups, $GLOBALS['FORUM_DRIVER']->get_super_admin_groups());
        $rows = $GLOBALS['FORUM_DRIVER']->member_group_query($supermember_groups, 1000);
        if (count($rows) >= 1000) {
            warn_exit(do_lang_tempcode('TOO_MANY_TO_CHOOSE_FROM'));
        }
        $all_usergroups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list();

        // Calculate
        $groups = new Tempcode();
        $groups_current = array();
        $old_group = mixed();
        foreach ($rows as $r) {
            $id = $GLOBALS['FORUM_DRIVER']->mrow_id($r);
            $current_group = $GLOBALS['FORUM_DRIVER']->mrow_group($r);
            $username = $GLOBALS['FORUM_DRIVER']->mrow_username($r);

            if (!array_key_exists($current_group, $all_usergroups)) {
                continue;
            }

            if (($current_group != $old_group) && (!is_null($old_group))) {
                $group_name = $all_usergroups[$old_group];
                $groups->attach(do_template('SUPERMEMBERS_SCREEN_GROUP', array('_GUID' => '32c8427ff18523fcd6b89fb5df365a88', 'ENTRIES' => $groups_current, 'GROUP_NAME' => $group_name)));
                $groups_current = array();
            }

            if (addon_installed('authors')) {
                // Work out their skills from their author profile
                $author_rows = $GLOBALS['SITE_DB']->query_select('authors', array('*'), array('member_id' => $id), '', 1);
                if (!array_key_exists(0, $author_rows)) {
                    $author_rows = $GLOBALS['SITE_DB']->query_select('authors', array('*'), array('author' => $username), '', 1);
                }
                $skills = array_key_exists(0, $author_rows) ? get_translated_tempcode('authors', $author_rows[0], 'skills') : new Tempcode();
            } else {
                $skills = new Tempcode();
            }

            $days = intval(round(floatval(time() - $GLOBALS['FORUM_DRIVER']->mrow_lastvisit($r)) / (60.0 * 60.0 * 24.0)));

            // URL's to them
            if (addon_installed('authors')) {
                $author_url = build_url(array('page' => 'authors', 'type' => 'browse', 'id' => $username), get_module_zone('authors'));
            } else {
                $author_url = new Tempcode();
            }
            $points_url = addon_installed('points') ? build_url(array('page' => 'points', 'type' => 'member', 'id' => $id), get_module_zone('points')) : new Tempcode();
            $pm_url = $GLOBALS['FORUM_DRIVER']->member_pm_url($id, true);
            $profile_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($id, false, true);

            // Template
            $groups_current[] = array(
                '_GUID' => '7fdddfe09a33a36762c281e8993327e3',
                'USERNAME' => $username,
                'DAYS' => integer_format($days),
                'PROFILE_URL' => $profile_url,
                'AUTHOR_URL' => $author_url,
                'POINTS_URL' => $points_url,
                'PM_URL' => $pm_url,
                'SKILLS' => $skills,
            );

            $old_group = $current_group;
        }
        if (count($groups_current) != 0) {
            $group_name = $all_usergroups[$old_group];
            $groups->attach(do_template('SUPERMEMBERS_SCREEN_GROUP', array('_GUID' => 'd2cbe67dafa0dc9872f90fc8834d21ca', 'ENTRIES' => $groups_current, 'GROUP_NAME' => $group_name)));
        }

        return do_template('SUPERMEMBERS_SCREEN', array('_GUID' => '93b875bc00b094810ca9cc3e2f4968b8', 'TITLE' => $this->title, 'GROUPS' => $groups, 'TEXT' => $text));
    }
}
