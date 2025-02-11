<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form name="edit-comment" id="edit-comment" action="<?php echo EBR::_('index.php?option=com_easyblog&controller=entry&task=updateComment');?>" method="post">
<ul class="reset-ul list-form tight">
<?php if( $system->config->get( 'comment_show_title') ){ ?>
	<li>
		<label class="label" for="title"><?php echo JText::_('COM_EASYBLOG_TITLE'); ?> <?php if($system->config->get('comment_requiretitle', 0)){ ?><small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small><?php } ?></label>
		<div>
			<input class="inputbox" type="text" id="title" name="title" size="45" value="<?php echo $this->escape( $comment->title ); ?>" />
		</div>
	</li>
<?php } ?>
	<li>
		<label class="label" for="comment"><?php echo JText::_('COM_EASYBLOG_COMMENT'); ?> <small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small></label>
		<div>
			<textarea id="comment" name="comment" class="inputbox" cols="50" rows="5"><?php echo $comment->comment; ?></textarea>
		</div>
	</li>
</ul>
<input class="inputbox" type="hidden" name="commentId" value="<?php echo $comment->id; ?>" />
<input class="inputbox" type="hidden" name="controller" value="entry" />
<input class="inputbox" type="hidden" name="task" value="updateComment" />
<?php echo JHTML::_( 'form.token' ); ?>
<div class="dialog-actions clearfix">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?>" class="button dialog-cancel" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="submit" value="<?php echo JText::_('COM_EASYBLOG_PROCEED_BUTTON');?>" class="button dialog-submit" id="edialog-submit" name="edialog-submit" style="margin-left: 3px !important;" />
</div>
</form>
