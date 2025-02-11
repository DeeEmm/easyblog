<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-mod mod-easyblogteamblogs<?php echo $modules->getWrapperClass();?>" data-eb-module-teamblogs>
    <?php if ($teams) { ?>
        <?php foreach ($teams as $team) { ?>
            <div class="mod-item mod-table cell-top">
                <div class="mod-cell cell-tight pr-10">
                    <a class="mod-avatar" href="<?php echo $team->getPermalink();?>">
                        <img src="<?php echo $team->getAvatar();?>" alt="<?php echo EB::string()->escape($team->title);?>" />
                    </a>
                </div>
                <div class="mod-cell">
                    <h3 class="mod-title">
                        <a href="<?php echo $team->getPermalink();?>"><?php echo $team->title;?></a>
                    </h3>
                    <div>
                        <?php echo $team->getDescription();?>
                    </div>
                    <div class="mod-meta mod-muted">
                        <div>
                           <?php echo JText::sprintf('MOD_EASYBLOGTEAMBLOGS_MEMBERS', $team->getMembersCount());?>
                        </div>
                    </div>
                </div>
            </div>
	   <?php } ?>
    <?php } ?>
</div>
