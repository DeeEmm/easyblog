<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once( EBLOG_ADMIN_INCLUDES . '/post/post.php');

class EasyBlogCache extends EasyBlog
{
	public $posts = null;
	public $categories = null;
	public $cats = null;
	public $teamblogs = null;
	public $bloggers = null;

	// Local scope
	private $post = array();
	private $category = array();
	private $meta = array();
	private $tag = array();
	private $author = array();
	private $revision = array();
	private $team = array();
	private $field = array();
	private $fieldgroup = array();
	private $categoryfieldgroup = array();
	private $fieldvalue = array();

	private $types = array('post', 'category', 'meta', 'tag', 'author', 'revision', 'team','fieldgroup','field','categoryfieldgroup','fieldvalue');


	/**
	 * Initializes the cache for all categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	array
	 * @return  boolean
	 */
	public function insertBloggers($items, $options = array())
	{
		$model = EB::model('Blogger');

		$bloggerIds = array();

		$cachePosts = isset($options['cachePosts']) ? $options['cachePosts'] : true;
		$cacheCategories = isset($options['cacheCategories']) ? $options['cacheCategories'] : true;
		$cacheCategoriesCount = isset($options['cacheCategoriesCount']) ? $options['cacheCategoriesCount'] : true;
		$cacheTags = isset($options['cacheTags']) ? $options['cacheTags'] : true;
		$cacheTagsCount = isset($options['cacheTagsCount']) ? $options['cacheTagsCount'] : true;


		foreach($items as $item) {
			$bloggerIds[] = $item->id;

			//initialize bloggers array
			$this->bloggers[$item->id] = array();
		}

		if ($cachePosts) {
			$limitPost = EB::getViewLimit('author_posts_limit', 'bloggers');

			$limitPost = $limitPost == 0 ? 5 : $limitPost;

			$posts = $model->preloadPosts($bloggerIds, $limitPost);
			if ($posts) {
				foreach($posts as $uid => $items) {
					$this->bloggers[$uid]['post'] = $items;
				}
			}
		}


		if ($cacheTags) {
			$tags = $model->preloadTagUsed($bloggerIds);
			if ($tags) {
				foreach($tags as $uid => $items) {
					$this->bloggers[$uid]['tag'] = $items;
				}
			}
		}

		// cache tags used count
		if ($cacheTagsCount) {
			$tagsCnt = $model->preloadTagUsed($bloggerIds, true);

			if ($tagsCnt) {
				foreach($tagsCnt as $uid => $cnt) {
					$this->bloggers[$uid]['tagCount'] = $cnt;
				}
			}
		}

		if ($cacheCategories) {

			// initiate the container.
			foreach ($bloggerIds as $uid) {
				$this->bloggers[$uid]['category'] = array();
			}

			$categories = $model->preloadCategoryUsed($bloggerIds);
			if ($categories) {
				foreach($categories as $uid => $items) {
					$this->bloggers[$uid]['category'] = $items;
				}
			}
		}

		// cache tags used count
		if ($cacheCategoriesCount) {

			// initiate the container.
			foreach ($bloggerIds as $uid) {
				$this->bloggers[$uid]['categoryCount'] = 0;
			}

			$catsCnt = $model->preloadCategoryUsed($bloggerIds, true);
			if ($catsCnt) {
				foreach($catsCnt as $uid => $cnt) {
					$this->bloggers[$uid]['categoryCount'] = $cnt;
				}
			}
		}

		// TODO: blogger subscription.
		$subs = $model->preloadBlogggerSubscribers($bloggerIds);
		if ($subs) {
			foreach($subs as $uid => $subid) {
				$this->bloggers[$uid]['subs'] = $subid;
			}
		}

	}

