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
 * Add an aggregate type instance.
 *
 * @param  SHORT_TEXT $aggregate_label Label for new instance
 * @param  ID_TEXT $aggregate_type What the instance is of
 * @param  array $_other_parameters Additional parameters
 * @param  ?TIME $add_time Add time (null: now)
 * @param  ?TIME $edit_time Edit time (null: not edited yet)
 * @param  boolean $sync Whether to activate it
 * @param  boolean $uniqify Whether to force the name as unique, if there's a conflict
 * @return AUTO_LINK ID of the new instance
 */
function add_aggregate_type_instance($aggregate_label, $aggregate_type, $_other_parameters, $add_time = null, $edit_time = null, $sync = true, $uniqify = false)
{
    // Check aggregate type
    $types = parse_aggregate_xml();
    if (!array_key_exists($aggregate_type, $types)) {
        warn_exit(do_lang_tempcode('MISSING_AGGREGATE_TYPE', escape_html($aggregate_type)));
    }

    $other_parameters = serialize($_other_parameters);

    // Error if label is a duplicate
    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('aggregate_type_instances', 'id', array('aggregate_type' => $aggregate_type, 'aggregate_label' => $aggregate_label));
    if (!is_null($test)) {
        if ($uniqify) {
            $aggregate_label .= '_' . uniqid('', false);
        } else {
            warn_exit(do_lang_tempcode('DUPLICATE_AGGREGATE_INSTANCE', escape_html($aggregate_label)));
        }
    }

    if (is_null($add_time)) {
        $add_time = time();
    }

    $id = $GLOBALS['SITE_DB']->query_insert('aggregate_type_instances', array(
        'aggregate_label' => $aggregate_label,
        'aggregate_type' => $aggregate_type,
        'other_parameters' => $other_parameters,
        'add_time' => $add_time,
        'edit_time' => $edit_time,
    ), true);

    if ($sync) {
        sync_aggregate_type_instance($id, $aggregate_label, $aggregate_label, $aggregate_type, $_other_parameters, $_other_parameters);
    }

    log_it('ADD_AGGREGATE_TYPE_INSTANCE', strval($id), $aggregate_label);

    if ((addon_installed('commandr')) && (!running_script('install'))) {
        require_code('resource_fs');
        generate_resource_fs_moniker('aggregate_type_instance', strval($id), null, null, true);
    }

    return $id;
}

/**
 * Edit an aggregate type instance.
 *
 * @param  AUTO_LINK $id The ID
 * @param  SHORT_TEXT $aggregate_label Label for instance
 * @param  ID_TEXT $aggregate_type What the instance is of
 * @param  array $_other_parameters Additional parameters
 * @param  boolean $uniqify Whether to force the name as unique, if there's a conflict
 * @param  ?TIME $add_time Add time (null: don't change)
 * @param  ?TIME $edit_time Edit time (null: now)
 */
function edit_aggregate_type_instance($id, $aggregate_label, $aggregate_type, $_other_parameters, $uniqify = false, $add_time = null, $edit_time = null)
{
    // Check aggregate type
    $types = parse_aggregate_xml();
    if (!array_key_exists($aggregate_type, $types)) {
        warn_exit(do_lang_tempcode('MISSING_AGGREGATE_TYPE', escape_html($aggregate_type)));
    }

    $other_parameters = serialize($_other_parameters);

    $old_aggregate_label = $GLOBALS['SITE_DB']->query_select_value_if_there('aggregate_type_instances', 'aggregate_label', array('id' => $id));
    $old_parameters = unserialize($GLOBALS['SITE_DB']->query_select_value_if_there('aggregate_type_instances', 'other_parameters', array('id' => $id)));

    // Error if label is a duplicate
    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('aggregate_type_instances', 'id', array('aggregate_type' => $aggregate_type, 'aggregate_label' => $aggregate_label));
    if ((!is_null($test)) && ($test != $id)) {
        if ($uniqify) {
            $aggregate_label .= '_' . uniqid('', false);
        } else {
            warn_exit(do_lang_tempcode('DUPLICATE_AGGREGATE_INSTANCE', escape_html($aggregate_label)));
        }
    }

    $map = array(
        'aggregate_label' => $aggregate_label,
        'aggregate_type' => $aggregate_type,
        'other_parameters' => $other_parameters,
        'edit_time' => is_null($edit_time) ? time() : $edit_time,
    );
    if (!is_null($add_time)) {
        $map['add_time'] = $add_time;
    }
    $GLOBALS['SITE_DB']->query_update('aggregate_type_instances', $map, array('id' => $id), '', 1);

    sync_aggregate_type_instance($id, $aggregate_label, $old_aggregate_label, $aggregate_type, $_other_parameters, $old_parameters);

    log_it('EDIT_AGGREGATE_TYPE_INSTANCE', strval($id), $aggregate_label);

    if ((addon_installed('commandr')) && (!running_script('install'))) {
        require_code('resource_fs');
        generate_resource_fs_moniker('aggregate_type_instance', strval($id));
    }
}

