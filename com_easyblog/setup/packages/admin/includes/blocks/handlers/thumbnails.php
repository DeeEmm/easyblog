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

class EasyBlogBlockHandlerThumbnails extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-th';
	public $element = 'gallery';

	public function meta()
	{
		static $meta;

		if (isset($meta)) {
			return $meta;
		}

		$meta = parent::meta();

		// Disable dimensions fieldset
		$meta->properties['fonts'] = false;

		$template = EB::themes();
		$meta->thumbContainer = $template->output('site/composer/blocks/handlers/thumbnails/container');
		$meta->thumbColumn = $template->output('site/composer/blocks/handlers/thumbnails/column');
		$meta->thumbItem = $template->output('site/composer/blocks/handlers/thumbnails/item');
		$meta->thumbPlaceholder = $template->output('site/composer/blocks/handlers/thumbnails/placeholder');

		return $meta;
	}

	public function data()
	{
		$data = (object) array();
		$data->layout = 'stack'; // grid, stack
		$data->column_count = 4; // 1-6
		$data->strategy = "fit"; // fit, fill
		$data->ratio = 4 / 3;

		return $data;
	}

	/**
	 * Validates if the block contains any contents
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function validate($block)
	{
		// Gallery block do not need to do validation. js return true.
		return true;
	}

	/**
	 * determine if current user can use this block or not in composer.
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function canUse()
	{
		$acl = EB::acl();
		return $acl->get('upload_image');
	}

	/**
	 * Renders the fieldset of a block
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getFieldset($meta)
	{
		$ratioList = [
			[
				'name'      => JText::_('COM_EASYBLOG_COMPOSER_THUMBNAIL_LAYOUT_WIDE'),
				'caption'   => '16:9',
				'value'     => '16:9',
				'padding'   => '56.25%',
				'classname' => 'ar-wide'
			],
			[
				'name'      => JText::_('35mm'),
				'caption'   => '3:2',
				'value'     => '3:2',
				'padding'   => '66.666667%',
				'classname' => 'ar-35mm'
			],
			[
				'name'      => JText::_('COM_EASYBLOG_COMPOSER_THUMBNAIL_LAYOUT_NORMAL'),
				'caption'   => '4:3',
				'value'     => '4:3',
				'padding'   => '75%',
				'classname' => 'ar-photo'
			],
			[
				'name'      => JText::_('COM_EASYBLOG_COMPOSER_THUMBNAIL_LAYOUT_SQUARE'),
				'caption'   => '1:1',
				'value'     => '1:1',
				'padding'   => '100%',
				'classname' => 'ar-square'
			]
		];

		$theme = EB::themes();
		$theme->set('ratioList', $ratioList);
		$theme->set('block', $this);
		$theme->set('data', $meta->data);
		$theme->set('params', $this->table->getParams());

		return $theme->output('site/composer/blocks/handlers/' . $this->type . '/fieldset');
	}

	/**
	 * Retrieve AMP html
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function getAMPHtml($block)
	{
		$html = '<amp-carousel width="1280" height="970" layout="responsive" type="slides">';

		// Get image blocks in this gallery
		$imageBlocks = $block->blocks;

		$imageUri = array();

		foreach ($imageBlocks as $imageBlock) {

			if (in_array($imageBlock->uid, $imageUri)) {
				continue;
			}

			$media = EB::mediamanager();
			$imagePath = $media->getPath($imageBlock->data->uri);

			$imageData = @getimagesize($imagePath);

			if (!$imageData || empty($imageData[1])) {
				continue;
			}

			$info = 'width="' . $imageData[0] . '" height="' . $imageData[1] . '"';

			$html .= '<figure><amp-img ' . $info . ' src="'. $imageBlock->data->url .'" layout="responsive" ></amp-img></figure>';

			$imageUri[] = $imageBlock->uid;
		}

		$html .= '</amp-carousel>';

		// if the thumbnail has no images, return false
		if (empty($imageUri)) {
			return false;
		}

		return $html;
	}
}
