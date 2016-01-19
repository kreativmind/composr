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
 * @package    cns_cpfs
 */

/**
 * Hook class.
 */
class Hook_addon_registry_cns_cpfs
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
        return 'Custom profile fields, so members may save additional details. If this is uninstalled any existing custom profile fields will remain in the system.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_adv_members',
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
        return 'themes/default/images/icons/48x48/menu/adminzone/tools/users/custom_profile_fields.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images/icons/24x24/menu/adminzone/tools/users/custom_profile_fields.png',
            'themes/default/images/icons/48x48/menu/adminzone/tools/users/custom_profile_fields.png',
            'sources/hooks/systems/resource_meta_aware/cpf.php',
            'adminzone/pages/modules/admin_cns_customprofilefields.php',
            'themes/default/templates/CNS_CPF_STATS_SCREEN.tpl',
            'uploads/cns_cpf_upload/index.html',
            'uploads/cns_cpf_upload/.htaccess',
            'themes/default/templates/CNS_CPF_PERMISSIONS_TAB.tpl',
            'lang/EN/cns_privacy.ini',
            'sources/hooks/systems/profiles_tabs_edit/privacy.php',
            'sources/hooks/systems/addon_registry/cns_cpfs.php',
            'sources/hooks/systems/commandr_fs/cpfs.php',
            'sources/hooks/systems/commandr_fs_extended_member/cpf_perms.php',
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
            'templates/CNS_CPF_PERMISSIONS_TAB.tpl' => 'cns_cpf_permissions_tab',
            'templates/CNS_CPF_STATS_SCREEN.tpl' => 'administrative__cns_cpf_stats_screen'
        );
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declaritive.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__cns_cpf_permissions_tab()
    {
        return array(
            lorem_globalise(do_lorem_template('CNS_CPF_PERMISSIONS_TAB', array(
                'FIELDS' => placeholder_fields(),
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
    public function tpl_preview__administrative__cns_cpf_stats_screen()
    {
        $lines = array();
        foreach (placeholder_array() as $value) {
            $lines[] = array(
                'CNT' => placeholder_number(),
                'VAL' => lorem_phrase(),
            );
        }

        return array(
            lorem_globalise(do_lorem_template('CNS_CPF_STATS_SCREEN', array(
                'TITLE' => lorem_title(),
                'STATS' => $lines,
            )), null, '', true)
        );
    }
}
