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

class EasyBlogBlockHandlerSoundcloud extends EasyBlogBlockHandlerAbstract
{
    public $icon = 'fdi fab fa-soundcloud';
    public $nestable = false;
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

    /**
     * We do not want to populate any html codes because the JS side will manipulate this data by creating
     * the necessary iframe and overlay.
     *
     * @since   5.0
     * @access  public
     */
    public function getEditableHtml($data)
    {
        return;
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
     * @since   5.0
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
        $contents = $theme->output('site/blocks/soundcloud');

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
        if (!isset($block->data->embed) || !$block->data->embed) {
            return;
        }

        // Try to get the src from the iframe
        preg_match('/src="([^"]+)"/', $block->data->embed, $src);

        // Parse the Url
        $parts = parse_url($src[1]);

        if (!isset($parts['query'])) {
            return;
        }

        // Get the query from the URL
        parse_str($parts['query'], $query);

        $url = $query['url'];

        $regex = '#http://api.soundcloud.com/tracks/([0-9]+)#s';

        if (preg_match($regex, $url, $match)) {

            $html = '<amp-soundcloud height=657 layout="fixed-height" data-trackid="' . $match[1] . '" data-visual="true"></amp-soundcloud>';

            return $html;
        }

        return;
    }
}