/**
 * Delete an aggregate type instance.
 *
 * @param  AUTO_LINK $id The ID
 * @param  boolean $delete_matches Whether to delete all associated resources
 */
function delete_aggregate_type_instance($id, $delete_matches = false)
{
    $aggregate_label = $GLOBALS['SITE_DB']->query_select_value_if_there('aggregate_type_instances', 'aggregate_label', array('id' => $id));
    if (is_null($aggregate_label)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'aggregate_type_instance'));
    }
    $aggregate_type = $GLOBALS['SITE_DB']->query_select_value_if_there('aggregate_type_instances', 'aggregate_type', array('id' => $id));

    $other_parameters = unserialize($GLOBALS['SITE_DB']->query_select_value_if_there('aggregate_type_instances', 'other_parameters', array('id' => $id)));
    foreach ($other_parameters as $key => $val) {
        unset($other_parameters[$key]);
        $other_parameters[strtoupper($key)] = $val;
    }

    // Delete all instance stuff of requested
    if ($delete_matches) {
        // Load details of aggregate type
        $types = parse_aggregate_xml();
        if (array_key_exists($aggregate_type, $types)) {
            $type = $types[$aggregate_type];

            // Process the resources
            require_code('resource_fs');
            foreach ($type['resources'] as $resource) {
                $tempcode = template_to_tempcode($resource['label']);
                $parameters = $other_parameters + array('LABEL' => $aggregate_label);
                $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                $resource['label'] = $tempcode->evaluate();

                // Can we bind to an existing resource? (using subpath and label)
                $object_fs = get_resource_commandr_fs_object($resource['type']);
                $filename = $object_fs->convert_label_to_filename($resource['label'], $resource['subpath'], $resource['type'], true);

                // If bound, delete resource
                if (!is_null($filename)) {
                    $object_fs->resource_delete($resource['type'], $filename, $resource['subpath']);
                }
            }
        }
    }

    $GLOBALS['SITE_DB']->query_delete('aggregate_type_instances', array('id' => $id), '', 1);

    log_it('DELETE_AGGREGATE_TYPE_INSTANCE', strval($id), $aggregate_label);

    if ((addon_installed('commandr')) && (!running_script('install'))) {
        require_code('resource_fs');
        expunge_resource_fs_moniker('aggregate_type_instance', strval($id));
    }
}

/**
 * Find the parameters an aggregate type needs for instances.
 *
 * @param  ID_TEXT $aggregate_type Aggregate type to find parameters for
 * @return array The aggregate type parameters
 */
function find_aggregate_type_parameters($aggregate_type)
{
    $parameters = array('label');

    $types = parse_aggregate_xml();
    if (array_key_exists($aggregate_type, $types)) {
        foreach ($types[$aggregate_type]['resources'] as $resource) {
            _find_parameters_in($resource['label'], $parameters);
            foreach ($resource['properties'] as $property) {
                _find_parameters_in($property['value'], $parameters);
            }
        }
    }

    return array_unique($parameters);
}

/**
 * Scan some aggregate type XML text for referenced parameters.
 *
 * @param  string $src_text Text
 * @param  array $parameters Reference to our parameter list
 *
 * @ignore
 */
