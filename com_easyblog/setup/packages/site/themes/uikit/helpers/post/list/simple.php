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
?>
<li>
	<div class="uk-grid-small" uk-grid>
		<div class="uk-width-expand" uk-leader>
			<a href="<?php echo EB::_('index.php?option=com_easyblog&view=entry&id=' . $post->id);?>">
				<span uk-icon="icon: file-text" class="uk-margin-small-right"></span>
				<span><?php echo $post->title;?></span>
			</a>
		</div>
		<div>
			<time ><?php echo $post->getDisplayDate($this->config->get('blogger_post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC3'));?></time>
		</div>
	</div>
</li>
