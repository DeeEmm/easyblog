<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

define('EB_ROOT', JPATH_ROOT . '/components/com_easyblog');
define('EBLOG_ROOT', EB_ROOT);
define('EBLOG_ADMIN_ROOT', JPATH_ROOT . '/administrator/components/com_easyblog');
define('EBLOG_THEMES', EBLOG_ROOT . '/themes');
define('EB_MEDIA', JPATH_ROOT . '/media/com_easyblog');
define('EB_SCRIPTS', EB_MEDIA . '/scripts');
define('EB_MODELS', EBLOG_ADMIN_ROOT . '/models');

// Legacy (Prior to 6.0.x)
define('EBLOG_MEDIA', EB_MEDIA);
define('EBLOG_SCRIPTS', EB_SCRIPTS);

define('EBLOG_ADMIN_THEMES', EBLOG_ADMIN_ROOT . '/themes');
define('EBLOG_ADMIN_UPDATES', EBLOG_ADMIN_ROOT . '/updates');
define('EBLOG_ADMIN_INCLUDES', EBLOG_ADMIN_ROOT . '/includes');
define('EBLOG_LIB', EBLOG_ADMIN_ROOT . '/includes');
define('EBLOG_GDPR_DOWNLOADS', EBLOG_MEDIA . '/downloads');
define('EBLOG_DEFAULTS', EBLOG_ADMIN_ROOT . '/defaults');

// Blog privacy states
define('BLOG_PRIVACY_PUBLIC', '0');
define('BLOG_PRIVACY_PRIVATE', '1');

// Filter states
define('EBLOG_FILTER_ALL', 'all');
define('EBLOG_FILTER_PUBLISHED', 'published');
define('EBLOG_FILTER_UNPUBLISHED', 'unpublished');
define('EBLOG_FILTER_SCHEDULE', 'scheduled');
define('EBLOG_FILTER_DRAFT', 'draft');
define('EBLOG_FILTER_PENDING', 'pending');
define('EBLOG_MAX_FEATURED_POST', '10');

// Subscription types
define('EBLOG_SUBSCRIPTION_SITE', 'site');
define('EBLOG_SUBSCRIPTION_CATEGORY', 'category');
define('EBLOG_SUBSCRIPTION_BLOGGER', 'blogger');
define('EBLOG_SUBSCRIPTION_TEAMBLOG', 'team');
define('EBLOG_SUBSCRIPTION_ENTRY', 'entry');

// Meta Tag ID for views
define('META_ID_LATEST', '1');
define('META_ID_GATEGORIES', '2');
define('META_ID_TAGS', '3');
define('META_ID_BLOGGERS', '4');
define('META_ID_TEAMBLOGS', '5');
define('META_ID_FEATURED', '6');
define('META_ID_ARCHIVE', '7');
define('META_ID_SEARCH', '8');
define('META_TYPE_POST', 'post');
define('META_TYPE_VIEW', 'view');
define('META_TYPE_BLOGGER', 'blogger');
define('META_TYPE_TEAM', 'team');
define('META_TYPE_SEARCH', 'search');
define('META_TYPE_CATEGORY', 'category');
define('META_TYPE_TAG', 'tag');

// Comment statuses
define('EBLOG_COMMENT_UNPUBLISHED', 0);
define('EBLOG_COMMENT_PUBLISHED', 1);
define('EBLOG_COMMENT_MODERATE', 2);

// Oauth integrations
define('EBLOG_OAUTH_LINKEDIN', 'linkedin');
define('EBLOG_OAUTH_FACEBOOK', 'facebook');
define('EBLOG_OAUTH_TWITTER', 'twitter');
define('EBLOG_OAUTH_FLICKR', 'flickr');
define('EBLOG_OAUTH_GOOGLE', 'google');

// category privacy follow acl status
define('CATEGORY_PRIVACY_ACL', '2');
define('CATEGORY_ACL_ACTION_VIEW', '1');
define('CATEGORY_ACL_ACTION_SELECT', '2');
define('CATEGORY_ACL_ACTION_SPECIFIC', '3');

