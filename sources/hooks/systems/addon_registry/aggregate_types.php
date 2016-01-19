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
 * @package    aggregate_types
 */

/**
 * Hook class.
 */
class Hook_addon_registry_aggregate_types
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
        return 'Define complex aggregate types in XML, and spawn them.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_aggregate_types',
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
            'requires' => array('commandr', 'import'),
            'recommends' => array(),
            'conflicts_with' => array(),
            'previously_in_addon' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/adminzone/structure/aggregate_types.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images/icons/24x24/menu/adminzone/structure/aggregate_types.png',
            'themes/default/images/icons/48x48/menu/adminzone/structure/aggregate_types.png',
            'sources/hooks/systems/addon_registry/aggregate_types.php',
            'sources/hooks/systems/resource_meta_aware/aggregate_type_instance.php',
            'lang/EN/aggregate_types.ini',
            'adminzone/pages/modules/admin_aggregate_types.php',
            'data/xml_config/aggregate_types.xml',
            'sources/hooks/systems/commandr_fs/aggregate_type_instances.php',
            'sources/aggregate_types.php',
            'sources/hooks/modules/admin_import_types/aggregate_types.php',
            'sources/hooks/systems/page_groupings/aggregate_types.php',
        );
    }
}
