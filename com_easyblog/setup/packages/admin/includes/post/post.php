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

use Foundry\Libraries\DateHelper;

class EasyBlogPost extends EasyBlog
{
	// @debug: Port list (remove when done)
	static $_methodsToPort = [];
	static $_propsToPort = [];

	// Data properties
	public $access;
	public $uid;
	public $id = 0;
	public $created_by;
	public $created;
	public $modified;
	public $title = '';
	public $permalink;
	public $content = '';
	public $intro = '';
	public $excerpt;
	public $category_id;
	public $published;
	public $state;
	public $publish_up;
	public $publish_down;
	public $ordering;
	public $vote;
	public $hits;
	public $allowcomment;
	public $login_to_read;
	public $lottie_files;
	public $polls;
	public $subscription;
	public $frontpage;
	public $isnew;
	public $blogpassword;
	public $latitude;
	public $longitude;
	public $address = '';
	public $text = '';
	public $posttype;
	public $source_id;
	public $source_type;
	public $robots = '';
	public $copyrights = '';
	public $image = '';
	public $language;
	public $locked;
	public $ip;
	public $doctype;
	public $document;
	public $revision_id;
	public $categories;
	public $tags;
	public $fields;
	public $keywords;
	public $description;
	public $canonical;
	public $custom_title = '';
	public $send_notification_emails;
	public $autoposting;
	public $association;
	public $params;
	public $author_alias = '';
	public $isfeatured;
	public $reactions;
	public $version;
	public $media;
	public $autopost_date;
	public $fields_class;
	public $readmore= '';
	public $event = '';
	public $creator;
	public $author;
	public $isFeatured;
	public $team_id;
	public $totalComments;
	public $category;
	public $assets;
	public $catid;
	public $contentImage;
	public $date;
	public $hasPinterest;
	public $komentodisable;
	public $komentoenable;
	public $komentolock;
	public $introtext;
	public $blogger;


	// This is to determine if the post is using its first image for its cover
	public $isUsingFirstImage = false;

	public $meta;

	static $enumerations = array(
		'id'           => array('bindable' => false, 'linked' => false),
		'created_by'   => array('bindable' => true , 'linked' => false),
		'created'      => array('bindable' => true , 'linked' => false),
		'modified'     => array('bindable' => false, 'linked' => false),
		'title'        => array('bindable' => true , 'linked' => false),
		'permalink'    => array('bindable' => true , 'linked' => false),
		'content'      => array('bindable' => true , 'linked' => false),
		'intro'        => array('bindable' => true , 'linked' => false),
		'excerpt'      => array('bindable' => true , 'linked' => false),
		'category_id'  => array('bindable' => true , 'linked' => false),
		'published'    => array('bindable' => true , 'linked' => false),
		'state'        => array('bindable' => false, 'linked' => false), // 5.0
		'publish_up'   => array('bindable' => true , 'linked' => false),
		'publish_down' => array('bindable' => true , 'linked' => false),
		'autopost_date'=> array('bindable' => true , 'linked' => false),
		'ordering'     => array('bindable' => true , 'linked' => false),
		'vote'         => array('bindable' => false, 'linked' => false),
		'hits'         => array('bindable' => false, 'linked' => false),
		'access'       => array('bindable' => true , 'linked' => false),
		'allowcomment' => array('bindable' => true , 'linked' => false),
		'login_to_read' => ['bindable' => true , 'linked' => false],
		'lottie_files' => ['bindable' => true , 'linked' => false],
		'polls' => ['bindable' => true , 'linked' => false],
		'subscription' => array('bindable' => true , 'linked' => false),
		'frontpage'    => array('bindable' => true , 'linked' => false),
		'isnew'        => array('bindable' => false, 'linked' => false),
		'blogpassword' => array('bindable' => true , 'linked' => false),
		'latitude'     => array('bindable' => true , 'linked' => false),
		'longitude'    => array('bindable' => true , 'linked' => false),
		'address'      => array('bindable' => true , 'linked' => false),
		'posttype'     => array('bindable' => true , 'linked' => false), // This is microblog.
		'source_id'    => array('bindable' => true , 'linked' => false), // 5.0
		'source_type'  => array('bindable' => true , 'linked' => false), // 5.0
		'reactions'    => array('bindable' => false , 'linked' => false), // 5.1
		'robots'       => array('bindable' => true , 'linked' => false),
		'copyrights'   => array('bindable' => true , 'linked' => false),
		'image'        => array('bindable' => true , 'linked' => false),
		'language'     => array('bindable' => true , 'linked' => false),
		'locked'       => array('bindable' => false, 'linked' => false),
		'ip'           => array('bindable' => false, 'linked' => false),
		'doctype'      => array('bindable' => true , 'linked' => false), // 5.0
		'document'     => array('bindable' => true , 'linked' => false), // 5.0
		'revision_id'  => array('bindable' => true , 'linked' => false), // 5.0
		'autoposting'  => array('bindable' => true , 'linked' => false),
		'categories'   => array('bindable' => true , 'linked' => true),
		'tags'         => array('bindable' => true , 'linked' => true),
		'fields'       => array('bindable' => true , 'linked' => true), // 5.0
		'fields_class' => array('bindable' => true , 'linked' => true), // 5.2
		'keywords'     => array('bindable' => true , 'linked' => true),
		'description'  => array('bindable' => true , 'linked' => true),
		'canonical' => array('bindable' => true , 'linked' => true),
		'custom_title' => array('bindable' => true , 'linked' => true),
		'send_notification_emails' => array('bindable' => true, 'linked' => false),
		'association' => array('bindable' => false, 'linked' => false),
		'author_alias' => array('bindable' => true , 'linked' => false),
		'isfeatured' => array('bindable' => true , 'linked' => false),
		'version' => array('bindable' => true , 'linked' => false),
		'media' => array('bindable' => true , 'linked' => false)
	);

	// Bind options
	static $defaultBindOptions = [

		// If true, allow binding even from non-bindable properties.
		// This is useful for migrators.
		'force' => false,

		// TODO: Rename blog_contribute to isssitewide in composer form.
		'remap' => array(
			array('eb_language'    , 'language')
		)
	];

	// Save options
	static $defaultSaveOptions = [
		'normalizeData' => true,
		'validateData' => true,
		'updateModifiedTime' => true,
		'applyDateOffset' => false,
		'checkEmptyTitle' => true,
		'checkBlockedWords' => true,
		'checkMinContentLength' => true,
		'logUserIpAddress' => true,
		'skipCreateRevision' => false,
		'useAuthorAsRevisionOwner' => false,
		'skipCustomFields' => false,
		'skipNotifications' => false,
		'triggerPlugins' => true,
		'triggerStateChangeOnly' => false,
		'processAutopost' => true,
		'skipCategoriesUpdate' => false,
		'skipTagsUpdate' => false,
		'skipAssociationUpdate' => false,
		'silent' => false, // Quietly save into db without executing postSave.
		'copyPost' => false,
		'isArchiveProcess' => false,
		'isScheduleProcess' => false,

		// Allow caller to specify reject message
		'rejectMessage' => ''
	];

	// Data source
	static $blank;
	public $original;
	public $workbench;
	public $revision;
	public $post;


	private $saveOptions = array();
	private $_debug = false;

	// Cache items, used to store data from caller.
	public static $commentCounts = array();
	public static $comments = array();
	public static $previewComments = array();
	public static $ratings = array();
	public static $customFields = array();
	public static $postMetas = array();
	public static $postVotes = array();

	// This stores the formatted contents for this post.
	private $formattedContents = array();
	private $formattedIntros = array();

	// Extended from EasyBlog class
	public $config;
	public $doc;
	public $app;
	public $input;
	public $my;


	// Globals
	public $user;
	public $acl;
	public $debug = false;
	public $contributionDisplay;
	public $featured;
	public $associations;
	public $jcfields;
	public $commentCount;
	public $showRating;
	public $protect;
	public $summary;
	public $cover;
	public $isCoverImage;
	public $videoEmbedCover;
	public $videoCover;

	public function __construct($uid = null, $userId = null)
	{
		// Load site's language file
		EB::loadLanguages();

		// This will call EasyBlog class to construct $config, $doc, $app, $input, $my.
		parent::__construct();

		// Globals
		$this->uid = $uid;

		// The author of this item
		$this->user = EB::user($userId);

		// The acl of the author
		$this->acl = EB::acl($this->user->id);

		// If this is a new post, we want to create a new workbench
		if (!$uid) {
			$this->createNewWorkbench();
		} else {
			$this->load($uid);
		}

		// Set the post object to the router so that they can easily retrieve it.
		EBR::setPost($this);
	}

	/**
	 * Parses the UID <postId>.<revisionId> into an object
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function parseUid($uid)
	{
		// Extract id & revision from uid.
		$parts = explode(".", $uid);

		$obj = new stdClass();
		$obj->postId = $parts[0];
		$obj->revisionId = isset($parts[1]) ? $parts[1] : null;

		return $obj;
	}

	/**
	 * Loads a post item given the uid of the item.
	 * UID is a string representation of:
	 * <postId>.<revisionId>
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function load($uid)
	{
		// Get the uid object
		$uid = self::parseUid($uid);
		$postId = $uid->postId;
		$revisionId = $uid->revisionId;

		// Load post
		$post = $this->loadPost($postId);

		if (!$post) {
			return;
		}

		$this->post = $post;

		// we need to reassign few property from the post jtable
		$this->isnew = $post->isnew;

		// If revisionId is not given, assume current revision used by post.
		if (empty($revisionId)) {
			$revisionId = (int) $post->revision_id;
			$this->uid = $post->id . '.' . $revisionId;
		}


		// If revisionId is still empty, this might be a legacy post with no revision binded to them.
		if (empty($revisionId)) {

			$this->checkoutFromPost();

			// This is most likely a legacy post as we need to create a new revision for and existing post without any revisions.
			$this->saveRevision();
			return;

		} else {

			$revision = self::loadRevision($revisionId);

			$this->revision = $revision;
			$this->revision_id = $revisionId;
		}

		$this->checkout();

		// this is to support falang.
		if (EB::isFalangActivated()) {
			$this->title = $post->title;
			// for 'translated' permalink to work correctly, admin must either enabled the 'unicode alias' setting in EB
			// so that the permalink prepend with post id or admin shouldn't translate the permalink at all from falang.
			$this->permalink = $post->permalink;
		}

		$this->hits = (int) $post->hits;
		$this->vote = $post->vote;
		$this->ordering = $post->ordering;
		$this->locked = $post->locked;
		$this->params = $post->params;

		$this->author_alias = $post->author_alias;

		// Determines if the post is featured
		$this->isfeatured = false;

		// For scheduled posts, we need to test this differently since scheduled post is always new
		if (!$this->isnew || $this->isScheduled()) {

			if (isset($post->featured)) {
				$this->isfeatured = $post->featured;
			} else if (isset($post->isFeatured)) {
				$this->isfeatured = $post->isFeatured;
			} else {
				$model = EB::model('Blog');
				$this->isfeatured = $model->isFeatured($this->id);
			}
		}

		// this is to ensure the source_type will be loaded correctly.
		$this->source_type = ($this->source_type) ? $this->source_type : $post->source_type;
		$this->posttype = ($this->posttype) ? $this->posttype : $post->posttype;
		$this->doctype = ($this->doctype) ? $this->doctype : $post->doctype;
		$this->reactions = $post->reactions;
		$this->version = $post->version;
		$this->media = $post->media;


		// Try to load the meta now
		$this->loadMeta();

		if ($this->meta->id) {
			$this->keywords = $this->meta->keywords;
			$this->description = $this->meta->description;
			$this->canonical = $this->meta->canonical;
		}
	}

	/**
	 * Loads the JTable Post object
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function loadPost($postId)
	{
		$post = null;
		$exists = false;

		if (EB::cache()->exists($postId, 'post') ) {
			$post = EB::cache()->get($postId, 'post');
			$exists = true;
		} else {
			$post = EB::table('Post');
			$exists = $post->load($postId);
		}

		if (!$exists) {

			// Perhaps the given id is an alias, try to look it up
			$state = $post->load(array('permalink' => $postId));

			if (!$state) {
				return false;
			}

			// Set the post id and revision id.
			$this->uid = $post->id;
			$this->revision_id = $post->revision_id;
		}

		return $post;
	}

	/**
	 * Loads a JTable Revision object
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function loadRevision($revisionId)
	{
		$revision = null;

		if (EB::cache()->exists($revisionId, 'revision') ) {
			$revision = EB::cache()->get($revisionId, 'revision');
			$exists = true;
		} else {

			$revision = EB::table('Revision');
			$revision->load($revisionId);
		}

		return $revision;
	}

	/**
	 * Given a permalink, find the post id and load the post.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function loadByPermalink($permalink)
	{
		$db = EB::db();

		if (EB::isFalangActivated()) {

			$alternateLink = EBString::str_ireplace(':', '-', $permalink);

			$query = array();
			$query[] = 'SELECT ' . $db->quoteName('reference_id') . ' FROM ' . $db->quoteName('#__falang_content');
			$query[] = 'WHERE ' . $db->quoteName('value') . ' IN (' . $db->Quote($permalink) . ',' . $db->Quote($alternateLink) . ')';
			$query[] = 'AND ' . $db->quoteName('reference_field') . '=' . $db->Quote('permalink');

			$query = implode(' ', $query);

			$db->setQuery($query);
			$id = (int) $db->loadResult();

			if ($id) {
				return $this->load($id);
			}
		}

		// Try to look for the permalink
		$query = array();
		$query[] = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('permalink') . '=' . $db->Quote($permalink);

		$query = implode(' ', $query);
		$db->setQuery($query);
		$id = (int) $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if (!$id) {
			$permalink = EBString::str_ireplace(':', '-', $permalink);

			// Try to look for the permalink
			$query = array();
			$query[] = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName('#__easyblog_post');
			$query[] = 'WHERE ' . $db->quoteName('permalink') . '=' . $db->Quote($permalink);

			$query = implode(' ', $query);

			$db->setQuery($query);
			$id = (int) $db->loadResult();
		}

		if ($id) {
			return $this->load($id);
		}

		return false;
	}

	/**
	 * Deprecated. Use post.list.icon helper instead
	 *
	 * @deprecated	6.0.0
	 */
	public function getIcon($classname = '')
	{
		static $icons = [];

		$posttype = $this->posttype ? $this->posttype : 'standard';

		$index = $posttype . $classname;

		if (!isset($icons[$index])) {
			$theme = EB::themes();
			$icons[$index] = $theme->html('post.icon', $this->posttype, $classname);
		}

		return $icons[$index];
	}