function _find_parameters_in($src_text, &$parameters)
{
    $matches = array();
    $cnt = preg_match_all('#\{(\w+)[^\}]+\}#', $src_text, $matches);
    for ($i = 0; $i < $cnt; $i++) {
        $parameters[] = strtolower($matches[1][$i]);
    }
}

/**
 * Load the aggregate XML types structure.
 *
 * @param  boolean $display_errors Whether errors should be displayed
 * @return array The aggregate types
 */
function parse_aggregate_xml($display_errors = false)
{
    static $_aggregate_types = array();
    if ($_aggregate_types != array()) {
        return $_aggregate_types;
    }

    require_code('xml');

    $xml = file_get_contents(is_file(get_custom_file_base() . '/data_custom/xml_config/aggregate_types.xml') ? (get_custom_file_base() . '/data_custom/xml_config/aggregate_types.xml') : (get_file_base() . '/data/xml_config/aggregate_types.xml'));
    if (trim($xml) == '') {
        return array();
    }

    require_code('tempcode_compiler');

    $parsed = new CMS_simple_xml_reader($xml);

    $parse_errors = array();
    $aggregate_types = array();

    list($root_tag, $root_attributes, , $this_children) = $parsed->gleamed;
    if ($root_tag == 'aggregatetypes') {
        foreach ($this_children as $_child) {
            if (!is_array($_child)) {
                continue;
            }
            list($row_tag, $row_attributes, $row_value, $row_children) = $_child;

            if ($row_tag == 'aggregatetype') {
                if (!array_key_exists('name', $row_attributes)) {
                    $parse_errors[] = 'Missing aggregateType.name';
                    continue;
                }

                $aggregate_type = $row_attributes['name'];

                if (array_key_exists($aggregate_type, $aggregate_types)) {
                    $parse_errors[] = 'Duplicate aggregateType.name';
                }

                $resync = (!array_key_exists('resync', $row_attributes)) || ($row_attributes['resync'] == 'true');

                $aggregate_type_resources = array();
                foreach ($row_children as $__child) {
                    if (!is_array($__child)) {
                        continue;
                    }
                    list($at_row_tag, $at_row_attributes, $at_row_value, $at_row_children) = $__child;

                    if ($at_row_tag == 'resource') {
                        if (!array_key_exists('type', $at_row_attributes)) {
                            $parse_errors[] = 'Missing resource.type';
                            continue;
                        }

                        $resource_type = $at_row_attributes['type'];
                        $resource_subpath = array_key_exists('subpath', $at_row_attributes) ? $at_row_attributes['subpath'] : '';
                        $resource_label = array_key_exists('label', $at_row_attributes) ? $at_row_attributes['label'] : null;
                        $resource_template_subpath = array_key_exists('template_subpath', $at_row_attributes) ? $at_row_attributes['template_subpath'] : '';
                        $resource_template_label = array_key_exists('template_label', $at_row_attributes) ? $at_row_attributes['template_label'] : mixed();
                        $resource_properties = array();
                        $resource_access = array();
                        $resource_privilege_presets = array();
                        $resource_privileges = array();
                        $resource_resync = (!array_key_exists('resync', $at_row_attributes)) || ($at_row_attributes['resync'] == 'true');

                        foreach ($at_row_children as $___child) {
                            if (!is_array($___child)) {
                                continue;
                            }
                            list($rs_row_tag, $rs_row_attributes, $rs_row_value, $rs_row_children) = $___child;

                            switch ($rs_row_tag) {
                                case 'property':
                                    if (!array_key_exists('key', $rs_row_attributes)) {
                                        $parse_errors[] = 'Missing property.key';
                                        continue 2;
                                    }

                                    $resource_properties[$rs_row_attributes['key']] = array(
                                        'value' => $rs_row_value,
                                        'resync' => (!array_key_exists('resync', $rs_row_attributes)) || ($rs_row_attributes['resync'] == 'true'),
                                    );
                                    break;

                                case 'access':
                                    if ((!array_key_exists('usergroup', $rs_row_attributes)) && (!array_key_exists('member', $rs_row_attributes))) {
                                        $parse_errors[] = 'Missing access.usergroup/access.member';
                                        continue 2;
                                    }
                                    if (!array_key_exists('value', $rs_row_attributes)) {
                                        $parse_errors[] = 'Missing access.value';
                                        continue 2;
                                    }

                                    $resource_access[] = array(
                                        'value' => $rs_row_attributes['value'],
                                        'usergroup' => $rs_row_attributes['usergroup'],
                                        'member' => array_key_exists('member', $rs_row_attributes) ? $rs_row_attributes['member'] : null,
                                        'resync' => (!array_key_exists('resync', $rs_row_attributes)) || ($rs_row_attributes['resync'] == 'true'),
                                    );
                                    break;

                                case 'privilege':
                                    if ((!array_key_exists('usergroup', $rs_row_attributes)) && (!array_key_exists('member', $rs_row_attributes))) {
                                        $parse_errors[] = 'Missing privilege.usergroup/privilege.member';
                                        continue 2;
                                    }

                                    if (array_key_exists('preset', $rs_row_attributes)) {
                                        $resource_privilege_presets[] = array(
                                            'value' => $rs_row_attributes['preset'],
                                            'usergroup' => array_key_exists('usergroup', $rs_row_attributes) ? $rs_row_attributes['usergroup'] : null,
                                            'member' => array_key_exists('member', $rs_row_attributes) ? $rs_row_attributes['member'] : null,
                                            'resync' => (!array_key_exists('resync', $rs_row_attributes)) || ($rs_row_attributes['resync'] == 'true'),
                                        );
                                    } else {
                                        if (!array_key_exists('name', $rs_row_attributes)) {
                                            $parse_errors[] = 'Missing privilege.name';
                                            continue 2;
                                        }
                                        if (!array_key_exists('value', $rs_row_attributes)) {
                                            $parse_errors[] = 'Missing privilege.value';
                                            continue 2;
                                        }

                                        $resource_privileges[] = array(
                                            'name' => $rs_row_attributes['name'],
                                            'value' => $rs_row_attributes['value'],
                                            'usergroup' => array_key_exists('usergroup', $rs_row_attributes) ? $rs_row_attributes['usergroup'] : null,
                                            'member' => array_key_exists('member', $rs_row_attributes) ? $rs_row_attributes['member'] : null,
                                            'resync' => (!array_key_exists('resync', $rs_row_attributes)) || ($rs_row_attributes['resync'] == 'true'),
                                        );
                                    }
                                    break;

                                default:
                                    $parse_errors[] = 'Unknown: ' . $at_row_tag;
                                    continue 2;
                            }
                        }

                        $aggregate_type_resources[] = array(
                            'type' => $resource_type,
                            'subpath' => $resource_subpath,
                            'label' => $resource_label,
                            'template_subpath' => $resource_template_subpath,
                            'template_label' => $resource_template_label,
                            'properties' => $resource_properties,
                            'access' => $resource_access,
                            'presets' => $resource_privilege_presets,
                            'privileges' => $resource_privileges,
                            'resync' => $resource_resync,
                        );
                    }
                }

                $aggregate_types[$aggregate_type] = array(
                    'resources' => $aggregate_type_resources,
                    'resync' => $resync,
                );
            }
        }
    }

    if ($display_errors) {
        foreach ($parse_errors as $error) {
            attach_message(escape_html($error), 'warn');
        }
    }

    ksort($aggregate_types);

    $_aggregate_types = $aggregate_types;
    return $aggregate_types;
}

