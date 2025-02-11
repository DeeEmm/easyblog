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

class EasyBlogXMLRPCServices extends EasyBlog
{
	/**
	 * Logs in the user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function login($username, $password)
	{
		// Get the global JAuthentication object
		jimport( 'joomla.user.authentication');
		$auth = JAuthentication::getInstance();

		$credentials = array('username' => $username, 'password' => $password);
		$options = array();

		$app = JFactory::getApplication();
		$response = $app->login($credentials);

		// Try to authenticate the user with Joomla
		if ($response === true) {
			$my = JFactory::getUser();

			if ($my->guest || $my->block) {
				return new xmlrpcresp(0, 403, JText::_('Login Failed'));
			}

			return true;
		}

		return new xmlrpcresp(0, 403, JText::_('Login Failed'));
	}

	/**
	 * Retrieves a list of blog posts
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getRecentPosts($blogid, $username, $password, $numposts)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		// Assuming that the user already logged in
		$my = JFactory::getUser();

		// Get the model
		$model = EB::model('XmlRpc');
		$result = $model->getRecentPosts($my->id);

		// If there's no entries, just throw a response
		if (!$result) {
			return new xmlrpcresp(new xmlrpcval(array(), $xmlrpcArray));
		}

		$data = array();

		foreach ($result as $post) {
			$item = self::prepPost($post->id);
			$data[] = new xmlrpcval($item, $xmlrpcStruct);
		}

		$response = new xmlrpcresp(new xmlrpcval($data, $xmlrpcArray));

		return $response;
	}

	/**
	 * Creates a new category on the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function newCategory($blogId, $username, $password, $data)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		$category = EB::table('Category');

		if (isset($data['name'])) {
			$category->title = $data['name'];
		}

		if (isset($data['slug'])) {
			$category->alias = EBR::normalizePermalink($data['slug']);
		}

		if (isset($data['parent_id'])) {
			$category->parent_id = $data['parent_id'];
		}

		// Set the state to published
		$category->published = true;
		$category->created_by = JFactory::getUser()->id;

		// Save the category.
		$state = $category->store();

		if (!$state) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, $category->getError());
		}


		return new xmlrpcresp(new xmlrpcval($category->id, $xmlrpcInt));
	}

	/**
	 * Retrieves a list of blog posts the user has created
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getUserBlogs($appkey, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		self::login($username, $password);

		$my = JFactory::getUser($username);
		$uri = JURI::getInstance();

		$admin = FH::isSiteAdmin($my);
		$domain	= $uri->toString( array('scheme', 'host', 'port'));
		$xmlrpcLink	= $domain . '/index.php?option=com_easyblog&controller=xmlrpc';

		$structArray = array();
		$structArray[] = new xmlrpcval(array(
			'isAdmin'	=> new xmlrpcval($admin, $xmlrpcBoolean),
			'url'		=> new xmlrpcval(JURI::root(), $xmlrpcString),
			'blogid'	=> new xmlrpcval($my->id, $xmlrpcString),
			'blogName'	=> new xmlrpcval($my->name . '\'s blog entries', $xmlrpcString),
			'xmlrpc'	=> new xmlrpcval($xmlrpcLink, $xmlrpcString)
			), 'struct');

		return new xmlrpcresp(new xmlrpcval( $structArray , $xmlrpcArray));
	}

	/**
	 * Associates a post with the categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function setPostCategories($postId, $username, $password, $categories)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		self::login($username, $password);

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		$post = EB::post($postId);

		// Bind the categories to the post
		$post->categories = array();

		$catCount = count($categories);

		foreach ($categories as $category) {

			if ((isset($category['isPrimary']) && $category['isPrimary']) || $catCount == 1) {
				$post->category_id = $category['categoryId'];
			} else {
				$post->categories[] = $category['categoryId'];
			}
		}

		$saveOptions = array(
						'validateData' => false,
						'useAuthorAsRevisionOwner' => true
						);

		// Save the post again
		$post->save($saveOptions);

		// Nothing to process. Just return true for now.
		return new xmlrpcresp(new xmlrpcval(true, $xmlrpcBoolean));
	}

	/**
	 * Binds the post data
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function bindPost(EasyBlogPost &$post, $data, $publish)
	{
		$config = EB::config();
		$acl = EB::acl();
		$my = JFactory::getUser();

		if ($my->guest || $my->block) {
			return new xmlrpcresp(0, 403, JText::_('Login Failed'));
		}

		$postData = array();

		// Default properties
		$postData['doctype'] = EASYBLOG_POST_DOCTYPE_LEGACY;
		$postData['title'] = '';
		$postData['intro'] = '';
		$postData['content'] = '';
		$postData['tags'] = array();
		$postData['allowcomment'] = true;
		$postData['published'] = EASYBLOG_POST_PUBLISHED;
		$postData['created'] = EB::date()->toSql();
		$postData['publish_up'] = $postData['created'];
		$postData['created_by'] = $my->id;
		$postData['access'] = false;
		$postData['posttype'] = '';
		$postData['language'] = '*';
		$postData['access'] = 0;

		// Should these be read from configuration?
		$postData['frontpage'] = true;
		$postData['send_notification_emails'] = true;

		// Post title
		// title
		if (isset($data['title']) && !empty($data['title'])) {
			$postData['title'] = $data['title'];
		}

		// Post category
		if (isset($data['categories']) && !empty($data['categories'])) {

			$catTitle = implode('', $data['categories']);

			// Get the categories
			$category = EB::model('Categories');
			$categories = $category->getCategoryTree();

			foreach ($categories as $category) {

				if ($category->title == $catTitle) {
					$postData['category_id'] = $category->id;
				}
			}
		}

		// Tags from marsedit
		// mt_tags
		if (isset($data['mt_tags']) && !empty($data['mt_tags'])) {

			if (is_array($data['mt_tags'])) {
				$data['mt_tags'] = implode(',', $data['mt_tags']);
			}

			$postData['tags'] = $data['mt_tags'];
		}

		// Keywords could possibly be used as tags
		// mt_keywords
		if (!$postData['tags'] && isset($data['mt_keywords']) && $data['mt_keywords']) {
			$postData['tags'] = $data['mt_keywords'];
		}

		// Post status from mars edit
		// post_status
		if (isset($data['post_status'])) {

			$status = $data['post_status'];

			if ($status == 'publish' || $status == 'private') {
				$postData['published'] = EASYBLOG_POST_PUBLISHED;
			}

			if ($status == 'private' || $status == 'pending') {
				$postData['private'] = true;
			}

			if ($status == 'draft') {
				$postData['published'] = EASYBLOG_POST_UNPUBLISHED;
			}

			if ($status == 'schedule') {
				$postData['published'] = EASYBLOG_POST_SCHEDULED;
			}
		}

		// Determines if the post allow comments
		// mt_allow_comments
		if (isset($data['mt_allow_comments'])) {

			if (is_numeric($data['mt_allow_comments'])) {
				$postData['allowcomment'] = $data['mt_allow_comments'] == 1;
			} else {
				$postData['allowcomment'] = $data['mt_allow_comments'] == 'open';
			}

		}

		// Post content
		// description
		if (isset($data['description']) && !empty($data['description'])) {
			$postData['intro'] = $data['description'];
		}

		// If intro text is still empty, try to check for mt_excerpt
		if (!$postData['intro'] && isset($data['mt_excerpt']) && !empty($data['mt_excerpt'])) {
			$postData['intro'] = $data['mt_excerpt'];
		}

		// Main content
		// mt_text_more
		if (isset($data['mt_text_more']) && !empty($data['mt_text_more'])) {
			$postData['content'] = $data['mt_text_more'];
		}

		if (!$postData['content'] && isset($data['more_text']) && $data['more_text']) {
			$postData['content'] = $data['more_text'];
		}

		// Wordpress API? Doesn't seem like we need this
		// mt_convert_breaks

		// Wordpress API? Permalink?
		// wp_slug
		if (isset($data['wp_slug']) && !empty($data['wp_slug'])) {
			$postData['permalink'] = $data['wp_slug'];
		}


		// Get the timestamp from the post
		if (isset($data['date_created_gmt']) && $data['date_created_gmt']) {
			$date = EB::date($data['date_created_gmt']);
			$postData['created'] = $date->toSql();
		}

		// If user wants to set a custom date for the creation date
		if (isset($data['dateCreated']) && $data['dateCreated']) {

			$now = EB::date();
			$tsNow = $now->toUnix();

			$date = EB::date($data['dateCreated']);

			// We need to add additional 10 seconds becuse blogsy is always 5s faster
			$tsDate = $date->toUnix() + 10;

			if ($tsDate > $tsNow) {
				$postData['published'] = EASYBLOG_POST_SCHEDULED;
				$postData['created'] = $now->toSql();
				$postData['publish_up'] = $date->toSql();
			} else {
				$postData['created'] = $date->toSql();
			}
		}

		// Bind the post data now
		$post->bind($postData);
	}

	/**
	 * Binds images
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function bindBlogImages(EasyBlogPost &$blog, $data, $publish)
	{
		// Get the config
		$config = EB::config();

		$my = JFactory::getUser();

		if ($my->guest || $my->block) {
			return new xmlrpcresp(0, 403, JText::_('Login Failed'));
		}

		// Group up the content
		$content = $blog->intro . $blog->content;
		$total = 0;

		// Match images wrapped in hyperlinks
		$pattern = '#<a.*?\><img[^>]*><\/a>#i';
		preg_match_all($pattern, $content, $matches);

		if ($matches && count($matches[0]) > 0) {

			foreach ($matches[0] as $match) {
				$input = $match;
				$largeImgPath = '';

				//getting large image path
				$pattern = '#<a[^>]*>#i';
				preg_match($pattern, $input, $anchors);

				if ($anchors) {
					preg_match('/href\s*=\s*[\""\']?([^\""\'\s>]*)/i', $anchors[0], $adata);

					if ($adata) {
						$largeImgPath = $adata[1];
					}
				}

				$input = $match;
				$pattern = '#<img[^>]*>#i';
				preg_match($pattern, $input, $images);

				if ($images) {
					preg_match('/src\s*=\s*[\""\']?([^\""\'\s>]*)/i', $images[0], $data);

					if ($data) {
						// Process the first image as the blog image
						if ($total == 0) {

							// Reconstruct the storage path and remove it from the image url
							$my = JFactory::getUser();
							$blogimage = new stdClass();
							$blogimage->place = 'user:' . $my->id;
							$blogimage->path = '/' . basename($largeImgPath);

							$blog->image = json_encode($blogimage);

							// Remove the image codes from the content
							$blog->intro = str_ireplace($images[0], '', $blog->intro);
							$blog->content = str_ireplace($images[0], '', $blog->content);
						} else {
							$largeImgPath = (empty($largeImgPath)) ? $data[1] : $largeImgPath;
							$largeImgPath = urldecode($largeImgPath);
							$largeImgPath = str_replace(' ', '-', $largeImgPath);

							$encodedurl = urldecode($data[1]);
							$encodedurl = str_replace(' ', '-', $encodedurl);
							$images[0] = str_replace($data[1], $encodedurl, $images[0]);

							$blog->intro = str_replace($input , '<a class="easyblog-thumb-preview" href="' . $largeImgPath . '">' . $images[0] . '</a>' , $blog->intro);
							$blog->content = str_replace($input , '<a class="easyblog-thumb-preview" href="' . $largeImgPath . '">' . $images[0] . '</a>' , $blog->content);

						}

						$total++;
					}
				}
			}

			return;
		}

		// If image isn't wrapped in hyperlinks, then we need to just match the image tags
		$pattern = '#<img[^>]*>#i';
		preg_match_all($pattern, $content, $matches);

		if ($matches && count($matches[0]) > 0) {

			$images = $matches[0];
			$total = 0;

			// Beautify the image by wrapping it with a lightbox
			foreach ($images as $image) {

				$pattern = '/src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
				preg_match($pattern, $image, $src);

				if ($src) {
					$original = $src[1];

					$src[1] = urldecode($src[1]);
					$src[1] = str_ireplace(' ', '-', $src[1]);

					// Process the first image as the blog image
					if ($total == 0) {

						// Reconstruct the storage path and remove it from the image url
						$url = $src[1];
						$my = JFactory::getUser();
						$blogimage = new stdClass();
						$blogimage->place = 'user:' . $my->id;
						$blogimage->path = '/' . basename($url);

						$blog->image = json_encode($blogimage);

						// Remove the image codes from the content
						$blog->intro = str_ireplace($image, '', $blog->intro);
						$blog->content = str_ireplace($image, '', $blog->content);

					} else {

						$decodedUrl = urldecode($original);
						$decodedUrl = str_ireplace(' ', '-', $decodedUrl);
						$imageUrl = str_ireplace($original, $decodedUrl, $image);

						$blog->intro = str_ireplace($image, '<a class="easyblog-thumb-preview" href="' . $src[1] . '">' . $imageUrl . '</a>', $blog->intro);
						$blog->content = str_ireplace($image, '<a class="easyblog-thumb-preview" href="' . $src[1] . '">' . $imageUrl . '</a>', $blog->content);

					}

					$total++;
				}
			}
		}
	}

	/**
	 * Creates a new blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function save($id, $username, $password, $data, $publish)
	{

		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		// Get the current user
		$my = JFactory::getUser();

		// Get the config
		$config = EB::config();

		// Get the acl of the current user
		$acl = EB::acl();

		// Check if user has permissions to create a new entry
		if (!$my->id || !$acl->get('add_entry')) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('You do not have permissions to create new blog post.'));
		}

		// Load up the post library
		$post = EB::post($id);

		// Bind the data
		self::bindPost($post, $data, $publish);

		// Bind the blog image
		self::bindBlogImages($post, $data, $publish);

		// Save post
		try {
			$post->save(array('applyDateOffset' => true));
		} catch(EasyBlogException $exception) {

			// Reject if there is an error while saving post
			return new xmlrpcresp(0, $xmlrpcerruser + 1, $exception->getMessage());
		}

		return new xmlrpcresp(new xmlrpcval($post->id, $xmlrpcInt));
	}

	/**
	 * Proxy method to create a new blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function newPost($blogid, $username, $password, $content, $publish)
	{
		return self::save(0, $username, $password, $content, $publish);
	}

	/**
	 * Proxy method to save an existing blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function editPost($postid, $username, $password, $content, $publish)
	{
		return self::save($postid, $username, $password, $content, $publish);
	}


	private static function prepPost($id)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		$my = JFactory::getUser();
		$post = EB::post($id);

		$item = array();

		// Permalink to the blog post
		$item['link'] = new xmlrpcval($post->getPermalink());
		$item['permaLink'] = $item['link'];

		// Author id
		$item['userid'] = new xmlrpcval($my->id);

		// Post title
		$item['title'] = new xmlrpcval($post->title);

		// Post contents
		$item['description'] = new xmlrpcval($post->intro);
		$item['mt_excerpt'] = $item['description'];
		$item['more_text'] = new xmlrpcval($post->content);
		$item['mt_text_more'] = new xmlrpcval($post->content);

		// Creation date
		$item['dateCreated'] = new xmlrpcval($post->getCreationDate()->toISO8601(), 'dateTime.iso8601');
		$item['date_created_gmt'] = $item['dateCreated'];

		// Allow comments
		$item['mt_allow_comments'] = new xmlrpcval($post->allowcomment);

		// Allow trackback
		$item['mt_allow_pings'] = new xmlrpcval(0);

		// Tags
		$item['mt_keywords'] = new xmlrpcval($post->tags);

		// Get publishing state
		$state = 'publish';

		if ($post->isScheduled()) {
			$state = 'pending';
		}

		if ($post->isDraft()) {
			$state = 'draft';
		}

		// Post status
		$item['post_status'] = new xmlrpcval($state);

		// Post id
		$item['postid'] = new xmlrpcval($post->id);

		// Post permalink
		$item['wp_slug'] = new xmlrpcval($post->permalink);

		// Get the categories
		$categories = $post->getCategories();
		$cats = array();

		foreach ($categories as $category) {
			$cats[] = new xmlrpcval($category->getTitle());
		}

		$item['categories'] = new xmlrpcval($cats, $xmlrpcArray);

		return $item;
	}

	/**
	 * Retrieves information about a blog post
	 *
	 * @since	5.4.0
	 * @access	public
	 */
	public static function getPost($id, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		// Get the current logged in user
		$my = JFactory::getUser();

		// Get the acl
		$acl = EB::acl();

		if (!$acl->get('add_entry')) {
			return new xmlrpcresp(0, $xmlrpcerruser+2, JText::_('You do not have permissions to create new blog post'));
		}

		// Load the blog post
		$post = EB::post($id);

		if (!$post->id) {
			return new xmlrpcresp(0, $xmlrpcerruser + 1, JText::_('Blog post not found.'));
		}


		$item = self::prepPost($post->id);

		$data = new xmlrpcval($item, $xmlrpcStruct);

		return new xmlrpcresp($data);
	}