// Powered by link
define('EBLOG_POWERED_BY_LINK', '<div style="text-align: center; padding: 20px 0;"><a href="https://stackideas.com">Powered by EasyBlog for Joomla!</a></div>');

// Ratings
define('EASYBLOG_RATINGS_ENTRY', 'entry');

// teamblog access
define('EBLOG_TEAMBLOG_ACCESS_MEMBER', 1);
define('EBLOG_TEAMBLOG_ACCESS_REGISTERED', 2);
define('EBLOG_TEAMBLOG_ACCESS_EVERYONE', 3);

define('EBLOG_COMMENT_STATUS_UNPUBLISH', 0);
define('EBLOG_COMMENT_STATUS_PUBLISHED', 1);
define('EBLOG_COMMENT_STATUS_MODERATED', 2);

define('EBLOG_AVATAR_LARGE_WIDTH', 200);
define('EBLOG_AVATAR_LARGE_HEIGHT', 200);
define('EBLOG_AVATAR_THUMB_WIDTH', 60);
define('EBLOG_AVATAR_THUMB_HEIGHT', 60);

// Featured settings
define('EBLOG_FEATURED_BLOG', 'post');
define('EBLOG_FEATURED_BLOGGER', 'blogger');
define('EBLOG_FEATURED_TEAMBLOG', 'teamblog');

// @since 2.1 (Media manager type)
define('EBLOG_MEDIA_FOLDER', 'folder');
define('EBLOG_MEDIA_IMAGE', 'image');
define('EBLOG_MEDIA_FILE', 'file');
define('EBLOG_VIDEO_FILE', 'video');
define('EBLOG_MEDIA_THUMBNAIL_PREFIX', 'thumb_');
define('EBLOG_MEDIA_PAGINATION_TOTAL', 200);
define('EBLOG_MEDIA_PERMISSION_ERROR', -300);
define('EBLOG_MEDIA_UPLOAD_SUCCESS', 5);
define('EBLOG_MEDIA_TRANSPORT_ERROR', -200);
define('EBLOG_MEDIA_SECURITY_ERROR', -400);
define('EBLOG_MEDIA_FILE_EXTENSION_ERROR', -601);
define('EBLOG_MEDIA_FILE_TOO_LARGE', -600);
define('EBLOG_GALLERY_EXTENSION', '.jpg|.png|.gif|.JPG|.PNG|.GIF|.jpeg|.JPEG');

// Services
define('EB_SERVICE_VERSION', 'https://stackideas.com/updater/manifests/easyblog');
define('EB_SERVICE_JUPDATE', 'https://stackideas.com/jupdates/manifest/easyblog');
define('EB_SERVICE_PACKAGES_DISCOVER', 'https://services.stackideas.com/packages/discover/easyblog');
define('EB_SERVICE_PACKAGES_DOWNLOAD', 'https://services.stackideas.com/packages/download/easyblog');


// @since 3.1.7506 (Reporting)
define('EBLOG_REPORTING_POST', 'post');

// @since 3.5.7531
// EasyDiscuss notification types.
define('EBLOG_NOTIFICATIONS_TYPE_COMMENT', 'comment');
define('EBLOG_NOTIFICATIONS_TYPE_BLOG', 'blog');
define('EBLOG_NOTIFICATIONS_TYPE_RATING', 'rating');

// @since 3.5.7706
// Microblogging types
define('EBLOG_MICROBLOG_EMAIL', 'email');
define('EBLOG_MICROBLOG_TEXT', 'text');
define('EBLOG_MICROBLOG_PHOTO', 'photo');
define('EBLOG_MICROBLOG_QUOTE', 'quote');
define('EBLOG_MICROBLOG_LINK', 'link');
define('EBLOG_MICROBLOG_VIDEO', 'video');
define('EBLOG_MICROBLOG_TWITTER', 'twitter');

define('EBLOG_STREAM_NUM_ITEMS', 3); // in days

// @since 3.5
// Pagination types
define('EBLOG_PAGINATION_BLOGGERS', 'bloggers');
define('EBLOG_PAGINATION_CATEGORIES', 'categories');


