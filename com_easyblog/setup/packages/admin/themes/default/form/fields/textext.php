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


$textKey = ($prefix) ? $prefix . $field->attributes->name : 'params[' . $field->attributes->name . ']';

$value = $params->get($prefix . $field->attributes->name, $default);
$id = $field->attributes->name;
$textId = $id . '_txt';
$radioId = $id . '_option';
$hiddenId = $id;

$config = EB::config();
$previousVal = $value != '-1' ? $value : $config->get('layout_' . $id, '3' );

$functionNameRadio = 'changeValue' . $id . 'Radio';
$functionName = 'changeValue' . $id;
?>

<script type="text/javascript">
    function <?php echo $functionNameRadio; ?>() {
        if (jQuery('input[name=<?php echo $radioId; ?>]:checked').val() == '-1') {
            jQuery('input#<?php echo $hiddenId;?>').val('-1');
            jQuery('input[name=<?php echo $textId;?>]').hide();
        } else {
            jQuery('input[name=<?php echo $textId;?>]').val('<?php echo $previousVal;?>');
            jQuery('input#<?php echo $hiddenId;?>').val('<?php echo $previousVal;?>');
            jQuery('input[name=<?php echo $textId;?>]').show();
        }
    }

    function <?php echo $functionName; ?>() {
        var val = jQuery('input[name=<?php echo $textId;?>]').val();
        jQuery('input#<?php echo $hiddenId;?>').val(val);
    }
</script>

<?php $checked = $value == '-1' ? ' checked="true" ' : ' '; ?>
<input type="radio" name="<?php echo $radioId; ?>" value="-1" onclick="<?php echo $functionNameRadio; ?>()" <?php echo $checked;?> />&nbsp; <?php echo JText::_('COM_EASYBLOG_USE_DEFAULT_OPTIONS'); ?>&nbsp;
<?php $checked = $value != '-1' ? ' checked="true" ' : ' '; ?>
<input type="radio" name="<?php echo $radioId; ?>" value="1" onclick="<?php echo $functionNameRadio; ?>()" <?php echo $checked;?> />&nbsp; <?php echo JText::_('COM_EASYBLOG_USE_BELOW_OPTIONS'); ?>

<?php $style = $value == '-1' ? ' style="display:none;" ' : ' '; ?>
<input type="text" class="form-control input-sm" name="<?php echo $textId; ?>" value="<?php echo $value; ?>" onchange="<?php echo $functionName; ?>()" <?php echo $style; ?> />

<input type="hidden" name="<?php echo $textKey; ?>" id="<?php echo $id; ?>" value="<?php echo $value; ?>" />
