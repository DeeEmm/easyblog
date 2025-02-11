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
<style type="text/css">
    .checkbox { display: block; margin: 0; padding: 0; position: relative; }
    .checkbox > input { display: none; }
    .checkbox > label { 
        display: block;
        margin: 0;
        padding: 0 0 0 45px;
        position: relative;
        line-height: 20px;
    }

    .checkbox > label:before,
    .checkbox > label:after {
        border-radius: 10px;
        content: '';
        display: block;
        position: absolute;
        left: 0;
        top: 0;
        height: 18px;
    }

    .checkbox > label:before {
        border: 1px solid #ccc;
        box-shadow: 0 0 3px rgba(0,0,0,.15) inset;
        background: #ddd;
        width: 33px;
        transition: all ease .3s;
        -moz-transition: all ease .3s;
        -webkit-transition: all ease .3s;
    }

    .checkbox > label:after {
        background: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 1px 1px rgba(0,0,0,.15);
        width: 18px;
        border-radius: 10px;
        transition: all ease .3s;
        -moz-transition: all ease .3s;
        -webkit-transition: all ease .3s;
    }

    .checkbox > input:checked + label:before {
        background: #C8E6C9;
        border-color: #81C784;
    }

    .checkbox > input:checked + label:after {
        background: #4CAF50;
        border-color: #388E3C;
        left: 15px;
    }
</style>
<div class="checkbox">
    <input type="checkbox" id="crop_photo" name="<?php echo $name;?>[crop]" value="1" data-cover-crop <?php echo $crop ? ' checked="checked"' : '';?>/>
    <label for="crop_photo">
        <?php echo JText::_('COM_EASYBLOG_ELEMENTS_CROP_PHOTO_COVER'); ?>
    </label>
</div>

<hr style="margin: 10px 0 10px 45px; max-width: 300px">

<?php if (!$hideFull) { ?>
<div class="checkbox">
    <input type="checkbox" id="full_width" name="<?php echo $name;?>[full]" value="1" data-cover-full-width <?php echo $full ? ' checked="checked"' : '';?> />
    <label for="full_width"><?php echo JText::_('COM_EASYBLOG_ELEMENTS_USE_FULL_WIDTH'); ?></label>
</div>
<?php } ?>

<div style="margin-top: 25px;">
    <div style="margin-top:10px;">
        <div style="display: table; width: 300px;">
            <div style="display: table-cell; vertical-align: middle; width: 120px;">
                <?php echo JText::_('COM_EASYBLOG_ELEMENTS_COVER_WIDTH');?>
            </div>
            <div style="display: table-cell; vertical-align: middle;">
                <div class="input-append">
                    <input class="span6 form-control text-center" type="text" name="<?php echo $name;?>[width]" value="<?php echo $width;?>" data-cover-width <?php echo $full ? ' disabled="disabled"' : '';?>/>
                    <span class="add-on"><?php echo JText::_('COM_EASYBLOG_ELEMENTS_PX');?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="<?php echo !$crop ? 'hide' : '';?>" style="margin-top:15px;" data-cover-height-wrapper>
        <div style="display: table; width: 300px;">
            <div style="display: table-cell; vertical-align: middle; width: 120px;">
                <?php echo JText::_('COM_EASYBLOG_ELEMENTS_COVER_HEIGHT');?>
            </div>
            <div style="display: table-cell; vertical-align: middle;">
                <div class="input-append">
                    <input class="span6 form-control text-center" id="appendedInput" type="text" name="<?php echo $name;?>[height]" value="<?php echo $height;?>" data-cover-height />
                    <span class="add-on"><?php echo JText::_('COM_EASYBLOG_ELEMENTS_PX');?></span>
                </div>
            </div>
        </div>
    </div>

     <div style="margin-top:15px;" data-photo-alignment-wrapper>
        <div style="display: table; width: 340px;">
            <div style="display: table-cell; vertical-align: middle; width: 120px;">
                <?php echo JText::_('COM_EASYBLOG_ELEMENTS_PHOTO_ALIGNMENT');?>
            </div>
            <div style="display: table-cell; vertical-align: middle;">
                <div class="alignment-input">
                    <input class="span6 form-control text-center" type="text" name="fixedAlignment" value="Center" data-photo-fixed-alignment disabled="disabled" style="display:<?php echo $full ? 'block' : 'none';?>"/>
                    
                    <select style="width:115px;" name="<?php echo $name;?>[alignment]" value="<?php echo $alignment;?>" data-photo-alignment style="display:<?php echo $full ? 'none' : 'block';?>">
                        <option <?php echo $alignment == 'center' ? ' selected="selected"' : '';?> value="center"><?php echo JText::_('COM_EASYBLOG_ELEMENTS_PHOTO_ALIGNMENT_CENTER'); ?></option>
                        <option <?php echo $alignment == 'left' ? ' selected="selected"' : '';?> value="left"><?php echo JText::_('COM_EASYBLOG_ELEMENTS_PHOTO_ALIGNMENT_LEFT'); ?></option>
                        <option <?php echo $alignment == 'right' ? ' selected="selected"' : '';?> value="right"><?php echo JText::_('COM_EASYBLOG_ELEMENTS_PHOTO_ALIGNMENT_RIGHT'); ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>


</div>

<script type="text/javascript">
EasyBlog.ready(function($) {
    var cropInput = $('[data-cover-crop]');
    var fullwidthInput = $('[data-cover-full-width]');
    var widthInput = $('[data-cover-width]');
    var heightWrapper = $('[data-cover-height-wrapper]');
    
    var alignmentDropdown = $('[data-photo-alignment-wrapper]').find('.alignment-input div');
    var alignmentFixedInput = $('[data-photo-fixed-alignment]');

    if (fullwidthInput.is(':checked')) {
        alignmentDropdown.hide();
    }

    cropInput.on('change', function() {
        var checked = $(this).is(':checked');

        if (checked) {
            heightWrapper.removeClass('hide');
            return;
        }

        heightWrapper.addClass('hide');
    });

    fullwidthInput.on('change', function() {
        var checked = $(this).is(':checked');

        if (checked) {
            widthInput.attr('disabled', 'disabled');
            alignmentDropdown.hide();
            alignmentFixedInput.show();
            return;
        }

        alignmentDropdown.show();
        alignmentFixedInput.hide();
        widthInput.removeAttr('disabled');
    });


});
</script>