// @since 3.5
define('EBLOG_BLOG_IMAGE_PREFIX', '2e1ax');
define('EBLOG_USER_VARIATION_PREFIX', 'a1sx2');
define('EBLOG_SYSTEM_VARIATION_PREFIX', 'b2ap3');
define('EBLOG_VARIATION_USER_TYPE', 'user');
define('EBLOG_VARIATION_SYSTEM_TYPE', 'system');

// @since 3.5
// Media sources
define('EBLOG_MEDIA_SOURCE_LOCAL', 'local');
define('EBLOG_MEDIA_SOURCE_FLICKR', 'flickr');
define('EBLOG_MEDIA_SOURCE_AMAZON', 'amazon');
define('EBLOG_MEDIA_SOURCE_JOMSOCIAL', 'jomsocial');
define('EBLOG_MEDIA_SOURCE_EASYSOCIAL', 'easysocial');

// @since 3.5
// Media types
define('EBLOG_MEDIA_TYPE_IMAGE', 'image');
define('EBLOG_MEDIA_TYPE_FOLDER', 'folder');
define('EBLOG_MEDIA_TYPE_VIDEO', 'video');
define('EBLOG_MEDIA_TYPE_AUDIO', 'audio');
define('EBLOG_MEDIA_TYPE_FILE', 'file');

// default resize type
define('EBLOG_IMAGE_DEFAULT_RESIZE', 'within');

// @since 4.0
// Session namespace
define('EBLOG_SESSION_NAMESPACE', 'easyblog');

// @since 4.0
// Language server
define('EBLOG_KEYWORDS_SERVER', 'https://services.stackideas.com/keywords/easyblog');
define('EBLOG_LANGUAGES_SERVER', 'https://services.stackideas.com/translations/easyblog');
define('EASYBLOG_OPTIMIZER_SERVER', 'https://services.stackideas.com/optimizer');

define('EB_LANGUAGES_INSTALLED', 1);
define('EB_LANGUAGES_NOT_INSTALLED', 0);
define('EB_LANGUAGES_NEEDS_UPDATING', 3);
define('EBLOG_LANGUAGES_INSTALLED', EB_LANGUAGES_INSTALLED);
define('EBLOG_LANGUAGES_NOT_INSTALLED', EB_LANGUAGES_NOT_INSTALLED);
define('EBLOG_LANGUAGES_NEEDS_UPDATING', EB_LANGUAGES_NEEDS_UPDATING);

// Exceptions
define('EASYBLOG_EXCEPTION_MESSAGE', 'message');
define('EASYBLOG_EXCEPTION_UPLOAD', 'upload');
define('EASYBLOG_MSG_SUCCESS', 'success');
define('EASYBLOG_MSG_WARNING', 'warning');
define('EASYBLOG_MSG_ERROR', 'error');
define('EASYBLOG_MSG_INFO', 'info');
define('EASYBLOG_ERROR_CODE_FIELDS', -500);

// Revisions
define('EASYBLOG_REVISION_FINALIZED', 0);
define('EASYBLOG_REVISION_DRAFT', 1);
define('EASYBLOG_REVISION_PENDING', 2);

// Publishing state
define('EASYBLOG_POST_BLANK', 9);
define('EASYBLOG_POST_DRAFT', 3);
define('EASYBLOG_POST_PENDING', 4);
define('EASYBLOG_POST_PUBLISHED', 1);
define('EASYBLOG_POST_SCHEDULED', 2);
define('EASYBLOG_POST_UNPUBLISHED', 0);

// Special case for trash filter in dashboard
define('EASYBLOG_DASHBOARD_TRASHED', -1);

// Post state
define('EASYBLOG_POST_NORMAL', 0);
define('EASYBLOG_POST_TRASHED', 1);
define('EASYBLOG_POST_ARCHIVED', 2);

