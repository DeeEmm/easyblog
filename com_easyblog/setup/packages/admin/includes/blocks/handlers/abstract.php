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

abstract class EasyBlogBlockHandlerAbstract
{
	public $doc = null;
	public $type;
	public $icon;
	public $title;
	public $keywords;
	public $table;
	public $nestable = false;
	public $visible = true;

	public function __construct(EasyBlogTableBlock &$block)
	{
		$this->doc = JFactory::getDocument();
		$this->type = $block->element;
		$this->title = JText::_('COM_EASYBLOG_BLOCKS_HANDLER_' . strtoupper($this->type) . '_TITLE');

		if ($block->keywords) {
			$this->keywords = $block->keywords;
		}

		// Fallback when keywords don't exist
		if (!$this->keywords) {
			$this->keywords = JText::_('COM_EASYBLOG_BLOCKS_HANDLER_' . strtoupper($this->type) . '_KEYWORDS');
		}

		$this->table = $block;
	}

	/**
	 * Retrieves the icon
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * Retrieves the description / help text for the current block
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getDescription()
	{
		return JText::_($this->table->description);
	}

	/**
	 * Standard method to format the output for displaying purposes
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function formatDisplay($item, EasyBlogPost &$blog)
	{
		return $item->html;
	}

	/**
	 * Standard meta data of a block object
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function meta()
	{
		$meta = new stdClass();

		// Standard descriptors
		$meta->type = $this->type;
		$meta->icon = $this->icon;
		$meta->title = $this->title;
		$meta->keywords = $this->keywords;
		$meta->data = $this->data();

		// Nestable
		$meta->nestable = $this->nestable;

		// Dimensions
		$meta->dimensions = new stdClass();
		$meta->dimensions->enabled = true;
		$meta->dimensions->respectMinContentSize = false;

		// Others
		$meta->properties = [
			'fonts' => true,
			'textpanel' => true,
			'css' => true
		];

		$theme = EB::themes();
		$theme->set('block', $this);
		$theme->set('data', $meta->data);
		$theme->set('params', $this->table->getParams());

		$meta->html = $theme->output('site/composer/blocks/handlers/' . $this->type . '/html');
		$meta->block = EB::blocks()->renderBlockContainer(EASYBLOG_BLOCK_MODE_EDITABLE, $this, $meta->html);

		// Fieldset & fieldgroup
		$meta->fieldset = $this->getFieldset($meta);
		$meta->fieldgroup = $theme->output('site/composer/blocks/fieldgroup', array('fieldset' => $meta->fieldset));

		return $meta;
	}

	/**
	 * Renders the fieldset of a block
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFieldset($meta)
	{
		$theme = EB::themes();
		$theme->set('block', $this);
		$theme->set('data', $meta->data);
		$theme->set('params', $this->table->getParams());

		return $theme->output('site/composer/blocks/handlers/' . $this->type . '/fieldset');
	}

	/**
	 * Retrieves the output for the block when it is being displayed
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getHtml($block, $textOnly = false)
	{
		return $block->html;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function validate($block)
	{
		$content = $block->html;

		// strip html tags to precise length count.
		$content = strip_tags($content);

		// convert html entities back to it string. e.g. &nbsp; back to empty space
		$content = html_entity_decode(mb_convert_encoding(stripslashes($content), "HTML-ENTITIES", 'UTF-8'));

		// replace special characters from redactor.
		$content = str_replace('&#8203;', '', $content);

		// replace line feed
		$content = preg_replace('/[\n\r]/', '', $content);

		// convert non-breaking space to normal space
		$content = preg_replace('/\xC2/', ' ', $content);
		$content = preg_replace('/\xA0/', ' ', $content);

		// remove any blank space.
		$content = trim($content);

		// get content length
		$contentLength = EBString::strlen($content);
		if ($contentLength > 0) {

			if ($content == JText::_('COM_EASYBLOG_BLOCKS_TEXT_PLACEHOLDER')) {
				return false;
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * use for acl checking on blocks. By default this method always return true. If a block needed acl checking,
	 * the block will need to override this method in their handler.
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function canUse()
	{
		return true;
	}

	/**
	 * Retrieves the output for the block when it is being edited
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getEditableHtml($block)
	{
		return isset($block->editableHtml) ? $block->editableHtml : '';
	}

	public function updateBlock($block, $data)
	{
		$block->html = '';
		return $block;
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		return false;
	}

	public abstract function data();

	public function getInstantHtmlFigure($block)
	{
		$url = '';

		if (!isset($block->data->url) || !$block->data->url) {
			$url = $block->data->source;
		} else {
			$url = $block->data->url;
		}

		return '<figure class="op-interactive"><iframe width="560" height="315" src="' . $url . '"></iframe></figure>';
	}

	public function getInstantHtmlVideo($block)
	{
		return '<figure><video><source src="' . $block->data->url . '"/></video></figure>';
	}

	public function getInstantHtmlEmbedded($block)
	{
		return '<figure class="op-interactive"><iframe>' . $block->data->embed . '</iframe></figure>';
	}

	public function getInstantHtmlGallery($block)
	{
		$html = '<figure class="op-slideshow">';

		if ($block->type == 'thumbnails') {
			if (count($block->blocks) > 2) {
				foreach ($block->blocks as $i => $item) {
					if ($i >= $block->data->column_count) {
						break;
					}
					$html .= '<figure><img src="' . $item->data->url . '"/></figure>';
				}
			} else {
				$html .= '<figure><img src="' . $block->blocks[0]->data->url . '"/></figure>';
			}
		} else {
			$items = $block->data->itemsArray;
			foreach ($items as $item) {
				if (!is_null($item)) {
					$url = str_replace('b2ap3_icon_', 'b2ap3_large_', $item->iconUrl);
					$html .= '<figure><img src="'. $url .'"/></figure>';
				}
			}
		}

		$html .= '</figure>';

		return $html;
	}

	public function getInstantHtmlArticle($block)
	{
		$content = trim(html_entity_decode($block->html));
		// $content = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $content);
		$content = preg_replace('/[\x00-\x1F\x7F]/', '', $content);
		$content = preg_replace('/[\x00-\x1F\x7F]/u', '', $content);
		$content = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $content);
		$content = preg_replace("#(&nbsp;)#", "", $content);
		$content = preg_replace("#<p>(\s|&nbsp;|</?\s?br\s?/?>)*</?p>#", "", $content);
		$content = preg_replace('#(<p>\s*<\/p>)#iUum', '', $content);
		$content = str_replace('<p></p>', '', $content);

		return $content;
	}

	public function getInstantHtmlTable($block)
	{
		return '<figure class="op-interactive"><iframe>' . $block->html . '</iframe></figure>';
	}

	public function getInstantHtmlBlockquote($block)
	{
		$content = trim(strip_tags($block->html));
		return '<blockquote>' . $content . '</blockquote>';
	}

	public function getInstantHtmlImage($block)
	{
		$url = str_replace('b2ap3_thumbnail_', 'b2ap3_large_', $block->data->url);
		return '<figure><img src="' . $url . '"/></figure>';
	}

	public function getInstantHtmlHeading($block)
	{
		$unallowed = array('h3', 'h4', 'h5', 'h6');

		$heading = str_replace(PHP_EOL, '', trim($block->html));
		$heading = preg_replace( "/\r|\n/", '', $heading);

		preg_match_all('#<(h[^>])>(.*?)<\/h[^>]>#', $heading , $matches);

		if (!$matches[0]) {
			return '';
		}

		// Do not append if the heading has empty value such as: <h1></h1>
		if ($matches[2][0] == '') {
			$html = '';
		} else {
			if (in_array($matches[1][0], $unallowed)) {
				$html = '<h2>' . trim($matches[2][0]) . '</h2>';
			} else {
				$html = trim($matches[0][0]);
			}
		}

		return $html;
	}

	public function getInstantHtmlSection($block)
	{
		$str = $block->html;

		// We remove any div tag from the content.
		$content = preg_replace('/\<[\/]{0,1}div[^\>]*\>/i', '', $str);

		return trim($content);
	}

	public function getInstantHtmlNone($block)
	{
		return '';
	}

	/**
	 * Retrieve Instant article html
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function getInstantHtml($block)
	{
		$html = '';

		// Validate the block whether have data or not.
		if (!$this->validate($block)) {
			return $html;
		}

		$class = 'getInstantHtml' . ucfirst($this->element);
		$html = $this->$class($block);

		return $html;
	}
}
