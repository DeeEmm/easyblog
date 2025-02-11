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
<div class="eb-composer-fieldset pl-0 pr-0 pt-0" data-name="usertags">
	<div data-form-user-wrapper>
		<div class="eb-composer-tags" data-eb-usertags data-eb-form-users-readonly data-eb-usertags-max="<?php echo $maxUsers;?>" data-eb-usertags-fieldName="<?php echo $name;?>">
			<div class="eb-composer-textboxlist o-form-control" data-eb-usertags-textboxlist>
				<input type="text" class="textboxlist-textField"
					data-textboxlist-textField
					placeholder="<?php echo JText::_('COM_EB_USERTAGS_SELECT_USERS');?>"
					autocomplete="off" />
			</div>
			<div class="eb-composer-tags-suggestions is-empty" data-eb-usertags-suggestions>
				<div class="eb-composer-tags-selection" data-eb-usertags-selection>
					<s></s>
					<small class="empty-tags"><?php echo JText::_('COM_EB_NO_USERS_AVAILABLE');?></small>
					<div class="eb-composer-tags-selection-itemgroup" data-eb-usertags-selection-itemgroup></div>
				</div>
				<div class="eb-composer-tags-actions">
					<small class="pull-left eb-composer-tags-toggle" data-eb-usertags-toggle-button>
						<i class="fdi fa fa-users"></i>
						<span>
							<span data-eb-usertags-count><?php echo $userTagCount;?></span><span><?php echo '/' . $maxUsers;?></span>
						</span>
					</small>
				</div>
			</div>

			<input type="hidden" name="users" value="" data-eb-users />
		</div>
		<textarea style="display: none;" data-eb-usertags-jsondata><?php echo json_encode($usertags);?></textarea>
	</div>
</div>

<div class="hide" data-usertags-template>
	<div class="textboxlist-item[%== (this.locked) ? ' is-locked' : '' %]" data-textboxlist-item>
		<span class="textboxlist-itemContent" data-textboxlist-itemContent>[%== html %]</span>
		[% if (!this.locked) { %]
		<div class="textboxlist-itemRemoveButton" data-textboxlist-itemRemoveButton>
			<i class="fdi fa fa-times"></i>
		</div>
		[% } else { %]
			<i class="fdi fa fa-lock"></i>
		[% } %]
	</div>
</div>
