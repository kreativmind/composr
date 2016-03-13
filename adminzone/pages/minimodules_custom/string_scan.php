<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    meta_toolkit
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$title = get_screen_title('Categorise language strings', false);
$title->evaluate_echo();

require_code('lorem');
require_code('lang_compile');

$files_admin = array();
$files_non_admin = array();

$hooks = find_all_hooks('systems', 'addon_registry');
foreach ($hooks as $hook => $place) {
    if ($place == 'sources_custom') {
        continue;
    }

    require_code('hooks/systems/addon_registry/' . filter_naughty($hook));

    $ob = object_factory('Hook_addon_registry_' . $hook);
    if (method_exists($ob, 'tpl_previews')) {
        $previews = $ob->tpl_previews();
        foreach ($previews as $template => $preview) {
            $full = 'themes/default/' . $template;
            if (file_exists(get_file_base() . '/' . $full)) {
                if (preg_match('#^administrative__#', $preview) != 0) {
                    $files_admin[] = $full;
                } else {
                    $files_non_admin[] = $full;
                }
            }
        }
    }
}
$files_admin = array_merge($files_admin, do_dir('adminzone'));
$files_admin = array_merge($files_admin, do_dir('cms'));
$files_admin = array_merge($files_admin, do_dir('collaboration'));
$files_non_admin = array_merge($files_non_admin, do_dir('site'));
$files_non_admin = array_merge($files_non_admin, do_dir('pages'));
$files_non_admin = array_merge($files_non_admin, do_dir('forum'));