/**
 * Re-sync all aggregate type instances.
 *
 * @param  ?ID_TEXT $type Restrict to this aggregate type (null: no restriction)
 */
function resync_all_aggregate_type_instances($type = null)
{
    $where = mixed();
    if (!is_null($type)) {
        $where['aggregate_type'] = $type;
    }

    $start = 0;
    do {
        $instances = $GLOBALS['SITE_DB']->query_select('aggregate_type_instances', array('*'), $where, '', 100, $start);
        foreach ($instances as $instance) {
            $other_parameters = unserialize($instance['other_parameters']);
            sync_aggregate_type_instance($instance['id'], $instance['aggregate_label'], $instance['aggregate_label'], $instance['aggregate_type'], $other_parameters, $other_parameters);
        }
        $start += 100;
    } while (count($instances) != 0);
}

/**
 * Sync an aggregate type instance.
 *
 * @param  AUTO_LINK $id The ID
 * @param  ?SHORT_TEXT $aggregate_label Label for instance (null: lookup)
 * @param  ?SHORT_TEXT $old_aggregate_label Old label for instance (null: lookup)
 * @param  ?ID_TEXT $aggregate_type What the instance is of (null: lookup)
 * @param  ?array $other_parameters Additional parameters (null: lookup)
 * @param  ?array $old_parameters Old additional parameters (null: lookup)
 */
