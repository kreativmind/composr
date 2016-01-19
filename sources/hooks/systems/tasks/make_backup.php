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
 * @package    backup
 */

/**
 * Hook class.
 */
class Hook_task_make_backup
{
    /**
     * Run the task hook.
     *
     * @param  string $file The filename to backup to
     * @param  string $b_type The type of backup to do
     * @set    full incremental
     * @param  integer $max_size The maximum size of a file to include in the backup
     * @return ?array A tuple of at least 2: Return mime-type, content (either Tempcode, or a string, or a filename and file-path pair to a temporary file), map of HTTP headers if transferring immediately, map of ini_set commands if transferring immediately (null: show standard success message)
     */
    public function run($file, $b_type, $max_size)
    {
        require_code('backup');

        return array('text/html', make_backup_2($file, $b_type, $max_size));
    }
}
