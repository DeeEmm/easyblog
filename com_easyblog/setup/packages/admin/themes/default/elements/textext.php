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
<fieldset id="<?php echo $eleId; ?>" class="radio btn-group">
    <?php $checked = $eleValue == '-1' ? ' checked="checked" ' : ' '; ?>
    <input type="radio" id="<?php echo $eleId; ?>0" data-<?php echo $radioId; ?> name="<?php echo $radioId; ?>" value="-1" <?php echo $checked;?>/>
    <label class="btn" for="<?php echo $eleId; ?>0"><?php echo JText::_('COM_EASYBLOG_USE_DEFAULT_OPTIONS');?></label>

    <?php $checked = $eleValue != '-1' ? ' checked="checked" ' : ' ';?>
    <input type="radio" id="<?php echo $eleId; ?>1" data-<?php echo $radioId; ?> name="<?php echo $radioId; ?>" value="1" <?php echo $checked;?> />
    <label class="btn" for="<?php echo $eleId; ?>1"><?php echo JText::_('COM_EASYBLOG_USE_BELOW_OPTIONS');?></label>

    <input type="text" data-<?php echo $textId; ?> name="<?php echo $textId;?>" <?php echo $dirname;?> value="<?php echo htmlspecialchars($eleValue, ENT_COMPAT, 'UTF-8');?>" <?php echo $extraAttr ?> />
    <input type="hidden" data-<?php echo $eleId; ?> name="<?php echo $eleName;?>" value="<?php echo $eleValue;?>" />
</fieldset>

<script type="text/javascript">
EasyBlog.ready(function($) {

    var previousValue = '<?php echo $previousVal;?>';
    var radioInput = $('[data-<?php echo $radioId;?>]');
    var hiddenInput = $('[data-<?php echo $eleId;?>]');
    var textInput = $('[data-<?php echo $textId;?>]')

    radioInput.on('click', function() {
        var value = $(this).val();

        if (value == '-1') {
            hiddenInput.val('-1');
            textInput.hide();
        } else {
            hiddenInput.val(previousValue);
            textInput.val(previousValue);
            textInput.show();
        }
    });

    textInput.on('change', function(){
        hiddenInput.val($(this).val());
    });
});
</script>
