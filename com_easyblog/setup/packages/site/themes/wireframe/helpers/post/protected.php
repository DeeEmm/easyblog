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
<?php if ($idRequired) { ?>
<div id="eb">
<?php } ?>

	<div class="eb-post-protected">
		<form class="eb-form-protect" method="POST" action="<?php echo JRoute::_('index.php');?>">
			<h4>
				<i class="fdi fa fa-lock text-muted"></i>&nbsp;
				<?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_AUTHENTICATION_REQUIRE'); ?>
			</h4>
			<p>
				<?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_AUTHENTICATION_INSTRUCTION'); ?>
			</p>
			<div class="form-inline">
				<div class="input-group">
					<input type="password" class="form-control" name="blogpassword_<?php echo $post->id; ?>"  id="blogpassword_<?php echo $post->id; ?>"  placeholder="<?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECT_PLACEHOLDER', true);?>" />
					<span class="input-group-btn">
						<button class="btn btn-default">
							<i class="fdi fa fa-lock"></i> <?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_READ');?>
						</button>
					</span>
				</div>

				<input type="hidden" name="option" value="com_easyblog" />
				<input type="hidden" name="task" value="posts.authorize" />
				<input type="hidden" name="id" value="<?php echo $post->id; ?>" />
			</div>
		</form>
	</div>

<?php if ($idRequired) { ?>
</div>
<?php } ?>