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

class EasyBlogBlockHandlerQuotes extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-quote-left';
	public $element = 'blockquote';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		return $meta;
	}

	public function data()
	{
		$data = (object) [
			// It could also come from $config->get();
			// Which means user can configure default data
			// in the backend.
			'style' => 'style-default',
			'citation' => 1
		];

		return $data;
	}

	/**
	 * Renders the fieldset of a block
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFieldset($meta)
	{
		$styles = [
			[
				'label' => JText::_('COM_EB_BLOCKS_QUOTE_FIELDSET_DEFAULT_OPTION'),
				'classname' => 'style-default'
			],
			[
				'label' => JText::_('COM_EB_BLOCKS_QUOTE_FIELDSET_MINIMAL_LIGHT_OPTION'),
				'classname' => 'style-minimallight'
			],
			[
				'label' => JText::_('COM_EB_BLOCKS_QUOTE_FIELDSET_MINIMAL_BOX_OPTION'),
				'classname' => 'style-minimalbox'
			],
			[
				'label' => JText::_('COM_EB_BLOCKS_QUOTE_FIELDSET_MODERN_BOX_OPTION'),
				'classname' => 'style-modern'
			]
		];

		$themes = EB::themes();
		$themes->set('styles', $styles);
		$themes->set('block', $this);
		$themes->set('data', $meta->data);
		$themes->set('params', $this->table->getParams());

		return $themes->output('site/composer/blocks/handlers/quotes/fieldset');
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		// style attribute is not allowed in AMP
		$output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $block->html);

		// Detect if there is anchor link in the content and replace it with amp 'style'
		$replace = '<span role="button" tabindex="0" on="tap:$2.scrollTo(duration=500, position=top)">$4</span>';
		$output = preg_replace('/<a(.*?)href="#([^"]+)"(.*?)>(.*?)<\/a>/', $replace, $output);

		// if there is a custom_id assigned, this is more likely to be the anchor content
		// So we wrap it in amp 'style' of anchor
		if (isset($block->data->custom_id)) {
			$output = '<span id="' . $block->data->custom_id . '">' . $output . '</span>';
		}

		return $output;
	}
}