	/**
	 * Loads meta data about the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function loadMeta()
	{
		// Store the meta data for this post now
		if (isset(self::$postMetas[$this->id])) {
			$meta = self::$postMetas[$this->id];
		} else {
			$meta = EB::table('Meta');
			$meta->loadByType(META_TYPE_POST, $this->id);
		}

		$this->meta = $meta;
	}

	/**
	 * Retrieves a list of fields
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function loadFields()
	{
	}

	/**
	 * Switches the post to the revision
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function updateToRevision()
	{

	}

	/**
	 * Creates a new workbench object of itself.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function createNewWorkbench()
	{
		// Create blank object
		// Original object clones from blank object when working on a new post.
		$workbench = new stdClass();

		foreach (self::$enumerations as $prop => $enum) {
			$workbench->$prop = null;
		}

		// If this is a new workbench, the isnew state should always be true
		$workbench->isnew = true;

		$this->setWorkbench($workbench);

		$this->categories = array();
		$this->tags = array();
		$this->fields = array();
	}

	/**
	 * Sets the workbench for this current instance
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setWorkbench($workbench)
	{
		// Assign to workbench
		$this->bind($workbench, array('force' => true));

		// Remember original values so that we can
		// check modifications made to this post
		// and normalize the values before saving.
		$this->original = clone $workbench;

		return $workbench;
	}

	/**
	 * Checkout from a specific revision
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function checkout()
	{
		// Checkout from revision
		// We check out from revision table instead of post table because it faster.
		// Even revision currently being used by post table will always be identical.
		$this->checkoutFromRevision();
	}

	/**
	 * Checkout from a specific revision
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function checkoutFromRevision()
	{
		$workbench = $this->revision->getContent();

		if (! $workbench) {

			// somehow the revision id is exists but the revisions.content is empty.
			// we need to regenerate the revisions.content.
			$this->checkoutFromPost();

		} else {
			$this->setWorkbench($workbench);
		}

		$this->revision_id = $this->revision->id;
	}

	/**
	 * Checkout from a specific post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function checkoutFromPost()
	{
		$workbench = new stdClass();

		// Get the post data from the property
		$post = $this->post;

		// Populate table properties
		foreach (self::$enumerations as $prop => $enum) {

			// Skip linked properties because they are not part of post table.
			if ($enum['linked']) {
				continue;
			}

			// TO BE REMOVED
			// temporary suppress the notice warning.
			@$workbench->$prop = $post->$prop;
		}

		// Populate categories
		// $categories = $post->getCategories();
		// foreach ($categories as $category) {
		// 	$this->categories[] = $category->id;
		// }

		// // Populate tags
		// $tags = $post->getTags();
		// foreach ($tags as $tag) {
		// 	$this->tags[] = $tag->title;
		// }

		// Get the meta of the post
		$this->loadMeta();

		// Get the fields related to the post
		$this->loadFields();

		// Deal with legacy posts (might not be needed with migrator in place)
		if (empty($this->source_id) && empty($this->source_type)) {
				$this->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;
				$this->source_id   = 0;
		}

		return $this->setWorkbench($workbench);
	}

	/**
	 * Creates a new post whenever the editor is initialized
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function create($options = array())
	{

		if (!empty($this->uid)) {
			throw EB::exception('Create method cannot be executed on an existing post.');
		}

		$checkACL = isset($options['checkAcl']) ? $options['checkAcl'] : true;

		if ($checkACL) {
			// Ensure that the current user is allowed to create new post
			if (!$this->canCreate()) {
				throw EB::exception('User not allowed to create post.');
			}
		}

		// Set the document type
		$this->doctype = $this->user->getEditor() == 'composer' ? 'ebd' : 'legacy';

		// if there is the doctype overriding flag passed in, we use that.
		if (isset($options['overrideDoctType']) && $options['overrideDoctType']) {
			$this->doctype = $options['overrideDoctType']; // accept 'ebd' or 'legacy' ONLY
		}

		// give default value so that dummy record creation will pass.
		$this->permalink = '';
		$this->posttype = '';
		$this->language = '*';
		$this->locked = 0;
		$this->ip = '';
		$this->params = '';

		// Fixed Joomla 4 JTable compatibility issue.
		$this->category_id = 0;

		// Ensures the current state of this post to be blank so that we know this is a new post.
		$this->published = EASYBLOG_POST_BLANK;

		// Save options. lets declare here.
		$saveOptions = array('validateData' => false);

		if (isset($options['skipCustomFields'])) {
			$saveOptions['skipCustomFields'] = $options['skipCustomFields'];
		}

		if (isset($options['overrideAuthorId']) && $options['overrideAuthorId']) {
			$saveOptions['overrideAuthorId'] = $options['overrideAuthorId'];
		}

		if (isset($options['checkAcl']) && $options['checkAcl']) {
			$saveOptions['checkAcl'] = $options['checkAcl'];
		}

		if (isset($options['triggerPlugins'])) {
			$saveOptions['triggerPlugins'] = $options['triggerPlugins'];
		}

		if (isset($options['fromFeedImport'])) {
			$saveOptions['fromFeedImport'] = $options['fromFeedImport'];
		}

		// if (isset($options['useAuthorAsRevisionOwner'])) {
		// 	$saveOptions['useAuthorAsRevisionOwner'] = $options['useAuthorAsRevisionOwner'];
		// }

		// Save post and skip validation while doing so.
		$this->save($saveOptions);

		$this->checkout();
	}

	/**
	 * A special function used to bind various data from the form post.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function restoreFromPost($data)
	{
		if (! $data) {
			return false;
		}

		// post publishing and state need to retrieve from original data.
		$oriPublishing = $this->published;

		// bind basic data
		$this->bind($data);

		// revert the publishing state from original data.
		$this->published = $oriPublishing;

		// now we need to bind the selected categories.
		// primary category
		if (isset($data['category_id'])) {
			$category = EB::table('Category');
			$category->load($data['category_id']);

			EB::cache()->set($category,'posts', $this->id, 'primarycategory');
			EB::cache()->set($category,'category');

		}

		// others categories
		if (isset($data['categories'])) {

			$primaryCatId = $data['category_id'];

			$categories = array();
			foreach ($data['categories'] as $catId) {
				$category = EB::table('Category');
				$category->load($catId);
				$category->primary = ($catId == $primaryCatId ) ? true : false;

				$categories[] = $category;

				EB::cache()->set($category,'category');
			}

			EB::cache()->set($categories,'posts', $this->id, 'category');
		}

		// tags
		if (isset($data['tags']) && $data['tags']) {

			$arrTags = explode(',', $data['tags']);
			$tags = array();

			foreach ($arrTags as $tag) {
				$table = EB::table('Tag');
				$table->load($tag, true);

				if ($table->id) {
					EB::cache()->set($table,'tag');
				} else {
					$table->title = $tag;
				}

				$tags[] = $table;
			}

			EB::cache()->set($tags,'posts', $this->id, 'tag');
		}

	}


	/**
	 * Binds a posted data
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function bind($data, $options = array(), $debug = false)
	{
		$bindOptions = array_merge_recursive(array(), self::$defaultBindOptions, $options);

		// Convert array to object
		if (is_array($data)) {
			$data = (object) $data;
		}

		// Remap data properties.
		foreach ($bindOptions['remap'] as $map) {

			$source = $map[0];
			$target = $map[1];

			if (isset($data->$source)) {
				$data->$target = $data->$source;

				unset($data->source);
			}
		}

		// Go through the list of properties of the post data
		foreach (self::$enumerations as $prop => $enum) {

			// Skip if this property is not bindable.
			// Non-bindable properties are usually those we do not want malicious users
			// to hijack just by altering post request values. For example, we don't want
			// users to alter the id, revision id, publishing state of a post.
			if (!$enum['bindable'] && !$bindOptions['force']) {
				continue;
			}

			if (isset($data->$prop)) {
				$this->$prop = $data->$prop;
			}
		}

		// bind association
		$this->association = array();

		if (isset($data->assoc_postids) && $data->assoc_postids) {
			for($i = 0; $i < count($data->assoc_postids); $i++) {
				if ($data->assoc_postids[$i]) {

					$obj = new stdClass();
					$obj->code = $data->assoc_code[$i];
					$obj->id = $data->assoc_postids[$i];
					$obj->post = $data->assoc_post[$i];

					$this->association[] = $obj;
				}
			}

			if ($this->language != '*' && $this->association) {
				// current selected language.
				$obj = new stdClass();
				$obj->code = $this->language;
				$obj->id = $this->id;
				$obj->post = $this->title;

				$this->association[] = $obj;
			}
		}

		// Bind post params
		if (isset($data->params) && $data->params) {
			$this->bindParams($data->params);
		}
	}

	/**
	 * Bind params in the post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function bindParams($newParams)
	{
		// Get stored params data
		$params = $this->getParams();

		// Merge the new params
		if ($newParams) {
			$newParams = json_encode($newParams);

			$newRegistry = new JRegistry($newParams);
			$data = $newRegistry->toArray();

			foreach ($data as $key => $value) {
				$params->set($key, $value);
			}
		}

		$this->params = $params->toString();
	}

	/**
	 * Archives a blog post on the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function trash()
	{
		$this->state = EASYBLOG_POST_TRASHED;

		// we need to set this date to empty so that the unpublish task will not pick up this entry.
		$this->publish_down	= '0000-00-00 00:00:00';

		// We do not want to run any validation since it's going to be trashed.
		$options = array('validateData' => false, 'normalizeData' => false, 'processAutopost' => false, 'skipCategoriesUpdate' => true, 'skipTagsUpdate' => true, 'skipCustomFields' => true);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_POST_TRASHED', 'post', array(
			'link' => $this->getEditLink(),
			'postTitle' => JText::_($this->title)
		));

		return $this->save($options);
	}

	/**
	 * Determines if this post allows comments
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function allowComments()
	{
		// If the EB comment option is turned off, then just return false.
		if (!$this->config->get('main_comment')) {
			return false;
		}

		// When comments are discontinued for a specific post, they don't appear anymore under the post
		if ($this->config->get('main_comment') && !$this->allowcomment) {
			// If this is the owner of the post or site admin, we'll allow to comment.
			if ($this->canEdit()) {
				return true;
			}

			return false;
		}

		return true;
	}

	/**
	 * Archives a blog post on the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function archive()
	{
		$this->state = EASYBLOG_POST_ARCHIVED;

		$options = array('validateData' => false, 'normalizeData' => false, 'skipCategoriesUpdate' => true, 'skipTagsUpdate' => true, 'skipCustomFields' => true, 'useAuthorAsRevisionOwner' => true, 'isArchiveProcess' => true, 'processAutopost' => false);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_POST_ARCHIVED', 'post', array(
			'link' => $this->getEditLink(),
			'postTitle' => JText::_($this->title)
		));

		return $this->save($options);
	}

	/**
	 * Unarchives a blog post on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @return	bool	Determines if the storing state is success
	 */
	public function unarchive()
	{
		$this->state = EASYBLOG_POST_NORMAL;

		$options = array('validateData' => false, 'normalizeData' => false, 'skipCategoriesUpdate' => true, 'skipTagsUpdate' => true, 'skipCustomFields' => true, 'useAuthorAsRevisionOwner' => true, 'isArchiveProcess' => true, 'processAutopost' => false);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_POST_UNARCHIVED', 'post', array(
			'link' => $this->getEditLink(),
			'postTitle' => JText::_($this->title)
		));

		return $this->save($options);
	}

	/**
	 * Allows caller to lock a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function lock()
	{
		$this->locked = true;

		$options = array('validateData' => false, 'normalizeData' => false, 'skipCategoriesUpdate' => true, 'skipTagsUpdate' => true, 'skipCustomFields' => true, 'skipAssociationUpdate' => true);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_POST_LOCKED', 'post', array(
			'link' => $this->getEditLink(),
			'postTitle' => JText::_($this->title)
		));

		return $this->save($options);
	}


	/**
	 * Allows caller to lock a blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unlock()
	{
		$this->locked = false;

		$options = array('validateData' => false, 'normalizeData' => false, 'skipCategoriesUpdate' => true, 'skipTagsUpdate' => true, 'skipCustomFields' => true, 'skipAssociationUpdate' => true);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_POST_UNLOCKED', 'post', array(
			'link' => $this->getEditLink(),
			'postTitle' => JText::_($this->title)
		));

		return $this->save($options);
	}

	/**
	 * Duplicates a post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function duplicate()
	{
		// Export the current data
		$data = $this->toData();

		$data->id = null;
		$data->published = EASYBLOG_POST_UNPUBLISHED;
		$data->title = JText::sprintf('COM_EASYBLOG_DUPLICATE_OF_POST', $this->title);

		// since duplicate will not normalize the data, we will need to manually normalize the permalink to
		// avoid same permalink. #520
		$model = EB::model('Blog');
		$data->permalink = $model->normalizePermalink($this->permalink);

		$data->revision_id = null;
		$data->autoposting = null;

		$post = EB::post();
		$post->bind($data);

		$state = $post->save(array('applyDateOffset' => false, 'normalizeData' => false, 'copyPost' =>true));

		return $state;
	}

	/**
	 * Moves the current blog post to a new category
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function move($categoryId)
	{
		$this->category_id = $categoryId;
		$this->categories = array($categoryId);

		// Set the save options
		$options = array_merge(array(), array('validateData' => false, 'normalizeData' => false, 'skipTagsUpdate' => true, 'processAutopost' => false, 'skipCustomFields' => true, 'skipAssociationUpdate' => true));

		return $this->save($options);
	}


	/**
	 * Allows moderator to reject this post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function reject($message, $userId = null)
	{
		$actor = JFactory::getUser($userId);

		// The state doesn't change.
		// Set the state of the post to draft again.
		$this->published = EASYBLOG_POST_DRAFT;

		// We do not want to run any validation since it's going to be rejected
		$options = [
			'validateData' => false,
			'normalizeData' => false,
			'processAutopost' => false,
			'skipCategoriesUpdate' => true,
			'skipTagsUpdate' => true,
			'skipCustomFields' => true,
			'skipAssociationUpdate' => true,
			'rejectMessage' => $message
		];

		$state = $this->save($options);

		return $state;
	}

	/**
	 * Allows moderator delete post under moderation.
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function deletePending()
	{
		// delete the pending approval revision attached to this post.
		$state = false;

		if ($this->revision && $this->revision->post_id && $this->revision->state == EASYBLOG_REVISION_PENDING) {

			$table = EB::table('Post');
			$table->load($this->revision->post_id);

			// if this is a new post that pending approval, we should just remove the actual post as well.
			if ($table->id && $table->published == EASYBLOG_POST_PENDING && $table->revision_id == $this->revision->id) {
				$this->delete();
			}

			// if not, we should just delete the pending approval revision.
			if ($table->revision_id != $this->revision->id) {
				$state = $this->revision->delete();
			}
		}

		return $state;
	}

	/**
	 * Allows moderator to approve this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function approve()
	{
		// Here we need to check if this post should be scheduled
		$this->published = EASYBLOG_POST_PUBLISHED;

		// Clear up any reject messages for this post if necessary
		$model = EB::model('PostReject');
		$model->clear($this->id);

		$this->save(array('validateData' => false, 'skipCategoriesUpdate' => false, 'skipTagsUpdate' => false, 'skipCustomFields' => false, 'skipAssociationUpdate' => true));
	}

	/**
	 * Publishes a blog post on the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function publish($options = [], $forceNotNew = false)
	{
		$hasNormalizeData = isset($options['normalizeData']) && $options['normalizeData'] ? $options['normalizeData'] : false;

		// Skip this if the publish post process do not need to normalize the post data
		if (!$hasNormalizeData) {
			$options = array_merge($options, array('normalizeData' => false));
		}

		// Set the publishing state
		$this->published = EASYBLOG_POST_PUBLISHED;
		$this->state = EASYBLOG_POST_NORMAL;

		// if that is schedule post update the isnew to 0
		if ($forceNotNew || $this->original->published == EASYBLOG_POST_SCHEDULED) {
			$this->isnew = false;
		}

		// Set the save options
		$saveOptions = [
				'validateData' => false,
				'skipCategoriesUpdate' => true,
				'skipTagsUpdate' => true,
				'skipCustomFields' => true,
				'skipAssociationUpdate' => true,
				'triggerStateChangeOnly' => true
			];

		$options = array_merge($options, $saveOptions);

		return $this->save($options);
	}

	/**
	 * Restore a blog post from the trash.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function restore($options = array())
	{
		$this->state = EASYBLOG_POST_NORMAL;

		// Set the save options
		$options = array_merge(array(), array('validateData' => false, 'normalizeData' => false, 'skipCategoriesUpdate' => true, 'skipTagsUpdate' => true, 'skipCustomFields' => true), $options);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_POST_RESTORED', 'post', array(
			'link' => $this->getEditLink(),
			'postTitle' => JText::_($this->title)
		));

		return $this->save($options);
	}

	/**
	 * Auto posts this post into social network sites
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function canAutopost()
	{
		// Determines if the primary category of this post requires auto posting or not
		$category = $this->getPrimaryCategory();
		return $category->autopost ? true : false;
	}



	/**
	 * Auto posts this post into social network sites
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function autopost($type, $force = false)
	{
		// If the post is not published, disallow this
		if (!$this->isPublished()) {
			EB::exception('COM_EASYBLOG_AUTOPOST_PLEASE_PUBLISH_BLOG', 'error');

			return false;
		}

		if ($type === 'schedule') {

			EB::autoposting()->shareSystem($this, null, $force);

			// Get if there is user autopost params
			$params = $this->getParams();

			$autoposting = json_decode($params->get('autoposting'));

			if (!empty($autoposting)) {
				// Only if the user choose to autopost ($this->autoposting)
				EB::autoposting()->shareUser($this, $autoposting, $force);
			}

			return true;
		}

		// Ensure that the auto posting for this type is allowed
		$key = $this->config->get('integrations_' . $type . '_api_key');
		$secret = $this->config->get('integrations_' . $type . '_secret_key');

		if (!$key || !$secret) {
			EB::exception(JText::sprintf('COM_EASYBLOG_AUTOPOST_KEYS_INVALID', ucfirst($type)), 'error');

			return false;
		}

		// First we need to auto post to the system authentications
		EB::autoposting()->shareSystem($this, $type, $force);

		// Then, we auto post for the respective user
		EB::autoposting()->shareUser($this, [$type], $force);
	}

	/**
	 * Unpublishes a post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unpublish($options = [])
	{
		// Set the state
		$this->published = EASYBLOG_POST_UNPUBLISHED;

		$defaultOptions = [
				'validateData' => false,
				'skipCategoriesUpdate' => true,
				'skipTagsUpdate' => true,
				'skipCustomFields' => true,
				'skipAssociationUpdate' => true,
				'triggerStateChangeOnly' => true
			];

		if ($options) {
			$defaultOptions = array_merge($defaultOptions, $options);
		}

		$state = $this->save($defaultOptions);

		if ($state) {

		}

		// Store the post
		return $state;
	}

	/**
	 * Add this post into user's favourites list
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function favourite($userId = null)
	{
		if (is_null($userId)) {
			$userId = $this->my->id;
		}

		// If this post has already been favourited, do not add it again
		if ($this->isFavourited()) {
			return false;
		}

		$table = EB::table('Favourites');
		$table->postId = $this->id;
		$table->userId = $userId;
		$table->type = 'post';
		$table->created = EB::date()->toSql();

		$state = $table->store();

		return $state;
	}

	/**
	 * Remove this post from user's favourites list
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function unfavourite($userId = null)
	{
		if (is_null($userId)) {
			$userId = $this->my->id;
		}

		// Check if this post is not inside user's favourites list.
		if (!$this->isFavourited()) {
			return false;
		}

		$model = EB::model('Favourites');
		$state = $model->removeFavourites($this->id, $userId);

		return $state;
	}

	/**
	 * Override functionality of JTable's hit method as we want to limit the hits based on the session.
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function hit($pk = null)
	{
		$pages = $this->input->get('pagestart', '', 'default');
		$allpages = $this->input->get('showall', '', 'default');

		// We know this is coming from pagebreak plugin. so do not count the hit.
		if ($pages || $allpages) {
			return true;
		}

		// Get users ip address
		$ip = $this->input->server->get('REMOTE_ADDR');

		// Match only known browsers
		$agent = $this->input->server->get('HTTP_USER_AGENT', '', 'default');
		$pattern = '/(Mozilla.*(Gecko|KHTML|MSIE|Presto|Trident)|Opera).*|Disqus/i';

		preg_match($pattern, $agent, $trackHits);

		// if this Disqus comment exist, we have to skip it
		// because every time this Disqus crawl on the site, it will use different IP address
		if ($trackHits && isset($trackHits[0]) && $trackHits[0] == 'Disqus') {
			return true;
		}

		if ($ip && !empty($this->id) && !empty($trackHits)) {

			$token = md5($ip . $this->id);
			$session = JFactory::getSession();
			$exists = $session->get($token, false);

			// If user was logged before, skip it
			if ($exists) {
				return true;
			}

			$session->set($token, 1);
		}

		// Load language files
		EB::loadLanguages();

		// Deduct points from respective systems
		// @rule: Integrations with EasyDiscuss
		EB::easydiscuss()->log('easyblog.view.blog', $this->my->id, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_VIEW_BLOG', $this->title));
		EB::easydiscuss()->addPoint('easyblog.view.blog', $this->my->id);
		EB::easydiscuss()->addBadge('easyblog.view.blog', $this->my->id);

		// Only give points if the viewer is viewing another person's blog post.
		if ($this->my->id != $this->created_by) {
			EB::easysocial()->assignBadge('blog.read', JText::_('COM_EASYBLOG_EASYSOCIAL_BADGE_READ_BLOG'));
			EB::easysocial()->assignPoints('blog.read');

			EB::altauserpoints()->assign('plgaup_easyblog_read_blog', $this->my->id, JText::sprintf('COM_EASYBLOG_AUP_READ_BLOG', $this->title));
		}

		// Mark notifications item in EasyDiscuss when the blog entry is viewed
		if ($this->config->get('integrations_easydiscuss_notification_blog')) {
			EB::easydiscuss()->readNotification($this->id, EBLOG_NOTIFICATIONS_TYPE_BLOG);
		}

		if ($this->config->get('integrations_easydiscuss_notification_comment')) {
			EB::easydiscuss()->readNotification($this->id, EBLOG_NOTIFICATIONS_TYPE_COMMENT);
		}

		if ($this->config->get('integrations_easydiscuss_notification_rating')) {
			EB::easydiscuss()->readNotification($this->id , EBLOG_NOTIFICATIONS_TYPE_RATING);
		}

		return $this->post->hit($pk);
	}

	/**
	 * retrieve media into post->media for easy reference.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getMedia()
	{
		$defaultData = array('images' => array(),'videos' => array(),'audios' => array(), 'galleries' => array(), 'pdf' => array());

		if (!$this->post) {
			return (object) $defaultData;
		}

		if (!$this->isEB51()) {
			$this->processPostIntroContent();
			$this->exportMedia();
		}

		if (!$this->post->media) {
			// we need to manually extract
			if ($this->doctype == 'ebd') {
				$document = EB::document($this->document);
				$data = $document->extractMedia();
			} else {
				$data = $this->extractLegacyMedia();
			}

			// update the db.
			$this->post->media = json_encode($data);
			$this->post->store();
		}

		$mediaObject = json_decode($this->post->media);

		return $mediaObject ? $mediaObject : (object) $defaultData;
	}


	/**
	 * extract media into post->media for easy reference.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function exportMedia()
	{
		// do not update the media if this is a draft or blank post.
		if ($this->isBlank() || $this->isDraft()) {
			return false;
		}

		$data = false;

		if ($this->doctype == 'ebd') {
			$document = EB::document($this->document);
			$data = $document->extractMedia();
		} else {
			// legacy post.
			$data = $this->extractLegacyMedia();
		}

		if ($data !== false) {
			$this->post->media = json_encode($data);
			$this->post->store();
		}

	}

	public function extractLegacyMedia()
	{
		// do not update the media if this is a draft or blank post.
		if ($this->isBlank() || $this->isDraft()) {
			return false;
		}

		// we should only process if the post belong to these type
		$supportedType = array('standard', 'email');

		$content = $this->intro . $this->content;

		$data = array('images' => array(),'videos' => array(),'audios' => array(), 'galleries' => array(), 'pdf' => array());

		if (isset($this->posttype) && in_array($this->posttype, $supportedType) || !$this->posttype) {

			$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

			$data['videos'] = EB::videos()->getItems($content, true, $useRelative);
			$data['pdf'] = EB::pdf()->getItems($content, true, $useRelative);
			$data['galleries'] = EB::gallery()->getItems($content, true, $useRelative);
			$data['audios'] = EB::audio()->getItems($content, true, $useRelative);
			$data['images'] = EB::truncater()->getImages($content, true);
		}

		return $data;
	}

	/**
	 * Init save options
	 *
	 * @since	5.3
	 * @access	public
	 */
	public function initSaveOptions($options = [])
	{
		// Set the save options
		$options = array_merge([], self::$defaultSaveOptions, $options);
		$this->saveOptions = $options;
	}

	/**
	 * Saves a blog post on the site. This method ensures that all saving process goes through the same routine.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function save($options = [], $debug = false)
	{
		// Set the save options
		$this->initSaveOptions($options);

		// Execute pre-saving routines
		$this->preSave($debug);


		// This needs to happen prior to saving the post so that the new revision get's created first.
		// If this post is going to be a draft and the existing revision is already finalized we should create a new revision and assign to this post first
		$createNewRevision = $this->isBeingDrafted() || $this->isBeingSubmittedForApproval();

		// Check to see if the prior revision was already finalized and we need to create a new revision.
		if ($createNewRevision && $this->revision->isFinalized()) {
			$this->createNewRevision();
		}

		// We need to save the post first in order to link the revision id with the post table.
		$this->savePost();


		$this->saveRevision();

		// export all media and save it under post->media for future reference.
		$this->exportMedia();

		// Store the categories that is associated with the post.
		if (!$this->saveOptions['skipCategoriesUpdate']) {
			$this->saveCategories();
		}

		if (!$this->saveOptions['skipTagsUpdate']) {
			// Store the tags that is associated with the post.
			$this->saveTags();
		}

		// Store the meta data that is associated with the post.
		$this->saveMeta();

		// Store the posts association that is associated with the post.
		if (!$this->saveOptions['skipAssociationUpdate']) {
			$this->saveAssociation();
		}

		// Stores the relationship of this post with any other sources.
		$this->saveRelation();

		// Store the fields that are related to the post
		if (!$this->saveOptions['skipCustomFields']) {
			$this->saveFields();
		}

		//save assets * for now only applied to link post
		if ($this->getType() == 'link') {
			$quickpost = EB::quickpost()->getAdapter('link');
			$quickpost->saveAssets($this);
		}

		// Store the featured option.
		$this->updateFeatured();


		// Store additional params
		$this->updateParams();

		// Clear the uploaded lottie files that no longer in the post after save
		$this->clearUnnecessaryLottie();

		// Execute post-saving routines
		$this->postSave();
	}

	/**
	 * Update neccessary params for this post.
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function updateParams()
	{
		// Update word count
		$this->getTotalWords(true);
	}

	/**
	 * update post featured.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function updateFeatured()
	{
		$model = EB::model('Featured');

		// check if this is a password protected or not. If yes, we will unfeatured it automaticially.
		if ($this->blogpassword) {
			return $model->removeFeatured(EBLOG_FEATURED_BLOG, $this->id);
		}

		if (!$this->acl->get('feature_entry')) {
			return true;
		}

		if ($this->isfeatured) {
			$model->makeFeatured(EBLOG_FEATURED_BLOG, $this->id);
		} else {
			$model->removeFeatured(EBLOG_FEATURED_BLOG, $this->id);
		}

		return true;
	}


	/**
	 * Before a blog post is stored, we want to perform specific operations here
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function preSave()
	{
		// Trigger content plugins before saving
		$triggerPlugins = isset($this->saveOptions['triggerPlugins']) ? $this->saveOptions['triggerPlugins'] : true;

		if ($triggerPlugins) {
			$this->triggerBeforeSave();
		}

		//apply date timezone offset if needed
		if ($this->saveOptions['applyDateOffset']) {
			$this->applyDateOffset();
		}

		// Normalize data
		if ($this->saveOptions['normalizeData']) {
			$this->normalize();
		}

		// Update post modified time
		if ($this->saveOptions['updateModifiedTime']) {
			$this->modified = EB::date()->toSql();
		}

		// Log ip to the last user modifying this post
		if ($this->saveOptions['logUserIpAddress']) {
			$this->ip = @$_SERVER['REMOTE_ADDR'];
		}

		// Validate data
		if ($this->saveOptions['validateData']) {
			$this->validate();
		}

		// the only time we should automatically set the publish date are:
		// 1. when a schedule post being publish either by cron or admin / author manually publish the scheduled post.
		// e.g. if user set the publish date to one month later. but tomorrow, user manually publish the post.

		if (!$this->isBackDated() && ($this->isBeingPublished() && $this->original->published == EASYBLOG_POST_SCHEDULED)) {
			$this->publish_up = EB::date()->toSql();
		}
	}

	/**
	 * Post processing when a blog post is saved
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function postSave()
	{
		// do not do anything if the post is a blank post.
		if ($this->isBlank()) {
			return;
		}

		// Whenever the blog post is stored, we need to clear the cache.
		$this->clearCache();

		// Triggers plugins after the post is stored
		$triggerPlugins = isset($this->saveOptions['triggerPlugins']) ? $this->saveOptions['triggerPlugins'] : true;

		if ($triggerPlugins) {
			$this->triggerAfterSave();
		}

		$actionlog = EB::fd()->getActionLog();

		// When this post is being submitted for approval we want to notify site administrator's
		if ($this->isBeingSubmittedForApproval()) {

			$actionlog->log('COM_EB_ACTIONLOGS_POST_SUBMIT_APPROVAL', 'post', array(
				'link' => $this->getEditLink(),
				'postTitle' => JText::_($this->title)
			));

			$this->notify(true, false);
		}

		// When this post is being approval and this post still under scheduled post we need to notify to post owner
		if ($this->isBeingScheduledForPublishing()) {
			$this->notify(false, EASYBLOG_POST_SCHEDULED, false, true);
		}

		// Process the autopost
		if ($this->saveOptions['processAutopost'] && $this->autopost_date == EASYBLOG_NO_DATE) {

			// When this post is saved and published we need to perform the auto posting for the system
			if ($this->isPublished()) {
				EB::autoposting()->shareSystem($this);
			}

			// When this post is saved, we might need to perform the auto posting
			// Only if the user choose to autopost it. (checkbox)
			if ($this->autoposting && $this->isPublished()) {
				EB::autoposting()->shareUser($this, $this->autoposting);
			}
		}

		// When this post is being published, we should add post actions here.
		if ($this->isBeingPublished()) {

			$actionlog->log('COM_EB_ACTIONLOGS_POST_BEING_PUBLISHED', 'post', array(
				'link' => $this->getEditLink(),
				'postTitle' => JText::_($this->title)
			));

			// Send notifications to subscribers
			if (!$this->isPasswordProtected()) {
				$this->notify();
			}

			// EasySocial Integrations
			// If $this->isBeingPublished, it will be always a new post.
			if (!isset($this->saveOptions['saveFromEasysocialStory'])) {
				EB::easysocial()->createBlogStream($this, true);
			}

			EB::easysocial()->updateBlogPrivacy($this->post);
			EB::easysocial()->assignPoints('blog.create', $this->created_by);
			EB::easysocial()->notifySubscribers($this, 'new.post');
			EB::easysocial()->addIndexerNewBlog($this);
			EB::easysocial()->assignBadge('blog.create', JText::_('COM_EASYBLOG_EASYSOCIAL_BADGE_CREATE_BLOG_POST'));

			// EasyDiscuss Integrations
			EB::easydiscuss()->log('easyblog.new.blog', $this->created_by, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_BLOG', $this->title));
			EB::easydiscuss()->addPoint('easyblog.new.blog', $this->created_by);
			EB::easydiscuss()->addBadge('easyblog.new.blog', $this->created_by);
			EB::easydiscuss()->insertNotification('new.blog', $this);

			// JomSocial integration
			EB::jomsocial()->insertActivity($this);

			// Points should only be rewarded when the post is being published
			EB::jomsocial()->assignPoints('com_easyblog.blog.add', $this->created_by);

			// Assign altauserpoints
			EB::altauserpoints()->assign('plgaup_easyblog_add_blog', $this->created_by);

			// Notify through web push
			EB::push()->notify($this);

			// Shorten urls if necessary
			EB::yourls()->shorten($this);

			// Automatically feature the blog post if required
			$isFeaturedAuthor = EB::isFeatured('blogger', $this->created_by);
			$isFeaturedPost = EB::isFeatured('post', $this->id);

			if ($this->config->get('main_autofeatured', 0) && $isFeaturedAuthor && !$isFeaturedPost) {
				$this->setFeatured('post', $this->id);
			}

		} else {
			if ($this->isPublished() && !$this->isBeingTrashed()) {
				// This action is an edit post
				EB::easysocial()->createBlogStream($this, false);

				// JomSocial integration
				EB::jomsocial()->insertActivity($this);
			}
		}

		// Approval by an admin
		// Or if that is moderation + schedule post
		if ($this->isBeingApproved() || $this->isBeingScheduledFromPending()) {
			$actionlog->log('COM_EB_ACTIONLOGS_POST_APPROVAL', 'post', array(
				'link' => $this->getEditLink(),
				'postTitle' => JText::_($this->title)
			));
		}

		// When the post is approved, we want to notify the author
		if ($this->isBeingApproved() && $this->config->get('notification_approval')) {
			// We do not need to notify the world that the post is published because it's already handled above under
			$this->notify(false, '1', false, true);
		}

		// When post is being approved, we should delete any rejection related to the post
		if ($this->isBeingApproved()) {
			$rejectModel = EB::model('PostReject');
			$rejectModel->clear($this->id);
		}

		// When this post is being unpublished, we should add triggers here.
		if ($this->isBeingUnpublished()) {

			$actionlog->log('COM_EB_ACTIONLOGS_POST_UNPUBLISHED', 'post', array(
				'link' => $this->getEditLink(),
				'postTitle' => JText::_($this->title)
			));

			// If the post is being unpublished, remove them from the stream
			EB::jomsocial()->removePostStream($this);
		}

		// When this post is rejected by the moderator, we should add triggers here
		if ($this->isBeingRejected()) {

			$actionlog->log('COM_EB_ACTIONLOGS_POST_REJECTED', 'post', [
				'link' => $this->getEditLink(),
				'postTitle' => JText::_($this->title)
			]);

			// When a post is rejected, add the necessary data on the reject table so we can determine why it's being rejected
			$reject = EB::table('PostReject');
			$reject->post_id = $this->id;
			$reject->created_by = $this->created_by;
			$reject->created = EB::date()->toSql();

			// How should we get the reject message from the composer?
			$rejectMessage = EB::normalize($this->saveOptions, 'rejectMessage', $this->input->get('message', '', 'default'));
			$reject->message = $rejectMessage;
			$reject->store();

			// Send notify
			$reject->notify();
		}

	}

	/**
	 * Triggers plugins after a blog post is saved
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function triggerAfterSave()
	{
		// cache this post so that other plugins can get the data without having load the data from db again.
		EB::cache()->cachePosts(array($this->post));

		// Import plugins
		JPluginHelper::importPlugin('finder');
		JPluginHelper::importPlugin('easyblog');

		$dispatcher = EB::dispatcher();

		if ($this->saveOptions['triggerStateChangeOnly']) {
			if (FH::isJoomla5()) {
				$dispatcher->trigger('onFinderChangeState', array('easyblog.blog', [$this->post->id], $this->post->published));
			} else {
				$dispatcher->trigger('onFinderChangeState', array('easyblog.blog', $this->post->id, $this->post->published));
			}
			
			return;
		}

		$this->introtext = '';
		$this->text = '';

		$dispatcher->trigger('onAfterEasyBlogSave', array(&$this, $this->isNew()));

		// Since content plugins uses introtext and text columns, we'll just need to mimic the introtext and text columns.
		$this->introtext = $this->intro;
		$this->text = $this->content;

		$dispatcher->trigger('onContentAfterSave', array('easyblog.blog', &$this, $this->isNew()));

		// finder index
		$dispatcher->trigger('onFinderAfterSave', array('easyblog.blog', &$this, $this->isNew()));

		// Revert back these properties
		$this->intro = $this->introtext;
		$this->content = $this->text;

		unset($this->introtext);
		unset($this->text);
	}

	/**
	 * Triggers plugins before a blog post is saved
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function triggerBeforeSave()
	{
		// Import plugins
		JPluginHelper::importPlugin('content');
		JPluginHelper::importPlugin('easyblog');

		// Load up dispatcher
		$dispatcher = EB::dispatcher();

		// Try to mimic Joomla's com_content behavior.
		EB::loadContentRouter();

		$dispatcher->trigger('onBeforeEasyBlogSave', array(&$this, $this->isNew()));

		// Since content plugins uses introtext and text columns, we'll just need to mimic the introtext and text columns.
		$this->introtext = $this->intro;
		$this->text = $this->content;

		$data = $this->toPostData();
		$data = EB::makeArray($data);

		$dispatcher->trigger('onContentBeforeSave', array('easyblog.blog', &$this, $this->isNew(), $data));

		// Since Joomla content plugins are expecting that this is an article object, we need to get the introtext and text value back
		$this->intro = $this->introtext;
		$this->content = $this->text;

		unset($this->introtext);
		unset($this->text);
	}

	/**
	 * Used in autosave and to determine if this post has changes or not before submit for saving.
	 * @since	5.0
	 * @access	public
	 */
	public function hasChanges($data)
	{
		// if ($this->isBlank()) {
		// 	return false;
		// }

		// check contents
		if ($this->doctype == 'ebd') {
			$document = EB::document($this->document);
			$oriContent = $document->getContent();

			// from form post
			$document = EB::document($data['document']);
			$newContent = $document->getContent();

		} else {
			$oriContent = $this->intro . $this->content;
			$oriContent = trim($oriContent);

			// replace special characters from redactor.
			$oriContent = str_replace('&#8203;', '', $oriContent);

			// replace line feed
			$oriContent = preg_replace('/[\n\r]/', '', $oriContent);

			$oriContent = preg_replace('/\xC2/', '', $oriContent);
			$oriContent = preg_replace('/\xA0/', '', $oriContent);


			//from form post
			$newContent = $data['content'];
			$newContent = trim($newContent);

			// replace special characters from redactor.
			$newContent = str_replace('&#8203;', '', $newContent);

			// replace line feed
			$newContent = preg_replace('/[\n\r]/', '', $newContent);

			$newContent = preg_replace('/\xC2/', '', $newContent);
			$newContent = preg_replace('/\xA0/', '', $newContent);
		}

		if ($oriContent != $newContent) {
			return true;
		}

		// // check categories changed.
		// $categories = $this->getCategories();
		// $oriCats = array();

		// if ($categories) {
		// 	foreach ($categories as $cat) {
		// 		$oriCats[] = $cat->id;
		// 	}
		// }

		// //from form post
		// $newCats = $data['categories'];

		// $oriCount = count($oriCats);
		// $newCount = count($newCats);

		// if ($oriCount != $newCount || array_diff($oriCats, $newCats)) {
		// 	return true;
		// }

		// // check tags changed.
		// $tags = $this->getTags();
		// $oriTags = array();
		// $newTags = array();

		// if ($tags) {
		// 	foreach ($tags as $tag) {
		// 		$oriTags[] = $tag->title;
		// 	}
		// }

		// //from form post
		// $tags = $data['tags'];
		// if ($tags) {
		// 	$newTags = explode(',', $tags);
		// }

		// $oriCount = count($oriTags);
		// $newCount = count($newTags);

		// if ($oriCount != $newCount || array_diff($oriTags, $newTags)) {
		// 	return true;
		// }


		return false;
	}

	/**
	 * process previews
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function renderPreviews($data)
	{
		// previews
		$data->preview_intro = '';
		$data->preview_content = '';


		if ($this->doctype == 'ebd') {
			// lets get the intro now.
			$document = EB::document($this->document);
			$data->preview_intro = $document->getIntro();
			$data->preview_content = $document->getContentWithoutIntro();
		} else {
			// this is legacy document.
			$data->preview_intro = $data->intro;
			$data->preview_content = $data->content;

			if (!$data->intro && $data->content) {

				$testContent = $data->content;
				// Search for readmore tags using Joomla's mechanism
				$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
				$pos = preg_match($pattern, $testContent);

				if ($pos) {
					list($intro, $main) = preg_split($pattern, $testContent, 2);

					$data->preview_intro = $intro;
					$data->preview_content = $main;
				}
			}
		}
	}

	/**
	 * Saves a key / value in the parameters of the post
	 *
	 * @since	5.4.7
	 * @access	public
	 */
	public function saveParam($key, $value)
	{
		$params = $this->getParams();
		$params->set($key, $value);

		$this->post->params = $params->toString();
		return $this->post->store();
	}

	/**
	 * Saves the blog post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function savePost()
	{
		// If this a new post, instantiate a new post table.
		if (!$this->post) {
			$this->post = EB::table('Post');
		}

		// If the revision is not the current post and it isn't being finalized we shouldn't do anything
		// as the revision should still be on draft state.
		if (!$this->isCurrent() && !$this->isFinalized() && !$this->isBlank()) {
			return;
		}

		// Get post data
		$data = $this->toPostData();

		if ($this->doctype == 'ebd') {
			// if this is a ebd, then we need to update the content column from easyblog_post table as well.
			$document = EB::document($this->document);

			if (isset($this->saveOptions['copyPost']) && $this->saveOptions['copyPost']) {
				$document->processCopy();
				$this->document = $document->toJSON();
			}

			$data->intro = $document->processIntro();
			$data->content = $document->processContent();

			if ($data->content && !$data->intro) {
				$data->intro = $data->content;
				$data->content = '';
			}
		}

		// Get params data
		$data->params = $this->toParamsData();

		// Bind post data
		$this->post->bind($data);

		if (isset($this->saveOptions['overrideAuthorId']) && $this->saveOptions['overrideAuthorId']) {
			$this->post->created_by = $this->saveOptions['overrideAuthorId'];
		}

		// Store post
		$state = $this->post->store();

		// If failed to store post, throw exception.
		if (!$state) {

			// debug error.
			// var_dump($this->post->getError());exit;

			throw EB::exception('COM_EASYBLOG_COMPOSER_UNABLE_STORE_POST');
		}

		// If this post is being created, assign post id to workbench.
		$this->id = $this->post->id;
		// if ($this->isBeingCreated()) {
		// 	$this->id = $this->post->id;
		// }

		return true;
	}

	/**
	 * Creates a new revision for the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function createNewRevision()
	{
		$revision = EB::table('Revision');
		$revision->created_by = $this->user->id;

		if (isset($this->saveOptions['overrideAuthorId']) && $this->saveOptions['overrideAuthorId']) {
			$revision->created_by = $this->saveOptions['overrideAuthorId'];
		}

		// Set revision post id
		$revision->post_id = $this->post->id;

		// Set revision content
		$revision->setContent($this->toRevisionData());

		// Set revision state
		// Draft     => Blank, Draft
		// Pending   => Pending
		// Finalized => Published, Scheduled, Unpublished
		if ($this->isBlank() || $this->isDraft()) {
			$revision->state = EASYBLOG_REVISION_DRAFT;
		} else if ($this->isPending()) {
			$revision->state = EASYBLOG_REVISION_PENDING;
		} else {
			$revision->state = EASYBLOG_REVISION_FINALIZED;
		}

		// Joomla 4 compatibility:
		// To Ensure this title columns have pass in something
		if (!$revision->title) {
			$revision->title = '';
		}

		// Store revision
		$state = $revision->store();

		// If failed to store revision, throw exception.
		if (!$state) {
			throw EB::exception('Unable to store revision.');
		}

		// Assign the newly created revision to the post library
		$this->revision = $revision;

		// Update revision_id & uid.
		$this->revision_id = $revision->id;
		$this->uid = $this->id . '.' . $this->revision_id;

		// If we managed to reach here, it means both revision and post
		// are successfully saved, so we'll just need to return true.
		return true;
	}

	/**
	 * Determines if the revision should be created as a new revision or update on existing revision.
	 *
	 * @since	5.0
	 * @access	public
	 * @return
	 */
	public function saveRevision()
	{
		// By default we don't want to change the post's revision
		$setAsCurrentRevision = false;

		// If this is a new post or legacy post w/o revision, create a new revision and set as current revision.
		if (!$this->revision) {

			$revision = EB::table('Revision');
			$setAsCurrentRevision = true;

			// Ensure that the revision author is created by the current user.
			$revision->created_by = $this->user->id;

		} else if (!$this->isUnderScheduled() && $this->revision->isFinalized() && !$this->saveOptions['skipCreateRevision']) {

			// If this revision is already finalized, create a new revision
			$revision = EB::table('Revision');

			// Set the revision author to the current user
			$revision->created_by = $this->user->id;

			// If this finalized revision is also the current revision used by post, set this revision as current revision.
			if ($this->isCurrent()) {
				$setAsCurrentRevision = true;
			}

		} else {

			// Else reuse current revision
			$revision = $this->revision;

			// Whenever we reuse an existing revision, we want to log the user's actions
			// as an audit in case they need to re-load these data again.
		}

		// If caller pass in useAuthorAsRevisionOwner flag, then we will use blog author as revision author.
		if (isset($this->saveOptions['useAuthorAsRevisionOwner']) && $this->saveOptions['useAuthorAsRevisionOwner']) {
			$revision->created_by = $this->created_by;
		}

		// Set revision post id
		$revision->post_id = $this->post->id;

		// Set revision content
		$revision->setContent($this->toRevisionData());

		// Set revision state
		// Draft     => Blank, Draft
		// Pending   => Pending
		// Finalized => Published, Scheduled, Unpublished
		if ($this->isBlank() || $this->isDraft()) {
			$revision->state = EASYBLOG_REVISION_DRAFT;
		} else if ($this->isPending()) {
			$revision->state = EASYBLOG_REVISION_PENDING;
		} else {
			$revision->state = EASYBLOG_REVISION_FINALIZED;
		}

		// Joomla 4 compatibility:
		// To Ensure this title columns have pass in something
		if (!$revision->title) {
			$revision->title = '';
		}

		// incase the revision created_by is still empty, we assign with the post's author id #2862
		if (!$revision->created_by) {
			$revision->created_by = $this->created_by ? $this->created_by : 0;

			// if still empty, lets just get one of the admin id.
			if (!$revision->created_by) {
				$revision->created_by = $this->config->get('main_orphanitem_ownership', EB::getDefaultSAIds());
			}
		}

		// If current is blank state then set a default title
		if ($this->isBlank()) {
			$revision->title = JText::_('COM_EASYBLOG_COMPOSER_INITIAL_POST');
		}

		// Store revision
		$state = $revision->store();

		// If failed to store revision, throw exception.
		if (!$state) {
			// var_dump('revision:', $revision, $revision->getError());exit;
			throw EB::exception('Unable to store revision.');
		}

		// If we should set this revision as current revision
		if ($setAsCurrentRevision) {
			$this->post->revision_id = $revision->id;

			$state = $this->post->store();

			if (!$state) {
				throw EB::exception('Unable to set as revision as current revision on post.');
			}
		}

		// Assign revision back to instance
		// in case we've created a new revision.
		$this->revision = $revision;

		// Update revision_id & uid.
		$this->revision_id = $revision->id;
		$this->uid = $this->id . '.' . $this->revision_id;

		// once the revisions and posts linked. we no need to update the post->intro and post->content
		// if its a legacy post.
		$this->processPostIntroContent();

		// Determine if this is coming from achive process via cron then we need to check this flag for cleanup the revision.
		$isArchiveProcess = isset($this->saveOptions['isArchiveProcess']) && $this->saveOptions['isArchiveProcess'] ? $this->saveOptions['isArchiveProcess'] : false;
		$isScheduleProcess = isset($this->saveOptions['isScheduleProcess']) && $this->saveOptions['isScheduleProcess'] ? $this->saveOptions['isScheduleProcess'] : false;


		// this current revision is finalized. we need to clean up the rivision records.
		if ($this->revision->isFinalized() && (FH::isSiteAdmin() || $this->created_by == $this->my->id || $isArchiveProcess || $isScheduleProcess)) {

			$model = EB::model('Revisions');

			if (! $this->config->get('layout_composer_history')) {
				// history revision disabled. we will only keep one revision record.
				$model->cleanUp($this->id, $this->revision->id, array('toLatest' => true));


			} else if ($this->config->get('layout_composer_history_limit')) {
				// we want to limit the revisions.
				$max = (int) $this->config->get('layout_composer_history_limit_max', 5);

				if (! $max) {
					$max = 5;
				}

				$model->cleanUp($this->id, $this->revision->id, array('toLimit' => $max));
			}

		}

		// If we managed to reach here, it means both revision and post
		// are successfully saved, so we'll just need to return true.
		return true;
	}

	/**
	 * Save the categories associated with the post.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function saveCategories()
	{
		// We should not be saving any categories if this was the first copy being initialized.
		if ($this->isBlank()) {
			return;
		}

		// If the current post and revision matches each other, we know that the revision is the copy used as the post.
		if ($this->isCurrent()) {

			// Delete any categories that are associated with this post id
			$model = EB::model('Categories');
			$model->deleteAssociation($this->id);

			if ($this->categories) {
				foreach ($this->categories as $id) {
					$id = (int) $id;

					$primaryCategory = $this->category_id == $id ? 1 : 0;

					$postCatTbl = EB::table('PostCategory');
					$postCatTbl->post_id = $this->id;
					$postCatTbl->category_id = $id;
					$postCatTbl->primary = $primaryCategory;

					$postCatTbl->store();
				}
			}
		}

	}

	/**
	 * Saves the tags when the blog post is stored.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function saveTags()
	{
		if ($this->isBlank()) {
			return;
		}

		$isRevision = $this->isCurrent() ? false : true;

		// If the current post and revision matches each other, we know that the revision is the copy used as the post.
		// if ($this->isCurrent()) {

		// Assuming that tags are a comma separated keywords
		$tags = $this->tags;

		// Delete any existing tags associated with this post
		$postTagModel = EB::model('PostTag');
		$postTagModel->deletePostTag($this->id, $isRevision);

		// Assuming that this is the current copy.
		if (!empty($this->tags)) {

			// Ensure that the tags are in an array
			$tags = explode(',', $this->tags);

			// Get a list of default tags on the site
			$model = EB::model('Tags');
			$defaultTags = $model->getDefaultTagsTitle();

			if (!empty($defaultTags)) {
				foreach ($defaultTags as $title) {
					$tags[] = $title;
				}
			}

			//remove spacing from start and end of the tags title.
			if ($tags) {
				for($i = 0; $i < count($tags); $i++) {
					$tags[$i] = EBString::trim($tags[$i]);
				}
			}

			// Ensure that the tags are unique
			$tags = array_unique($tags);

			if ($tags) {
				foreach ($tags as $tag) {
					$tag = trim($tag);

					// Ensure that the tag is valid
					if (empty($tag)) {
						continue;
					}

					$table = EB::table('Tag');
					$table->load($tag, true);
					$exists = $table->id ? true : false;

					// If the tag does not exist and the user does not have any privileges to create any tag,
					// we shouldn't allow them to create them.
					if (!$exists && !$this->acl->get('create_tag')) {
						continue;
					}

					// When the tag does not exist, create a new tag first
					if (!$exists) {

						$table->created_by = $this->created_by;
						$table->title = $tag;
						$table->created = EB::date()->toSql();
						$table->published = 1;
						$table->status = 0;

						// We need to assign the language here to follow the post's lang
						$table->language = $this->language != '*' ? $this->language : '';

						$state = $table->store();

						if (!$state) {
							EB::ajax()->notify($tag . ':' . $table->getError(), 'debug');
						}
					}

					// Add the association of tags here.
					$postTagModel->add($table->id, $this->id, EB::date()->toSql(), $isRevision);
				}
			}
		}
		// }
	}

	/**
	 * Saves the posts association
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function saveAssociation()
	{
		$db = EB::db();

		if (EB::isAssociationEnabled()) {

			// TOTO:: delete the existing association.
			$query = "delete a from `#__easyblog_associations` as a";
			$query .= " inner join `#__easyblog_associations` as b on a.`key` = b.`key`";
			$query .= " where b.`post_id` = " . $db->Quote($this->id);

			$db->setQuery($query);
			$db->query();

			if ($this->association) {

				// Adding new association for these items
				$key = md5(json_encode($this->association));

				$query = "insert into `#__easyblog_associations` (`id`, `post_id`, `key`) values ";

				$arr = array();
				foreach($this->association as $assoc) {
					$arr[] = "(null, " . $db->Quote($assoc->id) . "," . $db->Quote($key) . ")";
				}

				$values = implode(",", $arr);

				$query .= $values;

				$db->setQuery($query);
				$db->query();
			}

		}
	}

	/**
	 * Retrieves a list of language associations for posts
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getAssociation()
	{
		static $cache = [];

		if (!isset($cache[$this->id])) {
			$db = EB::db();

			$query = "select a.`post_id`, a.`key`, p.`language`, p.`title`";
			$query .= " from `#__easyblog_associations` as a";
			$query .= " inner join `#__easyblog_associations` as b on a.`key` = b.`key`";
			$query .= " inner join `#__easyblog_post` as p on a.`post_id` = p.`id`";
			$query .= " where b.`post_id` = " . $db->Quote($this->id);

			$db->setQuery($query);

			$results = $db->loadObjectList();

			$associations = array();

			if ($results) {
				foreach($results as $item) {
					$associations[$item->language] = $item;
				}
			}

			$cache[$this->id] = $associations;
		}

		return $cache[$this->id];
	}

	/**
	 * Saves the meta data of the blog post.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function saveMeta()
	{
		// Get the keywords
		$keywords = $this->keywords;
		$description = $this->description;
		$customTitle = $this->custom_title;
		$canonical = $this->canonical;

		// Try to get the meta id for this post
		$model = EB::model('Blog');
		$metaId = $model->getMetaId($this->id);

		// Store the meta data for this post now
		$meta = EB::table('Meta');
		$meta->load($metaId);

		$meta->content_id = $this->id;
		$meta->title = $customTitle;
		$meta->keywords = $keywords;
		$meta->canonical = $canonical;
		$meta->description = $description;
		$meta->type = META_TYPE_POST;

		$meta->store();
	}

	/**
	 * Associates the post with a different source.
	 *
	 * E.g: team blog, jomsocial group, jomsocial event
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function saveRelation()
	{
		// @TODO: Determine if there's any relation for this post.
		// @TODO: What will the composer be passing to us?

		// @TODO: Update this to use just the post table.
		return;

		// $relation = EB::table('PostRelation');

		// assuming this is finalized copy
		if ($this->isReady()) {
			// assuming there will be always one to one relation to the either teamblog / JS group / JS event,
			// then we will load the record based on post_id

			$relation->load(array('post_id' => $this->id));
			$isNew = ($relation->id) ? false : true;

			if ($isNew) {
				$relation->post_id = $this->id;
				$relation->created = EB::date()->toSql();
			}

			$relation->external_id = $this->source_id;
			$relation->external_source = $this->source_type;
			$relation->store();
		}

	}

	/**
	 * Saves the fields that are related to the blog post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function saveFields()
	{
		$fields = $this->fields;
		$fieldsClass = $this->fields_class;

		// Delete any existing field data for this post
		$model = EB::model('Fields');
		$model->deleteBlogFields($this->id);

		if ($fields) {

			$fieldsLibrary = EB::fields();

			foreach ($fields as $id => $value) {

				// Trigger onbeforesave for the fields object
				$field = EB::table('Field');
				$field->load($id);

				$fieldsLibrary->onBeforeSave($value, $field->type);

				$fieldClassValue = '';

				$before = $fieldsClass;

				if (is_object($fieldsClass)) {
					$fieldsClass = EB::makeArray($fieldsClass);
				}

				$after = $fieldsClass;

				// check for this field whether got custom class value
				if (isset($fieldsClass[$id])) {
					$fieldClassValue = $fieldsClass[$id];
				}

				// This is most likely a multiple value field
				if (is_array($value)) {
					foreach ($value as $subValue) {

						$table = EB::table('FieldValue');
						$table->field_id = $id;
						$table->post_id = $this->id;
						$table->value = $subValue;
						$table->class_name = $fieldClassValue;
						$table->store();
					}

					continue;
				}

				$table = EB::table('FieldValue');
				$table->field_id = $id;
				$table->post_id = $this->id;

				// If this is just a normal value, just store the value
				$table->value = $value;
				$table->class_name = $fieldClassValue;

				$table->store();
			}
		}
	}

	/**
	 * Sets a debug status
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function debug()
	{
		$this->_debug = true;
	}

	/**
	 * Deletes a post from the site
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function delete()
	{
		// Load site's language file just in case the blog post was deleted from the back end
		EB::loadLanguages();

		// Load our own plugins
		JPluginHelper::importPlugin('finder');
		JPluginHelper::importPlugin('easyblog');

		$dispatcher = EB::dispatcher();

		// Trigger
		$dispatcher->trigger('onBeforeEasyBlogDelete', array(&$this));

		// Delete the post from the db now
		$state = $this->post->delete();

		// Trigger
		$dispatcher->trigger('onAfterEasyBlogDelete', array(&$this));

		// Delete from finder.
		$dispatcher->trigger('onFinderAfterDelete', array('easyblog.blog', $this->post));

		// Delete all relations with this post
		$this->deleteRatings();
		$this->deleteReports();
		$this->deleteRevisions();
		$this->deleteCategoryRelations();
		$this->deleteBlogTags();
		$this->deleteMetas();
		$this->deleteComments();
		$this->deleteTeamContribution();
		$this->deleteAssets();
		$this->deleteFeedHistory();
		$this->deleteReactions();
		$this->deleteLottieFiles();

		// Delete all subscribers to this post
		$this->deleteSubscribers();

		// Delete from featured table
		$this->deleteFeatured();

		// Relocate media files into "My Media"
		$this->relocateMediaFiles();

		// Delete all other 3rd party integrations
		$this->deleteOtherRelations();

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_POST_DELETED', 'post', array(
			'link' => $this->getEditLink(),
			'postTitle' => JText::_($this->title)
		));

		return $state;
	}

	/**
	 * Relocate media files into "My Media"
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function relocateMediaFiles()
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/mediamanager/mediamanager.php');

		// Get a list of folders for this post
		$uri = 'post:' . $this->id;

		// Get the absolute path to the post's folder
		$path = EBMM::getPath($uri);

		// Check if it exists.
		jimport('joomla.filesystem.folder');

		// If it doesn't exist, we wouldn't want to do anything
		if (!JFolder::exists($path)) {
			return true;
		}

		// Construct the new uri
		$newUri = 'user:' . $this->created_by;
		$newPath = EBMM::getPath($newUri);

		// We need to create a new folder first with the name of this current title
		$title = JFile::makeSafe($this->title);

		// Change it to post id instead
		if (!$title) {
			$title = 'post_' . $this->id;
		}

		// Check if the new path is exists or not. #1503
		if (!JFolder::exists($newPath)) {
			JFolder::create($newPath);
		}

		$newPath = $newPath . '/' . $title;

		// Move the old folder to the new folder now
		$state = JFolder::move($path, $newPath);

		return $state;
	}

	/**
	 * Delete all other relations with the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteOtherRelations()
	{
		// Delete relationships from jomsocial stream
		EB::jomsocial()->removePostStream($this);
		EB::jomsocial()->assignPoints('com_easyblog.blog.remove', $this->created_by);

		// Deduct points from respective systems
		EB::easydiscuss()->log('easyblog.delete.blog', $this->created_by, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_DELETE_BLOG', $this->title));
		EB::easydiscuss()->addPoint('easyblog.delete.blog', $this->created_by);
		EB::easydiscuss()->addBadge('easyblog.delete.blog', $this->created_by);

		// Integrations with EasySocial
		EB::easysocial()->assignPoints('blog.remove', $this->created_by);
		EB::easysocial()->removePostStream($this);

		// Assign altauserpoints
		EB::altauserpoints()->assign('plgaup_easyblog_delete_blog', $this->created_by);
	}

	/**
	 * Delete category relations
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteCategoryRelations()
	{
		$model = EB::model('Categories');
		return $model->deleteAssociation($this->id);
	}

	/**
	 * Delete revisions associated with this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteRevisions()
	{
		$model = EB::model('Revisions');

		return $model->deleteRevisions($this->id);
	}


	/**
	 * Delete revisions other than the current associated revision.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function purgeRevisions($ignoreRevId = null)
	{

		$ignoreIds = array($this->revision->id);

		if ($ignoreRevId) {
			$ignoreIds[] = $ignoreRevId;
		}

		$model = EB::model('Revisions');
		return $model->cleanUp($this->id, $ignoreIds, array('toLatest' => true));
	}


	/**
	 * Delete subscribers for this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteSubscribers()
	{
		$model = EB::model('Subscriptions');

		return $model->deleteSubscriptions($this->id, 'entry');
	}

	/**
	 * Remove any featured items for this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteFeatured()
	{
		$model = EB::model('Featured');
		$state = $model->removeFeatured(EBLOG_FEATURED_BLOG, $this->id);

		return $state;
	}

	/**
	 * Delete ratings associated with this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteRatings()
	{
		$model = EB::model('Ratings');
		return $model->removeRating('entry', $this->id);
	}

	/**
	 * Delete reports associated with this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteReports()
	{
		$model = EB::model('Reports');
		return $model->deleteReports($this->id, EBLOG_REPORTING_POST);
	}

	/**
	 * Delete team associations with this current post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteTeamContribution()
	{
		// @TODO: Sam, replace this
	}

	/**
	 * Delete any tags associated with the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteBlogTags()
	{
		$model = EB::model('Tags');

		return $model->deleteAssociation($this->id);
	}

	/**
	 * Delete meta tags associated with this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteMetas()
	{
		$model = EB::model('Metas');

		return $model->deleteMetas($this->id, META_TYPE_POST);
	}

	/**
	 * Delete comments related to this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteComments()
	{
		// before we delete the comments,
		// there might be some comment stream in ES.
		// lets clear them 1st.
		EB::easysocial()->removePostCommentStream($this);

		$model = EB::model('Comments');
		return $model->deletePostComments($this->id);
	}

	/**
	 * Delete reactions related to this post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteReactions()
	{
		$model = EB::model('Reactions');

		return $model->deletePostReactions($this->id);
	}

	/**
	 * Delete assets that are related to the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteAssets()
	{
		$model = EB::model('Assets');

		return $model->deleteAssets($this->id);
	}

	/**
	 * Deletes any association with the feed history table
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function deleteFeedHistory()
	{
		$history = EB::table('FeedHistory');
		$exists = $history->load(array('post_id' => $this->id));

		if ($exists) {
			return $history->delete();
		}

		return false;
	}

	/**
	 * Returns the data of a post in a standard object
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toPostData()
	{
		$data = new stdClass();

		foreach (self::$enumerations as $prop => $enum) {

			// If this is a linked property, skip.
			// Linked property are not part of post table.
			if ($enum['linked']) {
				continue;
			}

			// Joomla 4 compatibility:
			// To Ensure id column type is integer
			if ($prop == 'isnew') {
				$this->$prop = (int) $this->$prop;
			}

			// Joomla 4 compatibility:
			// To Ensure state column should have a default value
			if ($prop == 'state' && empty($this->$prop)) {
				$this->$prop = EASYBLOG_POST_NORMAL;
			}

			if ($prop == 'locked' && empty($this->$prop)) {
				$this->$prop = 0;
			}

			if ($prop == 'content' && empty($this->$prop)) {
				$this->$prop = '';
			}

			if ($prop == 'intro' && empty($this->$prop)) {
				$this->$prop = '';
			}

			$data->$prop = $this->$prop;
		}

		return $data;
	}

	/**
	 * Returns formatted params data
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function toParamsData()
	{
		// Get stored params data
		$params = $this->getParams();

		// return as json string
		return $params->toString();
	}

	/**
	 * Get params data
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getParams($useCache = true)
	{
		static $data = array();

		if (!isset($data[$this->id]) || !$useCache) {
			$params = new JRegistry($this->params);
			$data[$this->id] = $params;
		}

		return $data[$this->id];
	}

	/**
	 * Exports current properties of the post that should be inserted into the revision data.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toRevisionData()
	{
		$data = $this->toData();

		if ($this->doctype == 'ebd') {
			// if this is a ebd, then we need to update the content attribute as well.
			$document = EB::document($this->document);
			$contents = $document->getContent();

			$data->content = $contents;
		}

		// We do not want to store the post id
		unset($data->revision);
		unset($data->uid);
		unset($data->revision_id);

		return $data;
	}

	/**
	 * Exports the post library to a standard object that can be sent back to the javascripts.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toData()
	{
		$data = new stdClass();

		foreach (self::$enumerations as $prop => $enum) {
			$data->$prop = $this->$prop;
		}

		// Add additional properties that is not part of enumerations
		$data->uid = $this->uid;
		$data->revision = $this->revision;

		return $data;
	}

	/**
	 * Format the polls from the content of the post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function formatPolls($content, $options = [])
	{
		if ($this->isEbd()) {
			$pattern = '/\<div data-eb-poll-id=\"([0-9]+)\"\>((.*)|[\s\S]+?)\<\/div>+/';

			preg_match_all($pattern, $content, $matches);
		}

		if (!$this->isEbd()) {
			$pattern = '/\<span style=\"(.*?)\" data-eb-poll-id=\"([0-9]+)\"\>(.*)\<\/span>+/';

			preg_match_all($pattern, $content, $matches);
		}

		if (empty($matches)) {
			return $content;
		}

		$textToReplace = [];
		$polls = [];

		foreach ($matches as $index => $match) {
			if ($index == 0) {
				$textToReplace = $match;
			}

			if ((($this->isEbd() && $index == 1) || (!$this->isEbd() && $index == 2)) && $match) {
				foreach ($match as $pollId) {
					$poll = EB::polls($pollId);
					$output = '';

					if ($this->config->get('main_polls') && $poll->id && $poll->isPublished()) {
						$themes = EB::themes();
						$output = $poll->getFormattedObject($options)->html;
					}

					$polls[$pollId] = $output;
				}
			}
		}

		$textToReplace = array_unique($textToReplace);

		foreach ($textToReplace as $text) {
			foreach ($polls as $id => $output) {
				$search = 'data-eb-poll-id="' . $id . '"';

				if (strpos($text, $search) !== false) {
					$content = str_replace($text, $output, $content);
				}
			}
		}

		return $content;
	}

	/**
	 * Format content to suit the layout of email notifications
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function formatEmailContent($content)
	{
		// if no content, do not process further.
		if (!$content) {
			return $content;
		}

		$parser = EB::simplehtml()->str_get_html($content);

		// Replace all a href tag that wrap the image to post permalink. #629
		$a = $parser->find('a');

		foreach ($a as $href) {

			// skip those href link is mailto
			preg_match('/mailto:([^\?]*)/', $href->href, $mailtoMatches);

			if (!$href->href || $href->href == 'javascript:void(0);' || $mailtoMatches) {
				continue;
			}

			// Convert those relative link to absolute path
			if ($href->href) {

				$url = htmlspecialchars_decode($href->href);

				$sh404Exist = EBR::isSh404Enabled();
				$root = JUri::root();

				// Ensure that URL doesn't have those scheme and the site domain
				if (strpos($url, 'http://') === false && strpos($url, 'https://') === false && strpos($url, '//') === false) {

					$uri = JURI::getInstance();
					$domain	= $uri->toString(array('host'));

					// Ensure that URL does't match with the current domain site.
					if (strpos($url, $domain) === false) {
						// append the domain name
						$href->href = $root . $url;
					}
				}

				// Ensure that is relative href link
				if (strpos($url, 'index.php?') !== false) {

					preg_match_all('#index.php\?([^"]+)#m', $url, $matches);

					foreach ($matches[1] as $urlQueryString) {

						$replacePath = JRoute::_($root . 'index.php?' . $urlQueryString);
						$absolutePath = str_replace($url, $replacePath, $url);
					}

					// if the site installed Sh404sef, convert to SEF URL.
					if ($sh404Exist) {

						foreach ($matches[0] as $urlQueryString) {

							// find the # bookmark/anchor tag
							preg_match('#(\#.*$)#m', $urlQueryString, $anchorMatches);
							$hasAnchorTag = false;

							if ($anchorMatches[0]) {
								$urlQueryString = str_replace($anchorMatches[0], '', $urlQueryString);
								$hasAnchorTag = true;
							}

							$absolutePath = Sh404sefHelperGeneral::getSefFromNonSef($urlQueryString, true, true);
							$absolutePath = str_replace('/administrator/', '/', $absolutePath);

							// append back the bookmark/anchor tag
							if ($hasAnchorTag) {
								$absolutePath = $absolutePath . $anchorMatches[0];
							}
						}
					}

					$href->href = $absolutePath;
				}
			}

			$children = $href->children();

			if (!$children) {
				continue;
			}

			foreach ($children as $child) {

				// Image tag found. Let's replace the parents href value
				if ($child->tag == 'img') {
					$href->href = $this->getPermalink(true, true);
				}
			}
		}

		// Save the formatted content
		$content = $parser->save();

		return $content;
	}

	/**
	 * Exports the post library to email data
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toEmailData()
	{
		static $data = false;

		if (!$data) {
			$author = $this->getAuthor();
			$category = $this->getPrimaryCategory();

			// Need to pass in do not strip tags with those HTML code from the content
			$options = array('fromRss' => true);

			$blogIntro = $this->getIntro(false, false, 'all', null, $options);
			$blogContent = $this->getContent('entry', true, true);

			if ($this->isPending()) {
				$content = $this->getContent('entry', true, true, array('isPreview' => true, 'ignoreCache' => true));
				$blogIntro = $content;
				$blogContent = $content;
			}

			// Replace all href of image popup to post permalink. #629
			$blogIntro = $this->formatEmailContent($blogIntro);
			$blogContent = $this->formatEmailContent($blogContent);

			// Truncate the content inside email.
			if ($this->config->get('notification_blog_truncate', false)) {

				$options['ignoreCache'] = true;
				$options['forceTruncateByChars'] = true;
				$options['forceCharsLimit'] = $this->config->get('notification_blog_truncate_limit', 300);
				$options['forceImage'] = true;

				$blogIntro = $this->getIntro(false, true, 'intro', null, $options);
				$blogContent = $this->getContent('list', true, true, $options);

				if ($this->isPending()) {
					$content = $this->getContent('list', true, true, array('isPreview' => true, 'ignoreCache' => true));
					$blogIntro = $content;
					$blogContent = $content;
				}
			}

			// Send email notifications to subscribers
			$data = array(
						'blogTitle' => $this->title,
						'blogAuthor' => $author->getName(),
						'blogAuthorAvatar' => $author->getAvatar(),
						'blogAuthorLink' => $author->getExternalPermalink(),
						'blogAuthorEmail' => $author->user->email,
						'blogIntro' => $blogIntro,
						'blogContent' => $blogContent,
						'blogCategory' => $category->getTitle(),
						'blogLink' => $this->getExternalPermalink(),
						'blogDate' => $this->getPublishDate()->format(JText::_('DATE_FORMAT_LC')),
						'blogCover' => $this->getImage('original', false, true)
					);

			// if user upload photo from eb media manager
			$pattern = '/src=\"(\/\/.*?)\"/';
			preg_match_all($pattern, $data['blogIntro'] . $data['blogContent'], $matches);

			$uri = JURI::getInstance();
			$scheme = $uri->toString(array('scheme'));
			$scheme = str_replace('://', ':', $scheme);

			foreach ($matches as $match) {

				if ($match) {
					$data['blogIntro'] = str_replace('src="//', 'src="' . $scheme . '//', $data['blogIntro']);
					$data['blogContent'] = str_replace('src="//', 'src="' . $scheme . '//', $data['blogContent']);
				}
			}

			// if user upload photo from e.g. JCE editor
			$pattern2 = '/src="(.*?)"/';
			preg_match_all($pattern2, $data['blogIntro'] . $data['blogContent'], $matches2);

			foreach ($matches2 as $match) {

				foreach($match as $imageurl) {

					// find the image url which do not exist http/https
					if (strpos($imageurl, 'http://') === false && strpos($imageurl, 'https://') === false) {

						$segments = explode('=', $imageurl);

						if (count($segments) > 1) {

							$url = $segments[1];

							$url = ltrim($url, '"');
							$url = rtrim($url, '"');

							$newurl = 'src="' . rtrim(JURI::root(), '/') . '/' . ltrim($url, '/') . '"';

							$data['blogIntro'] = str_replace($imageurl, $newurl, $data['blogIntro']);
							$data['blogContent'] = str_replace($imageurl, $newurl, $data['blogContent']);
						}
					}
				}
			}

			// Look for all href tag that use relative path, we need to convert it to absolute
			$pattern3 = '/href="((?!mailto).*?)"/';
			preg_match_all($pattern3, $data['blogIntro'] . $data['blogContent'], $matches3);

			foreach ($matches3 as $match) {
				foreach ($match as $link) {

					// Since we know that relative path will always pointing to the current site, so it is safe to do this checking.
					if (strpos($link, 'http://') === false && strpos($link, 'https://') === false && strpos($link, '//') === false) {
						$segments = explode('=', $link);

						if (count($segments) > 1) {
							$url = $segments[1];

							$url = ltrim($url, '"');
							$url = rtrim($url, '"');

							$newUrl = 'href="' . rtrim(JURI::root(), '/') . '/' . ltrim($url, '/') . '"';

							$data['blogIntro'] = str_replace($link, $newUrl, $data['blogIntro']);
							$data['blogContent'] = str_replace($link, $newUrl, $data['blogContent']);
						}
					}
				}
			}
		}

		// we need to remove anything style attribute then reformat to show width 100%
		// skip this process if detect that image tag contain float attribute, because user would like to show the correct format from the email content
		$data['blogIntro'] = preg_replace('/(<[^>]+) style="((?!float).)*?"/i', '$1', $data['blogIntro']);
		$data['blogContent'] = preg_replace('/(<[^>]+) style="((?!float).)*?"/i', '$1', $data['blogContent']);

		// Loop through all <img> tags
		$pattern4 = '/<img[^>]+>/ims';
		$imgFloatProperties = array('float: left;', 'float:left;', 'float: right;', 'float:right;');
		$replaceImgProperties = array('float: left; max-width: 100%;', 'float:left; max-width: 100%;', 'float: right; max-width: 100%;', 'float:right; max-width: 100%;');

		if (preg_match_all($pattern4, $data['blogIntro'] . $data['blogContent'], $matches4)) {

			foreach ($matches4 as $match) {

				// Replace all occurences of width/height
				$clean = preg_replace('/(width|height)=["\'\d%\s]+/ims', "", $match);

				// Inject max-width style property into all the content image tag
				$clean = str_replace($imgFloatProperties, $replaceImgProperties, $clean);

				// Replace with result within html
				$data['blogIntro'] = str_replace($match, $clean, $data['blogIntro']);
				$data['blogContent'] = str_replace($match, $clean, $data['blogContent']);
			}
		}

		// we need to inject max-width 100% for image tag which doesn't have any style attribute.
		$data['blogIntro'] = str_ireplace('src="', 'max-width="100%" style="max-width:100%" src="', $data['blogIntro']);
		$data['blogContent'] = str_ireplace('src="', 'max-width="100%" style="max-width:100%" src="', $data['blogContent']);

		return $data;
	}

	/**
	 * Normalize all posted / binded data.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalize()
	{
		// Normalize the post title
		$this->normalizeTitle();

		// Ensure that the author is set correctly
		$this->normalizeAuthor();

		// Ensure that the post's permalink is set correctly.
		$this->normalizeAlias();

		$this->normalizeSource();
		$this->normalizeDocument();
		$this->normalizeContent();

		// Normalize blog images
		$this->normalizeBlogImage();

		// Normalize content images
		$this->normalizeContentImage();

		// For blank posts, we shouldn't normalize the dates
		$this->normalizeDate();

		// Normalize categories for the post
		$this->normalizeCategories();

		// Ensure that the tags are properly set
		$this->normalizeTags();
		$this->normalizeFrontpage();
		$this->normalizePrivacy();
		$this->normalizeState();

		// Normalizes publishing state
		$this->normalizePublishingState();

		$this->normalizeNewState();

		$this->normalizeOthers();
	}

	/**
	 * Ensures that the author is set correctly.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeAuthor()
	{
		// If author hasn't been set yet, reset it to the original author.
		if (!isset($this->created_by)) {
			$this->created_by = $this->original->created_by;
		}

		// If author is still invalid, set the current user as the author.
		if (!isset($this->created_by)) {
			$this->created_by = $this->user->id;
		}
	}

	/**
	 * Ensures that the blog image data is appropriate
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeBlogImage()
	{
		// Try to convert the image property to an object
		$image = json_decode($this->image);

		// @legacy fix
		// We only want to store the URI for blog images.
		if (is_object($image)) {
			$this->image = $image->place . $image->path;
		}

		// Check for image title
		$imageTitle = $this->getParams()->get('image_cover_title');

		// Do not save image title if there are no cover image
		if (!$this->image && $imageTitle) {
			$this->getParams()->set('image_cover_title', '');
		}

		// Check for image title
		$imageCaption = $this->getParams()->get('image_cover_caption');

		// Do not save image title if there are no cover image
		if (!$this->image && $imageCaption) {
			$this->getParams()->set('image_cover_caption', '');
		}

		$embedCover = $this->isEmbedCover();

		// if this is embed post cover, we save the html into params
		if ($embedCover) {
			$pattern = '/' . $embedCover . '\:\/(.*)/is';

			preg_match($pattern, $this->image, $matches);

			if (empty($matches)) {
				return;
			}

			$url = $matches[1];

			$crawler = EB::crawler();

			// Crawl the url
			$state = $crawler->crawl($url);

			// Get the data from the crawled site
			$data = $crawler->getData();

			// check for the oembed video, if there doesn't return any oembed data
			// we need to return a message here
			if (isset($data['oembed'])) {
				$this->getParams()->set('image_cover_embed', json_encode($data['oembed']));
			}

		}
	}

	/**
	 * Determine if this has embed cover
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function isEmbedCover()
	{
		if (strpos($this->image, 'youtube:/') !== false) {
			return 'youtube';
		}

		if (strpos($this->image, 'vimeo:/') !== false) {
			return 'vimeo';
		}

		if (strpos($this->image, 'twitch:/') !== false) {
			return 'twitch';
		}

		return false;
	}

	/**
	 * Determine if this is an external image cover
	 *
	 * @since	6.0.3
	 * @access	public
	 */
	public function isExternalImageCover($cover = null)
	{
		if (!$this->isImageCover()) {
			return false;
		}

		if (!$cover) {
			$cover = $this->getImage();
		}

		$isExternalImageCover = EB::unsplash()->isValidUrl($cover);

		return $isExternalImageCover;
	}

	/**
	 * Retrieves the type of the post cover
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getCoverType()
	{
		$postCover = $this->getImage();
		$isImage = EB::image()->isImage($postCover) || EB::unsplash()->isValidUrl($postCover);

		if ($this->isEmbedCover() || $postCover && !$isImage) {
			return EB_TYPE_VIDEO;
		}

		return EB_TYPE_IMAGE;
	}

	/**
	 * Retrieve embed cover of the post
	 *
	 * @since	6.0
	 * @access	public
	 */
	public function getEmbedCover()
	{
		$params = $this->getParams(false);
		$embed = json_decode($params->get('image_cover_embed'));

		if (!isset($embed->html)) {
			return false;
		}

		return $embed->html;
	}

	/**
	 * Process any base64 images within the content to actual image
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function normalizeContentImage()
	{
		// Only for legacy content
		if ($this->isEbd()) {
			return;
		}

		$intro = $this->intro;
		$content = $this->content;

		// Scan all <img> tag that contain base64 data from intro content
		preg_match_all('/src=\"data:image\/([a-zA-Z]*);base64,([^\"]*)\"/i', $intro, $result);

		$match = $result[0];
		$extension = $result[1];
		$base64 = $result[2];

		jimport('joomla.filesystem.file');

		if (!empty($match)) {

			// Process each images
			foreach ($base64 as $key => $data) {

				$name = md5('intro' . $key . uniqid());

				$path = '/images/easyblog_articles/' . $this->id . '/' . $name . '.' . $extension[$key];
				$outputFile = JPATH_ROOT . $path;

				// Write the image
				JFile::write($outputFile, base64_decode($data));

				// Create image variation in media manager
				EB::imageset()->generate($outputFile);

				// Replace image path
				$intro = str_ireplace($match[$key], 'src="' . $path . '"', $intro);
			}
		}

		// Scan all <img> tag that contain base64 data from main content
		preg_match_all('/src=\"data:image\/([a-zA-Z]*);base64,([^\"]*)\"/i', $content, $result);

		$match = $result[0];
		$extension = $result[1];
		$base64 = $result[2];

		if (!empty($match)) {

			// Process each images
			foreach ($base64 as $key => $data) {

				$name = md5('content' . $key . uniqid());

				$path = '/images/easyblog_articles/' . $this->id . '/' . $name . '.' . $extension[$key];
				$outputFile = JPATH_ROOT . $path;

				JFile::write($outputFile, base64_decode($data));

				// Create image variation in media manager
				EB::imageset()->generate($outputFile);

				$content = str_ireplace($match[$key], 'src="' . $path . '"', $content);
			}
		}

		$this->intro = $intro;
		$this->content = $content;
	}

	/**
	 * Normalize the alias of the blog post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeAlias()
	{
		// If permalink hasn't been set yet, reset it to the original permalink.
		if (!isset($this->permalink)) {
			$this->permalink = $this->original->permalink;
		}

		// The user might be sending a messed up permalink, try to fix it here
		if ($this->permalink) {
			$model = EB::model('Blog');
			$this->permalink = $model->normalizePermalink($this->permalink, $this->id);
		}

		// If the permalink is still invalid, generate a permalink for this post.
		if (!$this->permalink && $this->title && !$this->isBlank()) {
			$this->permalink = $this->generatePermalink();
		}
	}

	/**
	 * Normalize the source
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeSource()
	{
		// If source type is invald, revert to original value.
		if (!isset($this->source_type)) {
			$this->source_type = $this->original->source_type;
		}

		// If original source type is also invalid,
		if (!isset($this->source_type)) {
			$this->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;
		}

		// If source type is easyblog sitewide, source id is always 0.
		if ($this->source_type==EASYBLOG_POST_SOURCE_SITEWIDE) {
			$this->source_id = 0;
		}

		// TODO: What's the strategy to normalize external source?
	}

	/**
	 * Normalizes the blog post's title.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeTitle()
	{
		// Trim whitespace on blog title
		if (isset($this->title)) {
			$this->title = EB::string()->removeEmoji($this->title);

			if (!is_array($this->title)) {
				$this->title = EBString::trim($this->title);
			}
		}
	}

	/**
	 * Normalizes the document type.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeDocument()
	{
		if ($this->document == null) {
			return;
		}

		// Sanitize the document json string and format it accordingly.
		$document = EB::document($this->document);

		// Enforce doctype
		$this->doctype = $document->type;

		// Normalize it back to json string
		$this->document = $document->toJSON();

		// We might not need this already since @normalizeContent already fixes things?
		// TODO: Translate document into intro & content.
		// Open back this intro is because when the user create post using composer, it will not store post introtext in the db when use readmore block
		$this->intro   = $document->getIntro();
		// $this->content = $document->getContent();
	}

	/**
	 * Normalizes the content of the post by cleaning and filtering the html codes
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeContent()
	{
		if ($this->content == null) {
			return;
		}

		// We want to skip this part if the blog post is being unpublished or republished to avoid the missing intro
		// when readmore break is exist.
		$task = $this->input->get('task', '', 'cmd');
		if (!$this->isDraft() && ($this->isBeingUnpublished() || ($this->isBeingRepublished() && $task != 'save'))) {
			return;
		}

		$content = $this->content;

		// Search for readmore tags using Joomla's mechanism
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$pos = preg_match($pattern, $content);

		if ($pos == 0) {
			$this->intro = $content;

			// @rule: Since someone might update this post, we need to clear out the content
			// if it doesn't contain anything.
			$this->content = '';
		} else {
			list($intro, $main) = preg_split($pattern, $content, 2);

			$this->intro = $intro;
			$this->content = $main;
		}

		// Remove editor generated html like <br mce_bogus="1">
		$this->intro = $this->string->cleanHtml($this->intro);
		$this->content = $this->string->cleanHtml($this->content);

		// Strip tags & attributes that are not allowed.
		$this->intro = $this->string->filterHtml($this->intro);
		$this->content = $this->string->filterHtml($this->content);
	}

	/**
	 * Ensures that all the dates associated with the post is set
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeDate()
	{
		// If creation date is invalid, revert to original value.
		if (!isset($this->created)) {
			$this->created = $this->original->created;
		}

		// If original creation date is also invalid, assign date now.
		if (!isset($this->created)) {
			$this->created = EB::date()->toSql();
		}

		// If publish date is invalid, revert to original value.
		if (!isset($this->publish_up)) {
			$this->publish_up = $this->original->created;
		}

		// If original publish date is also invalid, use creation date.
		if (!isset($this->publish_up)) {
			$this->publish_up = $this->created;
		}

		// If unpublish date is invalid, revert to original value.
		if (!isset($this->publish_down)) {
			$this->publish_down = $this->original->publish_down;
		}

		// If original unpublish date is also invalid, remove unpublish date.
		if (!isset($this->publish_down)) {
			$this->publish_down = EASYBLOG_NO_DATE;
		}

		// If unpublish date is an empty string, it means remove unpublish date.
		if ($this->publish_down=='') {
			$this->publish_down = EASYBLOG_NO_DATE;
		}

		// If autopost schedule date is invalid, revert to original value.
		if (!isset($this->autopost_date)) {
			$this->autopost_date = $this->original->autopost_date;
		}

		// If original autopost schedule date is also invalid, remove the date.
		if (!isset($this->autopost_date)) {
			$this->autopost_date = EASYBLOG_NO_DATE;
		}

		// If autopost schedule date is an empty string, it means remove the date.
		if ($this->autopost_date=='') {
			$this->autopost_date = EASYBLOG_NO_DATE;
		}
	}

	/**
	 * Normalize categories
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeCategories()
	{
		// If primary category is invalid, revert to original value.
		if (!isset($this->category_id)) {
			$this->category_id = $this->original->category_id;
		}

		// Do not assign to the default category from the revision because it will automatically assign those default category tag. #1724
		$fromFeedImport = isset($this->saveOptions['fromFeedImport']) ? $this->saveOptions['fromFeedImport'] : false;

		// If the original value is also invalid, set the default category
		if (!isset($this->category_id) && !$fromFeedImport) {
			$model = EB::model('Category');
			$this->category_id = $model->getDefaultCategoryId();
		}

		// If categories is invalid, revert to original value.
		if (!isset($this->categories)) {
			$this->categories = $this->original->categories;
		}

		// If original categories is also invalid,
		// create a new array of categories with
		// primary category as its member.
		if (!isset($this->categories)) {
			$this->categories = array($this->category_id);
		}


		// If primary category is not a member of the array of categories, add it in.
		if (!in_array($this->category_id, $this->categories)) {
			$this->categories[] = $this->category_id;
		}
	}

	/**
	 * Ensures that all of the default tags are being assigned to the blog.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeTags()
	{
		// If tags is invalid, revert to original tags.
		if (!isset($this->tags)) {
			$this->tags = $this->original->tags;
		}

		// If original tags is also invalid, assign to empty array.
		if (!isset($this->tags) || empty($this->tags)) {
			$this->tags = array();
		}

		if ($this->tags) {
			$tags = explode(',', $this->tags);
		} else {
			$tags = array();
		}

		// Check against catagories for tags that are mandatory.
		if ($this->categories && is_array($this->categories)) {

			foreach ($this->categories as $id) {
				$id = (int) $id;

				$category = EB::table('Category');
				$category->load($id);

				// Get the default tags from a category
				$tags = array_merge($tags, $category->getDefaultTags());
			}
		}

		// Get a list of default tags on the site
		$model = EB::model('Tags');
		$tags = array_merge($tags, $model->getDefaultTagsTitle());

		//remove spacing from start and end of the tags title.
		if ($tags) {
			for($i = 0; $i < count($tags); $i++) {
				$tags[$i] = EBString::trim($tags[$i]);
			}
		}

		$tags = array_unique($tags);
		$this->tags = implode(',', $tags);
	}

	/**
	 * Ensures that the front page state is set correctly
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeFrontpage()
	{

		$checkACL = isset($this->saveOptions['checkAcl']) ? $this->saveOptions['checkAcl'] : true;

		if ($checkACL) {
			// If user is not allowed to modify the frontpage property, revert to original value.
			if ($this->hasChanged('frontpage') && !$this->acl->get('contribute_frontpage')) {
				$this->frontpage = $this->original->frontpage;
			}
		}

		// If this is a new post, assign default frontpage value for new post.
		if ($this->isBlank()) {
			$this->frontpage = true;
		}
	}

	/**
	 * Normalize other properties of the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeOthers()
	{
		if (!isset($this->description)) {
			$this->description = '';
		}

		if (!isset($this->canonical)) {
			$this->canonical = '';
		}

		if (!isset($this->keywords)) {
			$this->keywords = '';
		}

		$checkACL = isset($this->saveOptions['checkAcl']) ? $this->saveOptions['checkAcl'] : true;
		if ($checkACL) {
			// Determines if comments are allowed for this post.
			if ($this->hasChanged('allowcomment') && !$this->acl->get('change_setting_comment')) {
				$this->allowcomment = $this->original->allowcomment;
			}

			if ($this->hasChanged('login_to_read') && !$this->acl->get('change_login_to_read')) {
				$this->login_to_read = $this->original->login_to_read;
			}
		}

		if ($this->isBlank()) {
			$this->allowcomment = $this->config->get('main_defaultallowcomment', 1);
			$this->login_to_read = $this->config->get('change_login_to_read');
			$this->send_notification_emails = $this->config->get('main_sendemailnotifications', 1);
			$this->subscription = $this->config->get('main_subscription', 1);
		}

		if (isset($this->autoposting)) {
			// We should save the $this->autoposting so that we can respect this when posting schedule autopost.
			$this->getParams()->set('autoposting', json_encode($this->autoposting));
		}
	}

	/**
	 * Normalizes the state column
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeState()
	{
		if (is_null($this->state)) {
			$this->state = EASYBLOG_POST_NORMAL;
		}
	}

	/**
	 * Normalizes the new state of the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizeNewState()
	{
		// If blog post is published, isnew should always be false.
		if ($this->isPublished()) {
			$this->isnew = false;
		}
	}

	/**
	 * Normalizes the privacy state
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizePrivacy()
	{
		// If user is not allowed to change the private property, revert it.
		if ($this->hasChanged('access') && !$this->acl->get('enable_privacy')) {
			$this->access = $this->original->access;
		}

		// If private property is not assigned, or admin enforces a value on the private property, assign it.
		if (!isset($this->access)) {
			$this->access = $this->config->get('main_blogprivacy');
		}
	}

	/**
	 * Normalizes the publishing states
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function normalizePublishingState()
	{
		$isPostPreviewProcess = isset($this->saveOptions['isPostPreviewProcess']) ? $this->saveOptions['isPostPreviewProcess'] : false;
		$checkAutosave = isset($this->saveOptions['checkAutosave']) ? $this->saveOptions['checkAutosave'] : false;

		// If this entry is being published, but the publish date is set in
		// the future, change the published state to scheduled state.
		if ($this->isBeingPublished()) {
			$checkACL = isset($this->saveOptions['checkAcl']) ? $this->saveOptions['checkAcl'] : true;

			if ($checkACL) {
				// If this entry is being published, but the user has no permission
				// to publish entry, change the published state to pending state.
				if (!$this->acl->get('publish_entry')) {
					$this->published = EASYBLOG_POST_PENDING;
				}
			} else {
				$this->published = EASYBLOG_POST_PUBLISHED;
			}
		}

		// If the post is being submitted for approval, it should not see the publishing date even that is draft, preview and auto save draft process
		if (!$this->isBeingSubmittedForApproval() && !$isPostPreviewProcess && !$checkAutosave && !$this->isDraft()) {

			// The publish state should be dependent on the `publish_up` column
			$today = EB::date();
			$publishDate = $this->getPublishDate();

			if ($publishDate->toUnix() > $today->toUnix()) {
				$this->published = EASYBLOG_POST_SCHEDULED;
			}
		}

		// If this post is being submitted for approval and the autosave kicks in, we need to ensure that the published state does not change to draft again.
		// This is to avoid the post from being auto rejected. #2951
		if ($checkAutosave && $this->original->published == EASYBLOG_POST_PENDING) {
			$this->published = EASYBLOG_POST_PENDING;
		}
	}

	/**
	 * Applies date offset to the post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function applyDateOffset($offset = null)
	{
		// If offset is not provided, use server date offset.
		if (!isset($offset)) {
			$offset = DateHelper::getOffset();
		}

		// if modified date and create date is the same,
		// also apply offset on modified date.
		if ($this->modified && $this->modified == $this->created) {
			$tmpDate = new JDate($this->modified, $offset);
			$this->modified = $tmpDate->toSql();
		}

		// Apply offset on creation date.
		if ($this->created && $this->created != EASYBLOG_NO_DATE) {
			$tmpDate = new JDate($this->created, $offset);
			$this->created = $tmpDate->toSql();
		}


		// Apply offset on publish date
		if ($this->publish_up && $this->publish_up != EASYBLOG_NO_DATE) {
			$tmpDate = new JDate($this->publish_up, $offset);
			$this->publish_up = $tmpDate->toSql();
		}

		// Apply offset on unpublish date
		if ($this->publish_down && $this->publish_down != EASYBLOG_NO_DATE) {
			$tmpDate = new JDate($this->publish_down, $offset);
			$this->publish_down = $tmpDate->toSql();
		}

		// Apply offset on autopost schedule date
		if ($this->autopost_date && $this->autopost_date != EASYBLOG_NO_DATE) {
			$tmpDate = new JDate($this->autopost_date, $offset);
			$this->autopost_date = $tmpDate->toSql();
		}
	}

	/**
	 * Generate an alias for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function generatePermalink()
	{
		$model = EB::model('Blog');
		$permalink = $model->normalizePermalink($this->title);

		return $permalink;
	}

	/**
	 * Validates the current blog post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function validate()
	{
		// Check if user have permission to save the post. #432
		$this->validateAcl();

		// Checks if the post title is valid
		$this->validateTitle();

		// Checks if the post category is accessible
		$this->validateCategory();

		// Checks if the post cover is mandatory
		$this->validatePostCover();

		// Checks if the post content is valid
		$this->validateContent();

		// Checks if the custom fields are all entered correctly.
		if (!$this->saveOptions['skipCustomFields']) {
			$this->validateFields();
		}
	}

	/**
	 * Check if user have permission to save the post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function validateAcl()
	{
		// Ensure that user really have the permission to publish the blog post. #432
		if (!$this->isBeingSubmittedForApproval() && !$this->canPublish()) {
			throw EB::exception('COM_EASYBLOG_COMPOSER_UNABLE_STORE_POST');
		}
	}

	/**
	 * Ensures that the post's category is accessible by category acl.
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function validateCategory()
	{
		// @task: If this post is not being approved by moderator
		// and this is not site admin.
		if (!$this->isBeingApproved() && !FH::isSiteAdmin()) {
			$category = EB::category();

			// We'll need to pass the created by because of mailbox publishing. #2221
			$allowed = $category::validateCategory($this->categories, $this->created_by);

			if (!$allowed->allowed) {
				throw EB::exception($allowed->message);
			}
		}
	}

	/**
	 * Validate the post cover
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function validatePostCover()
	{
		// Ensure that there is an image for the post cover if it is mandatory
		if ($this->config->get('layout_composer_cover_mandatory') && !$this->image) {
			throw EB::exception('COM_EB_POST_COVER_EMPTY_ERROR');
		}

		return true;
	}

	/**
	 * Ensures that the post title is valid
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function validateTitle()
	{
		// Check for empty title
		if ($this->saveOptions['checkEmptyTitle'] && empty($this->title)) {
			throw EB::exception('COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_ERROR');
		}

		$blockedWord = $this->string->hasBlockedWords($this->title);
		// Check for blocked words in title
		if ($this->saveOptions['checkBlockedWords'] && $blockedWord !== false) {
			throw EB::exception(JText::sprintf('COM_EASYBLOG_BLOG_TITLE_CONTAIN_BLOCKED_WORDS', $blockedWord));
		}
	}

	/**
	 * Validates the content and ensures that the content contains valid data.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function validateContent()
	{
		$content = "";
		$oriContent = "";

		$contentType = $this->config->get('main_post_min_by');

		// Ensure that enable content minimum check
		$hasCheckMinContentLength = $this->saveOptions['checkMinContentLength'] && $this->config->get('main_post_min');

		// Add extra space only for minimum content type set to word
		$addExtraSpace = $hasCheckMinContentLength && $contentType == 'words' ? ' ' : '';

		// Skip validation for ebd until we figure out
		// how to do normalizeDocument().
		if ($this->isEbd()) {

			$blocks = $this->getBlocks();

			// If there is no blocks at all, throw an error
			if (!$blocks) {
				throw EB::exception('COM_EASYBLOG_DASHBOARD_SAVE_CONTENT_ERROR');
			}

			// The blocks might contain empty data because of it's placeholders and stuffs like that
			$valid = array();

			foreach ($blocks as $block) {
				$lib = EB::blocks()->getBlockByType($block->type);

				$isValid = $lib->validate($block);

				if ($isValid) {

					// TODO: For poll block, we need to store the poll data first

					$output = EB::blocks()->renderViewableBlock($block);

					// Get the original content for word calculating
					$oriContent .= $output;

					// convert html entities back to it string. e.g. &nbsp; back to empty space
					$output = html_entity_decode($output);

					// Add extra space for the sentence between the HTML tag before stripped it #1669
					if ($hasCheckMinContentLength && $contentType == 'words') {
						$output = preg_replace('#\<(.+?)\>#', '<$1> ', $output);
					}

					// strip html tags to precise length count.
					$output = strip_tags($output, '<iframe>');

					// remove any blank space.
					$output = trim($output);

					// need to add extra space for each of the block, if not that last word will stick with the next block content
					// so if the validate minimum content type set to word, it will only count for 1
					$content .= $output . $addExtraSpace;
				}

				$valid[] = $isValid;
			}

			// Display an error message when the content is empty.
			if (!in_array(true, $valid) && empty($content)) {
				throw EB::exception('COM_EASYBLOG_DASHBOARD_SAVE_CONTENT_ERROR');
			}

		} else {
			// legacy post
			$content = $this->intro . $this->content;

			// Get the original content for word calculating
			$oriContent = $content;

			// strip html tags to precise length count.
			$content = strip_tags($content);

			// Do not allow blank content
			if (empty($content)) {
				// TODO: I don't like both the language string and the translated value.
				throw EB::exception('COM_EASYBLOG_DASHBOARD_SAVE_CONTENT_ERROR');
			}
		}

		// Ensure content exceeds minimum required length.
		if ($hasCheckMinContentLength) {

			if ($contentType == 'characters') {
				$length = EBString::strlen($content);
				$minLength = $this->config->get('main_post_length');
				$message = 'COM_EASYBLOG_CONTENT_LESS_THAN_MIN_LENGTH';
			}

			if ($contentType == 'words') {
				$length = EB::string()->countWord($oriContent, true);
				$minLength = $this->config->get('main_post_length_words');
				$message = 'COM_EB_CONTENT_LESS_THAN_MIN_WORDS';
			}

			if ($length < $minLength) {
				throw EB::exception(JText::sprintf($message, $minLength));
			}
		}

		$blockedWord = $this->string->hasBlockedWords($content);

		// Check for blocked words in content.
		if ($this->saveOptions['checkBlockedWords'] && $blockedWord !== false) {
			throw EB::exception(JText::sprintf('COM_EASYBLOG_BLOG_POST_CONTAIN_BLOCKED_WORDS', $blockedWord));
		}
	}

	/**
	 * Validates the custom fields for this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function validateFields()
	{
		foreach ($this->categories as $categoryId) {

			// Get a list of fields that are associated with the category
			$model = EB::model('Categories');
			$fields = $model->getCustomFields($categoryId);
			$isValid = true;

			foreach ($fields as $field) {

				if ($field->required) {

					if (is_array($this->fields) && (!isset($this->fields[$field->id]) || empty($this->fields[$field->id]))) {
						$isValid = false;
					}

					if (is_object($this->fields) && (!isset($this->fields->{$field->id}) || empty($this->fields->{$field->id}))) {
						$isValid = false;
					}
				}

			}

			if (!$isValid) {
				throw EB::exception('COM_EASYBLOG_FIELDS_REQUIRED_FIELDS_NOT_PROVIDED', EASYBLOG_MSG_ERROR, false, EASYBLOG_ERROR_CODE_FIELDS);
			}
		}
	}

	/**
	 * Retrieves the author of the item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getAuthor()
	{
		// lets try to get from cache
		if (EB::cache()->exists($this->post->created_by, 'author')) {
			return EB::cache()->get($this->post->created_by, 'author');
		} else {
			return EB::user($this->post->created_by);
		}
	}

	/**
	 * Get author name for this post
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAuthorName()
	{
		if ($this->config->get('layout_composer_author_alias', false) && !empty($this->author_alias)) {
			return $this->author_alias;
		}

		return $this->getAuthor()->getName();
	}

	/**
	 * Get author permalink for this post
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAuthorPermalink()
	{
		if ($this->config->get('layout_composer_author_alias', false) && !empty($this->author_alias)) {
			return 'javascript:void(0)';
		}

		return $this->getAuthor()->getProfileLink();
	}

	/**
	 * Determines if the post has author alias
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function hasAuthorAlias()
	{
		if ($this->config->get('layout_composer_author_alias', false) && !empty($this->author_alias)) {
			return true;
		}

		return false;
	}


	/**
	 * Retrieves asset associated with the post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAsset($key)
	{
		static $items = array();

		$index = $this->id . $key;

		if (!isset($items[$index])) {
			$asset = EB::table('BlogAsset');
			$asset->load(array('post_id' => $this->id, 'key' => $key));

			$items[$index] = $asset;
		}

		return $items[$index];
	}

	/**
	 * Retrieves assets associated with the blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAssets()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$model = EB::model('Assets', true);
			$assets = $model->getPostAssets($this->id);

			$items[$this->id] = $assets;
		}

		return $items[$this->id];
	}

	/**
	 * Retrieves the current revision that is being displayed on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getCurrentRevisionId()
	{
		return $this->post->revision_id;
	}

	/**
	 * Retrieves the active revision for this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getWorkingRevision()
	{
		return $this->revision;
	}

	/**
	 * Retrieves all revisions that are associated with this post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getRevisions($ordering = 'asc')
	{
		$model = EB::model('Revisions');

		$revisions = $model->getAllRevisions($this->id, $ordering);

		return $revisions;
	}

	/**
	 * Retrieves latest draft revision that are associated with this post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getLatestDraftRevision()
	{
		static $_cache = null;

		if (is_null($_cache)) {
			$model = EB::model('Revisions');
			$_cache = $model->getLatestDraftRevision($this->id);
		}

		return $_cache;
	}



	/**
	 * Retrieves the creation date of the item
	 *
	 * @since	5.0
	 * @access	public
	 * @return	EasyBlogDate
	 */
	public function getCreationDate($withOffset = false)
	{
		return EB::date($this->created, $withOffset);
	}

	/**
	 * Retrieve a list of blocks in this block post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getBlocks($limit = null, $includeChildBlocks = false)
	{
		$document = EB::document($this->document);
		$blocks = $document->getBlocks();

		if (!is_null($limit)) {
			$blocks = array_slice($blocks, 0, $limit);
		}

		// Determine whether need to include child block or not
		if ($includeChildBlocks) {

			$availableBlocks = array();

			if (!empty($blocks)) {
				foreach ($blocks as $block) {

					$availableBlocks[] = $block->type;

					// Get blocks inside block
					$this->getAvailableChildBlocks($block, $availableBlocks);
				}

				return $availableBlocks;
			}
		}

		return $blocks;
	}

	/**
	 * Get available child block in the post
	 *
	 * @since   5.2.5
	 * @access  public
	 */
	public function getAvailableChildBlocks($blockObj, &$availableBlocks)
	{
		if (!empty($blockObj->blocks)) {
			foreach ($blockObj->blocks as $block) {

				if (in_array($block->type, $availableBlocks)) {
					continue;
				}

				$availableBlocks[] = $block->type;

				// Get block inside block
				$this->getAvailableChildBlocks($block, $availableBlocks);
			}
		}
	}

	/**
	 * Determine if the blog content has pinterest block
	 *
	 * @since   5.2.5
	 * @access  public
	 */
	public function hasPinterest()
	{
		if ($this->doctype != 'ebd') {
			return false;
		}

		// Retrieve a list of available block type under this blog content
		$blocks = $this->getBlocks(null, true);
		$hasPinterest = false;

		foreach ($blocks as $block) {

			if ($block == 'pinterest') {
				$hasPinterest = true;
				break;
			}
		}

		return $hasPinterest;
	}

	/**
	 * Checks if this is a normal posting and is not related to an external or team source
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isStandardSource()
	{
		return $this->source_type == EASYBLOG_POST_SOURCE_SITEWIDE ? true : false;
	}

	/**
	 * check if this post is a teamblog post or not
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isTeamBlog()
	{
		return ($this->source_type == EASYBLOG_POST_SOURCE_TEAM) ? true : false;
	}

	/**
	 * Retrieves the blog contribution
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getBlogContribution()
	{
		if (!$this->source_type || $this->source_type == EASYBLOG_POST_SOURCE_SITEWIDE) {
			return false;
		}

		$contributor = EB::contributor()->load($this->source_id, $this->source_type);

		return $contributor;
	}

	/**
	 * Retrieves comments for this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getComments($limit = null)
	{
		if (!isset(self::$comments[$this->id])) {
			self::$comments[$this->id] = EB::comment()->getBlogComment($this->id, $limit, 'desc', true);
		}

		// If there's already data, we splice it out
		if (self::$comments[$this->id] && !is_null($limit)) {

			$comments = self::$comments[$this->id];

			if (count($comments) > $limit) {

				// if comment sorting set to desc
				if ($this->config->get('comment_sort') == 'desc') {
					$comments = array_reverse($comments);
				}

				array_splice($comments, $limit);

				return $comments;
			}
		}

		return self::$comments[$this->id];
	}

	/**
	 * Retrieves preview comments for this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPreviewComments($limit = null)
	{
		if (!isset(self::$previewComments[$this->id])) {
			self::$previewComments[$this->id] = EB::comment()->getPreviewBlogComment($this->id, $limit, 'desc', true);
		}

		// If there's already data, we splice it out
		if (self::$previewComments[$this->id] && !is_null($limit)) {

			$comments = self::$previewComments[$this->id];

			if (count($comments) > $limit) {

				// if comment sorting set to desc
				if ($this->config->get('comment_sort') == 'desc') {
					$comments = array_reverse($comments);
				}

				array_splice($comments, $limit);

				return $comments;
			}
		}

		return self::$previewComments[$this->id];
	}

	/**
	 * Get total number of comments for this blog post
	 *
	 * @since	5.1.8
	 * @access	public
	 */
	public function getTotalComments()
	{
		// If comments has been disabled altogether regardless if the integrations is enabled or not, we shouldn't display anything
		if (!$this->config->get('main_comment')) {
			return false;
		}

		if (!isset(self::$commentCounts[$this->id])) {
			$count = EB::comment()->getCommentCount($this);

			self::$commentCounts[$this->id] = $count;
		}

		return self::$commentCounts[$this->id];
	}

	/**
	 * Use @allowComments instead
	 *
	 * @deprecated	5.1
	 */
	public function displayCommentCount()
	{
		return $this->allowComments();
	}

	/**
	 * Retrieve the location image
	 *
	 * @since	5.0.37
	 * @access	public
	 */
	public function getLocationImage()
	{
		$gMapkey = $this->config->get('googlemaps_api_key');

		$url = '//maps.googleapis.com/maps/api/staticmap?size=1280x1280&scale=2&zoom=15&center=' . $this->latitude . ',' . $this->longitude . '&markers=' . $this->latitude . ',' . $this->longitude;

		if (!empty($gMapkey)) {
			$url = '//maps.googleapis.com/maps/api/staticmap?size=1280x1280&scale=2&zoom=15&center=' . $this->latitude . ',' . $this->longitude . '&markers=' . $this->latitude . ',' . $this->longitude . '&key=' . $gMapkey;
		}

		return $url;
	}

	/**
	 * Displays the display date
	 *
	 * @since	4.0
	 * @access	public
	 */
	// @ported to getModifiedDate
	public function getDisplayDate($column = 'created', $withOffset = true)
	{
		if (!isset($this->$column)) {
			$column = 'created';
		}

		$value = $this->$column;

		$date = EB::date($value, $withOffset);

		return $date;
	}

	/**
	 * Retrieves the date time value
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getFormDateValue($column = 'created')
	{
		$value = $this->$column;

		if (!$value) {
			return '';
		}

		$date = EB::date($value, true);
		$value = $date->toSQL(true);

		return $value;
	}

	/**
	 * Get the intro text of the blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getIntro($stripTags = false, $truncate = null, $source = 'intro', $limit = null, $options = array())
	{
		$index = $stripTags ? 'stripped' : 'raw';

		if (is_null($truncate)) {
			$truncate = $this->config->get('composer_truncation_enabled');
		}

		if (!$this->isEB51()) {
			$this->processPostIntroContent();
			$this->exportMedia();
		}

		// in some circumstances, we need to re-process the content.
		$ignoreCache = isset($options['ignoreCache']) ? $options['ignoreCache'] : false;

		if (!$ignoreCache && isset($this->formattedIntros[$index]) && $this->formattedIntros[$index]) {
			return $this->formattedIntros[$index];
		}

		// We need to specify if this post has a cover picture or not to avoid
		// multiple images being shown on the "listings"
		if (!isset($options['hasCover'])) {
			$options['hasCover'] = $this->hasImage();
		}

		// If this coming from rss feed, we need to format the content correctly.
		$fromRss = isset($options['fromRss']) ? $options['fromRss'] : false;

		// patch the fa icons
		$this->post->intro = EB::string()->patchContent($this->post->intro);
		$this->post->content = EB::string()->patchContent($this->post->content);

		// use the intro and content from eb posts table insteadd of revision.
		$this->intro = $this->post->intro;
		$this->content = $this->post->content;

		// If necessary, add nofollow to the anchor links of the blog post
		if ($this->config->get('main_anchor_nofollow')) {
			$this->intro = EB::string()->addNoFollow($this->intro);
			$this->content = EB::string()->addNoFollow($this->content);
		}

		// Set the text attribute first
		if ($source == 'all') {
			$this->text = $this->intro . $this->content;
		} else {
			if ($source == 'content' && (!$this->content && $this->intro)) {
				$this->text = $this->intro;
			} else if ($source == 'intro' && (!$this->intro && $this->content)) {
				$this->text = $this->content;
			} else {
				$this->text = $this->$source;
			}
		}

		$hasReadmore = $this->intro && $this->content ? true : false;

		if ($this->isEbd() && !$hasReadmore) {
			$pattern = '#data-type=\"readmore\"*#i';
			preg_match($pattern, $this->intro, $matches);

			if ($matches) {
				$hasReadmore = true;
			}
		}

		// if there is a readmore in the blog post, we should not process auto truncation at all.
		if ($hasReadmore) {
			$truncate = false;
		}

		if ($truncate) {

			$truncateOverrideOptions = array();

			if (isset($options['forceTruncateByChars'])) {
				$truncateOverrideOptions['forceTruncateByChars'] = true;
			}

			if (isset($options['forceCharsLimit']) && $options['forceCharsLimit']) {
				$truncateOverrideOptions['forceCharsLimit'] = $options['forceCharsLimit'];
			}

			if (isset($options['forceImage'])) {
				$truncateOverrideOptions['forceImage'] = true;
			}

			EB::truncater()->truncate($this, $limit, $truncateOverrideOptions);
		}

		// patch the fa icons
		$this->text = EB::string()->patchContent($this->text);

		if ($fromRss) {
			$this->text = $this->formatRssContent($this->text);
		}

		$fromModule = isset($options['fromModule']) ? $options['fromModule'] : false;

		// Loadmodule tag should not appear in the module or it will be an inception of modules.
		if ($fromModule) {
			$this->text = $this->removeLoadmodulesTags($this->text);
		}

		if ($stripTags) {
			// we need to trip the tags 1st before we trigger all the content plugins. #542

			$this->text = EB::truncater()->strip_only($this->text, '<script>', true);
			$this->text = EB::truncater()->strip_only($this->text, '<object>', true);
			$this->text = strip_tags($this->text);
		}

		$this->text = $this->formatPolls($this->text);

		// Fix issue with missing slashes on relative image.
		// Only process this when the post is loaded via infinite scroll or ajax load. #1450
		if (isset($this->viaAjax) && $this->viaAjax) {
			$this->text = EB::string()->relAddSlashes($this->text);
		}

		// Determines if we should trigger plugins
		$triggerPlugins = isset($options['triggerPlugins']) ? $options['triggerPlugins'] : true;

		// Perform legacy formatting for posts
		// Trigger plugins to prepare the content.
		if ($triggerPlugins) {
			$this->prepareContent('list', 'intro');
		}

		$this->formattedIntros[$index] = $this->text;

		return $this->formattedIntros[$index];
	}

	/**
	 * Retrieves the content of the blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getContent($type = 'list', $triggerPlugin = true, $fromRss = null, $options = [])
	{
		$idx = $type . (int) $triggerPlugin . (int) $fromRss;

		if (!$this->isEB51()) {
			$this->processPostIntroContent();
			$this->exportMedia();
		}

		$isPreview = isset($options['isPreview']) ? $options['isPreview'] : false;
		$ignoreCache = isset($options['ignoreCache']) ? $options['ignoreCache'] : false;
		$processAdsense = isset($options['processAdsense']) ? $options['processAdsense'] : true;

		// The reason we need to cache the content is to avoid javascripts from the blocks being collected multiple times.
		// Until we solve the issue with the javascript in the block handlers being collected more than once, we need to cache this contents.
		if (!$ignoreCache && isset($this->formattedContents[$idx]) && $this->formattedContents[$idx]) {
			return $this->formattedContents[$idx];
		}

		if ($isPreview) {
			if ($this->doctype == 'ebd') {

				$document = EB::document($this->document);
				$contents = $document->getContent();

				// Process any adsense codes
				$contents = EB::adsense()->process($contents, $this->created_by);

				// Since we are getting the entire contents from the block, the intro should be reset to empty
				$this->intro = '';
				$this->content = $contents;
			}

		} else {
			$this->intro = $this->post->intro;
			$this->content = $this->post->content;

			$this->intro = EB::string()->patchContent($this->intro);
			$this->content = EB::string()->patchContent($this->content);
		}

		if ($type == 'entry') {

			if ($processAdsense) {
				$this->intro = EB::adsense()->process($this->intro, $this->created_by);
				$this->content = EB::adsense()->process($this->content, $this->created_by);
			}

			if ($isPreview && $this->doctype != 'ebd') {
				// since this content is meant for previewing, thats mean the content is not being 'pre-processed'.
				// let process all the audio types for previewing.
				$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

				// Process pdf previewers
				EB::pdf()->format($this, false, $useRelative);

				// Process videos
				EB::videos()->format($this, false, $useRelative);

				// Process audio files
				EB::audio()->format($this, false, $useRelative);

				// Format gallery items
				EB::gallery()->format($this);

				// Remove known codes
				EB::truncater()->stripCodes($this);
			}

			if ($isPreview) {
				// Since in preview, we are only getting the entire contents from the blocks as mentioned above
				// So we should remove the readmore tag and assign back the content back to $this->content instead of $this->intro #2087
				$this->content = EB::truncater()->removeReadmore($this->content);
			} else {
				$this->intro = EB::truncater()->removeReadmore($this->intro);
			}
		}

		// If necessary, add nofollow to the anchor links of the blog post
		if ($this->config->get('main_anchor_nofollow')) {
			$this->intro = EB::string()->addNoFollow($this->intro);
			$this->content = EB::string()->addNoFollow($this->content);
		}

		// Truncate the contents first
		$this->text = $this->intro . $this->content;

		if ($type == 'list') {
			EB::truncater()->truncate($this, null, $options);
		}

		$this->text = $this->formatPolls($this->text, ['canVote' => $isPreview ? false : true]);

		// Clean up any unused comment code/option in content
		// For now only applicable to Komento
		EB::comment()->cleanup($this);

		// Check if this was fetched from rss feed.
		// We need to remove any loadmodule or loadposition as it is not viewable in rss layout.
		// #2942
		if ($fromRss) {
			$this->text = $this->formatRssContent($this->text);
		}

		// Trigger plugins to prepare the content.
		if ($triggerPlugin) {
			$this->prepareContent('list', 'content');
		}

		// Cache the item so the document will not be rendered more than once.
		$this->formattedContents[$idx] = $this->text;

		return $this->formattedContents[$idx];
	}

	/**
	 * Retrieves the content of the Instant Article
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getInstantContent($triggerPlugin = true)
	{
		$params = $this->getMenuParams();

		if (!$this->isEB51()) {
			$this->processPostIntroContent();
			$this->exportMedia();
		}

		// If this is a listing type, the contents might need to be truncated
		if ($this->doctype == 'ebd') {

			$document = EB::document($this->document);

			// Since we are getting the entire contents from the block, the intro should be reset to empty
			$this->intro = '';

			$contents = $document->getInstantContent();
			$this->content = $contents;
		}

		if ($this->doctype != 'ebd') {
			require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/facebook/instantArticles.php');

			$this->intro = EBIA::renders($this->intro);
			$this->content = EBIA::renders($this->content);
		}

		// Hide introtext if necessary
		if (!$params->get('show_intro', true) && !empty($this->content)) {
			$this->intro = '';
		}

		// $this->text = $this->intro . $this->content;
		$this->text = $this->intro;

		$ads = '';
		if ($this->config->get('facebook_ads_placement_id', false)) {
			//  Adding Audience Network tag for slot #2
			$ads = '<figure class="op-ad"><iframe width="' . $this->config->get('facebook_ads_width', 300) . '" height="' . $this->config->get('facebook_ads_height', 250) . '" style="border:0; margin:0;" src="https://www.facebook.com/adnw_request?placement=' . $this->config->get('facebook_ads_placement_id', false) . '&adtype=banner' . $this->config->get('facebook_ads_width', 300) . 'x' . $this->config->get('facebook_ads_height', 250) . '"></iframe></figure>';
		}

		$this->text .= $ads . $this->content;

		// We need to remove any loadmodule or loadposition as it is not viewable in rss layout.
		$this->text = $this->formatRssContent($this->text);

		// Trigger plugins to prepare the content.
		$this->prepareContent('list', 'content');

		return $this->text;
	}

	/**
	 * Retrieves the content for the AMP page
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getAMPContent($triggerPlugin = true)
	{
		$params = $this->getMenuParams();

		// If this is a listing type, the contents might need to be truncated
		if ($this->doctype == 'ebd') {

			$document = EB::document($this->document);

			// Since we are getting the entire contents from the block, the intro should be reset to empty
			$this->intro = '';

			$contents = $document->getAMPContent();

			$this->content = $contents;
		}

		if ($this->doctype != 'ebd') {

			// Cleanup the gallery to follow AMP standard
			$this->intro = EB::gallery()->processAMP($this->intro, $this->created_by);
			$this->content = EB::gallery()->processAMP($this->content, $this->created_by);

			// Cleanup the image to follow AMP standard
			$this->intro = EB::image()->processAMP($this->intro, $this->created_by);
			$this->content = EB::image()->processAMP($this->content, $this->created_by);

			// Cleanup the content to follow AMP standard
			$this->intro = EB::string()->processAMP($this->intro);
			$this->content = EB::string()->processAMP($this->content);

			// Cleanup the video to follow AMP standard
			$this->intro = EB::videos()->processAMP($this->intro);
			$this->content = EB::videos()->processAMP($this->content);
		}

		// Hide introtext if necessary
		if (!$params->get('show_intro', true) && !empty($this->content)) {
			$this->intro = '';
		}

		$this->text = $this->intro . $this->content;

		// We need to remove any loadmodule or loadposition as it is not viewable in rss layout.
		$this->text = $this->formatRssContent($this->text);

		// Trigger plugins to prepare the content.
		$this->prepareContent('list', 'content');

		$this->ampCleanup();

		return $this->text;
	}

	/**
	 * Cleaning up amp content to follow google standard
	 *
	 * @since   5.2.8
	 * @access  public
	 */
	public function ampCleanup()
	{
		$uri = JURI::getInstance();
		$scheme = $uri->toString(array('scheme'));
		$scheme = str_replace('://', ':', $scheme);

		// To be safe, we remove style attribute (if any)
		$this->text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $this->text);

		// Also remove any style block found in content
		$this->text = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $this->text);

		$this->text = str_replace('src="//', 'src="' . $scheme . '//', $this->text);
		$this->text = preg_replace("#<p>(\s|&nbsp;|</?\s?br\s?/?>)*</?p>#", "", $this->text);
		$this->text = str_replace('<iframe', '<amp-iframe', $this->text);
		$this->text = str_replace('</iframe', '</amp-iframe', $this->text);

		// Make sure to use https inside amp-iframe
		$this->text = preg_replace('/(<amp-iframe[^>]+).*src="http:(.*?)"/i', '$1 src="https:$2"', $this->text);

		// Amp-iframe should not have these attributes
		$this->text = preg_replace('/(<amp-iframe[^>]+)marginwidth=".*?"/i', '$1', $this->text);
		$this->text = preg_replace('/(<amp-iframe[^>]+)marginheight=".*?"/i', '$1', $this->text);

		$this->text = str_replace('target="_parent"', 'target="_blank"', $this->text);
		$this->text = str_replace('target=""', '', $this->text);

		// remove unallowed attribute from table tag
		$this->text = preg_replace('/(<table[^>]+)frame=".*?"/i', '$1', $this->text);
		$this->text = preg_replace('/(<table[^>]+)rules=".*?"/i', '$1', $this->text);
		$this->text = preg_replace('/(<table[^>]+)border=".*?"/i', '$1', $this->text);
		$this->text = preg_replace('/(<col[^>]+)width=".*?"/i', '$1', $this->text);

		// Remove any form tag from the content
		$this->text = preg_replace('/<form\b[^>]*>(.*?)<\/form>/is', "", $this->text);

		// Remove any script tag
		$this->text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $this->text);

		// Just in case <video> tag is exist in the content
		$this->text = str_replace('<video', '<amp-video', $this->text);
		$this->text = str_replace('</video', '</amp-video', $this->text);
		$this->text = preg_replace('/(<amp-video[^>]+)type=".*?"/i', '$1', $this->text);

		// Remove any grammarly browser extension injected code
		$this->text = preg_replace('/<grammarly-extension\b[^>]*>(.*?)<\/grammarly-extension>/i', '', $this->text);
		$this->text = str_replace('spellcheck="false"', '', $this->text);
		$this->text = str_replace('data-gramm="false"', '', $this->text);
		$this->text = preg_replace('/<lt-highlighter\b[^>]*>(.*?)<\/lt-highlighter>/i', '', $this->text);

		// Remove any Kaspersky password manager injected code
		$this->text = preg_replace('/wfd-id="(.*?)"/i', '', $this->text);
	}

	public function formatRssContent($content)
	{
		// Remove loadposition and loadmodules
		$content = $this->removeLoadmodulesTags($content);

		return $content;
	}

	/**
	 * Remove loadposition and loadmodules from the content
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function removeLoadmodulesTags($content)
	{
		// Expression to search for (positions)
		$regexpos = '/{loadposition\s(.*?)}/i';

		// Expression to search for(modules)
		// {loadmodule mod_easyblogshowcase, showcase}
		$regexmod = '/{loadmodule\s(.*?)}/i';

		// Expression to search for module(ID)
		// {loadmoduleid 169}
		$regexid = '/{loadmoduleid\s(.*?)}/i';

		// Expression to search for regular labs modules anywhere plugin
		// {module 169}
		$regexma = '/{module\s(.*?)}/i';

		// Expression to search for 3rd party module short code
		// [%module %]
		// sbm in $regexsbm means square bracket module
		$regexsbm = '/\[\%module\s(.*?)\%\]/i';

		// Find all instances of plugin and put in $matches for loadposition
		preg_match_all($regexpos, $content, $matchespos, PREG_SET_ORDER);
		preg_match_all($regexmod, $content, $matchesmod, PREG_SET_ORDER);
		preg_match_all($regexid, $content, $matchesid, PREG_SET_ORDER);
		preg_match_all($regexma, $content, $matchesma, PREG_SET_ORDER);
		preg_match_all($regexsbm, $content, $matchessbm, PREG_SET_ORDER);

		// loadposition matched
		if ($matchespos) {
			foreach ($matchespos as $match) {
				$content = str_replace($match[0], '', $content);
			}
		}

		// loadmodule matched
		if ($matchesmod) {
			foreach ($matchesmod as $match) {
				$content = str_replace($match[0], '', $content);
			}
		}

		// loadmoduleid matched
		if ($matchesid) {
			foreach ($matchesid as $match) {
				$content = str_replace($match[0], '', $content);
			}
		}

		// module matched
		if ($matchesma) {
			foreach ($matchesma as $match) {
				$content = str_replace($match[0], '', $content);
			}
		}

		// square bracket module matched
		if ($matchessbm) {
			foreach ($matchessbm as $match) {
				$content = str_replace($match[0], '', $content);
			}
		}

		return $content;
	}

	/**
	 * Remove known 3rd party tags from the content
	 *
	 * @since	5.4.9
	 * @access	public
	 */
	public function remove3rdPartyTags($content)
	{
		// E.g. {imageshow sl=90 sc=17 /}
		$imageshow = '/{imageshow\s(.*?)\s\/}/i';

		// E.g. {rsform 87}
		$rsform = '/{rsform\s(.*?)}/i';

		// {modulepos mainmenu}
		$modulepos = '/{modulepos\s(.*?)}/i';

		preg_match_all($imageshow, $content, $matchesImageshow, PREG_SET_ORDER);
		preg_match_all($rsform, $content, $matchesRsform, PREG_SET_ORDER);
		preg_match_all($modulepos, $content, $matchesModulepos, PREG_SET_ORDER);

		// Removing matched imageshow tag
		if ($matchesImageshow) {
			foreach ($matchesImageshow as $match) {
				$content = str_replace($match[0], '', $content);
			}
		}

		// Removing matched rsform tag
		if ($matchesRsform) {
			foreach ($matchesRsform as $match) {
				$content = str_replace($match[0], '', $content);
			}
		}

		// Removing matched modulepos tag
		if ($matchesModulepos) {
			foreach ($matchesModulepos as $match) {
				$content = str_replace($match[0], '', $content);
			}
		}

		return $content;
	}

	/**
	 * Retrieves the content of the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The type of display mode. list (May contain automated truncation). entry (Contains the full blog post content)
	 * @return
	 */
	public function getPlainContent()
	{
		static $cache = array();

		if (! $this->isEB51()) {
			$this->processPostIntroContent();
			$this->exportMedia();
		}

		$idx = $this->id;

		if (isset($cache[$idx])) {
			return $cache[$idx];
		}

		$content = $this->post->intro . $this->post->content;
		$content = EB::adsense()->process($content, $this->created_by);

		$cache[$idx] = $content;
		return $content;
	}


	/**
	 * Prepares the content without intro text.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getContentWithoutIntro($type = 'entry', $triggerPlugin = true, $options = [])
	{
		$index = 'non-intro-' . $type;

		if (!$this->isEB51()) {
			$this->processPostIntroContent();
			$this->exportMedia();
		}

		$isPreview = isset($options['isPreview']) ? $options['isPreview'] : false;
		$ignoreCache = isset($options['ignoreCache']) ? $options['ignoreCache'] : false;
		$truncate = FH::normalize($options, 'truncate', true);

		// The reason we need to cache the content is to avoid javascripts from the blocks being collected multiple times.
		// Until we solve the issue with the javascript in the block handlers being collected more than once, we need to cache this contents.
		if (!$ignoreCache && isset($this->formattedContents[$index]) && $this->formattedContents[$index]) {
			return $this->formattedContents[$index];
		}

		if ($isPreview) {
			if ($this->doctype == 'ebd') {

				$document = EB::document($this->document);
				$contents = $document->getContent();

				// Process any adsense codes
				$contents = EB::adsense()->process($contents, $this->created_by);

				// Since we are getting the entire contents from the block, the intro should be reset to empty
				$this->intro = '';
				$this->content = $contents;
			}
		} else {
			$this->intro = $this->post->intro;
			$this->content = $this->post->content;
		}

		$textLen = strip_tags($this->content);
		$textLen = str_replace(array(' ','&nbsp;', "\n", "\t", "\r","\r\n"), '', $textLen);

		// If content is empty, assign intro text into the content to avoid empty post.
		if (empty($textLen)) {
			$this->content = $this->intro;
		}

		// now we remove the intro so that plugin triggering will not process the intro
		$this->intro = '';

		// If necessary, add nofollow to the anchor links of the blog post
		if ($this->config->get('main_anchor_nofollow')) {
			$this->content = EB::string()->addNoFollow($this->content);
		}

		// Truncate the contents first
		$this->text = $this->content;

		$fromModule = isset($options['fromModule']) ? $options['fromModule'] : false;

		// Loadmodule tag should not appear in the module or it will be an inception of modules.
		if ($fromModule) {
			$this->text = $this->removeLoadmodulesTags($this->text);
		}

		if ($type == 'list' && $truncate) {
			EB::truncater()->truncate($this, null, $options);
		}

		$this->text = $this->formatPolls($this->text, ['canVote' => $isPreview ? false : true]);

		// Trigger plugins to prepare the content.
		if ($triggerPlugin) {
			$this->prepareContent($type, 'contentwithoutintro');
		}

		// Get the contents after content plugins processed the content.
		$contents = $this->text;

		// Cache the item so the document will not be rendered more than once.
		$this->formattedContents[$index] = $contents;

		return $this->formattedContents[$index];
	}


	/**
	 * Prepares the content before displaying it out.
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function prepareContent($type = 'list', $source = 'content')
	{
		static $prepareContent = [];

		$key = $this->id . $type . $source;

		if (!isset($prepareContent[$key])) {
			// Get the application
			$app = JFactory::getApplication();

			// Load up Joomla's dispatcher
			$dispatcher = EB::dispatcher();

			// @trigger: onEasyBlogPrepareContent
			JPluginHelper::importPlugin('easyblog');
			EB::triggers()->trigger('easyblog.prepareContent', $this);

			// @trigger: onEasyBlogPrepareContent
			JPluginHelper::importPlugin('content');
			EB::triggers()->trigger('prepareContent', $this);

			// For additional joomla content triggers, we need to store the output in various "sections"
			//onAfterDisplayTitle, onBeforeDisplayContent, onAfterDisplayContent trigger start
			$event = new stdClass();

			// @trigger: onAfterDisplayTitle / onContentAfterTitle
			$results = EB::triggers()->trigger('afterDisplayTitle', $this);
			$event->afterDisplayTitle = ($results) ? EBString::trim(implode("\n", $results)) : '';

			// @trigger: onBeforeDisplayContent / onContentBeforeDisplay
			$results = EB::triggers()->trigger('beforeDisplayContent', $this);
			$event->beforeDisplayContent = ($results) ? EBString::trim(implode("\n", $results)) : '';

			// @trigger: onAfterDisplayContent / onContentAfterDisplay
			$results = EB::triggers()->trigger('afterDisplayContent', $this);
			$event->afterDisplayContent = ($results) ? EBString::trim(implode("\n", $results)) : '';

			// Once the whole fiasco of setting the attributes back and forth is done, unset unecessary attributes.
			// unset($this->introtext);
			// unset($this->text);

			$prepareContent[$key] = $event;
		}

		$this->event = $prepareContent[$key];

		return true;
	}

	/**
	 * Retrieves the modified date of the item
	 *
	 * @since	5.0
	 * @access	public
	 * @return	EasyBlogDate
	 */
	public function getModifiedDate()
	{
		// @ported getDisplayDate
		return EB::date($this->modified);
	}

	/**
	 * Retrieves the published date of the item
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPublishDate($withOffset = false)
	{
		// @ported getPublishingDate
		return EB::date($this->publish_up, $withOffset);
	}

	/**
	 * Retrieves the unpublished date of the item
	 *
	 * @since	5.0
	 * @access	public
	 * @return	EasyBlogDate
	 */
	public function getUnpublishDate()
	{
		return EB::date($this->publish_down);
	}


	/**
	 * Retrieves a list of subscribers for this particular post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getSubscribers($excludeEmails = array())
	{
		$db = EB::db();

		$exclusion = '';

		if (!empty($excludeEmails)) {

			foreach ($excludeEmails as $email) {
				$exclusion .= empty($exclusion) ? $db->Quote($email) : ',' . $db->Quote($email);
			}
		}

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_subscriptions');
		$query[] = 'WHERE ' . $db->quoteName('uid') . '=' . $db->Quote($this->id);
		$query[] = 'AND ' . $db->quoteName('utype') . '=' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY);


		if (!empty($exclusion)) {
			$query[] = 'AND ' . $db->quoteName('email') . ' NOT IN(' . $exclusion . ')';
		}

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	/**
	 * Determines if the cover for this post is an image
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isImageCover()
	{
		return $this->getCoverType() === EB_TYPE_IMAGE;
	}

	/**
	 * Determines if the cover for this post is a video
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function isVideoCover()
	{
		return $this->getCoverType() === EB_TYPE_VIDEO;
	}

	/**
	 * Determines if the current item that is being saved is on the same revision
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isCurrent()
	{
		// Returns true if current revision is being used by post table
		return isset($this->revision) && ($this->post->revision_id == $this->revision->id);
	}

	/**
	 * Determines if the post is either published, scheduled or unpublished.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isFinalized()
	{
		return $this->isPublished() || $this->isScheduled() || $this->isUnpublished();
	}

	/**
	 * Determines if the post's revision is finalized and also being used by the current post table.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isReady()
	{
		// If this revision is being used by the post table and the revision is finalized.
		return $this->isCurrent() && $this->isFinalized();
	}

	/**
	 * Determines if the post is available or in other words, visible on the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isAvailable()
	{
		return $this->isCurrent() && $this->isPublished();
	}

	/**
	 * Determines if the post is accessible by the current user viewing the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isAccessible()
	{
		$allowed = EB::privacy()->checkPrivacy($this);

		if (!$allowed->allowed) {
			return $allowed;
		}

		$categories = $this->getCategories();

		foreach ($categories as $category) {
			if ($category->private != 0) {
				$allowed = $category->checkPrivacy();

				if (!$allowed->allowed) {
					// @task: Check if the current user is viewing his own post.
					// We'll allow the user to see his post.
					if ($this->isMine()) {
						$allowed = new stdClass();
						$allowed->allowed = true;
						$allowed->message = '';
					}

					return $allowed;
				}
			}
		}

		// // Check against the primary category permissions
		// $category = $this->getPrimaryCategory();

		// if ($category->private != 0) {
		// 	$allowed = $category->checkPrivacy();
		// }

		return $allowed;
	}

	/**
	 * When initiating a new post on the site the current publishing state of the blog post is blank
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBlank()
	{
		return $this->published == EASYBLOG_POST_BLANK;
	}

	/**
	 * Determines when a blog post is on the draft state
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isDraft()
	{
		return $this->published == EASYBLOG_POST_DRAFT;
	}

	/**
	 * Determines if this post has any revisions that is waiting for approval
	 *
	 * @since	1.4
	 * @access	public
	 */
	public function hasRevisionWaitingForApproval()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$model = EB::model('Revisions');
			$items[$this->id] = $model->isWaitingApproval($this->id);
		}

		return $items[$this->id];
	}

	/**
	 * Determines if this post has any revisions to purge or not
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canPurgeRevisions()
	{
		return $this->canDeleteRevision();
	}

	/**
	 * Determines if user has the rights to delete revision for this blog post.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canDeleteRevision()
	{
		if (FH::isSiteAdmin()) {
			return true;
		}

		if (!$this->isMine() && !$this->acl->get('moderate_entry')) {
			return false;
		}

		if (!$this->acl->get('add_entry')) {
			return false;
		}

		return true;
	}

	/**
	 * Determine if user is able to favourite this post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function canFavourite($userId = null)
	{
		if (!$this->config->get('main_favourite_post')) {
			return false;
		}

		$user = EB::user($userId);

		if (FH::isSiteAdmin()) {
			return true;
		}

		if ($user->id) {
			return true;
		}

		return false;
	}

	/**
	 * Determine if user already favourited this post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function isFavourited($userId = null)
	{
		static $_cache = array();

		if (is_null($userId)) {
			$userId = $this->my->id;
		}

		if (!$userId) {
			return false;
		}

		$idx = $this->id . '.' . $userId;

		if (!isset($_cache[$idx])) {

			$model = EB::model('Favourites');
			$isFavourited = $model->isFavourited($this->id, $userId);

			$_cache[$idx] = is_null($isFavourited) ? false : $isFavourited;
		}

		return $_cache[$idx];
	}

	/**
	 * Detmermines if the blog post is pending
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isPending()
	{
		// Note: "ispending" column is deprecated.
		// During upgrade we'll need to fetch all ispending=1 posts
		// and change the published state to pending.
		return $this->published == EASYBLOG_POST_PENDING;
	}

	/**
	 * Determines if the blog post is scheduled to be published
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isScheduled()
	{
		return $this->published == EASYBLOG_POST_SCHEDULED;
	}

	 /**
	 * Determine if this is a backdate post
	 *
	 * @since   5.2
	 * @access  public
	 */
	public function isBackDated()
	{
		$today = EB::date();
		$publishDate = $this->getPublishDate();

		return $publishDate->toUnix() < $today->toUnix();
	}

	/**
	 * Determines if the blog post is scheduled to be published
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isUnderScheduled()
	{
		return $this->original->published == EASYBLOG_POST_SCHEDULED && $this->published == EASYBLOG_POST_SCHEDULED;
	}

	/**
	 * Determines if this post belongs to the current logged in user
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isMine()
	{
		return $this->created_by == $this->my->id;
	}

	/**
	 * Determines if this post belongs to teamblog and the current logged in user is team admin
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isTeamBlogAdmin()
	{
		static $_cache = array();

		if ($this->my->guest) {
			return false;
		}

		if (!$this->isTeamBlog()) {
			return false;
		}

		$id = $this->getTeamAssociation();

		if ($id === false) {
			return false;
		}

		if (! isset($_cache[$this->my->id])) {
			$team = EB::table('TeamBlog');
			$team->load($id);

			$_cache[$this->my->id] = $team->isTeamAdmin($this->my->id);
		}

		return $_cache[$this->my->id];
	}





	/**
	 * Determines if the post item is published
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isPostPublished()
	{
		return $this->post->published == EASYBLOG_POST_PUBLISHED;
	}

	/**
	 * Determines if the post item is published
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isPostUnpublished()
	{
		return $this->post->published == EASYBLOG_POST_UNPUBLISHED;
	}


	/**
	 * Determines if the blog post is published
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isPublished()
	{
		// Note: This logic is different from the table version.
		return $this->published == EASYBLOG_POST_PUBLISHED;
	}

	/**
	 * Determines if the blog post is unpublished on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isUnpublished()
	{
		return $this->published == EASYBLOG_POST_UNPUBLISHED;
	}

	/**
	 * Determines if the item is trashed
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isTrashed()
	{
		return $this->state == EASYBLOG_POST_TRASHED;
	}

	/**
	 * Determines if the item is archived
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isArchived()
	{
		return $this->state == EASYBLOG_POST_ARCHIVED;
	}

	/**
	 * Determines if the item is being created.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingCreated()
	{
		return $this->published == EASYBLOG_POST_BLANK;
	}

	/**
	 * Determines if this post is being drafted
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingDrafted()
	{
		return $this->original->published != EASYBLOG_POST_DRAFT && $this->published == EASYBLOG_POST_DRAFT;
	}

	/**
	 * Determines if this current item is being submitted for approval
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingSubmittedForApproval()
	{
		return $this->original->published != EASYBLOG_POST_PENDING
			&& $this->published == EASYBLOG_POST_PENDING;
	}

	/**
	 * Determines if this item is being approved by a moderator.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingApproved()
	{
		return $this->original->published == EASYBLOG_POST_PENDING && $this->published == EASYBLOG_POST_PUBLISHED;
	}

	/**
	 * Determines if the post is being rejected
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingRejected()
	{
		return $this->original->published == EASYBLOG_POST_PENDING && $this->published == EASYBLOG_POST_DRAFT;
	}

	/**
	 *
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingScheduledForPublishing()
	{
		return $this->original->published != EASYBLOG_POST_SCHEDULED
			&& $this->published == EASYBLOG_POST_SCHEDULED;
	}

	/**
	 * Determines if this item is being approved by a moderator if the original publish state is pending then become schedule state.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingScheduledFromPending()
	{
		return $this->original->published == EASYBLOG_POST_PENDING
			&& $this->published == EASYBLOG_POST_SCHEDULED;
	}

	/**
	 * Determines if the post is being published on the site
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingPublished()
	{
		return $this->original->published != EASYBLOG_POST_PUBLISHED
			&& $this->published == EASYBLOG_POST_PUBLISHED
			&& $this->isNoLongerNew();
	}

	/**
	 * Determines if the post is being unpublished
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingUnpublished()
	{
		return $this->original->published != EASYBLOG_POST_UNPUBLISHED
			&& $this->published == EASYBLOG_POST_UNPUBLISHED;
	}

	/**
	 * Determines if the post is being republished again.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingRepublished()
	{
		return ($this->original->published != EASYBLOG_POST_PUBLISHED && $this->original->published != EASYBLOG_POST_DRAFT && $this->original->published != EASYBLOG_POST_BLANK)
			&& $this->published == EASYBLOG_POST_PUBLISHED
			&& !$this->isnew;
	}

	/**
	 * Determines if the post is being archived
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingArchived()
	{
		return $this->original->state != EASYBLOG_POST_ARCHIVED
			&& $this->state == EASYBLOG_POST_ARCHIVED;
	}

	/**
	 * Determines if the post is being trashed
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingTrashed()
	{
		return $this->original->state != EASYBLOG_POST_TRASHED
			&& $this->state == EASYBLOG_POST_TRASHED;
	}

	/**
	 * Determines if the post is being trashed
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBeingRestored()
	{
		return $this->original->state == EASYBLOG_POST_TRASHED
			&& $this->state == EASYBLOG_POST_PUBLISHED;
	}

	/**
	 * Determines if this is a new post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function isNew()
	{
		$isNew = false;

		if ($this->isnew) {
			$isNew = true;
		} else if ($this->original->isnew) {
			$isNew = true;
		}

		return $isNew;
	}

	/**
	 * Determines if this is an 'ebd' post type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isEbd()
	{
		return $this->doctype == 'ebd';
	}

	/**
	 * Determines if this is a legacy post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isLegacy()
	{
		if ($this->doctype == 'legacy') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if this post is no longer a new post.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isNoLongerNew()
	{
		return $this->original->isnew && !$this->isnew;
	}

	/**
	 * Verifies a password
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function verifyPassword()
	{
		// If the author is viewing their own post we do not need to verify the password.
		if (EB::user()->id == $this->getAuthor()->id) {
			return true;
		}

		$session = JFactory::getSession();
		$password = $session->get('PROTECTEDBLOG_' . $this->id, '', 'EASYBLOG');

		if ($password == $this->blogpassword) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if this post is password protected
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isPasswordProtected()
	{
		if ($this->config->get('main_password_protect', true) && !empty($this->blogpassword)) {
			if (!EB::verifyBlogPassword($this->blogpassword, $this->id)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Determines if this post is featured
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isFeatured()
	{
		if (!$this->id) {
			return false;
		}

		return $this->isfeatured;
	}

	/**
	 * Determines if this post is from a feed
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isFromFeed()
	{
		$db = EB::db();
		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_feeds_history');
		$query[] = 'WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($this->id);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$imported = $db->loadResult();

		return $imported;
	}

	/**
	 * Retrieves the preview link of the post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPreviewLink($xhtml = true)
	{
		$url = 'index.php?option=com_easyblog&view=entry&layout=preview&uid=' . $this->id . '.' . $this->revision_id;
		$url = EBR::getRoutedURL($url, $xhtml, true, false, true);

		return $url;
	}

	/**
	 * Retrieves the edit link of the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getEditLink($xhtml = true, $explicitFrontEnd = false)
	{
		if (EB::isFromAdmin() && !$explicitFrontEnd) {
			$url = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $this->id . '.' . $this->revision_id;

			return $url;
		}

		$url = EBR::getRoutedURL('index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $this->id . '.' . $this->revision_id, $xhtml, true);

		return $url;
	}

	/**
	 * Retrieves the alias of a post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getAlias()
	{
		static $permalinks = array();

		if (!isset($permalinks[$this->id])) {

			$date = EB::date($this->created);

			// Default permalink
			$permalink = $this->permalink;

			// Ensure that the permalink is valid.
			$permalink = EBR::normalizePermalink($permalink);

			if (EBR::isIDRequired()) {
				$permalink = $this->id . '-' . $permalink;
			}

			// Date based permalink
			$datePermalink = $date->format('Y') . '/' . $date->format('m') . '/' . $date->format('d');

			// Date based SEF settings
			if ($this->config->get('main_sef') == 'date') {
				$permalink = $datePermalink . '/' . $permalink;
			}

			// Category based permalink type
			if ($this->config->get('main_sef') == 'datecategory' || $this->config->get('main_sef') == 'category') {

				// Get the current primary category
				$category = $this->getPrimaryCategory();

				$categoryPermalink = $category->getAlias();

				// Date and category based permalink type
				if ($this->config->get('main_sef') == 'datecategory') {
					$permalink = $categoryPermalink . '/' . $datePermalink . '/' . $permalink;
				} else {
					$permalink = $categoryPermalink . '/' . $permalink;
				}
			}

			// Custom based permalink type
			if ($this->config->get('main_sef') == 'custom') {
				$permalink = EBR::getCustomPermalink($this);
			}

			$permalinks[$this->id] = $permalink;
		}

		return $permalinks[$this->id];
	}

	/**
	 * Retrieves the print link for a post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPrintLink()
	{
		$url = 'index.php?option=com_easyblog&view=entry&id=' . $this->id;
		$url = EBR::_($url, false);

		if (EBR::isSefEnabled()) {
			$url .= '?tmpl=component&print=1&format=print';

		} else {
			$url .= '&tmpl=component&print=1&format=print';
		}

		return $url;
	}

	/**
	 * Retrieves the permalink for this blog post
	 *
	 * @since	5.1.14
	 * @access	public
	 */
	public function getPermalink($xhtml = true, $external = false, $format = null, $routeSEF = true)
	{
		$id = $this->id;

		if ($this->isPending()) {
			$id .= '.' . $this->revision_id;
		}

		// Determine whether need to append the lang query string before route
		$nonSefEntryUrl = $this->normalizeBlogPermalink();

		if ($external) {
			$url = $this->getExternalPermalink();

		} else {

			// it will not return absolute URL after go through this
			$url = EBR::_($nonSefEntryUrl, $xhtml, null, false, false, $routeSEF);
		}

		// Remove administrator segment from the URL
		if ($url) {

			// it seems like the sef link that return from Artio JoomSef might contain the adsolute url,
			// we need to do a check for that before we append the leading slash.
			$isAbsoluteUrl = EBR::normalizeDomainURL($nonSefEntryUrl, $url);

			if (!$external && $isAbsoluteUrl === false && $routeSEF) {
				$url = '/' . ltrim($url, '/');
			}

			$url = str_replace('administrator/', '', $url);
		}

		$url = EBR::appendFormatToQueryString($url, $format);

		return $url;
	}

	public function getCommentsPermalink($xhtml = true, $external = false, $format = null)
	{
		$url = $this->getPermalink($xhtml, $external, $format);
		$url = $url . '#comments';

		return $url;
	}

	/**
	 * Retrieves the external permalink for this blog post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getExternalPermalink()
	{
		static $link = array();

		$id = $this->id;

		if ($this->isPending()) {
			$id .= '.' . $this->revision_id;
		}

		if (!isset($link[$id])) {

			// Determine whether need to append the lang query string before route
			$url = $this->normalizeBlogPermalink();

			$link[$id] = EBR::getRoutedURL($url, true, true);
		}

		return $link[$id];
	}

	/**
	 * Get external permalink to the blog post which xhtml is pass to false or can pass a blog link with comment anchor tag
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getExternalBlogLink($commentId = '')
	{
		// Determine whether need to append the lang query string before route
		$url = $this->normalizeBlogPermalink();

		// Route to SEF link
		$url = EBR::getRoutedURL($url, false, true);

		// Append the comment anchor tag
		if ($commentId) {
			$url .= '#comment-' . $commentId;
		}

		return $url;
	}

	/**
	 * Normalize the non sef link whether need to include the lang query string or not
	 *
	 * @since	5.4.5
	 * @access	public
	 */
	public function normalizeBlogPermalink()
	{
		$appendQueryLang = '';
		$defaultURL = 'index.php?option=com_easyblog&view=entry&id=' . $this->id;

		// Determine the site got enable multilingual or not
		$isSiteMultilingualEnabled = FH::isMultiLingual();

		// No need do anything if the site is not enable multilingue and the blog language is set to all
		if (!$isSiteMultilingualEnabled || ($this->language == '*' || $this->language == '')) {
			return $defaultURL;
		}

		// Retrieve the current post language
		$postLanguage = EBR::getSiteLanguageTag($this->language);

		if (!$postLanguage) {
			$postLanguage = $this->language;

			// Convert the language to 2 character format
			$postLanguage = substr($postLanguage, 0, 2);
		}

		// language query string
		$defaultURL = $defaultURL . '&lang=' . $postLanguage;

		return $defaultURL;
	}

	/**
	 * Get total word use in this post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getTotalWords($recalculate = null)
	{
		static $totalWords = null;

		if (!isset($totalWords[$this->id])) {

			if ($recalculate) {
				$content = $this->getContent(EASYBLOG_VIEW_ENTRY);

				$string = EB::string();
				$total = $string->countWord($content, true);

				// Store it into params
				$params = $this->getParams();
				$params->set('total_words', $total);

				$this->post->params = $params->toString();
				$this->post->store();
			} else {
				$params = $this->getParams(false);
				$total = $params->get('total_words');

				if (!$total) {
					$total = $this->getTotalWords(true);
				}
			}

			$totalWords[$this->id] = $total;
		}

		return $totalWords[$this->id];
	}

	/**
	 * Get estimate reading time for this blog
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getReadingTime($recalculate = null)
	{
		static $readingTime = null;

		if (!isset($readingTime[$this->id])) {

			$totalWords = $this->getTotalWords($recalculate);

			// Average reading time for most users is around 200 words per minute.
			$wordsPerMinute = 200;

			$minutes = floor($totalWords / $wordsPerMinute);
			$seconds = floor($totalWords % $wordsPerMinute / ($wordsPerMinute / 60));

			// Average the minutes
			if ($seconds >= 30 || $minutes == 0) {
				$minutes++;
			}

			$str = EB::string();

			// Average to hours
			if ($minutes > 59) {
				$hours = floor($minutes / 60);
				$minutes = $minutes - ($hours * 60);

				if ($minutes > 0) {
					$stringHours = 'COM_EB_READ_HOUR';
					$string = $str->getNoun($stringHours, $hours);

					$stringMinutes = '_MINUTE';
					$string .= $str->getNoun($stringMinutes, $minutes);

					$estimation = JText::sprintf($string, $hours, $minutes);
				} else {
					$string = 'COM_EB_READ_TIME_HOUR';
					$string = $str->getNoun($string, $hours);
					$estimation = JText::sprintf($string, $hours);
				}
			} else {
				$string = 'COM_EB_READ_TIME_MINUTE';
				$string = $str->getNoun($string, $minutes);
				$estimation = JText::sprintf($string, $minutes);
			}

			$readingTime[$this->id] = $estimation;
		}

		return $readingTime[$this->id];
	}

	/**
	 * Determines if this post has an image associated
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasImage()
	{
		$hasImage = !empty($this->image);

		return $hasImage;
	}

	/**
	 * Determines if this post has an post's image in the content or not
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function usePostImage($respectTruncation = true)
	{
		$hasReadmore = $this->post->intro && $this->post->content ? true : false;
		$doCheck = false;

		// only if auto truncation is required and enabled,
		// and admin enable the option 'use first image as cover'
		if ($respectTruncation
			&& !$hasReadmore
			&& $this->config->get('composer_truncation_enabled')
			&& $this->config->get('cover_firstimage', 0)) {
			$doCheck = true;
		}

		if ($doCheck) {
			// let try to get the 1st image from the post.
			$media = $this->getMedia();
			if (isset($media) && $media->images && $media->images[0]) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Retrieve image title
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getImageTitle()
	{
		$params = $this->getParams(false);
		$imageTitle = $params->get('image_cover_title');

		// Default to post title
		if (!$imageTitle) {
			$imageTitle = $this->getTitle();
		}

		return $imageTitle;
	}

	/**
	 * Retrieve image caption
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function getImageCaption()
	{
		$params = $this->getParams(false);

		return $params->get('image_cover_caption', '');
	}

	/**
	 * Get cover image dimension for the schema markup
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getCoverDimension($size = 'original')
	{
		static $dimensionCache = array();

		$index = $this->id . '-' . $size;

		if (!isset($dimensionCache[$index])) {
			$obj = new stdClass();
			$obj->width = '';
			$obj->height = '';

			$imageUri = $this->image;
			$imagePath = null;

			// Check for first image if it stored locally
			if (!$imageUri) {

				// let try to get the 1st image from the post.
				$media = $this->getMedia();

				if ($media && $media->images && isset($media->images[0])) {
					$image = $media->images[0];

					// Determine if this image is stored locally
					if (isset($image->isurl) && !$image->isurl && $image->uri) {
						$imageUri = $image->uri;
					}
				}
			}

			// Check for legacy editor image path
			if (!$imageUri && isset($media->images[0])) {
				$image = $media->images[0];

				if (isset($image->url)) {
					$imagePath = $image->url;
				}
			}

			if ($imageUri || $imagePath) {

				if ($imageUri) {
					// Ensure that the image is normalized
					$this->normalizeBlogImage();

					// Load up the media manager library
					$mm = EB::mediamanager();

					$url = $mm->getUrl($imageUri);
					$path = $mm->getPath($imageUri);
					$fileName = $mm->getFilename($imageUri);

					$exists = JFile::exists($path);

					if (!$exists) {
						$dimensionCache[$index] = $obj;
						return $dimensionCache[$index];
					}

					$image = EB::blogimage($path, $url);

					$obj = $image->getImageDimension($size);
				} else if ($imagePath) {
					$storage = JPATH_ROOT . '/' . $imagePath;
					$exists = JFile::exists($storage);

					if (!$exists) {
						$dimensionCache[$index] = $obj;
						return $dimensionCache[$index];
					}

					$dimension = getimagesize($storage);

					if ($dimension) {
						$obj->width = $dimension[0];
						$obj->height = $dimension[1];
					}
				}
			}

			$dimensionCache[$index] = $obj;
		}

		return $dimensionCache[$index];
	}

	/**
	 * Get the alternative text of the post cover
	 *
	 * @since	5.4.3
	 * @access	public
	 */
	public function getCoverImageAlt()
	{
		if (!$this->image) {
			return '';
		}

		$params = $this->getParams(false);
		$altText = $params->get('image_cover_alt');

		if (!$altText) {
			$altText =  $this->getImageTitle();
		}

		return $altText;
	}

	/**
	 * Get the first image from the content
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFirstImage($size = 'original', $protocol = false)
	{
		// let try to get the 1st image from the post.
		$media = $this->getMedia();
		$url = false;

		if (isset($media) && $media->images && $media->images[0]) {
			$url = $media->images[0]->url;

			$isExternalUrl = isset($media->images[0]->isurl) ? $media->images[0]->isurl : false;

			// Get the correct size if exists
			if (!$isExternalUrl) {

				// Load up the media manager library
				$mm = EB::mediamanager();

				$firstImage = $media->images[0];


				if (isset($firstImage->uri)) {

					$firstImageUrl = $mm->getUrl($firstImage->uri);
					$firstImagePath = $mm->getPath($firstImage->uri);
					$firstImageFileName = $mm->getFilename($firstImage->uri);

					// Ensure that the item really exist before even going to do anything on the original image.
					// If the image was manually removed from FTP or any file explorer, this shouldn't yield any errors.
					$exists = JFile::exists($firstImagePath);

					// If the blog image file doesn't exist, we use the default
					if ($exists) {
						$image = EB::blogimage($firstImagePath, $firstImageUrl);
						$url = $image->getSource($size, false, $protocol);
					}

				} else if ($this->isLegacy()) {

					// if this is a legacy post and we know if the image is added via media manager, then we will be able to get the image object.
					$searchPrefix = array( EBLOG_SYSTEM_VARIATION_PREFIX . '_amp_',
										EBLOG_SYSTEM_VARIATION_PREFIX . '_icon_',
										EBLOG_SYSTEM_VARIATION_PREFIX . '_small_',
										EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_',
										EBLOG_SYSTEM_VARIATION_PREFIX . '_medium_',
										EBLOG_SYSTEM_VARIATION_PREFIX . '_large_');

					$tmpPhotoUrl = $url;

					// Replace the prefix to empty value
					$oriPhotoUrl = str_replace($searchPrefix, '', $tmpPhotoUrl);

					// make sure this photo url is added from EB media manager.
					if ($oriPhotoUrl !== $url) {
						// Render absolute full path
						$oriPhotoUrlAbsolute = JPATH_ROOT . '/' . $oriPhotoUrl;

						$exists = JFile::exists($oriPhotoUrlAbsolute);
						if ($exists) {
							$image = EB::blogimage($oriPhotoUrlAbsolute, $oriPhotoUrl);
							$url = $image->getSource($size, false, $protocol);
						}
					}
				}

				$isExternalUrl = strpos($url, 'http') !== false || strpos($url, '//') === 0;
			}

			// Juri
			$uri = JURI::getInstance();

			if ($protocol && !$isExternalUrl) {

				// site root url (this might include the sub-folder)
				$root = rtrim(JURI::root(), '/');

				// to test if the url has the full domain name, we need to exclude the sub-directory.
				// e.g. http://www.abc.com/test/index.php vs http://www.abc.com/index.php
				$testRoot = $uri->toString(array('scheme', 'host'));
				$testRoot = rtrim($testRoot, '/');
				$testRoot = preg_replace("(^https?://)", "//", $testRoot);

				if (strpos($url, $testRoot) === false) {
					$url = $root . '/' . ltrim($url, '/');
				} else {

					$urlProtocal = $uri->toString(array('scheme'));
					$urlProtocal = str_replace('//', '', $urlProtocal);
					$url = $urlProtocal . $url;
				}
			}

			// here we need to further check if the url is now start with //
			// #627
			if ($protocol && strpos($url, 'http') === false) {
				// If the first 2 characters is //, we can automatically assume that this is pre 5.0 urls
				if ($url[0] == '/' && $url[1] == '/') {
					$urlProtocal = $uri->toString(array('scheme'));
					$urlProtocal = str_replace('//', '', $urlProtocal);
					$url = $urlProtocal . $url;
				}
			}
		}

		return $url;
	}

	/**
	 * Retrieves the blog image url for this blog post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getImage($size = 'original', $showPlaceholder = true, $protocol = false, $useFirstImage = true, $fallback = false)
	{
		static $cache = [];

		// Backward compatibility
		$size = in_array($size, EB::getDeprecatedImageSizes()) ? 'thumbnail' : $size;

		$index = $this->id . '-' . $size . (int) $showPlaceholder . (int) $protocol . (int) $useFirstImage;

		// Default blog image
		$default = false;

		// Display a default place holder
		if ($showPlaceholder) {
			$default = EB::getPlaceholderImage();
		}

		if (!isset($cache[$index]) || $fallback) {
			// Load up the media manager library
			$mm = EB::mediamanager();

			// If there's no image data for this post, skip this altogether
			if (!$this->image) {
				$cache[$index] = $default;

				if ($useFirstImage) {
					$url = $this->getFirstImage($size, $protocol);

					if ($url) {
						$this->isUsingFirstImage = true;
						$cache[$index] = $url;

						return $cache[$index];
					}
				}

				$url = $this->getCategoryDefaultPostCover();

				// Use its category default post cover if there is
				if ($url) {
					$cache[$index] = $url;
				}

				return $cache[$index];
			}

			// Flickr post covers
			if (strpos($this->image, 'flickr:/') !== false) {
				$pattern = '/flickr\:\/(.*)/is';

				preg_match($pattern, $this->image, $matches);

				if (!empty($matches)) {
					return $matches[1];
				}
			}

			// Unsplash post covers
			if (strpos($this->image, 'unsplash:/') !== false) {
				$pattern = '/unsplash\:\/(.*)/is';

				preg_match($pattern, $this->image, $matches);

				if (!empty($matches)) {
					return $matches[1];
				}
			}

			// Post covers stored on amazon
			if (strpos($this->image, 'amazon:') !== false) {

				$adapter = $mm->getAdapter('amazon');
				$url = $adapter->generateImageURL($this->image, $size);

				if (!$url) {
					return $default;
				}

				$cache[$index] = $url;
				return $url;
			}

			$embedCover = $this->isEmbedCover();

			if ($embedCover) {
				$pattern = '/' . $embedCover . '\:\/(.*)/is';

				preg_match($pattern, $this->image, $matches);

				if (!empty($matches)) {
					return $matches[1];
				}
			}

			// Ensure that the image is normalized
			$this->normalizeBlogImage();

			$url = $mm->getUrl($this->image);
			$path = $mm->getPath($this->image);
			$fileName = $mm->getFilename($this->image);

			// Ensure that the item really exist before even going to do anything on the original image.
			// If the image was manually removed from FTP or any file explorer, this shouldn't yield any errors.
			$exists = JFile::exists($path);

			// If the blog image file doesn't exist, we use the default
			if (!$exists) {
				$cache[$index] = $default;

				return $cache[$index];
			}

			$cache[$index] = $url;
			$isImage = EB::image()->isImage($path);

			if ($isImage) {
				$image = EB::blogimage($path, $url);

				$url = $image->getSource($size, false, $protocol);

				$cache[$index] = $url;

				$imageLib = EB::image();

				// Retrieve the fallback image of the webp image
				if ($fallback && $imageLib->isWebp($url)) {
					$fallbackUri = $mm->getWebpFallbackUri($this->image);

					$url = $mm->getUrl($fallbackUri);
					$path = $mm->getPath($fallbackUri);

					$image = EB::blogimage($path, $url);
					$url = $image->getSource($size, false, $protocol);

					$cache[$index] = $url;
				}
			}
		}

		return $cache[$index];
	}

	public function getContentImage()
	{
		$content = $this->getPlainContent();
		$img = EB::string()->getImage($content);

		return $img;
	}

	/**
	 * Retrieves the primary category for this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPrimaryCategory($forCurrentRevision = false)
	{
		static $items = array();

		$key = $this->id . $forCurrentRevision;

		if (!isset($items[$key])) {
			$category = null;

			// Get category for normal post
			if (!$forCurrentRevision) {
				// lets try to load from cache
				if (EB::cache()->exists($this->id,'posts')) {
					$data = EB::cache()->get($this->id,'posts');

					if (isset($data['primarycategory'])) {
						$category = $data['primarycategory'];
					}
				}

				if (! $category) {
					$model = EB::model('Categories');
					$category = $model->getPrimaryCategory($this->id);

					// Detect legacy category which uses `category_id`
					if ($category === false && $this->category_id) {
						$category = EB::table('Category');
						$category->load($this->category_id);
					}
				}
			// Try to retrieve category for current revision
			} else {
				$revision = $this->getWorkingRevision();
				$revisionContent = json_decode($revision->content);

				$catId = $revisionContent->category_id;

				$category = EB::table('Category');
				$category->load($catId);
			}

			$items[$key] = $category;
		}

		return $items[$key];
	}

	/**
	 * Determines if the blog post is associated with a team
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTeamAssociation()
	{
		if ($this->source_id && $this->source_type == EASYBLOG_POST_SOURCE_TEAM) {
			return $this->source_id;
		}

		return false;
	}

	/**
	 * Retrieves a list of users that subscribed to this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getRegisteredSubscribers($type = 'new', $exclusion = array())
	{
		if ($type == 'new') {
			$model = EB::model('Subscription');
			$subscribers = $model->getSiteSubscribers();

			$categoryModel = EB::model('Category');
			$subscribers = array_merge($subscribers, $categoryModel->getCategorySubscribers($this->category_id));
		}

		$result = array();

		if (!$subscribers) {
			return $result;
		}

		foreach ($subscribers as $subscriber) {
			if ($subscriber->user_id && !in_array($subscriber->user_id, $exclusion)) {
				$result[] = $subscriber->user_id;
			}
		}

		$result = array_unique($result);

		return $result;
	}

	/**
	 * This method will intelligently determine which menu params this post should be inheriting from
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getMenuParams()
	{
		static $items = array();

		if (!isset($items[$this->id])) {

			$model = EB::model('Menu');

			// Try to detect if the post's primary category has any overrides on the entry layout settings
			$categoryParams = $this->category->getParams();
			$categoryParamsArray = $categoryParams->toArray();

			// If there is no settings set for the category, we'll just inherit it.
			if (!$categoryParamsArray) {
				$categoryParamsArray['inherited'] = true;
			}

			// Get the entry layout keys
			$defaultParams = $model->getDefaultEntryXMLParams();
			$defaultKeys = $defaultParams->toArray();

			// New method to detect if the category overrides the global view "entry layout" settings.
			if (isset($categoryParamsArray['inherited']) && $categoryParamsArray['inherited']) {
				// We should inherit the settings from global view entry layout settings
				foreach ($defaultKeys as $key => $value) {
					$categoryParams->set($key, $this->config->get('layout_' . $key));
				}

			} else {

				// If it falls under this condition, then it's most likely that this category settings
				// was created prior to 5.1 and we need to check the legacy way
				foreach($categoryParamsArray as $key => $value) {

					// If stored value was -1, it means it should be inheriting from the default settings
					if ($value == '-1') {
						$categoryParams->set($key, $this->config->get('layout_' . $key));
						continue;
					}
				}
			}

			// If there is a menu item associated with this post, we'll use the menu settings instead
			$menuId = $model->getMenusByPostId($this->id);

			if ($menuId) {
				// Seems like menu->params will return null if the value is a negative value.
				// We'll need to manually retrieve the data with sql.
				$menuParams = $model->getMenuParamsById($menuId);
				$menuParamsArray = $menuParams->toArray();

				// For menu items created prior to 5.0, we will have to use the category params
				if (!isset($menuParamsArray['show_intro'])) {

					$items[$this->id] = $categoryParams;

					return $items[$this->id];
				}

				foreach ($menuParamsArray as $key => $value) {

					// If stored value was -1, it means it should be inheriting from the category settings
					if ($value == '-1') {
						$menuParams->set($key, $categoryParams->get($key));
						continue;
					}
				}

				$items[$this->id] = $menuParams;

			} else {
				// If there's no menu associated with the post, associate the params with the primary category
				$items[$this->id] = $categoryParams;
			}
		}

		return $items[$this->id];
	}

	/**
	 * Retrieves the rating of the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getRatings()
	{
		// If we don't have a copy of the ratings, we should get it.
		if (!isset(self::$ratings[$this->id])) {
			$model = EB::model('Ratings');
			$ratings = $model->preloadRatings(array($this->id));

			if (!$ratings) {
				self::$ratings[$this->id] = new stdClass();
				self::$ratings[$this->id]->ratings = 0;
				self::$ratings[$this->id]->total = 0;

				return self::$ratings[$this->id];
			}
			self::$ratings[$this->id] = $ratings[$this->id];
		}

		return self::$ratings[$this->id];
	}

	/**
	 * Retrieves the uid for this post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * Retrieves a list of seo keywords for this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getKeywords()
	{
		if (!$this->keywords) {
			return array();
		}

		$keywords = explode(',', $this->keywords);

		return $keywords;
	}

	/**
	 * Retrieves the title of the post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getTitle()
	{
		$title = $this->title ? $this->title : JText::_('COM_EASYBLOG_POST_NO_TITLE');

		return $title;
	}

	/**
	 * Retrieves the post type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getType()
	{
		return $this->posttype;
	}

	/**
	 * Retrieves reaction data related to the current post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getReactions()
	{
		$reactions = $this->reactions;

		if ($reactions == null || $reactions == 'null') {
			return false;
		}

		$reactions = json_decode($reactions, true);

		foreach ($reactions as &$reaction) {
			$reaction = (object) $reaction;
		}

		return $reactions;
	}

	/**
	 * Get a list of tag objects that are associated with this blog post.
	 *
	 * @access	public
	 * @param	null
	 * @return	Array	An Array of TableTag objects.
	 */
	public function getTags()
	{
		static $instances = [];

		if (!isset($instances[$this->id])) {
			$tags = [];

			// lets load from cache
			if (EB::cache()->exists($this->id, 'posts')) {
				$data = EB::cache()->get($this->id, 'posts');

				if (isset($data['tag'])) {
					$tags = $data['tag'];
				}
			}

			// Retrieve the tags if the cache is not exists or the tag data is not set from the cache
			if (empty($tags)) {
				$excludeRevision = $this->isCurrent() ? true : false;
				$model = EB::model('PostTag');
				$tags = $model->getBlogTags($this->id, true, $excludeRevision);
			}

			$instances[$this->id] = $tags;
		}

		return $instances[$this->id];
	}

	/**
	 * Retrieves a list of categories associated with this blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCategories()
	{
		static $categories = array();

		if (!isset($categories[$this->id])) {

			$results = array();

			// lets load from cache if there is any
			if (EB::cache()->exists($this->id, 'posts')) {
				$data = EB::cache()->get($this->id, 'posts');

				if (isset($data['category']) && $data['category']) {

					$results = $data['category'];
				}
			} else {
				$model 	= EB::model('Categories');
				$results = $model->getBlogCategories($this->id);
			}

			$categories[$this->id] = $results;
		}

		return $categories[$this->id];
	}


	/**
	 * Retrieves a list of categories associated with this blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRevisionCount($type = EASYBLOG_REVISION_DRAFT)
	{
		$model 	= EB::model('Revisions');

		return $model->getRevisionCount($this->id, $type);
	}



	/**
	 * Retrieves a list of custom fields associated to this blog post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCustomFields()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$categories = $this->getCategories();

			if (!$categories) {
				$items[$this->id] = false;
				return false;
			}

			$fields = array();
			$groupIds = array();
			$hasFields = false;

			foreach ($categories as $category) {

				$categoryFields = $category->getCustomFields('read');


				if ($categoryFields !== false) {

					if (in_array($categoryFields->group->id, $groupIds)) {
						continue;
					}

					$groupIds[] = $categoryFields->group->id;
					$fields[] = $categoryFields;
					$hasFields = true;
				}
			}

			if (!$hasFields) {
				$items[$this->id] = false;
				return false;
			}

			$items[$this->id] = $fields;

		}

		return $items[$this->id];
	}

	/**
	 * Determines if the current visitor has voted on this item
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasVoted($userId = null)
	{
		if (isset(self::$postVotes[$this->id])) {
			return self::$postVotes[$this->id];
		}

		if (is_null($userId)) {
			$userId = $this->my->id;
		}

		$hash   = '';
		$ipaddr = '';
		if (empty($userId)) {
			//mean this is a guest.
			$hash = JFactory::getSession()->getId();
			$ipaddr = @$_SERVER['REMOTE_ADDR'];
		}

		$model = EB::model('ratings');
		return $model->hasVoted($this->id, EASYBLOG_RATINGS_ENTRY, $userId, $hash, $ipaddr);
	}

	/**
	 * Determines if a property of the item has changed or not.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasChanged($prop)
	{
		return $this->$prop != $this->original->$prop;
	}

	/**
	 * Determines if the post has location
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasLocation()
	{
		if ($this->address && $this->latitude && $this->longitude) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if there is a readmore tag in this content
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function hasReadmore()
	{
		// By default, display the read more link if not configured to respect read more.
		if (!$this->config->get('composer_truncation_readmore')) {
			return true;
		}

		// If the post is enfoced to login to read
		if ($this->login_to_read) {
			return true;
		}

		// If there's a read more attribute injected on this library, we need to respect that
		if (isset($this->readmore)) {
			return $this->readmore;
		}

		$this->intro = $this->post->intro;
		$this->content = $this->post->content;


		// Get the maximum character before read more kicks in.
		$max = ($this->doctype == 'ebd') ? $this->config->get('composer_truncation_chars') : $this->config->get('layout_maxlengthasintrotext');

		// Final fallback is to check the characters length.
		$introlength	= EBString::strlen(strip_tags($this->intro));

		// we need to check if the content has value or not. since some user might place a readmore but there is no content on 'content' section.
		$fulllength	= EBString::strlen(strip_tags($this->intro . $this->content));

		// if fulllength is same as intro length, means this document has only intro text and no content text due to the readmore blocks.
		if ($fulllength == $introlength) {
			return false;
		}

		// If this is a legacy document, and we know that the intro and content is not empty, there's definitely a read more
		$hasReadmore = $this->intro && $this->content ? true : false;

		if ($this->isEbd() && !$hasReadmore) {
			$pattern = '#data-type=\"readmore\"*#i';
			preg_match($pattern, $this->intro, $matches);

			if ($matches) {
				$hasReadmore = true;
			}
		}

		if ($hasReadmore) {
			return true;
		}

		if ($introlength > $max && $this->config->get('composer_truncation_enabled')) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user needs to login to read the post.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function requiresLoginToRead()
	{
		// If the settings requires the user to be logged in, do not allow guests here.
		if ($this->my->guest && ($this->config->get('main_login_read') || $this->login_to_read)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current user can view this post or not.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function checkView()
	{
		// If the blog post is already deleted, we shouldn't let it to be accessible at all.
		if ($this->isTrashed()) {
			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		$view = $this->input->get('view', '', 'var');
		$layout = $this->input->get('layout', '', 'var');

		if ($view == 'entry' && $layout == 'preview') {

			// if that is preview, we should allow author or superadmin able to view unpublished post
			if ($this->isUnpublished() && (($this->my->id == $this->created_by) || FH::isSiteAdmin())) {
				return true;
			}

			// There is a situation where, admin want to preview a post from backend and the post is not publish
			// If the admin is not logged in frontend, we show him the login form.
			if ($this->my->guest && !$this->isPublished()) {

				// Once logged in, we redirect the user to the preview page
				$uri = base64_encode($this->getPreviewLink(false));
				$url = EBR::_('index.php?option=com_easyblog&view=login&return=' . $uri, false);

				return $this->app->redirect($url);
			}
		}

		// Check if the viewer is not author or superadmin, we shouldnn't allow to preview.
		if ($view == 'entry' && $layout == 'preview' && $this->isPublished() && ($this->my->id != $this->created_by && !FH::isSiteAdmin())) {
			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		// check if blog post is currently under pending approval and its a guest viewing.
		if ($view == 'entry' && $this->isPending() && $this->my->guest) {
			// Once logged in, we redirect the user to the entry page
			$uri = 'index.php?option=com_easyblog&view=entry&id=' . $this->id . '.' . $this->revision_id;
			$uri = base64_encode($uri);
			$url = EBR::_('index.php?option=com_easyblog&view=login&return=' . $uri, false);

			return $this->app->redirect($url);
		}

		// Check if the blog post is under pending approval or not.
		if ($this->isPending() && !FH::isSiteAdmin() && (!$this->acl->get('manage_pending') && !$this->acl->get('moderate_entry'))) {
			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		// Check if the blog post is trashed
		if (!$this->isPublished() && $this->my->id != $this->created_by && !FH::isSiteAdmin() && (!$this->acl->get('manage_pending') && !$this->acl->get('moderate_entry'))) {

			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		// Check if the user is allowed to view
		if (!$this->checkTeamPrivacy()) {
			return EB::exception('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY', 'error');
		}

		// Check for blog contribution privacy
		$contribution = $this->getBlogContribution();

		if ($contribution && !$contribution->canView()) {
			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		// Check if the blog post is accessible.
		$accessible = $this->isAccessible();

		if (!$accessible->allowed) {
			return EB::exception($accessible->error, 'error');
		}

		return true;
	}

	/**
	 * Determines if the current user can preview this post or not.
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function checkViewPreview()
	{
		// If the blog post is already deleted, we shouldn't let it to be accessible at all.
		if ($this->isTrashed()) {
			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		$view = $this->input->get('view', '', 'var');
		$layout = $this->input->get('layout', '', 'var');

		if (($view != 'entry' || $layout != 'preview')) {
			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		// There is a situation where, admin want to preview a post from backend and the post is not publish
		// If the admin is not logged in frontend, we show him the login form.
		if ($this->my->guest && !$this->isPublished()) {

			// Once logged in, we redirect the user to the preview page
			$uri = base64_encode($this->getPreviewLink(false));
			$url = EBR::_('index.php?option=com_easyblog&view=login&return=' . $uri, false);

			return $this->app->redirect($url);
		}


		// Check if the blog post is trashed
		if ($this->my->id != $this->created_by && !FH::isSiteAdmin() && (!$this->acl->get('manage_pending') && !$this->acl->get('moderate_entry'))) {
			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		return true;
	}

	/**
	 * Determines if the user is allowed to view this post if this post is associated with a team.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function checkTeamPrivacy()
	{
		$id = $this->getTeamAssociation();

		// This post is not associated with any team, so we do not need to check anything on the privacy
		if (!$id) {
			return true;
		}

		$team = EB::table('TeamBlog');
		$team->load($id);

		// If the team access is restricted to members only
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER && !$team->isMember($this->my->id) && !FH::isSiteAdmin()) {
			return false;
		}

		// If the team access is restricted to registered users, ensure that they are logged in.
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_REGISTERED && $this->my->guest) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the current user can create a new post
	 *
	 * @since	5.3.0
	 * @access	public
	 */
	public function canCreate($sourceId = 0, $sourceType = '')
	{
		if (FH::isSiteAdmin()) {
			return true;
		}

		if (!$this->acl->get('add_entry')) {
			return false;
		}

		// Determines if the source id and source type is provided
		$sourceId = $this->input->get('source_id', $sourceId, 'int');
		$sourceType = $this->input->get('source_type', $sourceType, 'default');

		if ($sourceId && $sourceType) {
			if (EB::contributor()->isEasySocial($sourceType)) {
				$contribution = EB::contributor()->load($sourceId, $sourceType);

				if (!$contribution->canCreatePost()) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Determines if the user can moderate the post
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function canModerate()
	{
		if (FH::isSiteAdmin($this->user->id)) {
			return true;
		}

		if ($this->acl->get('moderate_entry')) {
			return true;
		}

		return false;
	}

	/**
	 * Determine if user can approve the blog post
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function canApprove()
	{
		if (!$this->isPending()) {
			return false;
		}

		if ($this->canModerate()) {
			return true;
		}

		if ($this->acl->get('manage_pending')) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current viewer can delete this post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function canDelete()
	{
		// If the user is a site admin or has moderation access, they should always be able to delete entry
		if (FH::isSiteAdmin() || $this->acl->get('moderate_entry')) {
			return true;
		}

		if ($this->created_by == $this->user->id && $this->acl->get('delete_entry')) {
			return true;
		}

		$model = EB::model('TeamBlogs');

		if ($this->source_type == EASYBLOG_POST_SOURCE_TEAM && $model->isTeamAdmin($this->source_id)) {
			return true;
		}

		// Get the contribution type
		if (!$this->isStandardSource()) {
			$contribution = $this->getBlogContribution();

			if ($contribution->canDelete()) {
				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * Determines if the current viewer can edit this post.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function canEdit()
	{
		if ($this->acl->get('moderate_entry') || FH::isSiteAdmin()) {
			return true;
		}

		// when we copy a blog post, most likely the post is unpublish and the isnew is true.
		// if that is the case, we will allow user to edit.
		if ($this->created_by == $this->user->id && ($this->acl->get('edit_entry') || $this->isDraft() || ($this->isUnpublished() && $this->isNew()) )) {
			return true;
		}

		// If this is a team blog posting, ensure that the user has access to edit this
		if ($this->source_type == EASYBLOG_POST_SOURCE_TEAM) {

			// $model = EB::model('Teamblogs');

			// if ($model->isTeamAdmin($this->source_id)) {
			// 	return true;
			// }

			if ($this->isTeamBlogAdmin()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines if the current viewer is allowed to publish this post.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function canPublish()
	{
		if ($this->acl->get('publish_entry') || FH::isSiteAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Generates a list of class names for composer
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderClassnames($user=null)
	{
		$classes = array();

		if ($this->acl->get('publish_entry')) {
			$classes[] = 'can-publish';
		} else {
			$classes[] = 'can-save';
		}

		// If user can moderate this post
		if ($this->acl->get('moderate_entry')) {
			$classes[] = 'can-moderate';
		}

		// if (!$this->isBlank() && !$this->isDraft() && !$this->isPending()) {
		// 	$draftRevision = $this->getLatestDraftRevision();

		// 	if ($draftRevision) {
		// 		$classes[] = 'warning-draft';
		// 	}
		// }

		// Revision state
		$classes[] = $this->revision->getCssState();

		return implode(' ', $classes);
	}

	/**
	 * Used during post editing so we can render the necessary blocks
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderEditorContent($postTemplateIsLocked = false)
	{
		if ($this->doctype=='ebd') {

			// If this is a empty document,
			// start with a text block.
			if (empty($this->document)) {

				$blocks = EB::blocks();
				$block = $blocks->createBlock("text");
				$content = $blocks->renderEditableBlock($block);

			} else {

				// If this is an existing document,
				// get editable content.
				$document = EB::document($this->document);
				$content = $document->getEditableContent($postTemplateIsLocked);
			}
		} else {

			// Format the post content now
			$content = $this->intro;

			// Append the readmore if necessary
			if (!empty($this->intro) && !empty($this->content)) {
				$content .=  '<hr id="system-readmore" />';
			}

			// Append the rest of the contents
			$content .= $this->content;
		}

		return $content;
	}

	/**
	 * Get the page post title
	 *
	 * @since	5.2.0
	 * @access	public
	 */
	public function getPagePostTitle()
	{
		$postTitle = $this->title;

		// If a custom title is set, we need to set them here
		if (isset($this->custom_title) && !empty($this->custom_title)) {
			$postTitle = $this->custom_title;
		}

		$pageTitle = EB::getPagePostTitle($postTitle);

		return $pageTitle;
	}

	/**
	 * Initializes the header of the html page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function renderHeaders($options = array())
	{
		// Load meta data
		EB::setMeta($this->id, META_TYPE_POST);

		$isPreview = isset($options['isPreview']) ? $options['isPreview'] : false;

		// If there's robots set on the page, initialize it
		$robots = $this->robots ? $this->robots : $this->jconfig->get('robots');
		$this->doc->setMetaData('robots', $robots);

		// If there's a copyright notice, add it into the header
		if ($this->copyrights) {
			$this->doc->setMetaData('rights', $this->copyrights);
		}

		// Determines if the user wants to print this page
		$print = $this->input->get('print', 0, 'int');

		// Add noindex for print view by default.
		if ($print) {
			$this->doc->setMetadata('robots', 'noindex,follow');
		}

		$pageTitle = $this->getPagePostTitle();

		$this->doc->setTitle($pageTitle);

		// we dont process for social network if this is a preview
		if (!$isPreview) {
			// // Add opengraph tags if required.
			EB::facebook()->addOpenGraphTags($this);

			// Add Twitter card details on page.
			EB::twitter()->addCard($this);
		}
	}

	/**
	 * Clears the cache in Joomla for EasyBlog related items
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function clearCache()
	{
		$cache = EB::getCache();
		$cache->clean('com_easyblog');
		$cache->clean('_system');
		$cache->clean('page');
	}

	/**
	 * Sets the revision id for the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function useRevision()
	{
		// Only allow finalized revisions to be used as the current
		if (!$this->isFinalized()) {
			return false;
		}

		// Save the post
		$saveOptions = array('skipCreateRevision' => true,
							'applyDateOffset' => false,
							'normalizeData' => false,
							'updateModifiedTime' => false,
							'validateData' => false
						);

		return $this->save($saveOptions);
	}


	/**
	 * Notify subscribers when a new blog post is published
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function notify($underApproval = false, $published = '1', $featured = false, $approved = false)
	{
		// Load site's language file
		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

		if ($this->blogpassword) {
			return false;
		}

		$author = $this->getAuthor();

		// Export the data for email
		$data = $this->toEmailData();

		// Prepare the post title
		$title = EBString::substr($this->title, 0, $this->config->get('main_mailtitle_length'));
		$subject = JText::sprintf('COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_ADDED_WITH_TITLE', $title) . JText::_('COM_EASYBLOG_ELLIPSES');

		// Prepare ignored emails. We do not want to send notifications to the author
		$ignored = array($author->user->email);

		// Prepare emails
		$emails = array();

		// Load up notification library
		$notification = EB::notification();

		// If configured to notify custom emails, use that instead
		if ($this->config->get('notification_blogadmin')) {
			$notification->getAdminNotificationEmails($emails);
		}

		// If the blog post has been approved, we need to notify the author.
		if ($approved && ($published || $published == EASYBLOG_POST_SCHEDULED)) {

			$authorEmail = array();
			$obj = new stdClass();
			$obj->unsubscribe = false;
			$obj->email = $author->user->email;

			$authorEmail[$author->user->email] = $obj;

			$subject = JText::sprintf('COM_EASYBLOG_NOTIFICATION_NEW_BLOG_APPROVED', $title) . JText::_('COM_EASYBLOG_ELLIPSES');
			$emailTemplate = 'post.approved';

			if ($published == EASYBLOG_POST_SCHEDULED) {
				$subject = JText::sprintf('COM_EASYBLOG_NOTIFICATION_NEW_POST_SCHEDULED', $title) . JText::_('COM_EASYBLOG_ELLIPSES');
				$emailTemplate = 'post.scheduled';
			}

			$notification->send($authorEmail, $subject, $emailTemplate, $data);

			return true;
		}

		// Mailchimp integrations
		if (!$underApproval && $published && $this->config->get('subscription_mailchimp') && $this->config->get('mailchimp_campaign')) {
			EB::mailchimp()->notify($subject, $data, $this);
			return true;
		}

		if ($published) {
			// Send custom emails
			if ($emails) {
				$notification->send($emails, $subject, 'post.new', $data);

				foreach($emails as $el => $obj) {
					$ignored[] = $el;
				}
			}

			// only send to subscribers if the Notify Subscribers is toggled on
			if (!$this->saveOptions['skipNotifications']) {
				$notification->sendSubscribers($subject, 'post.new', $data, $this, $ignored);
			}
		}

		// If the blog post is featured, send notification to the author
		if ($featured) {

			$subject = JText::_('COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_FEATURED');
			$email = new stdClass();
			$email->unsubscribe = false;
			$email->email = $author->user->email;

			$notification->send(array($email), 'COM_EASYBLOG_EMAIL_TITLE_POST_FEATURED', 'post.featured', $data);
		}

		// Logics for email notifications if this post is being submitted for approval
		if ($underApproval) {

			$data['blogPreviewLink'] = $this->getPreviewLink();

			// If this blog post is submitted for approval, send the author an email letting them know that it is being moderated.
			$email = new stdClass();
			$email->unsubscribe = false;
			$email->email = $author->user->email;

			$notification->send(array($author->user->email => $email), 'COM_EASYBLOG_EMAIL_TITLE_POST_REQUIRES_APPROVAL', 'post.moderated', $data);

			// Send a notification to the site admin that a new post is made on the site and requires moderation.
			$emails = array();
			$notification->getAdminNotificationEmails($emails);

			$notification->send($emails, 'COM_EASYBLOG_EMAIL_TITLE_POST_REQUIRES_MODERATION', 'post.moderation', $data);
		}
	}

	/**
	 * Set's the post to be available on the front page.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setFrontpage()
	{
		$this->frontpage = 1;

		// We do not want to run any validation since it's going to be trashed.
		$options = array('validateData' => false, 'normalizeData' => false, 'skipCustomFields' => true);

		return $this->save($options);
	}

	/**
	 * Remove the post from the frontpage.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function removeFrontpage()
	{
		$this->frontpage = 0;

		// We do not want to run any validation since it's going to be trashed.
		$options = array('validateData' => false, 'normalizeData' => false, 'skipCustomFields' => true);

		return $this->save($options);
	}

	/**
	 * Reset post hits
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function resetHits()
	{
		$this->hits = 0;

		$options = array('validateData' => false, 'normalizeData' => false, 'skipCustomFields' => true);

		return $this->save($options);
	}

	/**
	 * Sets this post as a featured post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setFeatured()
	{
		$model = EB::model('Featured');

		$state = $model->makeFeatured(EBLOG_FEATURED_BLOG, $this->id);

		if (!$state) {
			return false;
		}

		// @EasySocial Integrations
		EB::easysocial()->createFeaturedBlogStream($this);

		// @JomSocial Integrations
		EB::jomsocial()->createFeaturedBlogStream($this);

		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_POST_FEATURED', 'post', array(
			'link' => $this->getEditLink(),
			'postTitle' => JText::_($this->title)
		));

		// Notify author of the blog post that their blog post is featured on the site
		$this->notify(false, 0, true);

		return true;
	}

	/**
	 * Removes this post as a featured post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function removeFeatured()
	{
		$actionlog = EB::fd()->getActionLog();
		$actionlog->log('COM_EB_ACTIONLOGS_POST_UNFEATURED', 'post', array(
			'link' => $this->getEditLink(),
			'postTitle' => JText::_($this->title)
		));

		return $this->deleteFeatured();
	}


	/**
	 * reassign blog author only.
	 *
	 */
	public function reassignAuthor($authorId)
	{

		$this->post->created_by = $authorId;
		// Store post
		$state = $this->post->store();

		// If failed to store post, throw exception.
		if (!$state) {
			return false;
		}

		$this->created_by = $authorId;

		// now we need to update author from the revisions.
		$revision = $this->revision;
		$revision->created_by = $authorId;
		$revision->setContent($this->toRevisionData());

		// Store revision
		$state = $revision->store();

		// If failed to store revision, throw exception.
		if (!$state) {
			return false;
		}

		// Assign revision back to instance
		$this->revision = $revision;

		return true;
	}

	public function processPostIntroContent()
	{
		// If the revision is not the current post and it isn't being finalized we shouldn't do anything
		// as the revision should still be on draft state.
		if (!$this->isCurrent() && !$this->isFinalized() && !$this->isBlank()) {
			return;
		}

		if ($this->doctype == 'ebd') {
			// if this is a ebd, then we need to update the content column from easyblog_post table as well.
			$document = EB::document($this->document);
			$this->post->intro = $document->processIntro();
			$this->post->content = $document->processContent();

			if ($this->post->content && !$this->post->intro) {
				$this->post->intro = $this->post->content;
				$this->post->content = '';
			}
		} else {

			$oriIntro = $this->intro;
			$oriContent = $this->content;

			$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

			// Process pdf previewers
			EB::pdf()->format($this, false, $useRelative);

			// Process videos
			EB::videos()->format($this, false, $useRelative);

			// Process audio files
			EB::audio()->format($this, false, $useRelative);

			// Format gallery items
			EB::gallery()->format($this);

			// Remove known codes
			EB::truncater()->stripCodes($this);

			$this->post->intro = $this->intro;
			$this->post->content = $this->content;

			//revert the intro and content as legacy editor see these two column.
			$this->intro = $oriIntro;
			$this->content = $oriContent;
		}

		$this->post->store();

	}

	/**
	 * Retrieve logo for schema
	 *
	 * @since   5.2.7
	 * @access  public
	 */
	public function getSchemaLogo()
	{
		$logoUrl = EB::getLogo('schema');
		$logoPath = EB::string()->abs2rel($logoUrl);

		$logoData = @getimagesize($logoPath);

		if (!$logoData) {
			return false;
		}

		$logo = array('@type' => 'ImageObject', 'url' => $logoUrl, 'width' => $logoData[0], 'height' => $logoData[1]);

		return json_encode($logo);
	}

	/**
	 * reassign blog author only.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getVersion($format = 'short')
	{
		$version = '5.0.44';
		if ($this->post->version) {
			$version = $this->post->version;
		}

		$segments = explode('.', $version);

		return $format == 'short' ? $segments[0] . '.' . $segments[1] : $version;
	}

	public function isEB51()
	{
		$shortVersion = $this->getVersion();
		return version_compare($shortVersion, '5.1', '>=');
	}

	/**
	 * Determine whether its post template is locked or not
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function isPostTemplateLocked()
	{
		$id = $this->getPostTemplateId();

		if (!$id) {
			return false;
		}

		$postTemplate = EB::table('PostTemplate');
		$postTemplate->load($id);

		if (!$postTemplate->id) {
			return false;
		}

		$isLocked = $postTemplate->isLocked();

		return $isLocked;
	}

	/**
	 * Get its post template id
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function getPostTemplateId()
	{
		$params = $this->post->getParams();

		$id = $params->get('post_template_id', false);

		if (!$id) {
			return '';
		}

		return $id;
	}

	/**
	 * Determine the blog post has the type of block being mentioned
	 *
	 * @since	5.4
	 * @access	public
	 */
	public function hasBlockType($type)
	{
		$blocks = $this->getBlocks();

		foreach ($blocks as $block) {
			if ($block->type == $type) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validate whether the current viewer can able to access this blog post under current site language
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public function validateMultilingualPostAccess(EasyBlogPost $post, $returnMsg = true)
	{
		// check for the current blog post language
		$postLang = $post->language;

		// Skip this if the post language is set to all
		if (!$postLang || $postLang == '*') {
			return true;
		}

		$isSiteMultilingualEnabled = FH::isMultiLingual();

		// The reason why need to check this is because this JoomSEF extension have their own language management
		// In order to use their own language management, the site have to turn off language filter plugin
		$isJoomSEFLanguageEnabled = EBR::isJoomSEFLanguageEnabled();

		// Skip this if site language filter plugin is not enabled
		if (!$isSiteMultilingualEnabled && !$isJoomSEFLanguageEnabled) {
			return true;
		}

		// check for the current active menu language
		$activeMenu = $this->app->getMenu()->getActive();
		$activeMenuLang = $activeMenu->language;

		// Determine for the current site language
		$currentSiteLang = JFactory::getLanguage()->getTag();

		if ($postLang == $currentSiteLang || $activeMenuLang == $postLang) {
			return true;
		}

		if ($activeMenuLang == '*' && ($postLang == $currentSiteLang)) {
			return true;
		}

		// Throw an error if the blog posted under different language which not match with the current active menu + site language
		if ($returnMsg) {
			throw EB::exception(JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND'), 404);
		}

		return false;
	}

	/**
	 * Validate if the current menu item is associated with the correct post.
	 *
	 * @since	6.0.7
	 * @access	public
	 */
	public function validatePostMenuItemAccess($menuItemId)
	{
		$config = EB::config();

		// ignore the checking if this feature disabled by site admin. #3089
		if (!$config->get('main_routing_entry_validate', 0)) {
			return true;
		}

		// if menu item is empty, just stop and do nothing.
		if (!$menuItemId) {
			return true;
		}

		// $active = JFactory::getApplication()->getMenu('site')->getActive();
		$menuItem = $this->app->getMenu('site')->getItem($menuItemId);
		if (!$menuItem) {
			return true;
		}

		$query = $menuItem->query;

		// check if this menu item belong to EasyBlog or not.
		if ($query['option'] !== 'com_easyblog') {
			return true;
		}

		// skip the processing if system configured to use certain menu item.
		if ($config->get('main_routing') == 'menuitemid') {
			return true;
		}

		// if this is a sngle post menu item, let it pass
		if (isset($query['view']) && $query['view'] == 'entry' && isset($query['id']) && $query['id']) {
			return true;
		}

		// real work start here:
		// single category
		if ($config->get('main_routing_entry') == 'categories') {

			$postCategories = [];
			$tmpCategories = $this->getCategories();
			foreach ($tmpCategories as $cat) {
				$postCategories[] = $cat->id;
			}

			// the current active menu item is a single category menu item.
			if (isset($query['view']) && isset($query['layout']) && $query['view'] == 'categories' && $query['layout'] == 'listings' &&
				isset($query['id']) && $query['id']) {
				$categoryId = $query['id'];

				if (!in_array($categoryId, $postCategories)) {
					return false;
				}
			} else {
				// since this menu items is not belong to single category, lets check if admin created a single category menu item or not.
				foreach ($postCategories as $post_cat) {
					$tmpMenuItem = EBR::getItemIdByCategories($post_cat);
					if ($tmpMenuItem) {
						// if exists, mean the associated menu item is wrong.
						return false;
					}
				}
			}
		}

		// single author
		if ($config->get('main_routing_entry') == 'blogger') {
			if (isset($query['view']) && isset($query['layout']) && $query['view'] == 'blogger' && $query['layout'] == 'listings' &&
				isset($query['id']) && $query['id']) {
				$authorId = $query['id'];

				if ($this->post->created_by != $authorId) {
					return false;
				}
			} else {
				// since this menu items is not belong to single author, lets check if admin created a single author menu item or not.
				$tmpMenuItem = EBR::getItemIdByBlogger($this->post->created_by);
				if ($tmpMenuItem) {
					// if exists, mean the associated menu item is wrong.
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Normalize the autopost content
	 *
	 * @since	5.4.7
	 * @access	public
	 */
	public function normalizeAutopostContent(EasyBlogPost $post, $charLimit = 350, $contentSource = 'intro', $oauthClient = '')
	{
		if ($contentSource === 'intro') {
			$content = $post->getIntro(false, false);
		} else {
			$content = $post->getContentWithoutIntro(EASYBLOG_VIEW_ENTRY, false);
		}

		$brArr = array('<br>', '<br/>', '<br />');

		// convert those <p> or <br> tag to new line
		$content = str_ireplace("<p>", "\n\n", $content);
		$content = str_ireplace($brArr, "\n", $content);
		$content = str_ireplace('&nbsp;', ' ', $content);

		// Remove adsense and video codes
		$content = EB::adsense()->strip($content);
		$content = EB::videos()->strip($content);

		// here only stripe the html tag
		$content = strip_tags($content);

		// total length from the content
		$totalContentLength = EBString::strlen($content);

		// Satisfy linkedin's criteria
		if ($oauthClient == 'linkedin') {
			$content = trim(htmlspecialchars(stripslashes($content)));
		}

		// determine whether need to add ellipses 3 dots
		if ($charLimit && ($totalContentLength > $charLimit)) {
			$content = EBString::substr($content, 0, $charLimit) . JText::_('COM_EASYBLOG_ELLIPSES');
		}

		$content = trim($content);

		return $content;
	}

	/**
	 * Retrieve the default cover by its primary category
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getCategoryDefaultPostCover($root = true)
	{
		$category = $this->getPrimaryCategory();

		if (!$category || !$category->id) {
			$defaultId = EB::getDefaultCategoryId();

			$category = EB::table('Category');
			$category->load($defaultId);
		}

		return $category->getDefaultPostCover($root);
	}

	/**
	 * Delete the Lottie files that have been uploaded for this blog post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function deleteLottieFiles()
	{
		$path = JPATH_ROOT . '/images/easyblog_lottie/' . $this->id;

		if (JFolder::exists($path)) {
			// Delete the folder
			JFolder::delete($path);
		}
	}

	/**
	 * Retrieve the reviewer's name of this blog post
	 *
	 * @since	6.0.10
	 * @access	public
	 */
	public function getReviewerName()
	{
		$params = $this->getParams();
		$name = trim($params->get('reviewer_name', ''));

		return $name;
	}

	/**
	 * Retrieve the reviewer's profile link of this blog post
	 *
	 * @since	6.0.10
	 * @access	public
	 */
	public function getReviewerLink()
	{
		$params = $this->getParams();
		$reviewerLink = trim($params->get('reviewer_link', ''));

		if (strpos($reviewerLink, 'http://') !== 0 && strpos($reviewerLink, 'https://') !== 0) {
			$reviewerLink = false;
		}

		return $reviewerLink;
	}

	/**
	 * Retrieve the fact checker's name of this blog post
	 *
	 * @since	6.0.10
	 * @access	public
	 */
	public function getFactCheckerName()
	{
		$params = $this->getParams();
		$name = trim($params->get('fact_checker_name', ''));

		return $name;
	}

	/**
	 * Retrieve the fact checker's profile link of this blog post
	 *
	 * @since	6.0.10
	 * @access	public
	 */
	public function getFactCheckerLink()
	{
		$params = $this->getParams();
		$link = trim($params->get('fact_checker_link', ''));

		if (strpos($link, 'http://') !== 0 && strpos($link, 'https://') !== 0) {
			$link = false;
		}

		return $link;
	}

	/**
	 * Clear the unnecessary lottie files that are no longer associated with this blog post
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function clearUnnecessaryLottie()
	{
		if (!$this->isEbd() || !$this->lottie_files) {
			return;
		}

		$folder = JPATH_ROOT . '/images/easyblog_lottie/' . $this->id;

		if (!JFolder::exists($folder)) {
			return;
		}

		// The lottie files that have being used on this blog post
		// The keys are the name of the files
		$lottieFiles = array_keys(EB::makeArray($this->lottie_files));

		$files = glob($folder . '/*', GLOB_NOSORT);
		$exclusion = [];

		foreach ($lottieFiles as $lottieFile) {
			foreach ($files as $file) {
				if (basename($file) == $lottieFile) {
					$exclusion[] = $file;
				}
			}
		}

		foreach ($files as $file) {
			if ($exclusion && in_array($file, $exclusion)) {
				continue;
			}

			JFile::delete($file);
		}
	}
}
