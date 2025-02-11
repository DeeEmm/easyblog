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

class EasyBlogBlocks extends EasyBlog
{
	/**
	 * Retrieves a list of blocks available on the site.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getAvailableBlocks($type = 'ebd')
	{
		// Instead of using 'static $blocks = null;' because trying to use values of type null, bool, int, float or resource as an array will always generate a notice in PHP 7.4.
		// source: https://www.php.net/manual/de/migration74.incompatible.php#migration74.incompatible.core.non-array-access
		static $blocks = [];

		if (!isset($blocks[$type]) || empty($blocks[$type])) {
			$model = EB::model('Blocks');
			$blocks[$type] = $model->getAvailableBlocks($type);
		}

		return $blocks[$type];
	}

	/**
	 * Retrieves the available block template on the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getBlockTemplates($options = array())
	{
		static $blockTemplates = [];

		$key = serialize($options);

		if (isset($blockTemplates[$key])) {
			return $blockTemplates[$key];
		}

		$model = EB::model('BlockTemplates');
		$templates = $model->getBlockTemplates($options);

		foreach ($templates as $template) {

			$blocks = json_decode($template->data);
			$html = '';

			foreach ($blocks as $block) {
				$html .= $this->renderEditableBlock($block, true, false, $template->id);
			}

			// Remove unused attribute.
			unset($template->data);

			$template->block = $html;

			// echo '===';
			// echo(json_encode($template, JSON_HEX_QUOT | JSON_HEX_TAG));exit;
		}

		$blockTemplates[$key] = $templates;
		return $blockTemplates[$key];
	}

	/**
	 * Retrieves a block handler
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function get(EasyBlogTableBlock $block)
	{
		if (!$block->element) {
			return;
		}

		// Include the abstract library
		require_once(__DIR__ . '/handlers/abstract.php');

		$libraryFile = __DIR__ . '/handlers/' . $block->element . '.php';

		if (!file_exists($libraryFile)) {
			return;
		}

		require_once($libraryFile);

		$class = 'EasyBlogBlockHandler' . ucfirst($block->element);
		$handler = new $class($block);

		return $handler;
	}

	/**
	 * Retrieves a handler provided with the element
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getBlockByType($type)
	{
		static $loaded = null;

		if (is_null($loaded)) {

			$model = EB::model('blocks');
			$blocks = $model->loadAllBlocks();

			if ($blocks) {
				foreach($blocks as $block) {
					$tbl = EB::table('Block');
					$tbl->bind($block);

					$loaded[$block->element] = $tbl;
				}
			}
		}

		// Do not try to render the unavailable block #2813
		if (!isset($loaded[$type])) {
			return;
		}

		return $this->get($loaded[$type]);
	}

	/**
	 * Creates a new block
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function createBlock($type, $data = array(), $props = array())
	{
		// Create block
		$block = (object) array_merge(array('type' => $type), $props);

		// Let block handler fill up the rest of the details
		$handler = $this->getBlockByType($type);
		$block = $handler->updateBlock($block, $data);

		return $block;
	}

	/**
	 * Renders a block html code
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderViewableBlock($block, $stripTags = false, $useRelative = false)
	{
		// Backward compatibility
		$this->legacyBlockCompatibility($block);

		// Get block handler
		$handler = $this->getBlockByType($block->type);

		// Do not try to render the unavailable block on the frontend #2813
		if (!$handler) {
			return;
		}

		// Block handler should be able to manipulate the html output of the block if they want to.
		$blockHtml = $handler->getHtml($block, $stripTags, $useRelative);

		// Render nested blocks
		$blockHtml = $this->renderNestedBlocks(EASYBLOG_BLOCK_MODE_VIEWABLE, $block, $blockHtml, $stripTags, $useRelative);

		// Render block container
		$html = $this->renderBlockContainer(EASYBLOG_BLOCK_MODE_VIEWABLE, $block, $blockHtml);

		return $html;
	}

	/**
	 * helper function to get block html for diff
	 *
	 * @since	5.1
	 * @access	public
	 */
	private function getBlockContent($block)
	{
		$handler = $this->getBlockByType($block->type);

		$html = $handler->getHtml($block);

		// we need to replace &nbsp; or else the html diff lib will break.
		$html = EBString::str_ireplace('&nbsp;', ' ', $html);

		return $html;
	}


