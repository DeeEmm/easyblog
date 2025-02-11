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
<input type="hidden" name="option" value="<?php echo $this->fd->getComponentName();?>" />
<input type="hidden" name="<?php echo $token;?>" value="1" />

<input type="hidden" name="task" value="<?php echo $task;?>" data-fd-table-task="<?php echo $this->fd->getName();?>" />
<input type="hidden" name="boxchecked" value="0" data-fd-table-checked="<?php echo $this->fd->getName();?>"  />

<?php if ($view) { ?>
<input type="hidden" name="view" value="<?php echo $view;?>" data-fd-table-view="<?php echo $this->fd->getName();?>" />
<?php } ?>

<?php if ($controller) { ?>
<input type="hidden" name="controller" value="<?php echo $controller;?>" data-fd-table-controller="<?php echo $this->fd->getName();?>" />
<?php } ?>
