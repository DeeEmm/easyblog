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

// Import necessary libraries
require_once(__DIR__ . '/libraries/xmlrpc.php');
require_once(__DIR__ . '/libraries/xmlrpcs.php');
require_once(__DIR__ . '/services.php');

class EasyBlogXmlRpc
{
	/**
	 * Creates a new xmlrpc server
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createServer()
	{
		// Get available methods
		$methods = $this->getServices();

		$server = new xmlrpc_server($methods, false);

		return $server;
	}

	public function getServices()
	{
		global $xmlrpcI4, $xmlrpcInt, $xmlrpcBoolean, $xmlrpcDouble, $xmlrpcString, $xmlrpcDateTime, $xmlrpcBase64, $xmlrpcArray, $xmlrpcStruct, $xmlrpcValue;

		return array
		(
			'metaWeblog.getRecentPosts' => array(
				'function' => 'EasyBlogXMLRPCServices::getRecentPosts',
				'docstring' => 'Returns a list of the most recent posts in the system.',
				'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcInt))
			),
			'metaWeblog.getUserInfo' => array(
				'function' => 'EasyBlogXMLRPCServices::getUserInfo',
				'docstring' => JText::_('Returns information about an author in the system.'),
				'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),

			// Wordpress API
			'wp.getCategories' => array(
				'function' => 'EasyBlogXMLRPCServices::getCategories',
				'docstring' => JText::_('Returns categories listing in the system'),
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),

			'wp.getTags' => array(
				'function' => 'EasyBlogXMLRPCServices::getTags',
				'docstring' => JText::_('Retrieves a list of tags from the system'),
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),

			'wp.newCategory' => array(
				'function' => 'EasyBlogXMLRPCServices::newCategory',
				'docstring' => JText::_('Retrieves a list of tags from the system'),
				'signature' => array(array($xmlrpcArray,$xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcStruct))
			),

			'wp.getPages' => array(
				'function' => 'EasyBlogXMLRPCServices::getPages',
				'docstring' => JText::_('Retrieves a list of tags from the system'),
				'signature' => array(array($xmlrpcArray,$xmlrpcString,$xmlrpcString,$xmlrpcString,$xmlrpcInt))
			),

			'mt.getPostCategories' => array(
				'function' => 'EasyBlogXMLRPCServices::getPostCategories',
				'docstring' => JText::_('Returns categories listing in the system'),
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),


			// For some reasons, marsedit seems to be loading this
			'blogger.getUsersBlogs' => array(
				'function' => 'EasyBlogXMLRPCServices::getUserBlogs',
				'docstring' => JText::_('Returns a list of weblogs to which an author has posting privileges.'),
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),
			'blogger.getUserInfo' => array(
				'function' => 'EasyBlogXMLRPCServices::getUserInfo',
				'docstring' => JText::_('Returns information about an author in the system.'),
				'signature' => array(array($xmlrpcStruct, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),
			'blogger.deletePost' => array(
				'function' => 'EasyBlogXMLRPCServices::deletePost',
				'docstring' => 'Deletes a post.',
				'signature' => array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean))
			),
			'blogger.getTemplate' => array(
				'function' => 'EasyBlogXMLRPCServices::deletePost',
				'docstring' => 'Deletes a post.',
				'signature' => array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean))
			),


			'metaWeblog.deletePost' => array(
				'function' => 'EasyBlogXMLRPCServices::deletePost',
				'docstring' => 'Deletes a post.',
				'signature' => array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcBoolean))
			),
			'metaWeblog.newPost' => array(
				'function' => 'EasyBlogXMLRPCServices::newPost',
				'docstring' => 'Creates a new post, and optionally publishes it.',
				'signature' => array(array($xmlrpcBoolean, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct, $xmlrpcBoolean))
			),
			'metaWeblog.editPost' => array(
				'function' => 'EasyBlogXMLRPCServices::editPost',
				'docstring' => 'Updates the information about an existing post.',
				'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct, $xmlrpcBoolean))
			),
			'metaWeblog.getPost' => array(
				'function' => 'EasyBlogXMLRPCServices::getPost',
				'docstring' => 'Returns information about a specific post.',
				'signature' => array(array($xmlrpcStruct, $xmlrpcInt, $xmlrpcString, $xmlrpcString))
			),
			'metaWeblog.getCategories' => array(
				'function' => 'EasyBlogXMLRPCServices::getCategories',
				'docstring' => 'Returns the list of categories',
				'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString))
			),

			'metaWeblog.newMediaObject' => array(
				'function' => 'EasyBlogXMLRPCServices::uploadMedia',
				'docstring' => 'Uploads media to the blog.',
				'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcStruct))
			),

			// MovableType API
			'mt.setPostCategories' => array(
				'function' => 'EasyBlogXMLRPCServices::setPostCategories',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcArray ))
			),

			'mt.getCategoryList' => array(
				'function' => 'EasyBlogXMLRPCServices::getCategories',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			),

			'mt.publishPost' => array(
				'function' => 'EasyBlogXMLRPCServices::mt_publishPost',
				'docstring' => '',
				'signature' => array(array($xmlrpcArray, $xmlrpcString, $xmlrpcString, $xmlrpcString ))
			)
		);
	}

}
