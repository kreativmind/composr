<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2015

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    chat
 */

/**
 * Hook class.
 */
class Hook_addon_registry_chat
{
    /**
     * Get a list of file permissions to set
     *
     * @return array File permissions to set
     */
    public function get_chmod_array()
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Chat rooms and instant messaging.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_chat',
        );
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(),
            'recommends' => array(),
            'conflicts_with' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/social/chat/chat.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images/icons/14x14/sound_effects.png',
            'themes/default/images/icons/28x28/sound_effects.png',
            'themes/default/images/icons/24x24/menu/social/chat/chat.png',
            'themes/default/images/icons/48x48/menu/social/chat/chat.png',
            'themes/default/images/icons/24x24/menu/social/chat/chatroom_add.png',
            'themes/default/images/icons/48x48/menu/social/chat/chatroom_add.png',
            'themes/default/images/icons/24x24/menu/social/chat/member_blocking.png',
            'themes/default/images/icons/48x48/menu/social/chat/member_blocking.png',
            'themes/default/images/icons/24x24/tabs/member_account/friends.png',
            'themes/default/images/icons/48x48/tabs/member_account/friends.png',
            'themes/default/images/icons/24x24/menu/social/chat/sound.png',
            'themes/default/images/icons/48x48/menu/social/chat/sound.png',
            'themes/default/images/icons/24x24/menu/social/chat/index.html',
            'themes/default/images/icons/48x48/menu/social/chat/index.html',
            'sources/chat_shoutbox.php',
            'sources/chat_sounds.php',
            'sources/chat_lobby.php',
            'sources/chat_logs.php',
            'sources/hooks/systems/snippets/im_friends_rejig.php',
            'site/pages/comcode/EN/popup_blockers.txt',
            'sources/blocks/side_friends.php',
            'themes/default/templates/BLOCK_SIDE_FRIENDS.tpl',
            'themes/default/templates/CHAT_FRIENDS.tpl',
            'sources/hooks/systems/startup/im.php',
            'sources/hooks/systems/notifications/im_invited.php',
            'sources/hooks/systems/notifications/new_friend.php',
            'sources/hooks/systems/notifications/member_entered_chatroom.php',
            'sources/hooks/systems/notifications/cns_friend_birthday.php',
            'sources/hooks/systems/config/chat_default_post_colour.php',
            'sources/hooks/systems/config/chat_default_post_font.php',
            'sources/hooks/systems/config/chat_flood_timelimit.php',
            'sources/hooks/systems/config/chat_private_room_deletion_time.php',
            'sources/hooks/systems/config/chat_show_stats_count_messages.php',
            'sources/hooks/systems/config/chat_show_stats_count_rooms.php',
            'sources/hooks/systems/config/chat_show_stats_count_users.php',
            'sources/hooks/systems/config/group_private_chatrooms.php',
            'sources/hooks/systems/config/chat_max_messages_to_show.php',
            'sources/hooks/systems/config/points_chat.php',
            'sources/hooks/systems/config/sitewide_im.php',
            'sources/hooks/systems/config/username_click_im.php',
            'sources/hooks/systems/realtime_rain/chat.php',
            'sources/hooks/systems/symbols/CHAT_IM.php',
            'sources/hooks/systems/profiles_tabs/friends.php',
            'sources/hooks/systems/sitemap/chat.php',
            'uploads/personal_sound_effects/index.html',
            'uploads/personal_sound_effects/.htaccess',
            'data/sounds/contact_off.mp3',
            'data/sounds/contact_on.mp3',
            'data/sounds/error.mp3',
            'data/sounds/invited.mp3',
            'data/sounds/message_initial.mp3',
            'data/sounds/message_sent.mp3',
            'data/sounds/you_connect.mp3',
            'sources/hooks/modules/chat_bots/default.php',
            'sources/hooks/modules/chat_bots/index.html',
            'themes/default/templates/CHAT_SET_EFFECTS_SCREEN.tpl',
            'themes/default/templates/CHAT_SET_EFFECTS_SETTING_BLOCK.tpl',
            'themes/default/templates/CHAT_SITEWIDE_IM.tpl',
            'themes/default/templates/CHAT_SITEWIDE_IM_POPUP.tpl',
            'themes/default/templates/CHAT_SOUND.tpl',
            'themes/default/templates/CHAT_MODERATE_SCREEN.tpl',
            'sources/hooks/modules/admin_import_types/chat.php',
            'sources/hooks/modules/admin_setupwizard/chat.php',
            'sources/hooks/modules/admin_themewizard/chat.php',
            'sources/hooks/systems/content_meta_aware/chat.php',
            'sources/hooks/systems/commandr_fs/chat.php',
            'sources/hooks/systems/addon_registry/chat.php',
            'sources/hooks/systems/cns_cpf_filter/points_chat.php',
            'themes/default/templates/BLOCK_SIDE_SHOUTBOX_MESSAGE.tpl',
            'themes/default/templates/BLOCK_SIDE_SHOUTBOX.tpl',
            'themes/default/templates/CHAT_ROOM_SCREEN.tpl',
            'themes/default/templates/CHATCODE_EDITOR_BUTTON.tpl',
            'themes/default/templates/CHATCODE_EDITOR_MICRO_BUTTON.tpl',
            'themes/default/templates/CHAT_INVITE.tpl',
            'themes/default/templates/CHAT_MESSAGE.tpl',
            'themes/default/templates/CHAT_PRIVATE.tpl',
            'themes/default/templates/CHAT_STAFF_ACTIONS.tpl',
            'themes/default/javascript/chat.js',
            'themes/default/templates/BLOCK_MAIN_FRIENDS_LIST.tpl',
            'sources/blocks/main_friends_list.php',
            'themes/default/templates/CHAT_LOBBY_SCREEN.tpl',
            'themes/default/templates/CHAT_LOBBY_IM_AREA.tpl',
            'themes/default/templates/CHAT_LOBBY_IM_PARTICIPANT.tpl',
            'themes/default/templates/CHAT_ROOM_LINK.tpl',
            'sources/hooks/modules/chat_bots/.htaccess',
            'adminzone/pages/modules/admin_chat.php',
            'themes/default/css/chat.css',
            'themes/default/images/EN/chatcodeeditor/index.html',
            'themes/default/images/EN/chatcodeeditor/invite.png',
            'themes/default/images/EN/chatcodeeditor/new_room.png',
            'themes/default/images/EN/chatcodeeditor/private_message.png',
            'cms/pages/modules/cms_chat.php',
            'data_custom/modules/chat/index.html',
            'data_custom/modules/chat/.htaccess',
            'lang/EN/chat.ini',
            'site/pages/comcode/EN/userguide_chatcode.txt',
            'site/pages/modules/chat.php',
            'sources/chat.php',
            'sources/chat_stats.php',
            'sources/chat_poller.php',
            'sources/chat2.php',
            'sources/hooks/blocks/side_stats/stats_chat.php',
            'sources/hooks/systems/commandr_commands/send_chatmessage.php',
            'sources/hooks/systems/commandr_commands/watch_chatroom.php',
            'sources/hooks/systems/commandr_notifications/chat.php',
            'sources/hooks/modules/members/chat.php',
            'sources/hooks/systems/page_groupings/chat.php',
            'sources/hooks/systems/rss/chat.php',
            'site/download_chat_logs.php',
            'site/messages.php',
            'sources/blocks/side_shoutbox.php',
            'themes/default/templates/CNS_MEMBER_PROFILE_FRIENDS.tpl',
            'sources/hooks/systems/block_ui_renderers/chat.php',
            'sources/hooks/systems/config/chat_message_check_interval.php',
            'sources/hooks/systems/config/chat_transitory_alert_time.php',
            'sources/hooks/systems/config/max_chat_lobby_friends.php',
        );
    }

    /**
     * Get mapping between template names and the method of this class that can render a preview of them
     *
     * @return array The mapping
     */
    public function tpl_previews()
    {
        return array(
            'templates/CHAT_MODERATE_SCREEN.tpl' => 'administrative__chat_moderate_screen',
            'templates/BLOCK_SIDE_SHOUTBOX_MESSAGE.tpl' => 'block_side_shoutbox',
            'templates/BLOCK_SIDE_SHOUTBOX.tpl' => 'block_side_shoutbox',
            'templates/CHAT_MESSAGE.tpl' => 'chat_message',
            'templates/CHAT_STAFF_ACTIONS.tpl' => 'chat_message',
            'templates/CHAT_PRIVATE.tpl' => 'chat_private',
            'templates/CHAT_INVITE.tpl' => 'chat_invite',
            'templates/CHAT_SOUND.tpl' => 'chat_lobby_screen',
            'templates/CHAT_LOBBY_IM_AREA.tpl' => 'chat_lobby_screen',
            'templates/CHAT_SITEWIDE_IM_POPUP.tpl' => 'chat_sitewide_im_popup',
            'templates/CHAT_LOBBY_IM_PARTICIPANT.tpl' => 'chat_lobby_screen',
            'templates/CHAT_SITEWIDE_IM.tpl' => 'chat_sitewide_im',
            'templates/CHAT_ROOM_LINK.tpl' => 'chat_lobby_screen',
            'templates/CHAT_LOBBY_SCREEN.tpl' => 'chat_lobby_screen',
            'templates/CHATCODE_EDITOR_BUTTON.tpl' => 'chat_room_screen',
            'templates/CHATCODE_EDITOR_MICRO_BUTTON.tpl' => 'chat_room_screen',
            'templates/COMCODE_EDITOR_MICRO_BUTTON.tpl' => 'chat_room_screen',
            'templates/CHAT_ROOM_SCREEN.tpl' => 'chat_room_screen',
            'templates/CHAT_SET_EFFECTS_SETTING_BLOCK.tpl' => 'chat_set_effects_screen',
            'templates/CHAT_SET_EFFECTS_SCREEN.tpl' => 'chat_set_effects_screen',
            'templates/BLOCK_MAIN_FRIENDS_LIST.tpl' => 'cns_member_profile_friends',
            'templates/CNS_MEMBER_PROFILE_FRIENDS.tpl' => 'cns_member_profile_friends',
            'templates/CHAT_FRIENDS.tpl' => 'chat_lobby_screen',
            'templates/BLOCK_SIDE_FRIENDS.tpl' => 'block_side_friends',
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__cns_member_profile_friends()
    {
        require_lang('chat');
        require_lang('cns');
        require_css('cns');

        $friend_map = array(
            'USERGROUP' => lorem_phrase(),
            'USERNAME' => lorem_phrase(),
            'URL' => placeholder_url(),
            'F_ID' => placeholder_id(),
            'BOX' => placeholder_table(),
        );
        $friends_arr = array();
        $friends_arr[] = $friend_map;

        $friends = do_lorem_template('BLOCK_MAIN_FRIENDS_LIST', array(
            'FRIENDS' => $friends_arr,
            'PAGINATION' => placeholder_pagination(),
            'BLOCK_PARAMS' => '',

            'START' => '0',
            'MAX' => '10',
            'START_PARAM' => 'x_start',
            'MAX_PARAM' => 'x_max',
        ));

        $tab_content = do_lorem_template('CNS_MEMBER_PROFILE_FRIENDS', array(
            'MEMBER_ID' => placeholder_id(),
            'ADD_FRIEND_URL' => placeholder_url(),
            'REMOVE_FRIEND_URL' => placeholder_url(),
            'BOX' => lorem_paragraph(),
        ));
        return array(
            lorem_globalise($tab_content, null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__chat_moderate_screen()
    {
        return array(
            lorem_globalise(do_lorem_template('CHAT_MODERATE_SCREEN', array(
                'URL' => placeholder_url(),
                'TITLE' => lorem_title(),
                'INTRODUCTION' => lorem_phrase(),
                'CONTENT' => placeholder_table(),
                'LINKS' => placeholder_array(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__chat_message()
    {
        require_lang('submitban');
        $chat_actions = do_lorem_template('CHAT_STAFF_ACTIONS', array(
            'CHAT_BAN_URL' => placeholder_url(),
            'CHAT_UNBAN_URL' => placeholder_url(),
            'EDIT_URL' => placeholder_url(),
            'BAN_URL' => placeholder_url(),
        ));

        return array(
            lorem_globalise(do_lorem_template('CHAT_MESSAGE', array(
                'SYSTEM_MESSAGE' => lorem_phrase(),
                'STAFF' => '1',
                'OLD_MESSAGES' => lorem_phrase(),
                'AVATAR_URL' => placeholder_avatar(),
                'STAFF_ACTIONS' => $chat_actions,
                'MEMBER' => lorem_word(),
                'MEMBER_ID' => placeholder_number(),
                'MESSAGE' => lorem_phrase(),
                'TIME' => placeholder_date(),
                'RAW_TIME' => placeholder_date(),
                'FONT_COLOUR' => 'blue',
                'FONT_FACE' => 'Arial',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__chat_private()
    {
        return array(
            lorem_globalise(do_lorem_template('CHAT_PRIVATE', array(
                'SYSTEM_MESSAGE' => lorem_phrase(),
                'MESSAGE' => lorem_phrase_html(),
                'MEMBER' => lorem_word(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__chat_invite()
    {
        return array(
            lorem_globalise(do_lorem_template('CHAT_INVITE', array(
                'USERNAME' => lorem_word(),
                'CHATROOM' => lorem_phrase(),
                'LINK' => placeholder_link(),
                'page' => lorem_phrase(),
                'type' => lorem_phrase(),
                'room_id' => placeholder_number(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_side_shoutbox()
    {
        $tpl = do_lorem_template('BLOCK_SIDE_SHOUTBOX_MESSAGE', array(
            'MEMBER' => lorem_word(),
            'MESSAGE' => lorem_phrase(),
            'TIME_RAW' => placeholder_date(),
            'TIME' => placeholder_date(),
        ));

        return array(
            lorem_globalise(do_lorem_template('BLOCK_SIDE_SHOUTBOX', array(
                'CHATROOM_ID' => placeholder_id(),
                'NUM_MESSAGES' => placeholder_number(),
                'LAST_MESSAGE_ID' => placeholder_id(),
                'MESSAGES' => $tpl,
                'URL' => placeholder_url(),
                'BLOCK_PARAMS' => '',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__chat_sitewide_im()
    {
        return array(
            lorem_globalise(do_lorem_template('CHAT_SITEWIDE_IM', array(
                'IM_AREA_TEMPLATE' => lorem_phrase(),
                'IM_PARTICIPANT_TEMPLATE' => lorem_phrase(),
                'CHAT_SOUND' => lorem_phrase(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__block_side_friends()
    {
        $friends = array();
        foreach (placeholder_array() as $key => $friend) {
            $friends[] = array(
                'DATE_AND_TIME_RAW' => placeholder_date(),
                'DATE_AND_TIME' => placeholder_date(),
                'MEMBER_PROFILE_URL' => placeholder_url(),
                'MEMBER_ID' => strval($key),
                'USERNAME' => lorem_word(),
                'ONLINE_TEXT' => lorem_phrase(),
                'ONLINE' => false,
            );
        }

        $friends_tpl = do_lorem_template('CHAT_FRIENDS', array(
            'FRIENDS_ONLINE' => $friends,
            'FRIENDS_OFFLINE' => $friends,
            'FRIENDS' => $friends,
            'CAN_IM' => true,
            'ONLINE_URL' => placeholder_url(),
            'SIMPLER' => true,
        ));

        return array(
            lorem_globalise(do_lorem_template('BLOCK_SIDE_FRIENDS', array(
                'FRIENDS' => $friends_tpl,
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__chat_lobby_screen()
    {
        $chat_sound = do_lorem_template('CHAT_SOUND', array(
            'SOUND_EFFECTS' => placeholder_array(),
            'KEY' => lorem_word(),
            'VALUE' => lorem_word_2(),
        ));

        $im_area_template = do_lorem_template('CHAT_LOBBY_IM_AREA', array(
            'MESSAGES_PHP' => find_script('messages'),
            'CHATROOM_ID' => '__room_id__',
        ));

        $im_participant_template = do_lorem_template('CHAT_LOBBY_IM_PARTICIPANT', array(
            'PROFILE_URL' => placeholder_url(),
            'ID' => '__id__',
            'CHATROOM_ID' => '__room_id__',
            'USERNAME' => '__username__',
            'ONLINE' => '__online__',
            'AVATAR_URL' => '__avatar_url__',
            'MAKE_FRIEND_URL' => placeholder_url(),
            'BLOCK_MEMBER_URL' => placeholder_url(),
        ));

        $fields = new Tempcode();

        foreach (placeholder_array() as $key => $room) {
            $users = array(
                '1' => 'Guest',
                '2' => 'admin',
            );

            $usernames = new Tempcode();

            foreach ($users as $user) {
                $usernames->attach(do_lorem_template('CNS_USER_MEMBER', array(
                    'FIRST' => $usernames->is_empty(),
                    'PROFILE_URL' => placeholder_url(),
                    'USERNAME' => $user,
                    'MEMBER_ID' => placeholder_id(),
                    'COLOUR' => 'black',
                    'AT' => lorem_phrase(),
                )));
            }

            $room_link = do_lorem_template('CHAT_ROOM_LINK', array(
                'PRIVATE' => true,
                'ID' => strval($key),
                'NAME' => $room,
                'USERNAMES' => $usernames,
                'URL' => placeholder_url(),
            ));
            $fields->attach($room_link);
        }

        $friends = array();
        foreach (placeholder_array() as $key => $friend) {
            $friends[] = array(
                'DATE_AND_TIME_RAW' => placeholder_date(),
                'DATE_AND_TIME' => placeholder_date(),
                'MEMBER_PROFILE_URL' => placeholder_url(),
                'MEMBER_ID' => strval($key),
                'USERNAME' => lorem_word(),
                'ONLINE_TEXT' => lorem_phrase(),
            );
        }

        $friends_tpl = do_lorem_template('CHAT_FRIENDS', array(
            'FRIENDS_ONLINE' => $friends,
            'FRIENDS_OFFLINE' => $friends,
            'FRIENDS' => $friends,
            'CAN_IM' => true,
            'ONLINE_URL' => placeholder_url(),
            'ONLINE' => false,
            'SIMPLER' => false,
        ));

        return array(
            lorem_globalise(do_lorem_template('CHAT_LOBBY_SCREEN', array(
                'TITLE' => lorem_title(),
                'MESSAGE' => lorem_phrase(),
                'CHAT_SOUND' => $chat_sound,
                'IM_PARTICIPANT_TEMPLATE' => $im_participant_template,
                'IM_AREA_TEMPLATE' => $im_area_template,
                'CAN_IM' => true,
                'FRIENDS' => $friends_tpl,
                'URL_ADD_FRIEND' => placeholder_url(),
                'URL_REMOVE_FRIENDS' => placeholder_url(),
                'CHATROOMS' => $fields,
                'PRIVATE_CHATROOM' => placeholder_link(),
                'CHATROOM_URL' => placeholder_url(),
                'PASSWORD_HASH' => placeholder_random(),
                'MOD_LINK' => placeholder_link(),
                'BLOCKING_LINK' => placeholder_link(),
                'SETEFFECTS_LINK' => placeholder_link(),
                'ADD_CHATROOM_URL' => placeholder_url(),
                'MEMBER_ID' => placeholder_id(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__chat_room_screen()
    {
        require_lang('comcode');
        require_javascript('chat');

        $chat_sound = do_lorem_template('CHAT_SOUND', array(
            'SOUND_EFFECTS' => placeholder_array(),
            'KEY' => lorem_word(),
            'VALUE' => lorem_word_2(),
        ));

        $_buttons = array(
            'private_message',
            'invite',
            'new_room'
        );

        $buttons = new Tempcode();
        foreach ($_buttons as $button) {
            $buttons->attach(do_lorem_template('CHATCODE_EDITOR_BUTTON', array(
                'TITLE' => do_lang_tempcode('INPUT_CHATCODE_' . $button),
                'B' => $button,
            )));
        }

        $micro_buttons = new Tempcode();
        $_micro_buttons = array(
            'b',
            'i'
        );

        $micro_buttons->attach(do_lorem_template('CHATCODE_EDITOR_MICRO_BUTTON', array(
            'TITLE' => lorem_phrase(),
            'B' => 'new_room',
        )));

        foreach ($_micro_buttons as $button) {
            $micro_buttons->attach(do_lorem_template('COMCODE_EDITOR_MICRO_BUTTON', array(
                'FIELD_NAME' => 'post',
                'TITLE' => do_lang_tempcode('INPUT_COMCODE_' . $button),
                'B' => $button,
            )));
        }

        $users = array(
            '1' => 'Guest',
            '2' => 'admin',
        );

        $usernames = new Tempcode();
        foreach ($users as $user) {
            $usernames->attach(do_lorem_template('CNS_USER_MEMBER', array(
                'FIRST' => $usernames->is_empty(),
                'PROFILE_URL' => placeholder_url(),
                'USERNAME' => $user,
                'MEMBER_ID' => placeholder_id(),
                'COLOUR' => 'black',
                'AT' => lorem_phrase(),
            )));
        }

        return array(
            lorem_globalise(do_lorem_template('CHAT_ROOM_SCREEN', array(
                'CHATTERS' => $usernames,
                'CHAT_SOUND' => $chat_sound,
                'CHATROOM_ID' => placeholder_number(),
                'DEBUG' => '0',
                'MESSAGES_PHP' => find_script('messages'),
                'CHATROOM_NAME' => lorem_word(),
                'MICRO_BUTTONS' => $micro_buttons,
                'BUTTONS' => $buttons,
                'YOUR_NAME' => lorem_word(),
                'MESSAGES_URL' => placeholder_url(),
                'POSTING_URL' => placeholder_url(),
                'OPTIONS_URL' => placeholder_url(),
                'SUBMIT_VALUE' => lorem_word(),
                'PASSWORD_HASH' => placeholder_random(),
                'INTRODUCTION' => '',
                'TITLE' => lorem_title(),
                'CONTENT' => lorem_phrase(),
                'LINKS' => placeholder_array(),
                'TEXT_COLOUR_DEFAULT' => lorem_word(),
                'FONT_NAME_DEFAULT' => 'Tahoma',
                'CHATCODE_HELP' => placeholder_url(),
                'COMCODE_HELP' => placeholder_url(),
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__chat_set_effects_screen()
    {
        require_javascript('checking');
        require_javascript('plupload');

        require_css('forms');

        $setting_blocks = new Tempcode();
        foreach (placeholder_array() as $member => $values) {
            $effects = array();
            foreach (placeholder_array() as $k => $v) {
                $effects[] = array(
                    'KEY' => strval($k),
                    'VALUE' => $v,
                    'MEMBER_ID' => strval($member),
                    'USERNAME' => lorem_phrase(),
                    'EFFECT_TITLE' => lorem_word(),
                    'EFFECT_SHORT' => lorem_word_2(),
                    'EFFECT' => lorem_word(),
                );
            }
            $block = do_lorem_template('CHAT_SET_EFFECTS_SETTING_BLOCK', array(
                'HAS_SOME' => false,
                'EFFECTS' => $effects,
                'LIBRARY' => placeholder_array(),
            ));
            $setting_blocks->attach($block);
        }

        return array(
            lorem_globalise(do_lorem_template('CHAT_SET_EFFECTS_SCREEN', array(
                'TITLE' => lorem_title(),
                'SUBMIT_ICON' => 'buttons__save',
                'SUBMIT_NAME' => lorem_word(),
                'HIDDEN' => '',
                'POST_URL' => placeholder_url(),
                'SETTING_BLOCKS' => $setting_blocks,
                'CHAT_SOUND' => '',
            )), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__chat_sitewide_im_popup()
    {
        $im_area_template = do_lorem_template('CHAT_LOBBY_IM_AREA', array(
            'MESSAGES_PHP' => find_script('messages'),
            'CHATROOM_ID' => placeholder_id(),
        ));
        return array(
            lorem_globalise(do_lorem_template('CHAT_SITEWIDE_IM_POPUP', array(
                'CONTENT' => $im_area_template,
                'CHAT_SOUND' => '',
            )), null, '', true)
        );
    }
}