function sync_aggregate_type_instance($id, $aggregate_label = null, $old_aggregate_label = null, $aggregate_type = null, $other_parameters = null, $old_parameters = null)
{
    require_lang('aggregate_types');

    // Load details from DB if required
    if ((is_null($aggregate_label)) || (is_null($aggregate_type)) || (is_null($other_parameters))) {
        $instance_rows = $GLOBALS['SITE_DB']->query_select('aggregate_type_instances', array('*'), array('id' => $id), '', 1);
        if (!array_key_exists(0, $instance_rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'aggregate_type_instance'));
        }
        $instance_row = $instance_rows[0];
        $aggregate_label = $instance_row['aggregate_label'];
        $aggregate_type = $instance_row['aggregate_type'];
        $other_parameters = unserialize($instance_row['other_parameters']);
    }
    if (is_null($old_aggregate_label)) {
        $old_aggregate_label = $aggregate_label;
    }
    if (is_null($old_parameters)) {
        $old_parameters = $other_parameters;
    }

    // Load details of aggregate type
    $types = parse_aggregate_xml();
    if (!array_key_exists($aggregate_type, $types)) {
        warn_exit(do_lang_tempcode('MISSING_AGGREGATE_TYPE', escape_html($aggregate_type)));
    }
    $type = $types[$aggregate_type];

    // Make sure we have values for all the parameters- default ones we don't have to blank -- and make it all Tempcode ready
    $parameters = array();
    foreach ($other_parameters as $key => $val) {
        $parameters[strtoupper($key)] = $val;
    }
    $parameters['LABEL'] = $aggregate_label;
    $parameters_needed = find_aggregate_type_parameters($aggregate_type);
    foreach ($parameters_needed as $parameter) {
        if (!array_key_exists(strtoupper($parameter), $parameters)) {
            $parameters[strtoupper($parameter)] = '';
        }
    }
    foreach ($old_parameters as $key => $val) {
        unset($old_parameters[$key]);
        $old_parameters[strtoupper($key)] = $val;
    }

    // Process the resources
    require_code('resource_fs');
    foreach ($type['resources'] as $resource) {
        if (!array_key_exists('label', $resource)) {
            $old_resource_label = $old_aggregate_label;

            $resource['label'] = $aggregate_label;
        } else {
            $tempcode = template_to_tempcode($resource['label']);
            $old_parameter_binding = $old_parameters + array('LABEL' => $old_aggregate_label) + $parameters;
            $tempcode = $tempcode->bind($old_parameter_binding, 'aggregate_types.xml');
            $old_resource_label = $tempcode->evaluate();

            $tempcode = template_to_tempcode($resource['label']);
            $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
            $resource['label'] = $tempcode->evaluate();
        }

        $tempcode = template_to_tempcode($resource['subpath']);
        $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
        $resource['subpath'] = $tempcode->evaluate();

        // Can we bind to an existing resource? (using subpath and label)
        $is_new = false;
        $object_fs = get_resource_commandr_fs_object($resource['type']);
        if (is_null($object_fs)) {
            warn_exit(do_lang_tempcode('MISSING_CONTENT_TYPE', escape_html($resource['type'])));
        }
        $filename = $object_fs->convert_label_to_filename($old_resource_label, $resource['subpath'], $resource['type'], true);

        // If not bound, create resource
        if (is_null($filename)) {
            $is_new = true;
        }

        if ((($type['resync']) && ($resource['resync'])) || ($is_new)) {
            $properties = array();
            $properties['label'] = $resource['label'];

            if (!$is_new) { // Load from current, if not new
                $existing_properties = $object_fs->resource_load($resource['type'], $filename, $resource['subpath']);
                if ($existing_properties === false) {
                    $is_new = true; // Load error
                } else {
                    $properties += $existing_properties;
                }
            }
            if (($is_new) && (!is_null($resource['template_label']))) { // Copy from template (using template_subpath and template_label), if new
                $tempcode = template_to_tempcode($resource['template_label']);
                $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                $resource['template_label'] = $tempcode->evaluate();
                $tempcode = template_to_tempcode($resource['template_subpath']);
                $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                $resource['template_subpath'] = $tempcode->evaluate();

                $template_filename = $object_fs->convert_label_to_filename($resource['template_label'], $resource['template_subpath'], $resource['type'], true);
                if (is_null($template_filename)) {
                    warn_exit(do_lang_tempcode('MISSING_CONTENT_TYPE_TEMPLATE', escape_html($resource['type']), escape_html($resource['template_label']), escape_html($resource['template_subpath'])));
                }

                $template_properties = $object_fs->resource_load($resource['type'], $template_filename, $resource['template_subpath']);
                $properties += $template_properties;
            }

            // Set properties
            foreach ($resource['properties'] as $property_key => $property) {
                if (($property['resync']) || ($is_new)) {
                    $tempcode = template_to_tempcode($property['value']);
                    $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                    $property['value'] = $tempcode->evaluate();
                    $properties[$property_key] = $property['value'];
                }
            }

            // Add/Edit
            if ($is_new) {
                $resource_id = $object_fs->resource_add($resource['type'], $resource['label'], $resource['subpath'], $properties);
                $filename = $object_fs->convert_id_to_filename($resource['type'], $resource_id);
            } else {
                $object_fs->resource_edit($resource['type'], $filename, $resource['subpath'], $properties);
            }

            $priv_reset = true;
            $usergroups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(false, true, true, null, null, false);

            // Load privilege presets
            $group_presets = array();
            $member_presets = array();
            foreach ($resource['presets'] as $preset) {
                if (($preset['resync']) || ($is_new)) {
                    $preset_value = 0;
                    switch ($preset['value']) {
                        case 'read':
                            $preset_value = 0;
                            break;
                        case 'submit':
                            $preset_value = 1;
                            break;
                        case 'unvetted':
                            $preset_value = 2;
                            break;
                        case 'moderate':
                            $preset_value = 3;
                            break;
                        default:
                            warn_exit(do_lang_tempcode('UNKNOWN_PRIVILEGE_PRESET', escape_html($preset['value'])));
                    }

                    if (!is_null($preset['member'])) {
                        $tempcode = template_to_tempcode($preset['member']);
                        $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                        $preset['member'] = $tempcode->evaluate();

                        $member_id = is_numeric($preset['member']) ? intval($preset['member']) : remap_portable_as_resource_id('member', array('label' => $preset['member']));
                        if (is_null($member_id)) {
                            warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($preset['member'])));
                        } else {
                            $member_presets[$member_id] = $preset_value;
                        }
                    }
                    if (!is_null($preset['usergroup'])) {
                        $tempcode = template_to_tempcode($preset['usergroup']);
                        $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                        $preset['usergroup'] = $tempcode->evaluate();

                        if ($preset['usergroup'] === '*') {
                            foreach (array_keys($usergroups) as $group_id) {
                                $group_presets[$group_id] = $preset_value;
                            }
                        } else {
                            $group_id = is_numeric($preset['usergroup']) ? intval($preset['usergroup']) : remap_portable_as_resource_id('group', array('label' => $preset['usergroup']));
                            if (is_null($group_id)) {
                                warn_exit(do_lang_tempcode('_GROUP_NO_EXIST', escape_html($preset['usergroup'])));
                            } else {
                                $group_presets[$group_id] = $preset_value;
                            }
                        }
                    }
                } else {
                    $priv_reset = false;
                }
            }

            // Load privileges
            $group_privileges = array();
            $member_privileges = array();
            foreach ($resource['privileges'] as $privilege) {
                if (($privilege['resync']) || ($is_new)) {
                    if (!is_null($privilege['member'])) {
                        $tempcode = template_to_tempcode($privilege['member']);
                        $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                        $privilege['member'] = $tempcode->evaluate();

                        $member_id = is_numeric($privilege['member']) ? intval($privilege['member']) : remap_portable_as_resource_id('member', array('label' => $privilege['member']));
                        if (is_null($member_id)) {
                            warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($privilege['member'])));
                        } else {
                            if (!array_key_exists($member_id, $member_privileges)) {
                                $member_privileges[$member_id] = array();
                            }
                            $member_privileges[$member_id][$privilege['name']] = $privilege['value'];
                        }
                    }
                    if (!is_null($privilege['usergroup'])) {
                        $tempcode = template_to_tempcode($privilege['usergroup']);
                        $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                        $privilege['usergroup'] = $tempcode->evaluate();

                        if ($privilege['usergroup'] === '*') {
                            foreach (array_keys($usergroups) as $group_id) {
                                if (!array_key_exists($group_id, $group_privileges)) {
                                    $group_privileges[$group_id] = array();
                                }
                                $group_privileges[$group_id][$privilege['name']] = $privilege['value'];
                            }
                        } else {
                            $group_id = is_numeric($privilege['usergroup']) ? intval($privilege['usergroup']) : remap_portable_as_resource_id('group', array('label' => $privilege['usergroup']));
                            if (is_null($group_id)) {
                                warn_exit(do_lang_tempcode('_GROUP_NO_EXIST', escape_html($privilege['usergroup'])));
                            } else {
                                if (!array_key_exists($group_id, $group_privileges)) {
                                    $group_privileges[$group_id] = array();
                                }
                                $group_privileges[$group_id][$privilege['name']] = $privilege['value'];
                            }
                        }
                    }
                } else {
                    $priv_reset = false;
                }
            }

            // Set privileges
            if (($priv_reset) && ((count($group_presets) != 0) || (count($member_presets) != 0) || (count($group_privileges) != 0) || (count($member_privileges) != 0))) {
                $object_fs->reset_resource_privileges($filename);
            }
            if (count($group_presets) != 0) {
                $object_fs->set_resource_privileges_from_preset($filename, $group_presets);
            }
            if (count($member_presets) != 0) {
                $object_fs->set_resource_privileges_from_preset__members($filename, $member_presets);
            }
            if (count($group_privileges) != 0) {
                $object_fs->set_resource_privileges($filename, $group_privileges);
            }
            if (count($member_privileges) != 0) {
                $object_fs->set_resource_privileges__members($filename, $member_privileges);
            }

            // Set access
            $group_access = array();
            $member_access = array();
            foreach ($resource['access'] as $access) {
                if (($property['resync']) || ($is_new)) {
                    if (!is_null($access['member'])) {
                        $tempcode = template_to_tempcode($access['member']);
                        $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                        $access['member'] = $tempcode->evaluate();

                        $member_id = is_numeric($access['member']) ? intval($access['member']) : remap_portable_as_resource_id('member', array('label' => $access['member']));
                        if (is_null($member_id)) {
                            warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($access['member'])));
                        } else {
                            $member_access[$member_id] = $access['value'];
                        }
                    }
                    if (!is_null($access['usergroup'])) {
                        $tempcode = template_to_tempcode($access['usergroup']);
                        $tempcode = $tempcode->bind($parameters, 'aggregate_types.xml');
                        $access['usergroup'] = $tempcode->evaluate();

                        if ($access['usergroup'] === '*') {
                            foreach (array_keys($usergroups) as $group_id) {
                                $group_access[$group_id] = $access['value'];
                            }
                        } else {
                            $group_id = is_numeric($access['usergroup']) ? intval($access['usergroup']) : remap_portable_as_resource_id('group', array('label' => $access['usergroup']));

                            if (is_null($group_id)) {
                                warn_exit(do_lang_tempcode('_GROUP_NO_EXIST', escape_html($access['usergroup'])));
                            } else {
                                $group_access[$group_id] = $access['value'];
                            }
                        }
                    }
                }
            }
            if (count($group_access) != 0) {
                $object_fs->set_resource_access($filename, $group_access);
            }
            if (count($member_access) != 0) {
                $object_fs->set_resource_access__members($filename, $member_access);
            }
        }
    }
}
