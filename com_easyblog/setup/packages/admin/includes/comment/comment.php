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

class EasyBlogComment extends EasyBlog
{
	public $pagination = null;

	/**
	 * Retrieves the adapter
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAdapter($engine)
	{
		$path = dirname(__FILE__) . '/adapters';

		$file = $path . '/' . strtolower($engine) . '.php';

		include_once($file);

		$className = 'EasyBlogComment' . ucfirst($engine);

		$obj = new $className();

		return $obj;
	}

	/**
	 * Format a list of stdclass objects into comment objects
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function format($items)
	{
		if (!$items) {
			return $items;
		}

		$my = JFactory::getUser();
		$model = EB::model('Comment');

		$comments = array();

		foreach ($items as $item) {

			$comment = EB::table('Comment');
			$comment->bind($item);

			// Load the author
			$comment->getAuthor();

			// Set the raw comments for editing
			$comment->raw = $comment->comment;

			// Set the comment depth
			if (isset($item->depth)) {
				$comment->depth = $item->depth;
			}

			// Set the comment childs aka replies
			if (isset($item->childs)) {
				$comment->childs = $item->childs;
			}

			// Format the comment
			$comment->comment = nl2br($comment->comment);
			$comment->comment = EB::comment()->parseBBCode($comment->comment);

			$comment->likesAuthor 	= '';
			$comment->likesCount 	= 0;
			$comment->isLike = false;

			if ($this->config->get('comment_likes')) {

				$data = $this->getLikesAuthors($comment->id, 'comment', $my->id);

				$comment->likesAuthor   = $data->string;
				$comment->likesCount 	= $data->count;
				$comment->isLike = $model->isLikeComment($comment->id, $my->id);
			}

			// Determine if the current user liked the item or not
			$comments[] = $comment;
		}

		return $comments;
	}

	/**
	 * Determines if we should allow preview comment
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function allowPreview()
	{
		// currently, we only allow preview for built in comment and Komento
		return $this->isBuiltin() || $this->config->get('comment_komento');
	}

	/**
	 * Determines if the comment system is a built in comment
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function isBuiltin()
	{
		if ($this->config->get('intensedebate')) {
			return false;
		}

		if ($this->config->get('comment_disqus')) {
			return false;
		}

		if ($this->config->get('comment_facebook')) {
			return false;
		}

		if ($this->config->get('comment_jomcomment')) {
			return false;
		}

		if ($this->config->get('comment_compojoom')) {
			return false;
		}

		if ($this->config->get('comment_jcomments')) {
			return false;
		}

		if ($this->config->get('comment_rscomments')) {
			return false;
		}

		if ($this->config->get('comment_komento')) {
			return false;
		}

		if ($this->config->get('comment_easysocial')) {
			return false;
		}

		if ($this->config->get('comment_jlex')) {
			return false;
		}

		if ($this->config->get('main_comment')) {
			return true;
		}

		return true;
	}

	/**
	 * Retrieves the comment count for the post
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getCommentCount($post)
	{
		static $counter = array();

		if (isset($counter[$post->id])) {
			return $counter[$post->id];
		}

		$adapter = false;

		// If configured to display multiple comments, we can't display the counter
		if ($this->config->get('main_comment_multiple')) {
			$counter[$post->id] = false;
			return false;
		}

		// @Intense debate comments
		if ($this->config->get('intensedebate')) {
			$counter[$post->id] = false;
			return false;
		}

		// @RSComments
		if ($this->config->get('comment_rscomments')) {
			return false;
		}

		// @FB Comments
		if ($this->config->get('comment_facebook')) {
			return false;
		}

		// easyblog builtin comment
		if ($this->config->get('main_comment')) {
			$adapter = $this->getAdapter('easyblog');
		}

		// @Komento
		if ($this->config->get('comment_komento')) {
			$tmpAdapter = $this->getAdapter('komento');

			if ($tmpAdapter->exists()) {
				$adapter = $tmpAdapter;
			}
		}

		// @EasySocial
		if ($this->config->get('comment_easysocial')) {
			$adapter = $this->getAdapter('easysocial');
		}

		// @Compojoom Comments
		if ($this->config->get('comment_compojoom')) {
			$adapter = $this->getAdapter('cjcomment');
		}

		// @Disqus comments
		if ($this->config->get('comment_disqus')) {
			$adapter = $this->getAdapter('disqus');
		}

		// @JComment comments
		if ($this->config->get('comment_jcomments')) {
			$adapter = $this->getAdapter('jcomments');
		}

		// @JLex comments
		if ($this->config->get('comment_jlex')) {
			$adapter = $this->getAdapter('jlex');
		}

		if ($adapter) {

			$counter[$post->id] = $adapter->getCount($post);
			return $counter[$post->id];
		}

		// Let's allow the plugin to also trigger the comment count.
		$params = EB::registry();
		$result = EB::triggerEvent('easyblog.commentCount', $post, $params, 0);

		$count = false;

		// Get the count
		if ($result) {
			$count = trim(implode(' ', $result));
		}

		if (!empty($count)) {
			$counter[$post->id] = $count;

		} else {
			$counter[$post->id] = 0;
		}

		return $counter[$post->id];
	}

	/**
	 * Retrieves the like authors for a particular comment
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getLikesAuthors($contentId, $type, $userId)
	{
		$db		= EB::db();
		$config	= EB::getConfig();

		$result = new stdClass();

		$displayFormat  = $config->get('layout_nameformat');
		$displayName    = '';

		switch($displayFormat){
			case "name" :
				$displayName = 'a.name';
				break;
			case "username" :
				$displayName = 'a.username';
				break;
			case "nickname" :
			default :
				$displayName = 'b.nickname';
				break;
		}

		$query	= 'select a.id as `user_id`, c.id, ' . $displayName . ' as `displayname`';
		$query	.= ' FROM `#__users` as a';
		$query	.= '  inner join `#__easyblog_users` as b';
		$query	.= '    on a.id = b.id';
		$query	.= '  inner join `#__easyblog_likes` as c';
		$query	.= '    on a.id = c.created_by';
		$query	.= ' where c.content_id = ' . $db->Quote($contentId);
		$query	.= ' and c.`type` = '. $db->Quote($type);
		$query	.= ' order by c.id desc';

		$db->setQuery($query);
		$list   = $db->loadObjectList();

		if (count($list) <= 0) {

			$result->string = '';
			$result->count = 0;

			return $result;
		}

		// else continue here
		$onwerInside = false;

		$names	= array();
		for ($i = 0; $i < count($list); $i++) {

			if ($list[$i]->user_id == $userId) {
				$onwerInside	= true;
				array_unshift($names, JText::_('COM_EASYBLOG_YOU') );
			} else {
				$names[]	= $list[$i]->displayname;
			}
		}

		$max	= 3;
		$total	= count($names);
		$break	= 0;

		if ($total == 1) {
			$break	= $total;
		} else {

			if ($max >= $total) {
				$break	= $total - 1;
			} elseif($max < $total) {
				$break	= $max;
			}
		}

		$main	= array_slice($names, 0, $break);
		$remain	= array_slice($names, $break);

		$stringFront	= implode(", ", $main);
		$returnString	= '';

		if(count($remain) > 1) {
			$returnString = JText::sprintf('COM_EASYBLOG_AND_OTHERS_LIKE_THIS', $stringFront, count($remain));
		} else if(count($remain) == 1) {
			$returnString = JText::sprintf('COM_EASYBLOG_AND_LIKE_THIS', $stringFront, $remain[0]);
		} else {
			if (EB::isLoggedIn() && $onwerInside) {
				$returnString = JText::sprintf('COM_EASYBLOG_LIKE_THIS_SINGULAR', $stringFront);
			} else {
				$returnString = JText::sprintf('COM_EASYBLOG_LIKE_THIS_PLURAL', $stringFront);
			}
		}

		$result->count = $total;
		$result->string = $returnString;

		return $result;
	}

	public static function getBlogCommentLite(  $blogId, $limistFrontEnd = 0, $sort = 'asc')
	{
		return EasyBlogComment::getBlogComment($blogId, $limistFrontEnd, $sort, true);
	}

	/**
	 * Retrieves a list of comments
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlogComment($postId, $limitFrontEnd = 0, $sort = 'asc', $lite = false)
	{
		$model = EB::model('Blog');

		$comments = $model->getBlogComment($postId, $limitFrontEnd, $sort, $lite);
		$pagination = $model->getPagination();

		return $comments;
	}

	/**
	 * Retrieves a list of preview comments
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPreviewBlogComment($postId, $limitFrontEnd = 0, $sort = 'asc', $lite = false)
	{
		if ($this->isBuiltin()) {
			$model = EB::model('Blog');

			$comments = $model->getBlogComment($postId, $limitFrontEnd, $sort, $lite);
			$pagination = $model->getPagination();

			return $comments;
		}

		// Komento integrations
		if ($this->config->get('comment_komento') && $this->getAdapter('komento')->exists()) {
			$comments = $this->getAdapter('komento')->getPreviewComments($postId, $limitFrontEnd);

			return $comments;
		}
	}

	/**
	 * Cleanup any unused code
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function cleanup(EasyBlogPost &$post)
	{
		if ($this->config->get('comment_komento') && $this->getAdapter('komento')->exists()) {

			$this->getAdapter('komento')->cleanup($post);
		}
	}

	/**
	 * Renders the output for comments area
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function html(EasyBlogPost &$post, $comments = [], $pagination = '', $options = [])
	{
		// Determines if multiple comment sources should be allowed
		$multiple = $this->config->get('main_comment_multiple');

		// Define default comment systems
		$types = [];

		// Facebook comments
		if ($this->config->get('comment_facebook') && $post->allowComments()) {
			$types['FACEBOOK'] = $this->getAdapter('facebook')->html($post);

			// If the system is configured to only display a single comment source
			if (!$multiple) {
				return $types['FACEBOOK'];
			}
		}

		// EasySocial comments
		if ($this->config->get('comment_easysocial') && EB::easysocial()->exists()) {

			$easysocial = EB::easysocial()->getCommentHTML($post);

			// Check whether easysocial plugin is enabled or not.
			if ($easysocial) {
				$types['EASYSOCIAL'] = $easysocial;
			}

			if (!$multiple) {
				return isset($types['EASYSOCIAL']) ? $types['EASYSOCIAL'] : '';
			}
		}

		// Compojoom comments
		if ($this->config->get('comment_compojoom') && $post->allowComments()) {

			EB::exception('Comment locking feature is not supported by CjComment', 'error');

			$types['COMPOJOOM'] = $this->getAdapter('CjComment')->html($post);

			if (!$multiple) {
				return $types['COMPOJOOM'];
			}
		}

		// Intensedebate
		if ($this->config->get('comment_intensedebate') && $post->allowComments()) {

			$types['INTENSEDEBATE'] = $this->getAdapter('IntenseDebate')->html($post);

			if (!$multiple) {
				return $types['INTENSEDEBATE'];
			}
		}

		// Disqus comments
		if ($this->config->get('comment_disqus') && $post->allowComments()) {

			// If multiple comment enabled, we return empty for disqus
			// Because we will later load it by ajax
			$types['DISQUS'] = '';

			if (!$multiple) {
				return $this->getAdapter('Disqus')->html($post);
			}
		}

		// HyperComments comments
		if ($this->config->get('comment_hypercomments') && $post->allowComments()) {

			$types['HYPERCOMMENTS'] = $this->getAdapter('HyperComments')->html($post);

			if (!$multiple) {
				return $types['HYPERCOMMENTS'];
			}
		}

		// JComments
		if ($this->config->get('comment_jcomments') && $post->allowComments()) {
			$types['JCOMMENTS']	= $this->getAdapter('jcomments')->html($post);

			if (!$multiple) {
				return $types['JCOMMENTS'];
			}
		}

		// RSComments
		if ($this->config->get('comment_rscomments') && $post->allowComments()) {
			$types['RSCOMMENTS'] = $this->getAdapter('rscomments')->html($post);

			if (!$multiple) {
				return $types['RSCOMMENTS'];
			}
		}

		// EasyDiscuss
		if ($this->config->get('comment_easydiscuss') && $post->allowComments()) {

			$easydiscuss = $this->getAdapter('easyDiscuss')->html($post);

			// Check whether easydiscuss plugin is enabled or not.
			if ($easydiscuss) {
				$types['EASYDISCUSS'] = $easydiscuss;

				if (!$multiple) {
					return $types['EASYDISCUSS'];
				}
			}
		}

		// Komento integrations
		if ($this->config->get('comment_komento') && $this->getAdapter('komento')->exists()) {
			$types['KOMENTO'] = $this->getAdapter('komento')->html($post);

			if (!$multiple) {
				return $types['KOMENTO'];
			}
		}

		// JLex integrations
		if ($this->config->get('comment_jlex') && $post->allowComments()) {

			$types['JLEX'] = $this->getAdapter('jlex')->html($post);

			if (!$multiple) {
				return $types['JLEX'];
			}
		}

		// Built in comments
		if ($this->config->get('main_comment', 1) && $this->config->get('comment_easyblog') && !$multiple) {
			$types['EASYBLOGCOMMENTS'] = $this->getAdapter('easyblog')->html($post, $options);
			return $types['EASYBLOGCOMMENTS'];
		}

		// If multiple comments are enabled, we should check if the user wants to have EasyBlog.
		if ($multiple && $this->config->get('main_comment') && $this->config->get('comment_easyblog')) {
			$types['EASYBLOGCOMMENTS'] = $this->getAdapter('easyblog')->html($post, $options);
		}

		// If there's 1 system only, there's no point loading the tabs.
		if (count($types) === 1) {
			return $types[key($types)];
		}

		// Reverse the comment systems array so that easyblog comments are always the first item.
		$types = array_reverse($types);

		$template = EB::themes();
		$template->set('types', $types);

		$output = $template->output('site/comments/multiple');

		return $output;
	}

	/**
	 * Deprecated. Use @html instead
	 *
	 * @deprecated	5.1
	 */
	public function getCommentHTML(EasyBlogPost &$blog, $comments = [], $pagination = '', $options = [])
	{
		return $this->html($blog, $comments, $pagination, $options);
	}

