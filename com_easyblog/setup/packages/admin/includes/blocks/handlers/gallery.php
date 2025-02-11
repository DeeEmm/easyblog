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

class EasyBlogBlockHandlerGallery extends EasyBlogBlockHandlerAbstract
{
	public $icon = 'fdi fa fa-image';
	public $element = 'gallery';

	public function meta()
	{
		static $meta;
		if (isset($meta)) return $meta;

		$meta = parent::meta();

		// We do not want to display the font attributes and font styles
		$meta->properties['fonts'] = false;

		$template = EB::themes();
		$meta->galleryContainer = $template->output('site/composer/blocks/handlers/gallery/container');
		$meta->galleryItem = $template->output('site/composer/blocks/handlers/gallery/item');
		$meta->galleryPlaceholder = $template->output('site/composer/blocks/handlers/gallery/placeholder');
		$meta->galleryMenuItem = $template->output('site/composer/blocks/handlers/gallery/menu_item');
		$meta->galleryListItem = $template->output('site/composer/blocks/handlers/gallery/list_item');

		return $meta;
	}

	public function data()
	{
		$config = EB::config();
		$data = (object) [];
		$data->strategy = "fill";
		$data->ratio = '16:9';
		$data->items = [];

		// Workaround to store arrays
		$data->itemsKeyArray = [];
		$data->itemsArray = [];
		$data->primary = null;
		$data->imageVariation = $config->get('main_media_variation');

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
	 * Retrieve Instant article html
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

		// if the gallery has no images, return false
		if (empty($imageUri)) {
			return false;
		}

		return $html;
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
				'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_WIDE'),
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
				'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_NORMAL'),
				'caption'   => '4:3',
				'value'     => '4:3',
				'padding'   => '75%',
				'classname' => 'ar-photo'
			],
			[
				'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_SQUARE'),
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
	 * Retrieve the html to be rendered during post edit
	 *
	 * @since   6.0
	 * @access  public
	 */
	public function getEditableHtml($block)
	{
		if ($block->type == 'gallery') {

			$originalContent = $block->html;
			$editableHtmlContent = $block->editableHtml;

			// Determine whether this block is EB5 gallery or not
			// EB5 gallery doesn't support swipe effect
			$isLegacy = EBString::strpos($originalContent, 'data-eb-gallery-container') === false ? true : false;

			if ($isLegacy) {
				$pattern = '/<div class="eb-gallery-viewport"[\s\S]style="[left|right]+:\s-(.*?)%;">/i';
				preg_match($pattern, $editableHtmlContent, $matches);

				if (isset($matches[1]) && $matches[1]) {
					$isLegacyGallery = true;
					$initialSlideNum = $matches[1] / 100;

					// add the image primary state into content
					if ($initialSlideNum) {
						$initialSlideAttribute = 'data-eb-gallery-initial-slide="' . $initialSlideNum . '">';
						$block->editableHtml = str_replace($matches[0], '<div class="eb-gallery-viewport" ' . $initialSlideAttribute, $editableHtmlContent);
					}
				}

				// Manually insert for those swiper js required dom
				$block->editableHtml = str_replace('<div class="eb-gallery-stage"', '<div class="eb-gallery-stage swiper-container" data-eb-gallery-container', $block->editableHtml);
				$block->editableHtml = str_replace('<div class="eb-gallery-viewport"', '<div class="eb-gallery-viewport swiper-wrapper"', $block->editableHtml);
				$block->editableHtml = str_replace('<div class="eb-gallery-item', '<div class="eb-gallery-item swiper-slide', $block->editableHtml);
			}

			// TODO: need to understand how the system store the editableHtml content so that no need to do this checking
			if (!$isLegacy) {
				// find this gallery block whether have get any image for primary
				$pattern = '/data-eb-gallery-initial-slide="([^"]*)"/';

				preg_match($pattern, $originalContent, $matches);

				if (isset($matches[1]) && $matches[1] && $editableHtmlContent) {
					$initialSlideIndexAttr = $matches[0];
					$pattern = '/<div class=\"eb-gallery-viewport swiper-wrapper\".*>/i';

					// manually add this initial slide index data attribute into editableHTML content
					preg_match($pattern, $editableHtmlContent, $matches);

					if (isset($matches[0]) && $matches[0]) {
						$block->editableHtml = str_replace('<div class="eb-gallery-viewport swiper-wrapper"', '<div class="eb-gallery-viewport swiper-wrapper" ' . $initialSlideIndexAttr, $editableHtmlContent);
					}
				}
			}

			// replace fa icon.
			$block->editableHtml = EB::string()->patchContent($block->editableHtml);
		}

		return $block->editableHtml;
	}
}
