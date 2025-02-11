<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogFormatterStandard extends EasyBlog
{
	protected $items 	= null;
	protected $cache 	= null;
	protected $options 	= null;
	protected $limitstart = '';

	public function __construct(&$items, $cache = true, $options = array())
	{
		parent::__construct();

		$this->items = $items;
		$this->cache = $cache;
		$this->options = $options;
		// $this->my = JFactory::getUser();
		// $this->config = EB::config();
		// $this->app = JFactory::getApplication();
		// $this->input = EB::request();
		$this->limitstart = $this->input->get('limitstart', 0, 'int');
	}

	/**
	 * Retrieves all post id's given a collection of items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostIds()
	{
		foreach ($this->items as $item) {
			$ids[] = (int) $item->id;
		}

		return $ids;
	}

	/**
	 * Retrieves a list of categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadPrimaryCategories()
	{
		$postIds = $this->getPostIds();

		if (!$postIds) {
			return array();
		}

		$model = EB::model('Categories');
		$result = $model->preload($postIds);

		return $result;
	}

	/**
	 * Preloads a list of authors
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadAuthors()
	{
		if (!$this->items) {
			return array();
		}

		// Get list of created_by
		$authorIds = array();

		foreach ($this->items as $item) {
			$authorIds[] = $item->created_by;
		}

		// Ensure that all id's are unique
		array_unique($authorIds);


		$model = EB::model('Blogger');
		$result = $model->preload($authorIds);

		return $result;
	}


	/**
	 * Retrieves a list of custom fields
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadCustomFields()
	{
		$postIds = $this->getPostIds();

		if (!$postIds) {
			return array();
		}

		$model = EB::model('Featured');
		$result = $model->preload($postIds);
	}

	/**
	 * Retrieves a list of featured items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadTags()
	{
		$postIds = $this->getPostIds();

		if (!$postIds) {
			return array();
		}

		$model = EB::model('PostTag');
		$result = $model->preload($postIds);

		return $result;
	}

	/**
	 * Retrieves a list of featured items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadFeaturedItems()
	{
		$postIds = $this->getPostIds();

		if (!$postIds) {
			return array();
		}

		$model = EB::model('Featured');
		$result = $model->preload($postIds);

		return $result;
	}

	/**
	 * Determines if the blog object requires password
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function password(EasyBlogPost &$blog)
	{
		if (!$this->config->get('main_password_protect') || empty($blog->password)) {
			return;
		}

		// If it proceeds here, check if user already entered the password to view the blog post
		$verified = EB::verifyBlogPassword($blog->blogpassword, $blog->id);

		if ($verified) {
			return;
		}

		// If it proceeds, we need to update the blog title to something different
		$blog->title = JText::sprintf('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_TITLE', $blog->title);

		return;
	}

	/**
	 * Formats the microblog posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function formatMicroblog(EasyBlogPost &$blog)
	{
		$adapter = EB::quickpost()->getAdapter($blog->posttype);

		if ($adapter === false) {
			return;
		}

		$adapter->format($blog);
	}

	/**
	 * Determines if a content requires a read more link.
	 *
	 * @since 	4.0
	 * @access	public
	 * @param 	EasyBlogTableBlog
	 */
	public function hasReadmore(EasyBlogPost &$blog)
	{
		// By default, display the read more link if not configured to respect read more.
		if (!$this->config->get('composer_truncation_readmore')) {
			return true;
		}

		// Get the maximum character before read more kicks in.
		$max = $this->config->get('layout_maxlengthasintrotext', 150);
		if ($blog->doctype == 'ebd') {
			$max = $this->config->get('composer_truncation_chars', 150);
		}

		// When introtext is not empty and content is empty
		if (empty($blog->content) && !empty($blog->intro)) {

			$length	= EBString::strlen(strip_tags($blog->intro));

			if ($length > $max && $this->config->get('composer_truncation_enabled')) {
				return true;
			}

			return false;
		}

		// As long as the content is not empty, show the read more
		if (!empty($blog->content)) {
			return true;
		}

		// If it falls anywhere else, always display the read more.
		return true;
	}

	/**
	 * Adds rel="nofollow" to all the links within the content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The content target
	 * @return	string	The content with applied rel="nofollow"
	 */
	public function addNoFollow($content)
	{
		if (!$this->config->get('main_anchor_nofollow')) {
			return $content;
		}

		$content = EB::string()->addNoFollow($content);
		return $content;
	}

	/**
	 * Reverse of strip_tags
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function strip_only($str, $tags, $stripContent = false)
	{
		$content = '';

		if (!is_array($tags)) {
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));

			if (end($tags) == '') {
				array_pop($tags);
			}
		}

		foreach ($tags as $tag) {
			if ($stripContent) {
				$content = '(.+</'.$tag.'[^>]*>|)';
			}
			$str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
		}
		return $str;
	}

	/**
	 * Remove known dirty codes from the content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function stripCodes(EasyBlogTableBlog &$blog)
	{
		// Remove video codes
		EB::videos()->stripCodes($blog);

		// Remove audio codes
		EB::audio()->stripCodes($blog);

		// Remove gallery codes
		EB::gallery()->stripCodes($blog);

		// Remove album codes
		EB::album()->stripCodes($blog);
	}

	public function truncateByWords($content)
	{
		$tag		= false;
		$count		= 0;
		$output		= '';

		// Remove uneccessary html tags to avoid unclosed html tags
		$content		= strip_tags( $content );

		$chunks		= preg_split("/([\s]+)/", $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		foreach($chunks as $piece)
		{

			if( !$tag || stripos($piece, '>') !== false )
			{
				$tag = (bool) (strripos($piece, '>') < strripos($piece, '<'));
			}

			if( !$tag && trim($piece) == '' )
			{
				$count++;
			}

			if( $count > $maxCharacter && !$tag )
			{
				break;
			}

			$output .= $piece;
		}

		return $output;
	}

	public function truncateByChars($content)
	{
		$maxCharacter	= $config->get('layout_maxlengthasintrotext', 150);

		// Remove uneccessary html tags to avoid unclosed html tags
		$content	= strip_tags( $content );

		// Remove blank spaces since the word calculation should not include new lines or blanks.
		$content	= trim( $content );

		$content 	= EBString::substr($content, 0, $maxCharacter);
	}

	public function truncateByBreak($content)
	{
		$position	= 0;
		$matches	= array();
		$tag		= '<br';

		$matches	= array();

		do
		{
			$position	= @EBString::strpos( strtolower( $content ) , $tag , $position + 1 );

			if( $position !== false )
			{
				$matches[]	= $position;
			}
		} while( $position !== false );

		$maxTag		= (int) $config->get( 'main_truncate_maxtag' );

		if( count( $matches ) > $maxTag )
		{
			$row->text	= EBString::substr( $content , 0 , $matches[ $maxTag - 1 ] + 6 );
			$row->readmore	= true;
		}
		else
		{
			$row->text	= $content;
			$row->readmore	= false;
		}
	}

	public function truncateByParagraph()
	{
		$position	= 0;
		$matches	= array();
		$tag		= '</p>';

		// @task: If configured to not display any media items on frontpage, we need to remove it here.
		if( $frontpage && $config->get( 'main_truncate_image_position' ) == 'hidden' )
		{
			// Need to remove images, and videos.
			$content 	= self::strip_only( $content , '<img>' );
		}

		do
		{
			$position	= @EBString::strpos( strtolower( $content ) , $tag , $position + 1 );

			if( $position !== false )
			{
				$matches[]	= $position;
			}
		} while( $position !== false );

		// @TODO: Configurable
		$maxTag		= (int) $config->get( 'main_truncate_maxtag' );

		if( count( $matches ) > $maxTag )
		{
			$row->text	= EBString::substr( $content , 0 , $matches[ $maxTag - 1 ] + 4 );

			$htmlTagPattern    		= array('/\<div/i', '/\<table/i');
			$htmlCloseTagPattern   	= array('/\<\/div\>/is', '/\<\/table\>/is');
			$htmlCloseTag   		= array('</div>', '</table>');

			for( $i = 0; $i < count($htmlTagPattern); $i++ )
			{

				$htmlItem   			= $htmlTagPattern[$i];
				$htmlItemClosePattern	= $htmlCloseTagPattern[$i];
				$htmlItemCloseTag		= $htmlCloseTag[$i];

				preg_match_all( $htmlItem , strtolower( $row->text ), $totalOpenItem );

				if( isset( $totalOpenItem[0] ) && !empty( $totalOpenItem[0] ) )
				{
					$totalOpenItem	= count( $totalOpenItem[0] );

					preg_match_all( $htmlItemClosePattern , strtolower( $row->text ) , $totalClosedItem );

					$totalClosedItem	= count( $totalClosedItem[0] );

					$totalItemToAdd	= $totalOpenItem - $totalClosedItem;

					if( $totalItemToAdd > 0 )
					{
						for( $y = 1; $y <= $totalItemToAdd; $y++ )
						{
							$row->text 	.= $htmlItemCloseTag;
						}
					}
				}
			}

			$row->readmore	= true;
		}
		else
		{
			$row->text		= $content;
			$row->readmore	= false;
		}
	}

	/**
	 * Used in json formatters
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sanitize($text) {

		$text = htmlspecialchars_decode($text);
		$text = str_ireplace('&nbsp;', ' ', $text);

		return $text;
	}
}
