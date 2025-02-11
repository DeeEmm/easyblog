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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_CATEGORIES_EDIT_GENERAL', 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_DETAILS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_NAME', 'catname'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'title', $category->title, 'catname'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_ALIAS', 'alias'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.text', 'alias', $category->alias, 'alias'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORY_PARENT_CATEGORY', 'parent_id'); ?>

					<div class="col-md-7">
						<?php echo $parentList; ?>
					</div>

					<input type="hidden" name="oriParentId" value="<?php echo $category->parent_id;?>" />
				</div>

				<?php if ($this->config->get('main_multi_language')) { ?>
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_LANGUAGE', 'language'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.languages', 'language', $category->language); ?>
					</div>
				</div>
				<?php } ?>


				<?php if ($this->config->get('layout_categoryavatar', true)){ ?>
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_EDIT_AVATAR', 'avatar'); ?>

					<div class="col-md-7">
						<?php if (!empty($category->avatar)) { ?>
							<div class="eb-avatar-wrap" data-category-avatar-image>
								<div class="eb-avatar-wrap__remove">
									<a href="javascript:void(0);" data-id="<?php echo $category->id;?>" data-category-avatar-remove-button>
										<i class="fdi fa fa-times"></i>
									</a>
								</div>
								<img class="img-rounded" src="<?php echo $category->getAvatar(); ?>" width="60" height="60" />
								<br/>
								<br/>
							</div>
						<?php }?>

						<?php if ($this->acl->get('upload_cavatar')) {?>
							<input id="file-upload" type="file" name="Filedata" size="33"/>
						<?php } ?>
					</div>
				</div>
				<?php } ?>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_PUBLISHED', 'published'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'published', $category->published); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_AUTOPOST', 'autopost'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'autopost', $category->autopost); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORY_OWNER', 'created_by'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.user', 'created_by', $category->created_by); ?>
					</div>
				</div>

				<div class="form-group" style="display: block">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_DESCRIPTION', 'description'); ?>

					<div class="col-md-12">
						<?php if (FH::isJoomla5()) { ?>
							<?php echo $editor->display('description', $category->get( 'description') , '99%', '200', '10', '10', ['image', 'readmore', 'pagebreak'], '', '', 'com_easyblog', []); ?>

							<?php } else { ?>
								<?php echo $editor->display('description', $category->get( 'description') , '99%', '200', '10', '10', ['image', 'readmore', 'pagebreak'], [], 'com_easyblog'); ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_AUTO_REPOSTING'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EB_AUTO_REPOSTING_SOCIAL', 'repost_autoposting'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.toggler', 'repost_autoposting', $category->repost_autoposting, '', 'data-repost-social'); ?>
					</div>
				</div>

				<div class="form-group <?php echo $category->repost_autoposting ? '' : 'hide';?>" data-repost-social-days>
					<?php echo $this->fd->html('form.label', 'COM_EB_AUTO_REPOSTING_SOCIAL_INTERVAL', 'repost_autoposting_interval'); ?>

					<div class="col-md-7">
						<div class="row">
							<div class="col-sm-5">
								<div class="input-group">
									<?php echo $this->fd->html('form.text', 'repost_autoposting_interval', $category->repost_autoposting_interval, null, array('class' => 'form-control text-center')); ?>

									<span class="input-group-addon"><?php echo JText::_('COM_EB_DAYS'); ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EB_CATEGORIES_DEFAULT_POST_COVER', 'COM_EB_CATEGORIES_DEFAULT_POST_COVER_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EB_CATEGORIES_PARAM_ENTER_COVER', 'post_cover'); ?>

					<div class="col-md-7">
						<div class="col-md-7">
							<?php $cover = $category->getDefaultPostCover(true, true); ?>
							<?php if ($cover) { ?>
								<div class="eb-img-holder" data-category-post-cover-image>
									<div class="eb-img-holder__remove">
										<a href="javascript:void(0);" data-id="<?php echo $category->id;?>" data-category-post-cover-remove-button>
											<i class="fdi fa fa-times"></i>
										</a>
									</div>
									<img class="img-rounded" src="<?php echo $cover; ?>" width="240" height="240" />
									<br/>
									<br/>
								</div>
							<?php } ?>

							<input id="file-upload" type="file" name="post_cover" size="33"/>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_CATEGORIES_DEFAULT_TAGS', 'COM_EASYBLOG_CATEGORIES_DEFAULT_TAGS_INFO'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_PARAM_ENTER_TAGS', 'params[tags]'); ?>

					<div class="col-md-7">
						<textarea name="params[tags]" id="params[tags]" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_PARAM_TAGS_PLACEHOLDER');?>"><?php echo $params->get('tags');?></textarea>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_CATEGORIES_EDIT_CUSTOMFIELDS', 'COM_EASYBLOG_CATEGORIES_EDIT_CUSTOMFIELDS_DESC'); ?>

			<div class="panel-body">
				<div class="mb-20">
					<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CUSTOMFIELDS_PERMISSIONS_DESC');?>
				</div>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_SELECT_FIELD_GROUP', 'field_group'); ?>

					<div class="col-md-7">
						<?php echo $this->fd->html('form.dropdown', 'field_group', $category->getCustomFieldGroup()->group_id, $customFieldGroups); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_EASYBLOG_CATEGORIES_EDIT_CUSTOM_TEMPLATE'); ?>

			<div class="panel-body">
				<p><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CUSTOM_TEMPLATE_INFO');?></p>

				<p><?php echo JText::sprintf('COM_EASYBLOG_CATEGORIES_TEMPLATE_INFO', $templateDisplay);?></p>

				<div class="form-group">
					<?php echo $this->fd->html('form.label', 'COM_EASYBLOG_CATEGORIES_EDIT_SELECT_CUSTOM_TEMPLATE', 'theme'); ?>

					<div class="col-md-7">
						<?php if ($categoryThemes) { ?>
							<?php echo $this->fd->html('form.dropdown', 'theme', $category->theme, $categoryThemes); ?>
						<?php } else { ?>
							<span class="text-warning"><?php echo JText::_('COM_EASYBLOG_SELECT_CUSTOM_TEMPLATE_EMPTY');?></span>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
