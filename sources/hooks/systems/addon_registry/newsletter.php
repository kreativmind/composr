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
 * @package    newsletter
 */

/**
 * Hook class.
 */
class Hook_addon_registry_newsletter
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
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
        return 'Support for users to join newsletters, and for the staff to send out newsletters to subscribers, and to specific usergroups.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_newsletter',
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
        return 'themes/default/images/icons/48x48/menu/site_meta/newsletters.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/import_subscribers.png',
            'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/newsletter_email_bounce.png',
            'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/newsletter_from_changes.png',
            'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/subscribers.png',
            'themes/default/images/icons/24x24/menu/site_meta/newsletters.png',
            'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/import_subscribers.png',
            'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/newsletter_email_bounce.png',
            'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/newsletter_from_changes.png',
            'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/subscribers.png',
            'themes/default/images/icons/48x48/menu/site_meta/newsletters.png',
            'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/index.html',
            'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/index.html',
            'sources/hooks/systems/block_ui_renderers/newsletters.php',
            'sources/hooks/modules/admin_setupwizard/newsletter.php',
            'sources/hooks/systems/config/interest_levels.php',
            'sources/hooks/systems/config/newsletter_text.php',
            'sources/hooks/systems/config/newsletter_title.php',
            'sources/hooks/systems/addon_registry/newsletter.php',
            'sources/hooks/systems/cron/newsletter_drip_send.php',
            'sources/hooks/systems/cron/newsletter_periodic.php',
            'sources/hooks/modules/admin_import_types/newsletter.php',
            'themes/default/text/NEWSLETTER_WHATSNEW_RESOURCE_FCOMCODE.txt',
            'themes/default/text/NEWSLETTER_WHATSNEW_SECTION_FCOMCODE.txt',
            'themes/default/text/NEWSLETTER_WHATSNEW_FCOMCODE.txt',
            'themes/default/templates/NEWSLETTER_CONFIRM_WRAP.tpl',
            'themes/default/templates/NEWSLETTER_SUBSCRIBER.tpl',
            'themes/default/templates/NEWSLETTER_SUBSCRIBERS_SCREEN.tpl',
            'adminzone/pages/modules/admin_newsletter.php',
            'lang/EN/newsletter.ini',
            'sources/hooks/systems/commandr_fs/newsletters.php',
            'sources/hooks/systems/commandr_fs/periodic_newsletters.php',
            'sources/hooks/systems/commandr_fs/newsletter_subscribers.php',
            'sources/hooks/systems/resource_meta_aware/newsletter.php',
            'sources/hooks/systems/resource_meta_aware/periodic_newsletter.php',
            'sources/hooks/systems/resource_meta_aware/newsletter_subscriber.php',
            'site/pages/modules/newsletter.php',
            'sources/blocks/main_newsletter_signup.php',
            'sources/hooks/modules/admin_import/newsletter_subscribers.php',
            'sources/hooks/blocks/main_staff_checklist/newsletter.php',
            'sources/hooks/modules/admin_newsletter/.htaccess',
            'sources_custom/hooks/modules/admin_newsletter/.htaccess',
            'sources/hooks/systems/page_groupings/newsletter.php',
            'sources/newsletter.php',
            'sources/hooks/systems/config/max_newsletter_whatsnew.php',
            'sources/hooks/modules/admin_newsletter/index.html',
            'sources_custom/hooks/modules/admin_newsletter/index.html',
            'themes/default/css/newsletter.css',
            'themes/default/templates/BLOCK_MAIN_NEWSLETTER_SIGNUP.tpl',
            'themes/default/templates/BLOCK_MAIN_NEWSLETTER_SIGNUP_DONE.tpl',
            'sources/hooks/systems/config/dual_format_newsletters.php',
            'sources/hooks/systems/config/mails_per_send.php',
            'sources/hooks/systems/config/minutes_between_sends.php',
            'themes/default/templates/PERIODIC_NEWSLETTER_REMOVE.tpl',
            'sources/hooks/systems/tasks/send_newsletter.php',
            'data/incoming_bounced_email.php',
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
            'text/NEWSLETTER_WHATSNEW_FCOMCODE.txt' => 'newsletter_automated_fcomcode',
            'text/NEWSLETTER_WHATSNEW_SECTION_FCOMCODE.txt' => 'newsletter_automated_fcomcode',
            'text/NEWSLETTER_WHATSNEW_RESOURCE_FCOMCODE.txt' => 'newsletter_automated_fcomcode',
            'templates/NEWSLETTER_SUBSCRIBER.tpl' => 'administrative__newsletter_subscribers_screen',
            'templates/NEWSLETTER_SUBSCRIBERS_SCREEN.tpl' => 'administrative__newsletter_subscribers_screen',
            'text/NEWSLETTER_DEFAULT_FCOMCODE.txt' => 'newsletter_default',
            'templates/NEWSLETTER_CONFIRM_WRAP.tpl' => 'administrative__newsletter_confirm_wrap',
            'templates/BLOCK_MAIN_NEWSLETTER_SIGNUP_DONE.tpl' => 'block_main_newsletter_signup_done',
            'templates/BLOCK_MAIN_NEWSLETTER_SIGNUP.tpl' => 'block_main_newsletter_signup',
            'templates/PERIODIC_NEWSLETTER_REMOVE.tpl' => 'periodic_newsletter_remove',
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__newsletter_automated_fcomcode()
    {
        $automatic = array();
        foreach (placeholder_array() as $k => $v) {
            $_content = do_lorem_template('NEWSLETTER_WHATSNEW_RESOURCE_FCOMCODE', array(
                'MEMBER_ID' => placeholder_id(),
                'URL' => placeholder_url(),
                'NAME' => lorem_word(),
                'DESCRIPTION' => lorem_paragraph(),
                'THUMBNAIL' => placeholder_image_url(),
                'CONTENT_TYPE' => lorem_word(),
                'CONTENT_ID' => placeholder_id(),
            ), null, false, null, '.txt', 'text');

            $tmp = do_lorem_template('NEWSLETTER_WHATSNEW_SECTION_FCOMCODE', array(
                'I' => lorem_word(),
                'TITLE' => lorem_phrase(),
                'CONTENT' => $_content,
            ), null, false, null, '.txt', 'text');
            $automatic[] = $tmp->evaluate();
        }

        $content = '';
        foreach ($automatic as $tp) {
            $content .= $tp;
        }

        return array(
            lorem_globalise(do_lorem_template('NEWSLETTER_WHATSNEW_FCOMCODE', array(
                'CONTENT' => $content,
            ), null, false, null, '.txt', 'text'), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__newsletter_subscribers_screen()
    {
        $out = new Tempcode();
        foreach (placeholder_array() as $k => $v) {
            $out->attach(do_lorem_template('NEWSLETTER_SUBSCRIBER', array(
                'EMAIL' => lorem_word(),
                'FORENAME' => lorem_word(),
                'SURNAME' => lorem_word(),
                'NAME' => lorem_word(),
                'NEWSLETTER_SEND_ID' => placeholder_id(),
                'NEWSLETTER_HASH' => lorem_word(),
            )));
        }

        $outs = array();
        $outs[] = array(
            'SUB' => $out,
            'TEXT' => lorem_phrase(),
        );

        return array(
            lorem_globalise(do_lorem_template('NEWSLETTER_SUBSCRIBERS_SCREEN', array(
                'SUBSCRIBERS' => $outs,
                'PAGINATION' => '',
                'TITLE' => lorem_title(),
                'DOMAINS' => placeholder_array(),
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
    public function tpl_preview__newsletter_default()
    {
        return array(
            lorem_globalise(do_lorem_template('NEWSLETTER_DEFAULT_FCOMCODE', array(
                'CONTENT' => lorem_phrase(),
                'LANG' => fallback_lang(),
                'SUBJECT' => lorem_phrase(),
            ), null, false, null, '.txt', 'text'), null, '', true)
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__newsletter_confirm_wrap()
    {
        $preview = do_lorem_template('NEWSLETTER_CONFIRM_WRAP', array(
            'TEXT_PREVIEW' => lorem_sentence(),
            'PREVIEW' => lorem_phrase(),
            'SUBJECT' => lorem_phrase(),
        ));

        return array(
            lorem_globalise(do_lorem_template('CONFIRM_SCREEN', array(
                'URL' => placeholder_url(),
                'BACK_URL' => placeholder_url(),
                'PREVIEW' => $preview,
                'FIELDS' => new Tempcode(),
                'TITLE' => lorem_title(),
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
    public function tpl_preview__block_main_newsletter_signup()
    {
        require_lang('javascript');
        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_NEWSLETTER_SIGNUP', array(
                'URL' => placeholder_url(),
                'NEWSLETTER_TITLE' => lorem_word(),
                'NID' => placeholder_id(),
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
    public function tpl_preview__block_main_newsletter_signup_done()
    {
        return array(
            lorem_globalise(do_lorem_template('BLOCK_MAIN_NEWSLETTER_SIGNUP_DONE', array(
                'PASSWORD' => lorem_phrase(),
                'NEWSLETTER_TITLE' => lorem_word(),
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
    public function tpl_preview__periodic_newsletter_remove()
    {
        return array(
            lorem_globalise(do_lorem_template('PERIODIC_NEWSLETTER_REMOVE', array(
                'TITLE' => lorem_title(),
                'URL' => placeholder_url(),
            )), null, '', true)
        );
    }
}