// Post source
define('EASYBLOG_POST_SOURCE_SITEWIDE', 'easyblog.sitewide');
define('EASYBLOG_POST_SOURCE_TEAM', 'easyblog.team');
define('EASYBLOG_POST_SOURCE_JOMSOCIAL_GROUP', 'jomsocial.group');
define('EASYBLOG_POST_SOURCE_JOMSOCIAL_EVENT', 'jomsocial.event');
define('EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT', 'easysocial.event');
define('EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP', 'easysocial.group');
define('EASYBLOG_POST_SOURCE_EASYSOCIAL_PAGE', 'easysocial.page');

// Block type
define('EASYBLOG_BLOCK_MODE_DIFF', 'compare');
define('EASYBLOG_BLOCK_MODE_VIEWABLE', 'viewable');
define('EASYBLOG_BLOCK_MODE_EDITABLE', 'editable');
define('EASYBLOG_POST_DOCTYPE_LEGACY', 'legacy');

// Blog rendering modes
define('EASYBLOG_VIEW_ENTRY', 'entry');
define('EASYBLOG_VIEW_LIST', 'list');
define('EASYBLOG_NO_DATE', '0000-00-00 00:00:00');
define('EASYBLOG_DATE_PLACEHOLDER', 'DD/MM/YYYY HH:MM');
define('EB_DATE_INPUT_FORMAT', 'd/m/Y h:m');

//no. of teamblog post to retrive for each team in team listing page.
define('EASYBLOG_TEAMBLOG_LISTING_NO_POST', 5);

// Post properties
define('EASYBLOG_STRIP_TAGS', true);

// Composer blocks publishing state
define('EASYBLOG_COMPOSER_BLOCKS_UNPUBLISHED', 0);
define('EASYBLOG_COMPOSER_BLOCKS_PUBLISHED', 1);
define('EASYBLOG_COMPOSER_BLOCKS_NOT_VISIBLE', 2);

define('EASYBLOG_POST_TEMPLATE_CORE', 1);
define('EASYBLOG_POST_TEMPLATE_NOT_CORE', 0);
define('EB_POST_TEMPLATE_NON_GLOBAL', 0);
define('EB_POST_TEMPLATE_GLOBAL', 1);
define('EB_POST_TEMPLATE_BLANK', 2);

define('EASYBLOG_JROUTER_MODE_SEF', 1);

// Categories publishing state
define('EASYBLOG_CATEGORY_PUBLISHED', 1);
define('EASYBLOG_CATEGORY_UNPUBLISHED', 0);

// GDPR download request state
define('EASYBLOG_DOWNLOAD_REQ_NEW', 0);
define('EASYBLOG_DOWNLOAD_REQ_LOCKED', 1);
define('EASYBLOG_DOWNLOAD_REQ_PROCESS', 2);
define('EASYBLOG_DOWNLOAD_REQ_READY', 3);

// Subscription process behaviour
// In this single opt-in behaviour, we still send out the confirmation email but this email just let user know you already subscribed successfully
define('EASYBLOG_SUBSCRIPTION_SINGLE_OPT_IN', 1);
define('EASYBLOG_SUBSCRIPTION_DOUBLE_OPT_IN', 2);
define('EASYBLOG_SUBSCRIPTION_WITHOUT_CONFIRMATION_EMAIL', 3);

// stream context type
define('EASYBLOG_STREAM_CONTEXT_TYPE', 'blog');

// media storage type
define('EASYBLOG_MEDIA_STORAGE_TYPE_JOOMLA', 'joomla');
define('EASYBLOG_MEDIA_STORAGE_TYPE_AMAZON', 'amazon');

// Packages
define('EB_PACKAGE_INSTALLED', 1);
define('EB_PACKAGE_NOT_INSTALLED', 0);
define('EB_PACKAGE_NEEDS_UPDATING', 3);

// Polls
define('EB_POLL_FORM_OPTION_SELECT', 'selectPoll');
define('EB_POLL_FORM_OPTION_SAVE', 'savePoll');

// Generic published state
define('EB_UNPUBLISHED', 0);
define('EB_PUBLISHED', 1);
define('EB_TYPE_IMAGE', 'image');
define('EB_TYPE_VIDEO', 'video');

// google import max items per page
define('EB_GOOGLEIMPORT_ITEMS_LIMIT', 30);
