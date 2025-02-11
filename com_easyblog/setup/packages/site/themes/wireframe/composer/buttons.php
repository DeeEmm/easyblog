<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($this->acl->get('manage_pending') && $isPending) { ?>
<button type="button" id="reject_post_button" onclick="eblog.editor.reject( '<?php echo $draft->id; ?>' );return false;" class="buttons sibling-l">
<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_REJECT_POST'); ?>
</button>
<button type="button" id="save_post_button" onclick="eblog.editor.save();return false;" class="buttons butt-green sibling-r">
<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_APPROVE_POST'); ?>
</button>
<?php } ?>

<a href="<?php echo EBR::_( 'index.php?option=com_easyblog&view=dashboard&layout=entries' );?>" class="buttons sibling-l"><?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' ); ?></a><?php if( !empty($this->acl->('publish_entry') ) ){ ?><button type="button" id="apply_post_button" onclick="eblog.editor.apply();return false;" class="buttons sibling-r"><?php echo JText::_( 'COM_EASYBLOG_APPLY_BUTTON');?></button><?php } ?>
<button type="button" id="save_post_button" class="buttons butt-green">
<?php if( $isEdit && !empty($this->acl->('publish_entry') ) ){ ?>
    <?php echo JText::_( 'COM_EASYBLOG_UPDATE_POST_BUTTON' ); ?>
<?php } else { ?>
    <?php if( empty($this->acl->('publish_entry') ) ){ ?>
        <?php echo JText::_( 'COM_EASYBLOG_SUBMIT_FOR_REVIEW_BUTTON' ); ?>
    <?php } else { ?>
        <?php echo JText::_( 'COM_EASYBLOG_PUBLISH_NOW_BUTTON' ); ?>xxxx
    <?php } ?>
<?php } ?>
</button>

<?php endif; ?>

<span class="has-tooltip">
    <a href="javascript:void(0)" onclick="eblog.dashboard.preview('<?php echo EBR::getItemId('entry');?>');return false;" class="buttons for-preview as-icon">
        <i><?php echo JText::_('COM_EASYBLOG_ENTRY_PREVIEW_BUTTON'); ?></i>
    </a>
    <div class="tip-item">
        <i></i>
        <div>
            <b><?php echo JText::_('COM_EASYBLOG_ENTRY_PREVIEW_BUTTON'); ?></b>
            <?php echo JText::_( 'COM_EASYBLOG_ENTRY_PREVIEW_BUTTON_TIPS' ); ?>
        </div>
    </div>
</span>

<?php if( empty($blog->id) ) : ?>
<span class="has-tooltip">
    <a href="javascript:void(0);" onclick="eblog.drafts.save();return false;" class="buttons for-draft as-icon">
        <i><?php echo JText::_('COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON'); ?></i>
    </a>
    <div class="tip-item">
        <i></i>
        <div>
            <b><?php echo JText::_('COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON'); ?></b>
            <?php echo JText::_( 'COM_EASYBLOG_SAVE_AS_DRAFT_BUTTON_TIPS' ); ?>
        </div>
    </div>
</span>
<?php endif; ?>


<span id="draft_status" class="small"><span></span></span>
