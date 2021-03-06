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

require_code('resource_fs');

/**
 * Hook class.
 */
class Hook_commandr_fs_emoticons extends Resource_fs_base
{
    public $file_resource_type = 'emoticon';

    /**
     * Standard Commandr-fs function for seeing how many resources are. Useful for determining whether to do a full rebuild.
     *
     * @param  ID_TEXT $resource_type The resource type
     * @return integer How many resources there are
     */
    public function get_resources_count($resource_type)
    {
        return $GLOBALS['FORUM_DB']->query_select_value('f_emoticons', 'COUNT(*)');
    }

    /**
     * Standard Commandr-fs function for searching for a resource by label.
     *
     * @param  ID_TEXT $resource_type The resource type
     * @param  LONG_TEXT $label The resource label
     * @return array A list of resource IDs
     */
    public function find_resource_by_label($resource_type, $label)
    {
        $_ret = $GLOBALS['FORUM_DB']->query_select('f_emoticons', array('e_code'), array('e_code' => $label));
        $ret = array();
        foreach ($_ret as $r) {
            $ret[] = $r['e_code'];
        }
        return $ret;
    }

    /**
     * Standard Commandr-fs add function for resource-fs hooks. Adds some resource with the given label and properties.
     *
     * @param  LONG_TEXT $filename Filename OR Resource label
     * @param  string $path The path (blank: root / not applicable)
     * @param  array $properties Properties (may be empty, properties given are open to interpretation by the hook but generally correspond to database fields)
     * @return ~ID_TEXT The resource ID (false: error, could not create via these properties / here)
     */
    public function file_add($filename, $path, $properties)
    {
        list($properties, $label) = $this->_file_magic_filter($filename, $path, $properties, $this->file_resource_type);

        require_code('cns_general_action');

        $theme_img_code = $this->_default_property_str($properties, 'theme_img_code');
        $relevance_level = $this->_default_property_int($properties, 'relevance_level');
        $use_topics = $this->_default_property_int($properties, 'use_topics');
        $is_special = $this->_default_property_int($properties, 'is_special');

        cns_make_emoticon($label, $theme_img_code, $relevance_level, $use_topics, $is_special);

        $this->_resource_save_extend($this->file_resource_type, $label, $filename, $label, $properties);

        return $label;
    }

    /**
     * Standard Commandr-fs load function for resource-fs hooks. Finds the properties for some resource.
     *
     * @param  SHORT_TEXT $filename Filename
     * @param  string $path The path (blank: root / not applicable). It may be a wildcarded path, as the path is used for content-type identification only. Filenames are globally unique across a hook; you can calculate the path using ->search.
     * @return ~array Details of the resource (false: error)
     */
    public function file_load($filename, $path)
    {
        list($resource_type, $resource_id) = $this->file_convert_filename_to_id($filename);

        $rows = $GLOBALS['FORUM_DB']->query_select('f_emoticons', array('*'), array('e_code' => $resource_id), '', 1);
        if (!array_key_exists(0, $rows)) {
            return false;
        }
        $row = $rows[0];

        $properties = array(
            'label' => $row['e_code'],
            'theme_img_code' => $row['e_theme_img_code'],
            'relevance_level' => $row['e_relevance_level'],
            'use_topics' => $row['e_use_topics'],
            'is_special' => $row['e_is_special'],
        );
        $this->_resource_load_extend($resource_type, $resource_id, $properties, $filename, $path);
        return $properties;
    }

    /**
     * Standard Commandr-fs edit function for resource-fs hooks. Edits the resource to the given properties.
     *
     * @param  ID_TEXT $filename The filename
     * @param  string $path The path (blank: root / not applicable)
     * @param  array $properties Properties (may be empty, properties given are open to interpretation by the hook but generally correspond to database fields)
     * @return ~ID_TEXT The resource ID (false: error, could not create via these properties / here)
     */
    public function file_edit($filename, $path, $properties)
    {
        list($resource_type, $resource_id) = $this->file_convert_filename_to_id($filename);
        list($properties,) = $this->_file_magic_filter($filename, $path, $properties, $this->file_resource_type);

        require_code('cns_general_action2');

        $label = $this->_default_property_str($properties, 'label');
        $theme_img_code = $this->_default_property_str($properties, 'theme_img_code');
        $relevance_level = $this->_default_property_int($properties, 'relevance_level');
        $use_topics = $this->_default_property_int($properties, 'use_topics');
        $is_special = $this->_default_property_int($properties, 'is_special');

        cns_edit_emoticon($resource_id, $label, $theme_img_code, $relevance_level, $use_topics, $is_special);

        $this->_resource_save_extend($this->file_resource_type, $resource_id, $filename, $label, $properties);

        return $resource_id;
    }

    /**
     * Standard Commandr-fs delete function for resource-fs hooks. Deletes the resource.
     *
     * @param  ID_TEXT $filename The filename
     * @param  string $path The path (blank: root / not applicable)
     * @return boolean Success status
     */
    public function file_delete($filename, $path)
    {
        list($resource_type, $resource_id) = $this->file_convert_filename_to_id($filename);

        require_code('cns_general_action2');
        cns_delete_emoticon($resource_id);

        return true;
    }

    /**
     * Get the resource ID for a filename (of file). Note that filenames are unique across all folders in a filesystem.
     *
     * @param  ID_TEXT $filename The filename, or filepath
     * @param  ?ID_TEXT $resource_type The resource type (null: assumption of only one folder resource type for this hook; only passed as non-null from overridden functions within hooks that are calling this as a helper function)
     * @return ?array A pair: The resource type, the resource ID (null: could not find)
     */
    public function file_convert_filename_to_id($filename, $resource_type = null)
    {
        if (is_null($resource_type)) {
            $resource_type = $this->file_resource_type;
        }

        $filename = preg_replace('#^.*/#', '', $filename); // Paths not needed, as filenames are globally unique; paths would not be in alternative_ids table

        $label = basename($filename, '.' . RESOURCE_FS_DEFAULT_EXTENSION); // Remove file extension from filename
        $resource_id = find_id_via_label($resource_type, $label);
        return array($resource_type, $resource_id);
    }
}
