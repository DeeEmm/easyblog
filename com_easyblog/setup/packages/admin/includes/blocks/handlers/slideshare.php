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

class EasyBlogBlockHandlerSlideshare extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fdi fab fa-slideshare';
    public $element = 'embedded';

    public function meta()
    {
        static $meta;

        if (isset($meta)) {
            return $meta;
        }

        $meta = parent::meta();

        // We do not want to display the font attributes and font styles
        $meta->properties['fonts'] = false;

        return $meta;
    }

    public function data()
    {
        $data = (object) array();

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
        // if no url specified, return false.
        if (!isset($block->data->url) || !$block->data->url) {
            return false;
        }

        return true;
    }

    /**
     * Standard method to format the output for displaying purposes
     *
     * @since   4.0
     * @access  public
     */
    public function getHtml($block, $textOnly = false)
    {
        if ($textOnly) {
            return;
        }

        // If the source isn't set ignore this.
        if (!isset($block->data->embed) || !$block->data->embed) {
            return;
        }

        $theme = EB::themes();
        $theme->set('block', $block);
        $contents = $theme->output('site/blocks/slideshare');

        return $contents;
    }
}