	/**
	 * Processes BBCode
	 *
	 * @since	5.2.8
	 * @access	public
	 */
	public static function parseBBCode($text)
	{
		$text = trim($text);

		$text = preg_replace_callback('/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms', 'escape' , $text );

		// BBCode to find...
		$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',
						 '/\[i\](.*?)\[\/i\]/ms',
						 '/\[u\](.*?)\[\/u\]/ms',
						 '/\[img\](.*?)\[\/img\]/ms',
						 '/\[email\](.*?)\[\/email\]/ms',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
						 '/\[quote](.*?)\[\/quote\]/ms',
						 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
						 '/\[list\](.*?)\[\/list\]/ms',
						 '/\[\*\]\s?(.*?)\n/ms'
		);
		// And replace them by...
		$out = array(	 '<strong>\1</strong>',
						 '<em>\1</em>',
						 '<u>\1</u>',
						 '<img src="\1" alt="\1" />',
						 '<a href="mailto:\1">\1</a>',
						 '<span style="font-size:\1%">\2</span>',
						 '<span style="color:\1">\2</span>',
						 '<blockquote>\1</blockquote>',
						 '<ol start="\1">\2</ol>',
						 '<ul>\1</ul>',
						 '<li>\1</li>'
		);

		$tmp    = preg_replace( $in , '' , $text );