	/**
	 * Allows remote user to delete a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function deletePost($appkey, $id, $username, $password, $publish)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		// Get the user's acl
		$acl = EB::acl();

		if (!$acl->get('delete_entry')) {
			return new xmlrpcresp(0, $xmlrpcerruser+2, JText::_('You do not have permissions to delete this post.'));
		}

		// Load the blog
		$post = EB::post($id);

		if (!$post->id) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('The blog post does not exists on the site.'));
		}

		// Try to delete the blog post
		$state = $post->delete();

		if (!$state) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, $post->getError());
		}

		return new xmlrpcresp(new xmlrpcval(true, $xmlrpcBoolean));
	}

	/**
	 * Retrieves a list of categories associated with a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPostCategories($postId, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		$post = EB::post($postId);

		// Get the post categories
		$categories = $post->getCategories();

		// Get the primary category
		$primary = $post->getPrimaryCategory();

		if (!$categories) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('No categories available currently.'));
		}

		$structArray = array();

		foreach ($categories as $category) {

			$structArray[] = new xmlrpcval(array(
				'categoryName' => new xmlrpcval($category->title),
				'categoryId' => new xmlrpcval($category->id),
				'isPrimary' => new xmlrpcval($category->id == $primary->id)
			), 'struct');
		}

		return new xmlrpcresp(new xmlrpcval($structArray, $xmlrpcArray));
	}

	/**
	 * Retrieves a list of categories created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getCategories($blogid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}


		// $model = EB::model('XmlRpc');
		// $categories = $model->getCategories();



		$model = EB::model('Categories');
		$categories = $model->getCategoriesHierarchy(false);


		if (!$categories) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('No categories available currently.'));
		}

		$structArray = array();

		foreach ($categories as $category) {

			$structArray[] = new xmlrpcval(array(
				'title' => new xmlrpcval($category->title),
				'description' => new xmlrpcval($category->title),
				'categoryId' => new xmlrpcval($category->id),
				'parentId' => new xmlrpcval('0'),
				'categoryDescription' => new xmlrpcval($category->title),
				'categoryName' => new xmlrpcval($category->title),
				'htmlUrl' => new xmlrpcval(''),
				'rssUrl' => new xmlrpcval('')
			), 'struct');
		}

		return new xmlrpcresp(new xmlrpcval($structArray, $xmlrpcArray));
	}

	/**
	 * Retrieves a list of categories created on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getTags($blogid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		$model = EB::model('XmlRpc');
		$tags = $model->getTags();

		if (!$tags) {
			return new xmlrpcresp(0, $xmlrpcerruser+1, JText::_('No tags available currently.'));
		}

		$structArray = array();

		foreach ($tags as $tag) {

			$structArray[] = new xmlrpcval(array(
				'tagid' => new xmlrpcval($tag->id),
				'name' => new xmlrpcval($tag->title),
				'count' => new xmlrpcval(0),
				'slug' => new xmlrpcval($tag->alias),
				'html_url' => new xmlrpcval($tag->getPermalink()),
				'rss_url' => new xmlrpcval('test')
			), 'struct');
		}

		return new xmlrpcresp(new xmlrpcval($structArray, $xmlrpcArray));
	}

	/**
	 * Retrieves a list of categories created on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getPages($blogid, $username, $password)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		// We don't need to do anything here since we don't use pages
	}

	/**
	 * Stores the media object that is sent from the xmlrpc client
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function uploadMedia($blogid, $username, $password, $file)
	{
		global $xmlrpcerruser, $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		// Login the user
		$state = self::login($username, $password);

		if ($state !== true) {
			return $state;
		}

		// Get the config
		$config = EB::config();

		// Get the user's acl
		$acl = EB::acl();

		// Get the current user
		$my = JFactory::getUser();

		// Check if user has permissions to upload images
		if (!$acl->get('upload_image')) {
			return new xmlrpcresp(0, $xmlrpcerruser+2, JText::_('You do not have permissions to upload files to the site.'));
		}

		// Get the main image storage path
		$path = rtrim($config->get('main_image_path'), '/');

		$relativePath = $path . '/' . $my->id;
		$absolutePath = JPATH_ROOT . '/' . $path . '/' . $my->id;
		$absolutePath = JPath::clean($absolutePath);
		$absoluteUri = rtrim(JURI::root(), '/') . '/' . str_ireplace('\\', '/', $relativePath) . '/' . $my->id;

		// If the user's folder doesn't exist yet, create it first.
		if (!JFolder::exists($absolutePath)) {
			JFolder::create($absolutePath);
		}

		// Set the temporary folder
		$tmp = JPATH_ROOT . '/tmp';

		$media = EB::mediamanager();

		// Normalize the file name
		$file['name'] = $media->normalizeFileName($file['name']);

		// prevent if the user upload the same image name
		$dateTime = EB::date()->format('YmdHis');
		$file['name'] = $dateTime . '_' . $file['name'];

		// Write the file to the
		$tmpFile = $tmp . '/' . $file['name'];
		JFile::write($tmpFile, $file['bits']);

		// Enter some dummy data so we can run some sanity checks on the file
		$file['tmp_name'] = $tmpFile;
		$file['size'] = 0;

		$error = '';
		$allowed = EB::image()->canUploadFile($file);

		// If file uploads aren't allowed for some reasons, we need to revert
		if ($allowed !== true) {
			JFile::delete($file['tmp_name']);
			return new xmlrpcresp(0, $xmlrpcerruser+1, $error);
		}

		// Ensure that the image goes through the media manager resizing format
		$uri = 'user:' . $my->id;
		$adapter = $media->getAdapter($uri);
		$result = $adapter->upload($file, $uri);

		// Once it's gone through media manager, delete the temporary file
		JFile::delete($file['tmp_name']);

		// Build the url for the xmlrpc client so that they can replace the links accordingly within the content
		$url = rtrim(JURI::root(), '/') . '/' . $relativePath . '/' . $file['name'];

		return new xmlrpcresp(new xmlrpcval(array('url'=> new xmlrpcval($url)), 'struct'));
	}
}