	/**
	 * Initializes the cache for all categories
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function insertCategories($items, $options = array())
	{
		$model = EB::model('Categories');

		$catIds = array();
		$postCatIds = array();

		$cachePosts = isset($options['cachePosts']) ? $options['cachePosts'] : true;
		$cacheAuthors = isset($options['cacheAuthors']) ? $options['cacheAuthors'] : true;
		$cacheAuthorsCount = isset($options['cacheAuthorsCount']) ? $options['cacheAuthorsCount'] : true;

		foreach($items as $item) {
			$catIds[] = $item->id;

			// pre-init array.
			$this->cats[$item->id] = array();
			$postCatIds[$item->id][] = $item->id;

			//cache category
			$tbl = EB::table('Category');
			$tbl->bind($item);

			$this->set($tbl, 'category');
		}

		// cache user category subscription.
		$subs = $model->preloadUserSubscription($catIds, $this->my->email);
		if ($subs) {
			foreach($subs as $uid => $val) {
				$this->cats[$uid]['subs'] = '1';
			}
		}

		// cache authors
		if ($cacheAuthors) {
			$authors = $model->preloadActiveAuthors($catIds);

			if ($authors) {
				foreach($authors as $uid => $author) {
					$this->cats[$uid]['author'] = $author;
				}
			}
		}

		// cache authors
		if ($cacheAuthorsCount) {
			$authorsCnt = $model->preloadActiveAuthorsCount($catIds);

			if ($authorsCnt) {
				foreach($authorsCnt as $uid => $cnt) {
					$this->cats[$uid]['authorCount'] = $cnt;
				}
			}
		}


		// cache category's childs?
		$childs = $model->preloadCategoryChilds($catIds);

		if ($childs) {
			foreach($childs as $uid => $child) {
				$this->cats[$uid]['child'] = $child;

				//break it down so that we can reference the child id later.
				foreach($child as $item) {
					$postCatIds[$uid][] = $item->id;
				}
			}
		}

		// cache category and its childs posts
		if ($cachePosts) {

			$limitPost = EB::getViewLimit('categories_post_limit', 'categories');
			$limitPost = $limitPost == 0 ? 5 : $limitPost;

			$posts = $model->preloadPosts($postCatIds, $limitPost);
			if ($posts) {
				foreach($posts as $uid => $post) {
					$this->cats[$uid]['post'] = $post;
				}
			}
		}

	}



	/**
	 * Initializes the cache for all teamblog items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	array
	 * @return  boolean
	 */
	public function insertTeams($items)
	{
		$model = EB::model('TeamBlogs');

		$teamIds = array();

		// var_dump($items);exit;

		foreach($items as $team) {
			$teamIds[] = $team->id;

			// we need to pre-init the values here
			$this->teamblogs[$team->id] = array();

			//cache team
			$tbl = EB::table('TeamBlog');
			$tbl->bind($team);

			$this->set($tbl, 'team');
		}


		// preload members
		$members = $model->preloadTeamMembers($teamIds);
		if ($members) {
			foreach($members as $member) {

				$tbl = EB::table('TeamBlogUsers');
				$tbl->bind($member);

				$this->teamblogs[$member->team_id]['member'][$member->user_id] = $tbl;
			}
		}

		// preload member counts
		$memberCounts = $model->preloadTotalMemberCount($teamIds);
		if ($memberCounts) {
			foreach($memberCounts as $tid => $mc) {
				$this->teamblogs[$tid]['count'] = $mc;
			}
		}

		// preload total post count.
		$postCounts = $model->preloadPostCount($teamIds);
		if ($postCounts) {
			foreach($postCounts as $tid => $pc) {
				$this->teamblogs[$tid]['postcount'] = $pc;
			}
		}

		// preload team posts
		$posts = $model->preloadPosts($teamIds, EASYBLOG_TEAMBLOG_LISTING_NO_POST);
		if ($posts) {
			foreach($posts as $tid => $items) {
				$this->teamblogs[$tid]['post'] = $items;
			}
		}

	}


