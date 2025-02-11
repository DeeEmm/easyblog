<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogMailboxMapAbstract extends EasyBlog
{
    /**
     * Main constructor
     *
     * @since   1.3
     * @access  public
     * @param   EasyBlogTableBlog   The blog table object
     * @param   int     The author id of the blog post.
     */
    public function __construct(EasyBlogPost &$blog, $authorId)
    {
        // Set the config
        $this->config = EB::config();

        $this->blog = $blog;

        // Get the author id
        $this->authorId = JFactory::getUser($authorId)->id;

        // Get the default storage path
        $path = rtrim($this->config->get('main_image_path'), '/');

        // Get the relative path to the user's folder
        $this->relativePath = $path . '/' . $authorId;

        // Get the absolute path to the user's uploads
        $this->absolutePath = JPATH_ROOT . '/' . $path . '/' . $authorId;
        $this->absolutePath = JPath::clean($this->absolutePath);

        // Ensure that the user's folder exists
        if (!JFolder::exists($this->absolutePath)) {
            JFolder::create($this->absolutePath);
        }

        // Get the absolute url to the user's folder
        $this->absoluteUrl = rtrim(JURI::root(), '/') . '/' . $path . '/' . $authorId;

        // Get the temporary folder
        $this->tmp = JPATH_ROOT . '/tmp';
    }
}
