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

class EasyBlogBlockHandlerTabs extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fdi far fa-folder';
    public $nestable = true;
    public $element = 'none';

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
        $data = (object) array(
            'tabs' => array(
                (object) array(
                    'default' => 1,
                    'content' => JText::_('COM_EASYBLOG_BLOCK_TABS_DEFAULT_TITLE')
                )
            )
        );

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
		$tabTitle = JText::_('COM_EASYBLOG_BLOCK_TABS_DEFAULT_TITLE');

		$theme = EB::themes();
		$theme->set('tabTitle', $tabTitle);
		$theme->set('block', $this);
		$theme->set('data', $meta->data);
		$theme->set('params', $this->table->getParams());

		return $theme->output('site/composer/blocks/handlers/' . $this->type . '/fieldset');
	}

    /**
     * Validates if the block contains any contents
     *
     * @since   5.0
     * @access  public
     */
    public function validate($block)
    {

        $blockLib = EB::blocks();
        $content = $blockLib->renderViewableBlock($block, true);

        // convert html entities back to it string. e.g. &nbsp; back to empty space
        $content = html_entity_decode($content);

        // strip html tags to precise length count.
        $content = strip_tags($content);

        // remove any blank space.
        $content = trim($content);

        // get content length
        $contentLength = EBString::strlen($content);
        if ($contentLength > 0) {
            return true;
        } else {
            return false;
        }
    }
}