	/**
	 * Initializes the cache for all post items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	array
	 * @return  boolean
	 */
	public function insert($items, $options = array())
	{
		$revIds = array();
		$postIds = array();
		$authorIds = array();
		$teamIds = array();

		// if caller pass in an empty array, we stop the caching
		if (!$items) {
			return;
		}

		// Cache posts
		$this->cachePosts($items);

		// We need to get some of the unique id's to be cached
		foreach ($items as $item) {

			$postIds[] = $item->id;
			$authorIds[] = $item->created_by;

			if ($item->revision_id) {
				$revIds[] = $item->revision_id;
			}

			if ($item->source_type == EASYBLOG_POST_SOURCE_TEAM) {
				$teamIds[] = $item->source_id;
			}
		}

		$cacheComment = isset($options['cacheComment']) ? $options['cacheComment'] : true;
		$cacheCommentCount = isset($options['cacheCommentCount']) ? $options['cacheCommentCount'] : true;
		$cacheRatings = isset($options['cacheRatings']) ? $options['cacheRatings'] : true;
		$cacheVoted = isset($options['cacheVoted']) ? $options['cacheVoted'] : true;
		$cacheTags = isset($options['cacheTags']) ? $options['cacheTags'] : true;
		$cacheRevisions = isset($options['cacheRevisions']) ? $options['cacheRevisions'] : true;
		$cacheAuthors = isset($options['cacheAuthors']) ? $options['cacheAuthors'] : true;
		$cacheFields = isset($options['cacheFields']) ? $options['cacheFields'] : true;
		$cacheMetas = isset($options['cacheMetas']) ? $options['cacheMetas'] : true;
		$cacheCategories = isset($options['cacheCategories']) ? $options['cacheCategories'] : true;

		// Cache voted items
		if ($cacheVoted) {
			$this->cacheVoted($postIds);
		}

		// Cache ratings
		if ($cacheRatings) {
			$this->cacheRatings($postIds);
		}

		// Cache comments
		if ($cacheComment) {
			$this->cacheComments($postIds);
		}

		// Cache comment counters
		if ($cacheCommentCount) {
			$this->cacheCommentCount($postIds);
		}

		// Cache categories
		if ($cacheCategories) {
			$this->cacheCategories($postIds);
		}

		// Cache tags
		if ($cacheTags) {
			$this->cacheTags($postIds);
		}

		// Cache revision items
		if ($cacheRevisions) {
			$this->cacheRevisions($revIds);
		}

		// Cache author items
		if ($cacheAuthors) {
			$this->cacheAuthors($authorIds);
		}

		// Cache custom fields for posts
		if ($cacheFields) {
			$this->cacheFields($postIds);
		}

		// Cache metas for posts used in facebook opengraph
		if ($cacheMetas) {
			$this->cacheMetas($postIds);
		}

	}

