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
 * @package    custom_comcode
 */

/**
 * Block class.
 */
class Block_main_custom_gfx
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
        $info['version'] = 1;
        $info['locked'] = false;
        $info['parameters'] = array('param', 'data', 'font_size', 'x', 'y', 'color', 'font', 'center');
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param                           array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        // Loads up correct hook and returns rendering

        require_lang('custom_comcode');

        $type_id = array_key_exists('param', $map) ? $map['param'] : '';

        if ((!file_exists(get_file_base() . '/sources/hooks/blocks/main_custom_gfx/' . filter_naughty_harsh($type_id) . '.php')) && (!file_exists(get_file_base() . '/sources_custom/hooks/blocks/main_custom_gfx/' . filter_naughty_harsh($type_id) . '.php'))) {
            return paragraph(do_lang_tempcode('NO_SUCH_RENDERER', $type_id), '', 'red_alert');
        }

        require_code('hooks/blocks/main_custom_gfx/' . filter_naughty_harsh($type_id), true);
        $object = object_factory('Hook_main_custom_gfx_' . $type_id);
        return $object->run($map, $this);
    }

    /**
     * Utility method for writing text onto images.
     *
     * @param  ID_TEXT $cache_id ID.
     * @param  array $map A map of parameters.
     * @param  URLPATH $img_path The image path.
     * @return mixed URL of completed image OR Tempcode error.
     */
    public function _do_image($cache_id, &$map, $img_path)
    {
        if (!array_key_exists('font_size', $map)) {
            $map['font_size'] = '8';
        }
        if (!array_key_exists('data', $map)) {
            $map['data'] = do_lang('FILL_IN_DATA_PARAM');
        }

        // Cache to auto_thumbs
        if ((!file_exists(get_custom_file_base() . '/uploads/auto_thumbs/' . $cache_id . '.png')) || (get_option('is_on_block_cache') == '0')) {
            // Ok so not cached yet

            $_color = array_key_exists('color', $map) ? $map['color'] : 'FFFFFF';
            if (substr($_color, 0, 1) == '#') {
                $_color = substr($_color, 1);
            }
            $font = array_key_exists('font', $map) ? $map['font'] : 'Vera';
            $center = ((array_key_exists('center', $map) ? $map['center'] : '1') == '1');

            $file_base = get_custom_file_base() . '/data_custom/fonts/';
            if (!file_exists($file_base . '/' . $font . '.ttf')) {
                $file_base = get_file_base() . '/data/fonts/';
            }

            $file_contents = file_get_contents(((strpos($img_path, '/default/images/') !== false) ? get_file_base() : get_custom_file_base()) . '/' . $img_path);
            $img = @imagecreatefromstring($file_contents);
            if ($img === false) {
                return paragraph(do_lang_tempcode('CORRUPT_FILE', escape_html($img_path)), '', 'red_alert');
            }

            imagealphablending($img, true);
            imagesavealpha($img, true);

            $colour = imagecolorallocate($img, hexdec(substr($_color, 0, 2)), hexdec(substr($_color, 2, 2)), hexdec(substr($_color, 4, 2)));

            $pos_y = intval(array_key_exists('y', $map) ? $map['y'] : '16');

            require_code('character_sets');
            $text = foxy_utf8_to_nce($map['data']);
            foreach (explode("\n", $text) as $line) {
                if ($line == '') {
                    $line = ' ';
                } // Otherwise our algorithm breaks

                list(, , , , $width, , ,) = imagettfbbox(floatval($map['font_size']), 0.0, $file_base . $font . '.ttf', $line);
                $pos_x = intval(array_key_exists('x', $map) ? $map['x'] : '0');
                $width = max($width, -$width);
                if ($center) {
                    $pos_x += intval(imagesx($img) / 2 - $width / 2);
                }
                if ($pos_x < 0) {
                    $pos_x = 0;
                }
                $pos_x--;

                if (strpos($text, '&#') === false) {
                    $previous = mixed();
                    $nxpos = 0;
                    for ($i = 0; $i < strlen($line); $i++) {
                        list(, , $rx1, $ry1, $rx2, $ry2) = imagettfbbox(floatval($map['font_size']), 0.0, $file_base . $font . '.ttf', $previous);
                        if (!is_null($previous)) // check for existing previous character
                        {
                            $nxpos += max($rx1, $rx2) + 1;
                        }
                        imagettftext($img, floatval($map['font_size']), 0.0, $pos_x + $nxpos, $pos_y, $colour, $file_base . $font . '.ttf', $line[$i]);
                        $previous = $line[$i];
                    }
                } else {
                    imagettftext($img, floatval($map['font_size']), 0.0, $pos_x, $pos_y, $colour, $file_base . $font . '.ttf', $text);
                }

                $pos_y += ($ry1 - $ry2) + 5;
            }

            imagepng($img, get_custom_file_base() . '/uploads/auto_thumbs/' . $cache_id . '.png', 9);
            imagedestroy($img);
            require_code('images_png');
            png_compress(get_custom_file_base() . '/uploads/auto_thumbs/' . $cache_id . '.png');
        }

        $url = get_custom_base_url() . '/uploads/auto_thumbs/' . $cache_id . '.png';

        return $url;
    }
}