		$config = EB::config();

		$text	= preg_replace($in, $out, $text);

		// Smileys to find...
		$in = array( 	 ':D',
						 ':)',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);

		// And replace them by...
		$out = array(
					'<span title=":D" class="markItUpButton markitup-happy"></span>',
					'<span title=":)" class="markItUpButton markitup-smile"></span>',
					'<span title=":o" class="markItUpButton markitup-surprised"></span>',
					'<span title=":p" class="markItUpButton markitup-tongue"></span>',
					'<span title=":(" class="markItUpButton markitup-unhappy"></span>',
					'<span title=";)" class="markItUpButton markitup-wink"></span>'
				);

		$text = str_replace($in, $out, $text);

		// Replace bbcode url
		$text = EasyBlogComment::replaceBBCodeURL($text);

		// Process the rest of hyperlink
		$text = EasyBlogComment::replaceURL($tmp, $text);

		// paragraphs
		$text = str_replace("\r", "", $text);
		$text = "<p>".preg_replace("/(\n){2,}/", "</p><p>", $text)."</p>";


		$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "removeBr", $text);
		$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);

		$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', "removeBr", $text);
		$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);

		return $text;
	}

	public static function replaceBBCodeURL($text)
	{
		preg_match_all('/\[url\="?(.*?)"?\](.*?)\[\/url\]/ims', $text, $matches);

		if (!empty($matches) && isset($matches[0]) && !empty($matches[0])) {

			// Get the list of url tags
			$urlTags = $matches[0];
			$urls = $matches[1];
			$titles = $matches[2];

			$total = count($urlTags);

			for ($i = 0; $i < $total; $i++) {

				$url = $urls[$i];

				// Check for http protocol
				if (stristr($url, 'http://') === false && stristr($url, 'https://') === false && stristr($url, 'ftp://') === false) {
					$url = 'http://' . $url;
				}

				$title = isset($titles[$i]) && $titles[$i] ? $titles[$i] : $url;

				// Append target blank and rel no follow by default.
				$targetBlank = ' target="_blank"';
				$noFollow = ' rel="nofollow"';
				$text = str_ireplace($urlTags[$i], '<a href="' . $url . '"' . $targetBlank . $noFollow .'>' . $title . '</a>', $text);
			}
		}

		return $text;
	}

	/**
	 * Converts hyperlink text into real hyperlinks
	 *
	 * @since	5.0.37
	 * @access	public
	 */
	public static function replaceURL($tmp, $text)
	{
		$pattern = '@(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		preg_match_all($pattern, $tmp, $matches);

		// Do not proceed if there are no links to process
		if (!isset($matches[0]) || !is_array($matches[0]) || empty($matches[0])) {
			return $text;
		}

		$tmplinks = $matches[0];

		$links = array();
		$linksWithProtocols = array();
		$linksWithoutProtocols = array();

		// We need to separate the link with and without protocols to avoid conflict when there are similar url present in the content.
		if ($tmplinks) {
			foreach($tmplinks as $link) {
				if (stristr($link, '[/url') !== false) {
					continue;
				}

				if (stristr($link , 'http://') === false && stristr($link, 'https://') === false && stristr($link, 'ftp://') === false) {
					$linksWithoutProtocols[] = $link;
				} else if (stristr($link, 'http://') !== false || stristr($link, 'https://') !== false || stristr( $link , 'ftp://' ) === false ) {
					$linksWithProtocols[] = $link;
				}
			}
		}

		// the idea is the first convert the url to [EDWURLx] and [EDWOURLx] where x is the index. This is to prevent same url get overwritten with wrong value.
		$linkArrays = array();

		// global indexing.
		$idx = 1;

		// lets process the one with protocol
		if ($linksWithProtocols) {
			$linksWithProtocols = array_unique($linksWithProtocols);

			foreach ($linksWithProtocols as $link) {

				$mypattern = '[EBWURL' . $idx . ']';

				$text = str_ireplace($link, $mypattern, $text);

				$obj = new stdClass();
				$obj->index = $idx;
				$obj->link = $link;
				$obj->newlink = $link;
				$obj->customcode = $mypattern;

				$linkArrays[] = $obj;

				$idx++;
			}
		}

		// Now we process the one without protocol
		if ($linksWithoutProtocols) {
			$linksWithoutProtocols = array_unique($linksWithoutProtocols);

			foreach ($linksWithoutProtocols as $link) {
				$mypattern = '[EBWOURL' . $idx . ']';
				$text = str_ireplace($link, $mypattern, $text);

				$obj = new stdClass();
				$obj->index = $idx;
				$obj->link = $link;
				$obj->newlink = 'http://'. $link;
				$obj->customcode = $mypattern;

				$linkArrays[] = $obj;

				$idx++;
			}
		}

		// Let's replace back the link now with the proper format based on the index given.
		foreach ($linkArrays as $link) {
			$text = str_ireplace($link->customcode, $link->newlink, $text);

			$patternReplace = '@(?<![.*">])\b(?=https?|ftp|file://[a-z]\.)[-A-Z0-9+&#/%=~_|$?!:,.]*[A-Z0-9+&#/%=~_|$]@i';

			// Use preg_replace to only replace if the URL doesn't has <a> tag
			$text = preg_replace($patternReplace, '<a href="\0" target="_blank" rel="nofollow">\0</a>', $text);
		}



		return $text;
	}
}

// clean some tags to remain strict
// not very elegant, but it works. No time to do better ;)
if (!function_exists('removeBr')) {
	function removeBr($s) {
		return str_replace("<br />", "", $s[0]);
	}
}

// BBCode [code]
if (!function_exists('escape')) {
	function escape($s) {
		global $text;
		$text = strip_tags($text);
		$code = $s[1];
		$code = htmlspecialchars($code);
		$code = str_replace("[", "&#91;", $code);
		$code = str_replace("]", "&#93;", $code);
		return '<pre><code>'.$code.'</code></pre>';
	}
}