	/**
	 * Cached posts meta
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheMetas($postIds = array())
	{
		$model = EB::model('Metas');
		$metas = $model->preloadPostMetas($postIds);

		foreach ($postIds as $id) {
			$meta = EB::table('Meta');

			if (isset($metas[$id])) {
				$meta->bind($metas[$id]);
			}

			EasyBlogPost::$postMetas[$id] = $meta;
		}
	}

	/**
	 * Cached voted items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheVoted($postIds = array())
	{
		$userId = $this->my->id;

		$hash   = '';
		$ipaddr = '';
		if (empty($userId)) {
			//mean this is a guest.
			$hash = JFactory::getSession()->getId();
			$ipaddr = @$_SERVER['REMOTE_ADDR'];
		}

		$model = EB::model('Ratings');
		$votes = $model->preloadUserPostRatings($postIds, $userId, $hash, $ipaddr);

		foreach ($postIds as $id) {
			if (isset($votes[$id])) {
				EasyBlogPost::$postVotes[$id] = $votes[$id];
			} else {
				EasyBlogPost::$postVotes[$id] = 0;
			}
		}

	}

	/**
	 * Cache teams for posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheTeams($teamIds = array())
	{
		// dump($teamIds);
	}

	/**
	 * Cache custom fields for posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheFields($postIds = array())
	{

		$model = EB::model('Fields');
		$fields = $model->preloadFields($postIds);

		if ($fields) {

			//let pre-define the 'container' for post custom fields;
			foreach($postIds as $pid) {
				$this->posts[$pid]['customfields'] = array();
			}

			$categories = array();

			foreach($fields as $row) {

				//field groups
				$fg = new stdClass();
				$fg->id = $row->fg_id;
				$fg->title = $row->fg_title;
				$fg->description = $row->fg_description;
				$fg->created = $row->fg_created;
				$fg->state = $row->fg_state;
				$fg->read = $row->fg_read;
				$fg->write = $row->fg_write;
				$fg->params = $row->fg_params;

				$fgTbl = EB::table('FieldGroup');
				$fgTbl->bind($fg);

				// $this->set($fgTbl, 'fieldgroup');

				// fields
				$f = new stdClass();
				$f->id = $row->f_id;
				$f->group_id = $row->f_group_id;
				$f->title = $row->f_title;
				$f->help = $row->f_help;
				$f->state = $row->f_state;
				$f->required = $row->f_required;
				$f->type = $row->f_type;
				$f->params = $row->f_params;
				$f->created = $row->f_created;
				$f->options = $row->f_options;
				$f->ordering = $row->f_ordering;

				$fTbl = EB::table('Field');
				$fTbl->bind($f);

				$this->set($fTbl, 'field');

				// category field groups
				$cfg = new stdClass();
				$cfg->id = $row->cat_fg_id;
				$cfg->category_id = $row->cat_fg_category_id;
				$cfg->group_id = $row->cat_fg_group_id;

				$cfgTbl = EB::table('CategoryFieldGroup');
				$cfgTbl->bind($cfg);

				// $this->set($cfgTbl, 'categoryfieldgroup');

				$this->categories[$cfg->category_id]['group'] = $fgTbl;
				$this->categories[$cfg->category_id]['field'][$fTbl->id] = $fTbl;

				// field value
				if ($row->fv_id) {
					$fv = new stdClass();
					$fv->id = $row->fv_id;
					$fv->field_id = $row->fv_field_id;
					$fv->post_id = $row->fv_post_id;
					$fv->value = $row->fv_value;

					$fvTbl = EB::table('FieldValue');
					$fvTbl->bind($fv);

					// $this->set($fvTbl, 'fieldvalue');

					$this->posts[$row->fv_post_id]['customfields'][$fv->field_id][$fv->id] = $fvTbl;

				}

			}
		}
	}

	/**
	 * Caches votes created for each posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheRatings($postIds = array())
	{
		$model = EB::model('Ratings');
		$ratings = $model->preloadRatings($postIds);

		foreach ($postIds as $id) {

			if (!isset($ratings[$id])) {
				$obj = new stdClass();
				$obj->ratings = 0;
				$obj->total = 0;
			} else {
				$obj = $ratings[$id];
			}

			EasyBlogPost::$ratings[$id] = $obj;
		}
	}

	/**
	 * Caches the comments
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheComments($postIds = array())
	{
		// we only cache the comment if built-in comment enabled.
		if (!EB::comment()->isBuiltin()) {
			return;
		}

		$model = EB::model('Comment');
		$comments = $model->preloadComments($postIds);

		foreach ($postIds as $id) {
			// We can only cache this if the comment is built in
			$result = isset($comments[$id]) ? $comments[$id] : array();
			EasyBlogPost::$comments[$id] = $result;
		}
	}

	/**
	 * Caches the comment counters
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheCommentCount($postIds = array())
	{
		// Only get count if comments are built int.
		if (!EB::comment()->isBuiltin()) {
			return;
		}


		$model = EB::model('Comment');
		$counters = $model->preloadCommentCount($postIds);

		// Determines if system is running on built in comments
		$builtIn = EB::comment()->isBuiltin();

		foreach ($postIds as $id) {
			$total = isset($counters[$id]) ? $counters[$id] : 0;

			// We can only retrieve the comment counter if it is not built in.
			if (!$builtIn) {
				$post = $this->get($id, 'post');
				$tmppost = EB::post();
				$tmppost->bind($post, array('force' => true));

				$total = EB::comment()->getCommentCount($tmppost);
			}

			EasyBlogPost::$commentCounts[$id] = $total;
		}
	}

	/**
	 * Caches the post objects
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function cachePosts($items = array())
	{
		foreach ($items as $item) {

			$data = $item;

			if ($item instanceof EasyBlogPost) {
				$data = $item->toData();

				// reset the intro and content so that it will cache the post->intro and post->content.
				if (!is_null($item->post)) {
					$data->intro = $item->post->intro;
					$data->content = $item->post->content;
				}
			}

			$post = EB::table('Post');
			$post->bind($data);

			if (isset($data->featured)) {
				$post->featured = $data->featured;
			}

			$this->set($post, 'post');
		}
	}

	/**
	 * Caches revision items
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function cacheRevisions($revisionIds = array())
	{
		$model = EB::model('Revisions');
		$revisions = $model->preload($revisionIds);

		if ($revisions) {
			foreach ($revisions as $revision) {
				$table = EB::table('Revision');
				$table->bind($revision);

				// Assign the revision
				$this->set($table, 'revision');
			}
		}
	}

	/**
	 * Caches all category items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheCategories($postIds = array())
	{
		$model = EB::model('Category');
		$categories = $model->preloadByPosts($postIds);

		if ($categories) {

			foreach($categories as $category) {

				// Predefine default values
				if (!isset($this->posts[$category->post_id]['primarycategory'])) {
					$this->posts[$category->post_id]['primarycategory'] = null;
				}

				if (!isset($this->posts[$category->post_id]['category'])) {
					$this->posts[$category->post_id]['category'] = array();
				}

				$table = EB::table('Category');
				$table->bind($category);

				// This determines if the category is a primary category
				$table->primary = $category->primary;

				// Set into posts categories
				$this->posts[$category->post_id]['category'][] = $table;

				if ($category->primary) {
					$this->posts[$category->post_id]['primarycategory'] = $table;
				}

				// Save the category into the cache
				$this->set($table, 'category');

				// save into categories
				// used in custom fields later.
				$this->categories[$table->id] = array();
			}
		}
	}

	/**
	 * Caches all author items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheAuthors($authorIds = array())
	{
		// Unique the author id's
		$authorIds = array_unique($authorIds);

		// $model = EB::model('Blogger');
		// $authors = $model->preload($authorIds);

		// if ($authors) {

		// 	foreach ($authors as $author) {
		// 		$this->set($author, 'author');
		// 	}

		// }

		//preload users.
		EB::user($authorIds);

		foreach ($authorIds as $userId) {
			$author = EB::user($userId);
			$this->set($author, 'author');
		}

	}
	/**
	 * Caches all tag items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cacheTags($postIds)
	{
		$model = EB::model('Tags');
		$tags = $model->preloadByPosts($postIds);

		if ($tags) {

			foreach ($tags as $tag) {
				$table = EB::table('Tag');
				$table->bind($tag);

				// Ensure that the post id contains a tag array
				if (!isset($this->posts[$tag->post_id]['tag'])) {
					$this->posts[$tag->post_id]['tag'] = array();
				}

				// Assign tags into the post index
				$this->posts[$tag->post_id]['tag'][] = $table;

				// Assign this into the tag cache
				$this->set($table, 'tag');
			}
		}
	}

	/**
	 * Adds a cache for a specific item type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	object, string
	 * @type 	'post', 'category', 'meta', 'tag', 'author', 'revision', 'team'
	 * @return  boolean
	 */
	public function set($item, $type = 'post', $uid = null, $utype = null)
	{
		// Check if this item already exists.
		if ($uid && $utype) {
			$this->{$type}[$uid][$utype] = $item;
		} else {
			$this->{$type}[$item->id] = $item;
		}
	}

