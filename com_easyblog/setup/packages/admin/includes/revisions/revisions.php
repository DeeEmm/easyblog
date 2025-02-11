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

require(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/htmldiff/html_diff.php');

class EasyBlogRevisions extends EasyBlog
{
	/**
	 * Compares 2 different revision the html content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function compare($content1, $content2)
	{
		// let replace the <!--blockx--> into <block uid="x"></block> so that the diff lib understand
		$pattern = '/\<!--block(\d+)--\>/ims';
		$replace = '<block uid="$1"></block>';

		$nContent1 = preg_replace($pattern, $replace, $content1);
		$nContent2 = preg_replace($pattern, $replace, $content2);

		// lets do the diff here
		$newText = html_diff($nContent1, $nContent2, true);

		// now we need convert back the <block uid="x"></block> back to <!--blockx--> so that
		// the block lib know how to replace the nestedBlocks
		$pattern = '/\<block\suid="(\d+)"\>\<\/block\>/ims';
		$replace = '<!--block$1-->';
		$oriText = preg_replace($pattern, $replace, $newText);

		return $oriText;
	}

	public function compareTest(EasyBlogTableRevision $source, EasyBlogTableRevision $target)
	{
		$sourceDoc = $source->getDocument();
		$targetDoc = $target->getDocument();

		$sourceData = $source->getContent();
		$targetData = $target->getContent();

		// echo $sourceDocument->getContent();
		// exit;

		echo html_diff($targetDoc->getContent(), $sourceDoc->getContent(), true);
		exit;

		echo $targetDocument->getContent();
		exit;

		echo html_diff($sourceDoc->getContent(), $targetDoc->getContent(), true);
		exit;

		echo html_diff($targetDocument->getContent(), $sourceDocument->getContent(), true);
		exit;
		echo html_diff($sourceData->intro, $targetData->intro);
		exit;

		echo html_diff($targetDocument->getContent(), $sourceDocument->getContent());
		exit;

		$sourceBlocks = $sourceDocument->getBlocks();
		$targetBlocks = $targetDocument->getBlocks();

		$diff = html_diff($sourceDocument->getContent(), $targetDocument->getContent(), true);
		echo $diff;
		exit;

		return $diff;
	}


	public function diff()
	{

		// dump($sourceDocument);
		// Source = 3 ; target = 2
		$sourceData	= json_decode($source->content);
		$targetData	= json_decode($target->content);

		$diff = array();
		$diff['title']	=	html_diff($sourceData->title,$targetData->title, true);
		$diff['permalink']	=	html_diff($sourceData->permalink,$targetData->permalink, true);
		$diff['intro']	=	html_diff($sourceData->intro,$targetData->intro);
		// $diff['state']	=	html_diff($sourceData->state,$targetData->state, true);

		// TODO: get category title

		$sourceCategory = EB::table('Category');
		$sourceCategory->load($sourceData->category_id);
		$diff['source_category_title']	=	$sourceCategory->title;

		$targetCategory = EB::table('Category');
		$targetCategory->load($targetData->category_id);
		$diff['diff_category_title']	=	$targetCategory->title;

		return $diff;

	}
}
