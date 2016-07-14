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
 * @package    core_cleanup_tools
 */

/**
 * Hook class.
 */
class Hook_cleanup_image_thumbs
{
    /**
     * Find details about this cleanup hook.
     *
     * @return ?array Map of cleanup hook info (null: hook is disabled).
     */
    public function info()
    {
        if (!function_exists('imagetypes')) {
            return null;
        }

        $info = array();
        $info['title'] = do_lang_tempcode('IMAGE_THUMBNAILS');
        $info['description'] = do_lang_tempcode('DESCRIPTION_IMAGE_THUMBNAILS');
        $info['type'] = 'optimise';

        return $info;
    }

    /**
     * Run the cleanup hook action.
     *
     * @return Tempcode Results
     */
    public function run()
    {
        erase_thumb_cache();
        erase_comcode_cache();

        return new Tempcode();
    }

    /**
     * Create filename-mirrored thumbnails for the given directory stub (mirrors stub/foo with stub_thumbs/foo).
     *
     * @param  string $dir Directory to mirror
     */
    public function directory_thumb_mirror($dir)
    {
        require_code('images');

        $full = get_custom_file_base() . '/uploads/' . $dir;
        $dh = @opendir($full);
        if ($dh !== false) {
            while (($file = readdir($dh)) !== false) {
                if (is_image($file, IMAGE_CRITERIA_GD_WRITE)) {
                    $target = get_custom_file_base() . '/' . $dir . '_thumbs/' . $file;
                    if (!file_exists($target)) {
                        require_code('images');
                        convert_image($full . '/' . $file, $target, -1, -1, intval(get_option('thumb_width')));
                    }
                }
            }
        }
        closedir($dh);
    }
}