$lang_strings_admin_initial = explode("\n", trim('
ABRUPTED_DIRECTIVE_OR_BRACE
ABSTRACT_FILE_MANAGEMENT
ACTIONLOG_NOTIFICATION_MAIL
ACTIONLOG_NOTIFICATION_MAIL_SUBJECT
ACTIONLOG_USERCOUNT
ACTIONLOG_USERCOUNT_UNI
ACTION_SCREENS
ADDING_CONTENT_IN_LANGUAGE
ADDING_CONTENT_IN_LANGUAGE_STAFF
ADDITIONAL_ACCESS
ADDON_FILE_IS_COMCODE_PAGE
ADDON_FILE_IS_PHP
ADDON_FILE_NORMAL
ADDON_FILE_WILL_OVERWRITE
ADDON_FILE_WILL_OVERWRITE_BACKUP
ADDON_WARNING_GENERAL
ADDON_WARNING_INCOMPATIBILITIES
ADDON_WARNING_MISSING_DEPENDENCIES
ADDON_WARNING_OVERWRITE
ADDON_WARNING_PHP
ADDON_WARNING_PRESENT_DEPENDENCIES
ADD_NEW_CUSTOM_PRODUCT
ADD_NEW_FORWARDING_DOMAIN
ADD_NEW_PERMISSION_PRODUCT
ADD_NEW_POP3_DOMAIN
ADD_STAFF
ADD_TICKET_AS
ADMINZONE_SEARCH
ADMIN_BANNERS
ADMIN_ZONE_SEARCH_RESULTS
ADMIN_ZONE_SEARCH_SYNTAX
ADMIN_ZONE_TEXT
ALLOWED_FILES
ALLOWED_POST_SUBMITTERS
ALLOW_ALPHA_SEARCH
ALLOW_AUDIO_VIDEOS
ALLOW_AUTO_NOTIFICATIONS
ALLOW_COMMENTS
ALLOW_COMMENTS_ONLY
ALLOW_RATING
ALLOW_REVIEWS
ALLOW_TRACKBACKS
ANTISPAM_CONFIDENCE
ANTISPAM_CONFIDENCE_NA
ANTISPAM_RESPONSE_ACTIVE
ANTISPAM_RESPONSE_ACTIVE_UNKNOWN_STALE
ANTISPAM_RESPONSE_ERROR
ANTISPAM_RESPONSE_STALE
ANTISPAM_RESPONSE_UNLISTED
BACKUPS
BACKUP_FINISHED
BACKUP_OVERWRITE
BACKUP_README
BACKUP_REGULARITY
BACKUP_SERVER_HOSTNAME
BACKUP_SERVER_PASSWORD
BACKUP_SERVER_PATH
BACKUP_SERVER_PORT
BACKUP_SERVER_USER
BLOCK
BLOCKS_EM
BLOCKS_TYPE_ADDON
BLOCKS_TYPE_bottom
BLOCKS_TYPE_main
BLOCKS_TYPE_side
BRUTEFORCE_LOGIN_HACK
BRUTE_FORCE
BRUTE_FORCE_INSTANT_BAN
BRUTE_FORCE_LOGIN_MINUTES
BRUTE_FORCE_THRESHOLD
BYPASS_COMCODE_PAGE_VALIDATION
BYPASS_DOWNLOAD_VALIDATION
BYPASS_NEWS_BLOG_VALIDATION
BYPASS_NEWS_VALIDATION
BYPASS_POST_VALIDATION
BYPASS_TOPIC_VALIDATION
BYPASS_VALIDATION_BANNER
BYPASS_VALIDATION_CALENDAR_EVENT
BYPASS_VALIDATION_CALENDAR_EVENT__NON_PUBLIC
BYPASS_VALIDATION_CATALOGUE_ENTRY
BYPASS_VALIDATION_HACK
BYPASS_VALIDATION_MEDIA
BYPASS_VALIDATION_POLL
BYPASS_VALIDATION_QUIZ
BYPASS_WIKI_VALIDATION
CANNOT_DELETE_FORUM_OPTION
CANNOT_DELETE_ROOT_FORUM
CANNOT_MERGE_CATALOGUES
CATALOGUES_IMPORT_MISSING_KEY_FIELD
CATALOGUES_IMPORT_MISSING_META_DESCRIPTION_FIELD
CATALOGUES_IMPORT_MISSING_META_KEYWORDS_FIELD
CATALOGUES_IMPORT_MISSING_NOTES_FIELD
CHARGE_MEMBER
CHARGE_TEXT
COMCODE_GROUP_CUSTOM
COMMANDR_BUTTON
COMMANDR_CHAT_ANNOUNCE
COMMANDR_DESCRIPTIVE_TITLE
CONFLICTING_EMOTICON_CODE
CONFLICTING_PAGE_NAME
CONFLICTING_ZONE_NAME
CONFLICTING_ZONE_NAME__PAGE
CONTEXTUAL_CSS_EDITING
CONTEXTUAL_CSS_EDITING_GLOBAL
COOKIE
COOKIE_CONFLICT_DELETE_COOKIES
COOKIE_DAYS
COOKIE_DAYS_EXAMPLE
COOKIE_DAYS_TEXT
COOKIE_DOMAIN
COOKIE_DOMAIN_CANT_USE
COOKIE_DOMAIN_EXAMPLE
COOKIE_DOMAIN_MUST_MATCH
COOKIE_DOMAIN_MUST_START_DOT
COOKIE_DOMAIN_TEXT
COOKIE_EXAMPLE
COOKIE_PASSWORD
COOKIE_PASSWORD_EXAMPLE
COOKIE_PASSWORD_TEXT
COOKIE_PATH
COOKIE_PATH_EXAMPLE
COOKIE_PATH_MUST_MATCH
COOKIE_PATH_TEXT
COOKIE_SETTINGS
COOKIE_TEXT
CORRUPT_FILE
CORRUPT_FILES_CROP
CORRUPT_FILES_LOWERCASE
CORRUPT_INSTALLATION_FILE
CORRUPT_TAR
DASHBOARD
DATABASE_HOST
DATABASE_HOST_TEXT
DATABASE_SETTINGS
DATABASE_VALID
DECRYPT
DECRYPTION_ERROR
DECRYPTION_KEY
DECRYPT_DATA
DECRYPT_DESCRIPTION
DECRYPT_LABEL
DECRYPT_TITLE
DEEPER_ADMIN_BREADCRUMBS
TENT
DEFAULT_POST_TEMPLATE_bug_text
DEFAULT_POST_TEMPLATE_bug_title
DEFAULT_POST_TEMPLATE_fault_text
DEFAULT_POST_TEMPLATE_fault_title
DEFAULT_POST_TEMPLATE_task_text
DEFAULT_POST_TEMPLATE_task_title
DELETE_AGGREGATE_TYPE_INSTANCE
DELETE_ATTACHMENTS
DELETE_AUTHOR
DELETE_AWARD_TYPE
DELETE_BANNER
DELETE_BANNER_TYPE
DELETE_BY_EMPTYING
DELETE_CATALOGUE
DELETE_CATALOGUE_CATEGORY
DELETE_CATALOGUE_ENTRY
DELETE_CLUB
DELETE_CUSTOM_PROFILE_FIELD
DELETE_DATA_AVAILABLE
DELETE_DOWNLOAD
DELETE_DOWNLOAD_CATEGORY
DELETE_DOWNLOAD_LICENCE
DELETE_EMOTICON
DELETE_EVENT_TYPE
DELETE_FORUM
DELETE_FORUM_GROUPING
DELETE_GALLERY
DELETE_GROUP
DELETE_IMAGE
DELETE_LIVE_POLL
DELETE_MEDIA
DELETE_MEMBER
DELETE_MENU_ITEM
DELETE_MESSAGE
DELETE_MULTI_MODERATION
DELETE_NEWS
DELETE_NEWSLETTER
DELETE_NEWS_BLOG
DELETE_NEWS_CATEGORY
DELETE_OWN_AUTHOR
DELETE_OWN_BANNER
DELETE_OWN_CALENDAR_EVENT
DELETE_OWN_CALENDAR_EVENT__NON_PUBLIC
DELETE_OWN_CATALOGUE_ENTRY
DELETE_OWN_DOWNLOAD
DELETE_OWN_GALLERY
DELETE_OWN_LIVE_POLL
DELETE_OWN_MEDIA
DELETE_OWN_NEWS
DELETE_OWN_NEWS_BLOG
DELETE_OWN_NEWS_CATEGORY
DELETE_OWN_POLL
DELETE_OWN_POST
DELETE_OWN_QUIZ
DELETE_OWN_TOPIC
DELETE_POLL
DELETE_POST_TEMPLATE
DELETE_PRIVATE_CALENDAR_EVENT
DELETE_PUBLIC_CALENDAR_EVENT
DELETE_QUIZ
DELETE_QUIZ_RESULTS
DELETE_SAVED_WARNING
DELETE_SEARCH_STATS
DELETE_SOME_MESSAGES
DELETE_THEME
DELETE_THEME_IMAGE
DELETE_TICKET_TYPE
DELETE_TIME_LIMIT
DELETE_TRACKBACKS
DELETE_USERGROUP_SUBSCRIPTION
DELETE_VIDEO
DELETE_WARNING
DELETE_WELCOME_EMAIL
DELETE_WITHOUT_MERGING
DELETE_ZONE
DELETE_ZONE_ERROR
DESCRIPTION_ALLOW_COMMENTS
DESCRIPTION_ALLOW_RATING
DESCRIPTION_ALLOW_TRACKBACKS
DESCRIPTION_BROKEN_URLS
DESCRIPTION_BROWSER_SHARE
DESCRIPTION_CACHE_FORUMS
DESCRIPTION_CACHE_MEMBERS
DESCRIPTION_CACHE_TOPICS
DESCRIPTION_COMCODE_CACHE
DESCRIPTION_COMCODE_PAGE_CACHE
DESCRIPTION_CORRECT_MYSQL_SCHEMA_ISSUES
DESCRIPTION_DECRYPT_DATA
DESCRIPTION_DEFAULT_PERMISSIONS_FROM
DESCRIPTION_DEFAULT_PERMISSIONS_FROM_NEW
DESCRIPTION_DEFINES_ORDER_DESCENDING
DESCRIPTION_DELETE_DAYS
DESCRIPTION_DELETE_LOSE_CONTENTS
DESCRIPTION_DELETE_ROOT_CONTENTS
DESCRIPTION_DELETE_SEARCH_STATS
DESCRIPTION_DEMOGRAPHICS
DESCRIPTION_DISPLAY_REVIEW_STATUS
DESCRIPTION_DOWNLOADS_STATISTICS
DESCRIPTION_FINISH_STARTED_ALREADY
DESCRIPTION_FIX_INVALID_HTML
DESCRIPTION_FORUM_TOPIC_ID
DESCRIPTION_FTP_FILES_PER_GO
DESCRIPTION_GAE_APPLICATION
DESCRIPTION_GAE_BUCKET_NAME
DESCRIPTION_GUESTS
DESCRIPTION_GUEST_ZONE_ACCESS
DESCRIPTION_HAVE_DEFAULT_BANNERS_ADVERTISING
DESCRIPTION_HAVE_DEFAULT_BANNERS_DONATION
DESCRIPTION_HAVE_DEFAULT_CATALOGUES_CONTACTS
DESCRIPTION_HAVE_DEFAULT_CATALOGUES_FAQS
DESCRIPTION_HAVE_DEFAULT_CATALOGUES_LINKS
DESCRIPTION_HAVE_DEFAULT_CATALOGUES_PROJECTS
DESCRIPTION_HAVE_DEFAULT_CPF_SET
DESCRIPTION_HAVE_DEFAULT_FULL_EMOTICON_SET
DESCRIPTION_HAVE_DEFAULT_RANK_SET
DESCRIPTION_HAVE_DEFAULT_WORDFILTER
DESCRIPTION_IMPORT_ALL
DESCRIPTION_IMPORT_BASE_URL
DESCRIPTION_IMPORT_POSITION
DESCRIPTION_IMPORT_REPLACE
DESCRIPTION_INTRO_POST
DESCRIPTION_IP_ADDRESS_DISTRIBUTION
DESCRIPTION_KEEP_BLOGS
DESCRIPTION_KEEP_DEFAULT_NEWS_CATEGORIES
DESCRIPTION_KEEP_PERSONAL_GALLERIES
DESCRIPTION_LANGUAGE_CACHE
DESCRIPTION_MAIL_COST
DESCRIPTION_MENU_TITLE
DESCRIPTION_META_ADD_TIME
DESCRIPTION_METADATA
DESCRIPTION_META_EDIT_TIME
DESCRIPTION_META_URL_MONIKER
DESCRIPTION_META_VIEWS
DESCRIPTION_MONTHLY_SPEC_TYPE
DESCRIPTION_MYSQL_OPTIMISE
DESCRIPTION_NC_IMAGE
DESCRIPTION_NEED_FTP
DESCRIPTION_NEW_UGROUP_SUB_FOR
DESCRIPTION_NEXT_REVIEW_DATE
DESCRIPTION_OLD_COMCODE_PAGE
DESCRIPTION_ONE_PER_MEMBER
DESCRIPTION_ORDER
DESCRIPTION_ORPHANED_LANG_STRINGS
DESCRIPTION_ORPHANED_TAGS
DESCRIPTION_ORPHANED_UPLOADS
DESCRIPTION_OS_SHARE
DESCRIPTION_OVERVIEW_STATISTICS
DESCRIPTION_OWNER
DESCRIPTION_OWN_TEMPLATE
DESCRIPTION_PAGE_BANNER_SOURCE_SITE
DESCRIPTION_PAGE_LINK_BOOKMARK
DESCRIPTION_PAGE_NAME
DESCRIPTION_PAGE_STATS_DELETE
DESCRIPTION_PERMISSION_HOURS
DESCRIPTION_PERMISSION_SCOPE_category
DESCRIPTION_PERMISSION_SCOPE_module
DESCRIPTION_PERMISSION_SCOPE_page
DESCRIPTION_PERMISSION_SCOPE_privilege
DESCRIPTION_PERMISSION_SCOPE_type
DESCRIPTION_PERMISSION_SCOPE_zone
DESCRIPTION_POSTING_RATES
DESCRIPTION_PRIMARY_GROUP
DESCRIPTION_PROBATION
DESCRIPTION_PURCHASE_MAIL
DESCRIPTION_RSS_FEED
DESCRIPTION_SEARCH_STATISTICS
DESCRIPTION_SECONDARY_GROUP
DESCRIPTION_SELF_LEARNING_CACHE
DESCRIPTION_SEND_TRACKBACKS
DESCRIPTION_SHORTNAME
DESCRIPTION_SMART_TOPIC_NOTIFICATION
DESCRIPTION_STATS_CACHE
DESCRIPTION_SUPER_MEMBERS
DESCRIPTION_SUPER_MODERATORS
DESCRIPTION_TEMPLATES
DESCRIPTION_THEME
DESCRIPTION_THEME_IMAGES_CACHE
DESCRIPTION_USE_INTEREST_LEVELS
DESCRIPTION_VIEWS_PER_DAY
DESCRIPTION_VIEWS_PER_HOUR
DESCRIPTION_VIEWS_PER_MONTH
DESCRIPTION_VIEWS_PER_WEEK
DESCRIPTION_VIEW_FTP_FILES
DESCRIPTION_WELCOME_MESSAGE
DESCRIPTION_WP_XML
DESCRIPTION_ZONE_RENAME
DESCRIPTION_ZONE_RENAME_DEFAULT_ZONE
DETECTED_BAN
DETECT_JAVASCRIPT
DETECT_LANG_BROWSER
DETECT_LANG_FORUM
DEVELOPMENT_VIEWS
DEV_DATABASE_SETTINGS
DEV_DATABASE_SETTINGS_HELP
DIFF_NONE
DIFF_TOO_MUCH
DIRECTORY_NOT_FOUND
DISABLED_FUNCTION
DISABLE_PLUPLOAD_CONFIRM
DISK_USAGE
DISPATCH_CONFIRMATION_MESSAGE
DISPATCH_STATUS
DISPLAY_ELEMENTS
DISPLAY_PHP_ERRORS
DISPLAY_REVIEW_STATUS
DLOAD_SEARCH_INDEX
DODGY_GET_HACK
DOMAIN_EXAMPLE
DOWNLOAD_LICENCES
DT_FIELDMAPS
DT_GRID
DT_TABULAR
DT_TITLELIST
ECOMMERCE_TEST_MODE
ECOM_CATD_image
ECOM_CATD_price_pre_tax
ECOM_CATD_reduction_end
ECOM_CATD_reduction_start
ECOM_CATD_sku
ECOM_CATD_stock_level
ECOM_CATD_stock_level_maintain
ECOM_CATD_stock_level_warn_at
ECOM_CATD_tax_type
ECOM_CATD_weight
ECOM_CAT_description
ECOM_CAT_image
ECOM_CAT_price_pre_tax
ECOM_CAT_product_title
ECOM_CAT_reduction_end
ECOM_CAT_reduction_start
ECOM_CAT_sku
ECOM_CAT_stock_level
ECOM_CAT_stock_level_maintain
ECOM_CAT_stock_level_warn_at
ECOM_CAT_tax_type
ECOM_CAT_weight
EDITAREA
EDITING_CONTENT_IN_LANGUAGE_STAFF
EDIT_FORWARDING_DOMAIN
EDIT_MEDIA
EDIT_MENU_ITEM
EDIT_OWN_AUTHOR
EDIT_OWN_BANNER
EDIT_OWN_CATALOGUE_ENTRY
EDIT_OWN_DOWNLOAD
EDIT_OWN_GALLERY
EDIT_OWN_LIVE_POLL
EDIT_OWN_MEDIA
EDIT_OWN_NEWS
EDIT_OWN_NEWS_BLOG
EDIT_OWN_NEWS_CATEGORY
EDIT_OWN_POLL
EDIT_OWN_QUIZ
EDIT_PERMISSION_PRODUCT
EDIT_POP3_DOMAIN
EDIT_STAFF
EDIT_TIME
EDIT_TIME_LIMIT
EDIT_UNDER
EDIT_WARNING
EDIT_WIKI_PAGE_BODY
EDIT_WIKI_PAGE_SUBJECT
EDIT_WIKI_POST_BODY
EDIT_WIKI_POST_SUBJECT
EMAILS_ONLY
EMOTICON_RELEVANCE_LEVEL_0
EMOTICON_RELEVANCE_LEVEL_1
EMOTICON_RELEVANCE_LEVEL_2
EMOTICON_RELEVANCE_LEVEL_3
EMOTICON_RELEVANCE_LEVEL_4
ENCRYPTION_ERROR
ENCRYPTION_KEY
ENCRYPTION_KEY_ERROR
ERROR_FRACTIONAL_EDIT
ERROR_HANDLING_RSS_FEED
ERROR_MAIL
ERROR_NOT_ACCEPT_CONTAINER
ERROR_NOT_ACCEPT_IMAGES
ERROR_NOT_ACCEPT_VIDEOS
ERROR_NOT_CORRECT_DATABASE
ERROR_NOT_CORRECT_FILES
ERROR_NOT_CORRECT_LINKING
ERROR_NOT_CORRECT_LINKING_POSSIBLY
ERROR_OCCURRED_SUBJECT
EVAL_ERROR
EVAL_HACK
EVIL_POSTED_FORM_2_HACK
EVIL_POSTED_FORM_3_HACK
EVIL_POSTED_FORM_HACK
EXPORT_COMCODE_PAGE
EXPORT_COMCODE_PAGE_TEXT
FAILED_TO_UPLOAD_BACKUP_BODY
FAILED_TO_UPLOAD_BACKUP_SUBJECT
FIELD_TYPES__CHOICES
FIELD_TYPES__MAGIC
FIELD_TYPES__NUMBERS
FIELD_TYPES__OTHER
FIELD_TYPES__REFERENCES
FIELD_TYPES__TEXT
FIELD_TYPES__UPLOADSANDURLS
FILEDUMP_COUNT_FILES
FILEDUMP_DELETE
FILEDUMP_DELETE_FILE
FILEDUMP_DELETE_FOLDER
FILEDUMP_DISK_USAGE
FILEDUMP_EDIT
FILEDUMP_EMBED
FILEDUMP_IMAGE_URLS_LARGE
FILEDUMP_IMAGE_URLS_MEDIUM
FILEDUMP_IMAGE_URLS_SMALL
FILEDUMP_MOVE
FILTERCODE_OP_CO
FILTERCODE_OP_EQ
FILTERCODE_OP_EQE
FILTERCODE_OP_FT
FILTERCODE_OP_GT
FILTERCODE_OP_GTE
FILTERCODE_OP_LT
FILTERCODE_OP_LTE
FILTERCODE_OP_NE
FILTERCODE_OP_NEE
FILTERCODE_OP_RANGE
FILTERCODE_UNKNOWN_FIELD
FRACTIONAL_EDIT
FRACTIONAL_EDIT_CANCELLED
FRACTIONAL_EDIT_CANCEL_CONFIRM
FXML_FIELD_NOT_IN_SET
FXML_FIELD_NOT_IN_SET_SECRETIVE
FXML_FIELD_PATTERN_FAIL
FXML_FIELD_SHUNNED
FXML_FIELD_SHUNNED_SUBSTRING
FXML_FIELD_TOO_LONG
FXML_FIELD_TOO_SHORT
GAE_APPLICATION
GAE_BUCKET_NAME
GALLERIES_DEFAULT_SORT_ORDER
GALLERIES_SUBCAT_NARROWIN
GALLERY_ENTRIES_FLOW_PER_PAGE
GALLERY_ENTRIES_REGULAR_PER_PAGE
GALLERY_FEATURES
GALLERY_FEEDBACK_FIELDS
GALLERY_FOR_DOWNLOAD
GALLERY_IMAGE_LIMIT_HIGH
GALLERY_IMAGE_LIMIT_LOW
GALLERY_MEDIA_TITLE_REQUIRED
GALLERY_MEMBER_SYNCED
GALLERY_MODE_IS
GALLERY_NAME_ORDER
GALLERY_PERMISSIONS
GALLERY_REP_IMAGE
GALLERY_SELECTORS
GALLERY_VIDEO_LIMIT_HIGH
GALLERY_VIDEO_LIMIT_LOW
GALLERY_WATERMARKS
GD_THUMB_ERROR
HAS_LOW_MEMORY_LIMIT
HAVE_DEFAULT_BANNERS_ADVERTISING
HAVE_DEFAULT_BANNERS_DONATION
HAVE_DEFAULT_CATALOGUES_CONTACTS
HAVE_DEFAULT_CATALOGUES_FAQS
HAVE_DEFAULT_CATALOGUES_LINKS
HAVE_DEFAULT_CATALOGUES_PROJECTS
HAVE_DEFAULT_CPF_SET
HAVE_DEFAULT_FULL_EMOTICON_SET
HAVE_DEFAULT_RANK_SET
HEADER_SPLIT_HACK
HEADER_TEXT_ADMINZONE
HEADER_TEXT_collaboration
HELP_INSERT_DISTINGUISHING_TEMPCODE
HIT_REFERER
HIT_SEARCH
HONEYPOT_PHRASE
HONEYPOT_URL
IMAP
IMAP_ERROR
IMPORT_ALL
IMPORT_ALL_FINISHED
IMPORT_MATCH_OR_SKIP
IMPORT_MATCH_OR_SPECIFIC_POSITION
IMPORT_MATCH_OR_WARN
IMPORT_NEWS_DONE
IMPORT_NOT_IMPORTED
IMPORT_NO_COMMENTS_FORUM
IMPORT_POSITION
IMPORT_REBUILD_CACHE
IMPORT_REPLACE
IMPORT_REPLACE_OVERWRITE
IMPORT_REPLACE_SKIP
IMPORT_SESSION_NEW
IMPORT_SESSION_NEW_DELETE
IMPORT_TO_SPECIFIC_POSITION
IMPORT_WARNINGS_GIVEN
IMPORT_WARNING_CONVERT
IMPORT_WORDPRESS_DONE
IMPOSSIBLE_TYPE_USED
INSERT_ABSTRACTION_SYMBOL
INSERT_ARITHMETICAL_SYMBOL
INSERT_DIRECTIVE
INSERT_FORMATTING_SYMBOL
INSERT_LOGICAL_SYMBOL
INSERT_PROGRAMMATIC_SYMBOL
INSERT_SYMBOL
INSTALLED_CNS
INSTALL_ADDON
INSTALL_BLOCK
INSTALL_COMPLETE
INSTALL_ERROR
INSTALL_MODULE
INSTALL_SLOW_SERVER
INSTALL_WRITE_ERROR
IPN_ADDRESS
IPN_ADDRESS_TEST
IPN_BAD_TRIAL
IPN_DIGEST
IPN_EMAIL_ERROR
IPN_PASSWORD
IPN_SOCKET_ERROR
IPN_SUB_PERIOD_WRONG
IPN_SUB_RECURRING_WRONG
IPN_UNVERIFIED
JAVASCRIPT_ERROR
JAVASCRIPT_EXECUTED
LAME_SPAM_HACK
LANGUAGE_CACHE
LANGUAGE_CORRUPTION
LAST_RAN_AT
LEADER_BOARD_SIZE
LEADER_BOARD_START_DATE
LEECH_BLOCK
LENGTH_UNIT_d
LENGTH_UNIT_m
LENGTH_UNIT_w
LENGTH_UNIT_y
LOWLEVEL_PERMISSIONS
LOW_DISK_SPACE_MAIL
LOW_DISK_SPACE_SUBJECT
LOW_MEMORY_LIMIT
MISSING_AGGREGATE_TYPE
MISSING_ATTACHMENT
MISSING_AVATAR
MISSING_CAPTION_ERROR
MISSING_CONTENT_TYPE
MISSING_CONTENT_TYPE_TEMPLATE
MISSING_CUSTOM_FIELD
MISSING_FILE
MISSING_FILES
MISSING_FORUM
MISSING_IMPORT_SESSION
MISSING_INSTALLATION_FILE
MISSING_LANG_STRING
MISSING_LANG_FILE
MISSING_MENU
MISSING_MODULE_REFERENCED
MISSING_PARAM
MISSING_PHOTO
MISSING_RESOURCE_COMCODE
MISSING_SOURCE_FILE
MISSING_SYMBOL
MISSING_TEMPLATE_FILE
MISSING_TEMPLATE_PARAMETER
MISSING_TEXT_FILE
MISSING_THEME_IMAGE_FOR_MENU
MISSING_URL_COMCODE
MISSING_URL_ERROR
MODULE_TRANS_NAME_admin
MODULE_TRANS_NAME_cms
MYSQL_ONLY
MYSQL_OPTIMISE
MYSQL_QUERY_CHANGES_MADE
MYSQL_TOO_OLD
NAG_CONTENT_REVIEWS
NAG_COPYRIGHT_DATE
NAG_FORUMS
NAG_MONITOR_GIFTS
NAG_OPEN_WEBSITE
NAG_POINTSTORE
NAG_SETUP_CRON
NAG_SETUP_PROFILE
NAG_VALIDATE
NAG_WIKI
NEXT_ITEM_add_image_to_this
NEXT_ITEM_add_one_catalogue
NEXT_ITEM_add_one_image
NEXT_ITEM_add_one_video
NEXT_ITEM_add_to_catalogue
NEXT_ITEM_add_to_category
NEXT_ITEM_add_video_to_this
NEXT_ITEM_adminzone
NEXT_ITEM_edit_one_catalogue
NEXT_ITEM_edit_one_image
NEXT_ITEM_edit_one_video
NEXT_ITEM_edit_this_catalogue
NEXT_ITEM_edit_this_category
NEXT_ITEM_view_this_category
NEXT_REVIEW_DATE
NO_FTP_ACCESS
NO_FTP_CONNECT
NO_FTP_DIR
NO_FTP_LOGIN
NO_GD_ON_SERVER
NO_GD_ON_SERVER_PNG
NO_PHP_IN_TEMPLATES
NO_SHELL_ZIP_POSSIBLE
NO_SHELL_ZIP_POSSIBLE2
NO_UPGRADE_DONE
NO_XML_ON_SERVER
NO_ZIP_ON_SERVER
ORDERBY_HACK
ORDER_PLACED_MAIL_MESSAGE
ORDER_PLACED_MAIL_SUBJECT
PERMISSIONS_TREE
PERMISSIONS_TREE_EDITOR_MULTI_SELECTED
PERMISSIONS_TREE_EDITOR_ONE_SELECTED
PERMISSIONS_TREE_EDITOR_SAVED
PHPINFO
PINTERACE_HELP
PINTERFACE_VIEW_NO
POST_HISTORY_DAYS
POST_HISTORY_MEMBER
POST_HISTORY_POST
POST_HISTORY_TOPIC
QUERY_FAILED
QUERY_FAILED_TOO_BIG
QUERY_NULL
REALTIME_RAIN
REALTIME_RAIN_BUTTON
REMOVE_PERIODIC_NEWSLETTER
REMOVE_STAFF
RENAME_THEME
REPEAT_PERMISSION_NOTICE
REQUIRES_TTF
SAFE_MODE
TABLES_CREATED
TABLE_ERROR
TABLE_FIXED
TEMPCODE_NOT_ARRAY
TEMPCODE_TESTER
TEMPCODE_TOO_MANY_CLOSES
TEMPLATES_CSS
TEMPLATES_HTML
TEMPLATES_JAVASCRIPT
TEMPLATES_TEXT
TEMPLATES_WITH_EDIT_LINKS
TEMPLATES_WITH_HTML_COMMENT_MARKERS
TEMPLATES_XML
TEMPLATE_ALTERED
TEMPLATE_CACHE
TEMPLATE_RENAMED
TEMPLATE_TREE
TEMPLATE_WILL_NEED_RESTORING
TERMINAL_PROBLEM_ACCESSING_RESPONSE
TEXT_ABSTRACT_FILE_MANAGEMENT
THEMEWIZARD_2_SAMPLE_CONTENT
THEMEWIZARD_2_SAMPLE_TITLE
THEME_IMAGES_CACHE
THEME_IMAGE_EDITING
THEME_IMAGE_NEW
THEME_IMAGE_RENAMED
THEME_TO_SAVE_INTO
TOO_MANY_QUERIES
UNINSTALLED
UNINSTALL_ADDON
UNINSTALL_MODULE
UNKNOWN_DIRECTIVE
UNKNOWN_PRIVILEGE_PRESET
UNMATCHED_DIRECTIVE
UNRESOLVABLE_COLOURS
UNVALIDATED_ENTRIES
UNVALIDATED_MAIL_A
UNVALIDATED_MAIL_B
UNVALIDATED_MAIL_C
UNVALIDATED_RESOURCES
URL_MONIKER
URL_MONIKERS_ENABLED
URL_MONIKER_CONFLICT_PAGE
URL_MONIKER_CONFLICT_ZONE
URL_MONIKER_TAKEN
WEBSTANDARDS_CHECK
WEBSTANDARDS_COMPAT
WEBSTANDARDS_CSS
WEBSTANDARDS_ERROR
WEBSTANDARDS_EXT_FILES
WEBSTANDARDS_JAVASCRIPT
WEBSTANDARDS_WCAG
WEBSTANDARDS_XHTML
VB_UNIQUE_ID
VB_UNIQUE_ID_DESCRIP
VERSION_INFO
VIEW_PAGE_QUERIES
WARNING_DISK_SPACE
WARNING_FILE_ADDON
WARNING_FILE_ALIEN
WARNING_FILE_CHMOD
WARNING_FILE_FROM_UNINSTALLED_ADDON
WARNING_FILE_FUTURE_FILES
WARNING_FILE_MISSING_FILE_ENTIRELY
WARNING_FILE_MISSING_ORIGINAL_BUT_HAS_OVERRIDE
WARNING_FILE_OUTDATED
WARNING_FILE_OUTDATED_ORIGINAL
WARNING_FILE_OUTDATED_ORIGINAL_AND_OVERRIDE
WARNING_MAX_EXECUTION_TIME
WARNING_MBSTRING_FUNC_OVERLOAD
WARNING_MOVED_MODULES
WARN_TO_STACK_TRACE
WARN_TO_STACK_TRACE_2
WARN_TO_STACK_TRACE_3
WRITE_ERROR_CREATE
WRITE_ERROR_DIRECTORY_REPAIR
WRITE_ERROR_MISSING_DIRECTORY
XML_ATTRIBUTE_ERROR
XML_BROKEN_END
XML_JS_TAG_ESCAPE
XML_MORE_CLOSE_THAN_OPEN
XML_NEEDED
XML_NO_CLOSE
XML_NO_CLOSE_MATCH
XML_PARSING_NOT_SUPPORTED
XML_TAG_BAD_ATTRIBUTE
XML_TAG_CLOSE_ANOMALY
XML_TAG_DUPLICATED_ATTRIBUTES
XML_TAG_OPEN_ANOMALY
X_PER_DAY
X_PER_MONTH
X_PER_WEEK
X_PER_YEAR
ZIP_ERROR
ZIP_ERROR_MZIP
_DELETE_MEMBER_ADMIN
_DELETE_MEMBER_MERGE
_DESCRIPTION_BANNER_TYPE
_DESCRIPTION_MAIL_COST
_DESCRIPTION_THEME
_DESCRIPTION_THEME_CNS
_EDIT_CUSTOM_PRODUCT
_EDIT_FORWARDING_DOMAIN
_EDIT_MENU
_EDIT_PERMISSION_PRODUCT
_EDIT_POP3_DOMAIN
_IMPORT_ALL_FINISHED
_LOGOWIZARD
__SUHOSIN_MAX_VARS_TOO_LOW
__TRANSLATE_CODE
__TRANSLATE_CONTENT
ADD_MENU
ADD_MENU_ITEM
CODE_EDITOR
MISSING_BLOCK_FILE
MM_APPLY_TWICE
MM_TOOLTIP_AUDIT
MM_TOOLTIP_CMS
MM_TOOLTIP_DASHBOARD
MM_TOOLTIP_DOCS
MM_TOOLTIP_SECURITY
MM_TOOLTIP_SETUP
MM_TOOLTIP_STRUCTURE
MM_TOOLTIP_STYLE
MM_TOOLTIP_TOOLS
MODERATE_CHATROOMS
NOTIFICATION_SPAM_CHECK_BLOCK_BODY_APPROVE
NOTIFICATION_SPAM_CHECK_BLOCK_BODY_BAN
NOTIFICATION_SPAM_CHECK_BLOCK_BODY_BLOCK
NOTIFICATION_SPAM_CHECK_BLOCK_SUBJECT_APPROVE
NOTIFICATION_SPAM_CHECK_BLOCK_SUBJECT_BAN
NOTIFICATION_SPAM_CHECK_BLOCK_SUBJECT_BLOCK
NO_DATA_IMPORTED
NO_DELETE_ROOT
NO_POP3
NO_SUCH_CONTENT_TYPE
NO_SUCH_IMAGE
NO_SUCH_RENDERER
NO_SUCH_THEME
NO_SUCH_THEME_IMAGE
NO_THEME_PERMISSION
ORPHANED_TAGS
PATH_HACK
RECURSIVE_TREE_CHAIN
RSS_UNKNOWN_VERSION
RSS_XML_ERROR
RSS_XML_MISSING
SETUPWIZARD_1_DESCRIBE
SETUPWIZARD_1_DESCRIBE_ALT
SETUPWIZARD_5x_DESCRIBE
SETUPWIZARD_NOT_RUN
SET_REDIRECTS
SMTP
SOFTWARE_CHAT_EXTRA
SOFTWARE_CHAT_STANDALONE
SPAMMER_DETECTION
SPAM_REPORT_NO_EMAIL_OR_USERNAME
SPAM_REPORT_SITE_FLOODING
SPAM_REPORT_TRIGGERED_SPAM_HEURISTICS
SPECIAL_CLICK_TO_EDIT
STAFF_USERNAME_CHANGED_MAIL
STAFF_USERNAME_CHANGED_MAIL_SUBJECT
SUHOSIN_MAX_VALUE_TOO_SHORT
SUHOSIN_MAX_VARS_TOO_LOW
SYNDICATED_IP_BAN
TICKET_CANNOT_BIND_MAIL
TICKET_CANNOT_BIND_SUBJECT
TOO_MANY_IMPORT_SESSIONS
UNBAN_MEMBER_MAIL
UNBAN_MEMBER_MAIL_SUBJECT
UNBLOCK_MEMBER
UNCLOSED_DIRECTIVE_OR_BRACE
UNCLOSED_SYMBOL
UNKNOWN_AUTH_SCHEME_IN_DB
VIEW_FTP_FILES
VIEW_PAST_WINNERS
YOUR_NEW_ZONE
YOUR_NEW_ZONE_PAGE
ZONE_ACCESS
ZONE_BETWEEN
ZONE_EDITOR
_ZONE_EDITOR
ASCII_ENTITY_URL_HACK
AUTO_BAN_HACK_MESSAGE
AUTO_BAN_SUBJECT
CAPTCHAFAIL_HACK
HACK_ATTACK_SUBJECT
REFERRER_IFRAME_HACK
SCRIPT_TAG_HACK
SCRIPT_UPLOAD_HACK
SCRIPT_URL_HACK
SCRIPT_URL_HACK_2
SQL_INJECTION_HACK
TICKET_OTHERS_HACK
UNBAN_IP
UNBAN_MEMBER
MULTI_MODERATION_WILL
MULTI_MODERATION_WILL_CLOSE
MULTI_MODERATION_WILL_MOVE
MULTI_MODERATION_WILL_OPEN
MULTI_MODERATION_WILL_PIN
MULTI_MODERATION_WILL_POST
MULTI_MODERATION_WILL_SINK
MULTI_MODERATION_WILL_TITLE_SUFFIX
MULTI_MODERATION_WILL_UNPIN
MULTI_MODERATION_WILL_UNSINK
CONTENT_REVIEWS
CONTENT_REVIEW_AUTO_ACTION
CONTENT_REVIEW_AUTO_ACTION_delete
CONTENT_REVIEW_AUTO_ACTION_leave
CONTENT_REVIEW_AUTO_ACTION_unvalidate
PAYMENT_GATEWAY_TESTING_MODE
PAYMENT_GATEWAY_manual
PAYMENT_GATEWAY_paypal
PAYMENT_GATEWAY_secpay
PAYMENT_GATEWAY_worldpay
PAYMENT_STATE_active
PAYMENT_STATE_cancelled
PAYMENT_STATE_delivered
PAYMENT_STATE_new
PAYMENT_STATE_paid
PAYMENT_STATE_pending
'));

$lang_strings_admin = array_merge($lang_strings_admin_initial, find_strings($files_admin));
$lang_strings_non_admin = array_diff(find_strings($files_non_admin), $lang_strings_admin_initial);
$lang_strings_unknown = array();

$hooks = find_all_hooks('systems', 'config');
foreach ($hooks as $hook => $place) {
    if ($place == 'sources_custom') {
        continue;
    }

    require_code('hooks/systems/config/' . filter_naughty($hook));

    $ob = object_factory('Hook_config_' . $hook);
    if (method_exists($ob, 'get_details')) {
        $details = $ob->get_details();
        $lang_strings_admin[] = $details['human_name'];
    }
}

$all_strings = array();
$all_strings_in_lang = array();
$strings_files = array();

$lang = get_param_string('lang', fallback_lang());

$d = get_file_base() . '/lang/' . fallback_lang();
$dh = opendir($d);
while (($f = readdir($dh)) !== false) {
    if (substr($f, -4) == '.ini') {
        $strings = get_lang_file_map(fallback_lang(), basename($f, '.ini'), true);
        $all_strings = array_merge($all_strings, array_keys($strings));

        $strings_lang = get_lang_file_map($lang, basename($f, '.ini'), true);
        $all_strings_in_lang += $strings_lang;

        foreach (array_keys($strings) as $str) {
            $strings_files[$str] = $f;
        }

        foreach (array_diff(array_keys($strings), $lang_strings_non_admin, $lang_strings_admin) as $str) {
            if ($f == 'tips' || $f == 'lookup' || $f == 'backups' || $f == 'debrand' || $f == 'version' || $f == 'errorlog' || $f == 'profiling' || $f == 'email_log' || $f == 'cns_config' || $f == 'realtime_rain' || $f == 'custom_comcode' || $f == 'aggregate_types' || $f == 'cns_multi_moderations' || $f == 'webstandards.ini' || $f == 'commandr.ini' || $f == 'stats.ini' || $f == 'actionlog.ini' || $f == 'abstract_file_manager.ini' || $f == 'submitban.ini' || $f == 'ssl.ini' || $f == 'addons.ini' || $f == 'installer.ini' || $f == 'cleanup.ini' || $f == 'staff_checklist.ini' || $f == 'themes.ini' || $f == 'upgrade.ini' || preg_match('#^(MODULE_TRANS_NAME_admin|MODULE_TRANS_NAME_cms|ABSTRACTION_SYMBOL_|ARITHMETICAL_SYMBOL_|PROGRAMMATIC_SYMBOL_|DIRECTIVE_|FORMATTING_SYMBOL_|LOGICAL_SYMBOL_|SYMBOL_|PRIVILEGE|FU|FIELDTYPE|CHECKLIST|CONFIG_OPTION|CONFIG_CATEGORY|CONFIG_GROUP|DOC|TIP|WCAG|XHTML|CSS|COMCODE_TAG|CMD|BLOCK)_#', $str) != 0) {
                $lang_strings_admin[] = $str;
            } elseif (preg_match('#^ACCESS_DENIED__|ACTIVITY_|NOTIFICATION_TYPE_#', $str) != 0) {
                $lang_strings_non_admin[] = $str;
            } else {
                $lang_strings_unknown[] = $str;
            }
        }
    }
}
closedir($dh);

$lang_strings_admin = array_intersect($all_strings, array_unique($lang_strings_admin));
$lang_strings_non_admin = array_intersect($all_strings, array_unique($lang_strings_non_admin));
$lang_strings_unknown = array_intersect($all_strings, array_unique($lang_strings_unknown));
sort($lang_strings_admin);
sort($lang_strings_non_admin);
sort($lang_strings_unknown);

$langs = find_all_langs();
echo '<p>Show completeness for a language:</p><ul>';
foreach (array_keys($langs) as $lang) {
    echo '<li><a href="' . escape_html(get_self_url(true, false, array('lang' => $lang))) . '">' . escape_html($lang) . '</a></li>';
}
echo '</ul>';

echo '<p>These are known admin language strings:</p><ul>';
foreach (array_diff($lang_strings_admin, $lang_strings_non_admin) as $str) {
    $has = isset($all_strings_in_lang[$str]);
    echo '<li>' . escape_html($str) . ' (from ' . $strings_files[$str] . ') ' . ($has ? '<span style="color: green">&#x2713;</span>' : '<span style="color: red">&#x2717;</span>') . '</li>';
}
echo '</ul>';

echo '<p>These are known non-admin language strings:</p><ul>';
foreach (array_diff($lang_strings_non_admin, $lang_strings_admin) as $str) {
    $has = isset($all_strings_in_lang[$str]);
    echo '<li>' . escape_html($str) . ' (from ' . $strings_files[$str] . ') ' . ($has ? '<span style="color: green">&#x2713;</span>' : '<span style="color: red">&#x2717;</span>') . '</li>';
}
echo '</ul>';

echo '<p>These are shared language strings:</p><ul>';
foreach (array_intersect($lang_strings_non_admin, $lang_strings_admin) as $str) {
    $has = isset($all_strings_in_lang[$str]);
    echo '<li>' . escape_html($str) . ' (from ' . $strings_files[$str] . ') ' . ($has ? '<span style="color: green">&#x2713;</span>' : '<span style="color: red">&#x2717;</span>') . '</li>';
}
echo '</ul>';

echo '<p>These are strings of unknown status:</p><ul>';
foreach ($lang_strings_unknown as $str) {
    $has = isset($all_strings_in_lang[$str]);
    echo '<li>' . escape_html($str) . ' (from ' . $strings_files[$str] . ') ' . ($has ? '<span style="color: green">&#x2713;</span>' : '<span style="color: red">&#x2717;</span>') . '</li>';
}
echo '</ul>';

function do_dir($dir)
{
    $files = array();

    $d = get_file_base() . '/' . $dir;

    $dh = opendir($d);
    while (($f = readdir($dh)) !== false) {
        if (is_dir($dir . '/' . $f) && $f != '.' && $f != '..' && strpos($f, 'custom') === false) {
            $files = array_merge($files, do_dir($dir . '/' . $f));
        } else {
            if (substr($f, -4) == '.php') {
                $files[] = $dir . '/' . $f;
            }
        }
    }
    closedir($dh);

    return $files;
}

function find_strings($from)
{
    $out = array();

    foreach ($from as $file) {
        $c = file_get_contents(get_file_base() . '/' . $file);
        $matches = array();

        $num_matches = preg_match_all('#do_lang(_tempcode)?\(\'([^\']+)\'(?! \.)#', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $str = $matches[2][$i];
            $str = preg_replace('#.*:#', '', $str);
            $out[$str] = true;
        }

        $num_matches = preg_match_all('#\{\!(\w+)[\},]#', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $str = $matches[1][$i];
            $str = preg_replace('#.*:#', '', $str);
            $out[$str] = true;
        }
    }

    return array_keys($out);
}
