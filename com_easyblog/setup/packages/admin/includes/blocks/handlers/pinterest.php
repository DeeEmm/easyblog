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

class EasyBlogBlockHandlerPinterest extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fdi fab fa-pinterest';
    public $element = 'figure';

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
     * Renders the pinterest script on the header of the page.
     *
     * @since   5.0
     * @access  public
     */
    public function loadScript()
    {
        static $loaded = false;

        if (!$loaded) {
            $doc = JFactory::getDocument();
            $doc->addScript('//assets.pinterest.com/js/pinit.js');

            $loaded = true;
        }
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
     * @since   5.0
     * @access  public
     */
    public function getHtml($block, $textOnly = false)
    {
        if ($textOnly) {
            return;
        }

        // If the source isn't set ignore this.
        if (!isset($block->data->url) || !$block->data->url) {
            return;
        }

		// changed to load this init script from js part #2189
        // Load the pinterest scripts
        // $this->loadScript();

        $theme = EB::themes();
        $theme->set('block', $block);
        $contents = $theme->output('site/blocks/pinterest');

        return $contents;
    }

    /**
     * Retrieve AMP html
     *
     * @since   5.1
     * @access  public
     */
    public function getAMPHtml($block)
    {
        // If the source isn't set ignore this.
        if (!isset($block->data->url) || !$block->data->url) {
            return;
        }

        $url = $block->data->url;

        $html = '<amp-pinterest width=245 height=330 data-do="embedPin" data-url="' . $url . '"></amp-pinterest>';

        return $html;
    }
}