	/**
	 * set cache for the object type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string, string
	 * @type 	'post', 'category', 'meta', 'tag', 'author', 'revision'
	 * @return  object
	 */
	public function get($id, $type = 'post')
	{
		if (isset($this->$type) && isset($this->{$type}[$id])) {
			return $this->{$type}[$id];
		}

		// There should be a fallback method if the cache doesn't exist
		return $this->fallback($id, $type);
	}

	/**
	 * Retrieves a fallback
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function fallback($id, $type)
	{
		if ($type == 'team') {
			$table = EB::table('Teamblog');
			$table->load($id);
		}

		if ($type == 'author') {
			$table = EB::user($id);
		}

		if ($type != 'team' && $type != 'author') {

			$table = EB::table($type);
			$table->load($id);
		}

		// Post is a little different
		// if ($type == 'post') {
		// 	$post = EB::post();
		// 	$post->bind($table, array('force' => true));

		// 	$this->set($post, $type);

		// 	return $post;
		// }

		$this->set($table, $type);

		return $table;
	}

	/**
	 * check if the cache for the object type exists or not
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string, string
	 * @type 	'post', 'category', 'meta', 'tag', 'author', 'revision'
	 * @return  boolean
	 */
	public function exists($id, $type = 'post')
	{
		if (isset($this->$type) && isset($this->{$type}[$id])) {
			return true;
		}

		return false;
	}

}
