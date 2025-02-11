<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views.php');

class EasyBlogViewMigrators extends EasyBlogAdminView
{
	var $err = null;

	public function migrateArticle()
	{
		$component = $this->input->get('component', '', 'string');

		if (!$component) {
			die('Invalid migration');
		}

		if ($component == 'xml_wordpress') {
			$fileName = $this->input->get('xmlFile', '', 'string');
			$authorId = $this->input->get('authorId', '', 'int');
			$firstimagecover = $this->input->get('firstimagecover', 0, 'int');
			$wpimagepathfrom = $this->input->get('wpimagepathfrom', '', 'default');
			$wpimagepathto = $this->input->get('wpimagepathto', '', 'default');
			$wraptext = $this->input->get('wraptext', 0, 'int');

			$obj = new stdClass();
			$obj->firstimagecover = $firstimagecover;
			$obj->wpimagepathfrom = $wpimagepathfrom;
			$obj->wpimagepathto = $wpimagepathto;
			$obj->wraptext = $wraptext;

			$migrator = EB::migrator()->getAdapter('wordpress_xml');
			$migrator->migrate($fileName, $authorId, $obj);

			return;
		}

		if ($component == 'com_blog') {
			$migrateComment	= $this->input->get('migrateComment', 0, 'int');
			$migrateImage	= $this->input->get('migrateImage', 0, 'int');
			$imagePath		= $this->input->get('imagepath', '', 'string');

			$migrator = EB::migrator()->getAdapter('smartblog');

			$migrator->migrate($migrateComment, $migrateImage, $imagePath);

			return;
		}

		if ($component == 'com_content') {
			$authorId 	= $this->input->get('authorId', '', 'int');
			$catId = $this->input->get('categoryId', 0, 'int');
			$stateId = $this->input->get('state', '', 'default');
			$ebcategory = $this->input->get('ebcategory', 0, 'int');
			$myblogSection = $this->input->get('myblog', '', 'int');
			$jomcomment = $this->input->get('migrateComment', 0, 'int');
			$start		= 1;
			$sectionId	= '';

			$migrator = EB::migrator()->getAdapter('content');

			$migrator->migrate($authorId, $stateId, $catId, $sectionId, $ebcategory, $myblogSection , $jomcomment);

			return;
		}

		if ($component == 'com_wordpress') {
			$wpBlogId	= $this->input->get('blogId', '', 'int');

			$migrator = EB::migrator()->getAdapter('wordpress');
			$migrator->migrate( $wpBlogId );
			return;
		}


		if ($component == 'xml_blogger') {
			$fileName 	= $this->input->get('xmlFile', '', 'string');
			$authorId 	= $this->input->get('authorId', '', 'int');
			$categoryId 	= $this->input->get('categoryId', '', 'int');

			$migrator = EB::migrator()->getAdapter('blogger_xml');

			$migrator->migrate( $fileName, $authorId, $categoryId );
			return;
		}

		if ($component == 'com_k2') {
			$migrateComment	= $this->input->get('migrateComment', '', 'bool');
			$migrateAll		= $this->input->get('migrateAll', '', 'bool');
			$catId	= $this->input->get('categoryId', 0, 'int');

			$migrator = EB::migrator()->getAdapter('k2');
			$migrator->migrate($migrateComment, $migrateAll, $catId);

			return;
		}

		if ($component == 'com_zoo') {
			$applicationId 	= $this->input->get('applicationId', '', 'int');

			$migrator = EB::migrator()->getAdapter('zoo');
			$migrator->migrate($applicationId);
			return;
		}
	}
}

