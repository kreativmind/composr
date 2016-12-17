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
 * @package    banners
 */

/**
 * Block class.
 */
class Block_main_top_sites
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
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
        $info['parameters'] = array('param');
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = 'array(array_key_exists(\'param\',$map)?$map[\'param\']:\'\')';
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 60 * 24;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        if (!array_key_exists('param', $map)) {
            $map['param'] = '';
        }

        require_code('banners');
        require_lang('banners');

        $b_type = $map['param'];
        $myquery = banner_select_sql($b_type) . ' ORDER BY hits_from+hits_to DESC';
        $_banners = $GLOBALS['SITE_DB']->query($myquery, 200);

        $banners = array();
        foreach ($_banners as $banner) {
            $description = get_translated_tempcode('banners', $banner, 'caption');

            $bd = show_banner($banner['name'], $banner['b_title_text'], $description, $banner['b_direct_code'], $banner['img_url'], '', $banner['site_url'], $banner['b_type'], $banner['submitter']);

            $banners[] = array(
                'BANNER' => $bd,
                'NAME' => $banner['name'],
                'URL' => $banner['site_url'],
                'DESCRIPTION' => $description,
                'HITS_FROM' => strval($banner['hits_from']),
                'HITS_TO' => strval($banner['hits_to']),
                'VIEWS_FROM' => strval($banner['views_from']),
                'VIEWS_TO' => strval($banner['views_to']),
                'ADD_DATE' => strval($banner['add_date']),
                'SUBMITTER' => strval($banner['submitter']),
            );
        }

        if ((has_actual_page_access(null, 'cms_banners', null, null)) && (has_submit_permission('mid', get_member(), get_ip_address(), 'cms_banners'))) {
            $submit_url = build_url(array('page' => 'cms_banners', 'type' => 'add', 'redirect' => SELF_REDIRECT), get_module_zone('cms_banners'));
        } else {
            $submit_url = new Tempcode();
        }

        return do_template('BLOCK_MAIN_TOP_SITES', array('_GUID' => '776cecc3769b4f4e082be327da5b7248', 'TYPE' => $map['param'], 'BANNERS' => $banners, 'SUBMIT_URL' => $submit_url));
    }
}
