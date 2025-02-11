<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<li data-team-member
    data-id="<?php echo $user->id;?>"
    data-teamid="<?php echo $team->id;?>"
    id="members-<?php echo $user->id;?>"
    class="members-item">
    <input type="hidden" name="members[]" value="<?php echo $user->id;?>" />

    <span class="<?php echo $isAdmin ? ' admin-member' : ' normal-member';?>">
        <a class="remove_item" href="javascript:void(0);" data-remove-member><i class="fdi fa fa-times-circle"></i></a>

        <span><?php echo $user->name;?></span>

        <?php if ($isAdmin) { ?>
            <a href="javascript:void(0);" class="remove_admin" data-remove-admin><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_REMOVE_ADMIN');?></a>
        <?php } else { ?>
            <a href="javascript:void(0);" class="set_admin" data-set-admin><?php echo JText::_('COM_EASYBLOG_TEAMBLOGS_SET_ADMIN');?></a>
        <?php } ?>
    </span>
</li>