	/**
	 * Compare and Renders the diff block
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderDiffBlocks($block, $arrBlocks)
	{

		// ini_set('xdebug.var_display_max_depth', -1);
		// ini_set('xdebug.var_display_max_children', -1);
		// ini_set('xdebug.var_display_max_data', -1);

		$blockHtml = '';
		// Get editable html from block handler
		if (array_key_exists($block->uid, $arrBlocks)) {

			$target = $arrBlocks[$block->uid];
			$targetText = $this->getBlockContent($target);

			$blockText = $this->getBlockContent($block);

			$blockHtml = EB::revisions()->compare($targetText, $blockText);

		} else {

			$blockText = $this->getBlockContent($block);

			$blockHtml = EB::revisions()->compare('', $blockText);
		}

		// for the diff, we need to handle the nesteblock abit different.
		if (isset($block->blocks)) {

			$nestedBlocks = $block->blocks;

			// Go through every nested block
			foreach ($nestedBlocks as $nestedBlock) {

				$nestedBlockHtml = $this->renderDiffBlocks($nestedBlock, $arrBlocks);
				// Replace nested block placeholder with nested block html
				$blockHtml = EBString::str_ireplace('<!--block' . $nestedBlock->uid . '-->', $nestedBlockHtml, $blockHtml);
			}
		}

		// Render block container
		$html = $this->renderBlockContainer(EASYBLOG_BLOCK_MODE_DIFF, $block, $blockHtml);

		// Render block data
		$html .= $this->renderBlockData($block);

		return $html;
	}

	/**
	 * Renders editable block html codes
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderEditableBlock($block, $renderData = true, $postTemplateIsLocked = false, $blockTemplateId = false)
	{
		// Backward compatibility
		$this->legacyBlockCompatibility($block);

		// Get block handler
		$handler = $this->getBlockByType($block->type);

		if (!$handler) {
			return;
		}

		// Get editable html from block handler
		$blockHtml = $handler->getEditableHtml($block);

		// Render nested blocks
		$blockHtml = $this->renderNestedBlocks(EASYBLOG_BLOCK_MODE_EDITABLE, $block, $blockHtml, false, false, $postTemplateIsLocked);

		// Render block container
		$html = $this->renderBlockContainer(EASYBLOG_BLOCK_MODE_EDITABLE, $block, $blockHtml, $postTemplateIsLocked, $blockTemplateId);

		// Render block data
		if ($renderData) {
			$html .= $this->renderBlockData($block, $handler);
		}

		return $html;
	}

	/**
	 * Renders the block container to be used with the composer
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderBlockContainer($mode, $block, $blockHtml, $postTemplateIsLocked = false, $blockTemplateId = false)
	{
		// Block type
		$blockType = 'data-type="' . $block->type . '"';

		// Block style
		$blockStyle = '';

		if (isset($block->style)) {
			$blockStyle = 'style="' . $block->style . '"';
		}

		$blockTemplate = '';
		$blockTitle = '';

		if ($blockTemplateId || isset($block->data->template_id)) {
			$templateId = false;

			// Assigned block template id has higher priority.
			if ($blockTemplateId) {
				$templateId = $blockTemplateId;
			}

			if (!$templateId) {
				$templateId = isset($block->data->template_id) ? $block->data->template_id : 0;
			}

			$template = EB::table('blockTemplates');
			$template->load($templateId);

			if ($template->id) {
				$blockTemplate = 'data-template-id="' . $templateId . '"';
				$blockTitle = 'data-block-title="' . EB::string()->escape($template->title) . '"';
			}
		}

		// Block nest
		$blockNest = '';

		// Is a block that contains nested blocks
		$blockHasNested = '';

		if (isset($block->blocks) && $block->blocks) {
			$blockHasNested = 'has-nested';
		}


		if (isset($block->nested) && $block->nested) {
			$blockNest .= ' is-nested';
		}

		if (isset($block->position)) {
			$blockNest .= ' nest-' . $block->position;
		}

		if (isset($block->isolated) && $block->isolated) {
			$blockNest .= ' is-isolated';
		}

		$blockUid = '';
		if (isset($block->uid) && !$blockTemplateId) {
			$blockUid = 'data-uid="' . $block->uid . '"';
		}

		// Custom id on the wrapper
		$blockCustomId = '';

		if (isset($block->data->custom_id) && $this->config->get('layout_composer_customid') && $block->data->custom_id) {
			$blockCustomId = $block->data->custom_id;
		}

		// Custom css on the wrapper
		$blockCustomCss = '';

		if (isset($block->data->custom_css) && $this->config->get('layout_composer_customcss')) {
			$blockCustomCss = $block->data->custom_css;
		}

		// Block html
		$blockHtml = trim($blockHtml);

		$isLocked = false;

		if ($postTemplateIsLocked) {
			// Actions can't be made on the blocks if the selected post template is locked
			$isLocked = true;
		}

		$tpl = EB::themes();
		$tpl->set('block', $block);
		$tpl->set('blockUid', $blockUid);
		$tpl->set('blockHasNested', $blockHasNested);
		$tpl->set('blockType', $blockType);
		$tpl->set('blockNest', $blockNest);
		$tpl->set('blockStyle', $blockStyle);
		$tpl->set('blockHtml', $blockHtml);
		$tpl->set('blockCustomCss', $blockCustomCss);
		$tpl->set('blockCustomId', $blockCustomId);
		$tpl->set('postTemplateIsLocked', $isLocked);
		$tpl->set('blockTemplate', $blockTemplate);
		$tpl->set('blockTitle', $blockTitle);

		return $tpl->output('site/composer/document/blocks/' . $mode);
	}

	/**
	 * Renders a instant block html code
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderInstantBlock($block, $stripTags = false)
	{
		// Get block handler
		$handler = $this->getBlockByType($block->type);

		// Block handler should be able to manipulate the html output of the block if they want to.
		$blockHtml = $handler->getInstantHtml($block, $stripTags);

		// Render nested blocks
		$blockHtml = $this->renderNestedInstantBlocks($block, $blockHtml);

		return $blockHtml;
	}

	public function renderNestedInstantBlocks($block, $blockHtml, $stripTags = false)
	{
		// If there are nested blocks
		if (isset($block->blocks)) {

			$emptyHtml = empty($blockHtml);

			$nestedBlocks = $block->blocks;

			foreach ($nestedBlocks as $nestedBlock) {
				$blockOutput = EB::blocks()->renderViewableBlock($nestedBlock);

				$instantBlocks = array('video', 'gallery', 'image', 'text', 'heading');

				if (in_array($nestedBlock->type, $instantBlocks)) {
					$blockOutput = EB::blocks()->renderInstantBlock($nestedBlock);
				}

				// Could be a container (section) block
				if ($emptyHtml) {
					$blockHtml .= $blockOutput;
				}

				if (!$emptyHtml) {
					$blockHtml = str_ireplace('<!--block' . $nestedBlock->uid . '-->', $blockOutput, $blockHtml);
				}
			}
		}

		return $blockHtml;
	}

	/**
	 * Renders a instant block html code
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderAMPBlock($block, $stripTags = false)
	{
		// Backward compatibility
		$this->legacyBlockCompatibility($block);

		// Get block handler
		$handler = $this->getBlockByType($block->type);

		// Do not try to render the unavailable block on the AMP page
		if (!$handler) {
			return;
		}

		// Block handler should be able to manipulate the html output of the block if they want to.
		$blockHtml = $handler->getAMPHtml($block, $stripTags);

		// Render nested blocks
		$blockHtml = $this->renderNestedAMPBlocks($block, $blockHtml);

		return $blockHtml;
	}

	public function renderNestedAMPBlocks($block, $blockHtml, $stripTags = false)
	{

		// If there are nested blocks
		if (isset($block->blocks)) {

			$nestedBlocks = $block->blocks;

			foreach ($nestedBlocks as $nestedBlock) {

				$blockOutput = EB::blocks()->renderAMPBlock($nestedBlock);

				$blockHtml = str_ireplace('<!--block' . $nestedBlock->uid . '-->', $blockOutput, $blockHtml);
			}
		}

		return $blockHtml;
	}

	public function renderNestedBlocks($mode, $block, $blockHtml, $stripTags = false, $useRelative = false, $postTemplateIsLocked = false)
	{
		// If there are nested blocks
		if (isset($block->blocks)) {

			$nestedBlocks = $block->blocks;

			// Go through every nested block
			foreach ($nestedBlocks as $nestedBlock) {

				// Get nested block html
				switch ($mode) {

					// case EASYBLOG_BLOCK_MODE_DIFF:
					// 	$nestedBlockHtml = $this->renderDiffBlock($nestedBlock);
					// 	break;

					case EASYBLOG_BLOCK_MODE_VIEWABLE:
						$nestedBlockHtml = $this->renderViewableBlock($nestedBlock, $stripTags, $useRelative);
						break;

					case EASYBLOG_BLOCK_MODE_EDITABLE:
						$nestedBlockHtml = $this->renderEditableBlock($nestedBlock, true, $postTemplateIsLocked);
						break;
				}

				// Replace nested block placeholder with nested block html
				// $blockHtml = EBString::str_ireplace('<!--block' . $nestedBlock->uid . '-->', $nestedBlockHtml, $blockHtml);
				$blockHtml = str_ireplace('<!--block' . $nestedBlock->uid . '-->', $nestedBlockHtml, $blockHtml);
			}
		}

		return $blockHtml;
	}

	/**
	 * Renders the inline block data which can be used by the js later.
	 * The data consists of a textarea with json encoded meta data from the block
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderBlockData($block, $handler = null)
	{
		// There are possibilities where the block needs to manipulate the output before json_encode it. For instance,
		// if the block data contains ", they need to be entities first.
		if ($handler && method_exists($handler, 'normalizeData')) {
			$data = $handler->normalizeData($block->data);

			if ($data) {
				$block->data = $data;
			}
		}

		$out = '<textarea data-block>' . json_encode($block->data, JSON_HEX_QUOT | JSON_HEX_TAG) . '</textarea>';

		return $out;
	}

	/**
	 * Formats blocks in the blog post.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function format(EasyBlogPost &$blog, $blocks, $type = 'list')
	{
		static $_cache = array();

		if (!$blocks) {
			return array();
		}

		// Determines the total number of blocks
		$total = count($blocks);

		// Get the maximum number of blocks
		$max = $this->config->get('composer_truncation_blocks');

		// Determines if content truncation should happen
		if ($type == 'list' && $this->config->get('composer_truncation_enabled') && $max) {
			// Get the total number of blocks
			$blocks = array_splice($blocks, 0, $max);
		}

		// Default read more to false
		$blog->readmore = false;

		// Default contents
		$contents = '';

		foreach ($blocks as $item) {

			// If the read more is present at this point of time, we should skip processing the rest of the blocks
			if ($blog->readmore && $type == 'list') {
				continue;
			}

			// Load from cache
			if (!isset($_cache[$item->type])) {
				$tblElement = EB::table('Block');
				$tblElement->load(array('element' => $item->type));

				$_cache[$item->type] = $tblElement;
			}

			$table = $_cache[$item->type];
			$block = EB::blocks()->get($table);

			$contents .= $block->formatDisplay($item, $blog);
		}

		// If the total is larger than the iterated blocks, we need to display the read more
		if ($total > count($blocks)) {
			$blog->readmore = true;
		}

		return $contents;
	}

	/**
	 * Install new blocks
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function install($file)
	{
		FH::checkToken();

		if (!$file['tmp_name']) {
			$this->setError(Jtext::_('COM_EASYBLOG_BLOCKS_INSTALLER_NO_PACKAGE_FOUND'));
			return false;
		}

		$source = $file['tmp_name'];
		$fileName = md5( $file['name'] . EB::date()->toSql());
		$fileExtension = '_blocks_install.zip';
		$destination = JPATH_ROOT . '/tmp/' . $fileName . $fileExtension;

		// Upload the zip archive
		$state = JFile::upload($source, $destination, false, true);

		if (!$state) {
			$this->setError(JText::_('COM_EASYBLOG_BLOCKS_INSTALLER_ERROR_COPY_FROM_PHP'));

			return false;
		}

		// Extract the zip
		$extracted = dirname($destination) . '/' . $fileName . '_block_install';
		$state = JArchive::extract($destination, $extracted);

		// Delete the zip now so it won't hogged the disk space
		JFile::delete($destination);

		if (!$state) {
			$this->setError(JText::_('COM_EASYBLOG_BLOCKS_INSTALLER_ERROR_EXTRACT'));
			return false;
		}

		// Get the blocks name from the manifest file
		$manifest = JFolder::files($extracted, '.json');
		$manifest = $manifest[0];

		// Read the manifest file
		$blockData = json_decode(file_get_contents($extracted . '/' . $manifest));

		// Check for manifest data
		if (!$blockData || $blockData === false) {
			$this->setError(Jtext::_('COM_EASYBLOG_BLOCKS_INSTALLER_ERROR_READING_MANIFEST_FILE'));
			return false;
		}

		// Check all the necessary block data
		if (!isset($blockData->element) || !isset($blockData->group) || !isset($blockData->title)) {
			$this->setError(JText::_('COM_EASYBLOG_BLOCKS_INSTALLER_ERROR_READING_MANIFEST_FILE'));
			return false;
		}

		// All checked. Let's install the block now
		$table = EB::table('Block');
		$table->load(array('element' => $blockData->element));

		// Bind block data
		$table->bind($blockData);

		// Save the block
		$table->store();

		// Get all files
		$files = JFolder::files( $extracted , '.' , false , true );

		// Get all folders
		$folders = JFolder::folders($extracted, '.', false, true);

		// Target folder
		$targetFolder = EBLOG_THEMES . '/wireframe/composer/blocks/handlers';

		// Copy the folders away
		foreach ($folders as $folder) {

			$folderName = basename($folder);
			$targetFolder = $targetFolder . '/' . $folderName;

			JFolder::copy($folder, $targetFolder, '', true);
		}

		// Build designation path for each file
		$blocks = array(
			'json' => EBLOG_ADMIN_ROOT . '/defaults/blocks',
			'php' => EBLOG_ADMIN_INCLUDES . '/blocks/handlers',
			'js' => EBLOG_SCRIPTS . '/composer/blocks/handlers'
		);

		// Copy all files to designated destination
		foreach ($files as $block) {

			// Get file extension
			$ext = JFile::getExt($block);
			$filename = basename($block);

			JFile::copy($block, $blocks[$ext] . '/' . $filename);
		}

		// Delete tmp folder
		JFolder::delete($extracted);

		return true;
	}

	/**
	 * Backward compatibility from note to alert, this changes since 6.0.3
	 *
	 * @since	6.0.4
	 * @access	public
	 */
	public function legacyBlockCompatibility($block)
	{
		$backwardBlocks = [
			'note' => 'alerts'
		];

		// Backward compatibility
		if (in_array($block->type, array_keys($backwardBlocks))) {
			$block->type = $backwardBlocks[$block->type];
		}
	}
}
