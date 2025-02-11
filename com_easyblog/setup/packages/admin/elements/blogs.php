<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldModal_Blogs extends JFormField
{

	protected $type = 'Modal_Blogs';

	protected function getInput()
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

		if (!EB::isFoundryEnabled()) {
			return;
		}

		EBCompat::renderModalLibrary();

		$app = JFactory::getApplication();

		if ($this->value == '0') {
			$this->value = JText::_( 'Select an entry' );
		} else {
			$post = EB::post($this->value);
			$value = $blog->title;
		}

		ob_start();
		?>
		<script type="text/javascript">
		function insertBlog(id, name, alias) {
			document.id('<?php echo $this->id;?>_id' ).value 	= id;
			document.id('<?php echo $this->id;?>_name' ).value	= name;
			SqueezeBox.close();
		}
		</script>

		<span class="input-append">
			<input type="text" id="<?php echo $this->id;?>_name" readonly="readonly" value="<?php echo $value; ?>" disabled="disabled" class="input-large disabled" />
			<a rel="{handler: 'iframe', size: {x: 750, y: 475}}" href="<?php echo JRoute::_('index.php?option=com_easyblog&view=blogs&tmpl=component&browse=1&browsefunction=insertBlog' );?>" class="modal btn btn-primary">
				<i class="icon-file"></i> <?php echo JText::_('COM_EASYBLOG_MENU_OPTIONS_SELECT_POST'); ?>
			</a>
		</span>

		<input type="hidden" id="<?php echo $this->id;?>_id" name="<?php echo $this->name;?>" value="<?php echo $this->value;?>" />

		<?php
		$output		= ob_get_contents();
		ob_end_clean();

		return $output;
	}
}
