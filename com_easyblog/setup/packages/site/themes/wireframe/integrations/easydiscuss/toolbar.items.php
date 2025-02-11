<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<li>
	<a href="<?php echo EDR::_('view=forums');?>"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_FORUMS');?></a>
</li>

<li>
	<a href="<?php echo EDR::_('view=index');?>"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_RECENT');?></a>
</li>

<li>
	<a href="<?php echo EDR::_('view=categories');?>"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES');?></a>
</li>

<li>
	<a href="<?php echo EDR::_('view=tags');?>"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS');?></a>
</li>

<?php if ($edConfig->get('main_favorite')) { ?>
<li>
	<a href="<?php echo EDR::_('view=favourites');?>"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES');?></a>
</li>
<?php } ?>

<li>
	<a href="<?php echo EDR::_('view=mypost');?>"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_POSTS');?></a>
</li>

<?php if (ED::isModerator()) { ?>
<li>
	<a href="<?php echo EDR::_('view=assigned');?>"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_ASSIGNED_POSTS');?></a>
</li>
<?php } ?>
