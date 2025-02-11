<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>


<div class="eb-float-menu">
    <div class="eb-float-btn-wrap pull-left">
        <a class="eb-float-publish-btn btn btn-success eb-composer-primary-button eb-composer-publish-post-button" data-eb-composer-publish-post-button>
            <i class="fa eb-loader-o size-sm color-white eb-composer-loader hide"></i>
            <?php echo JText::_('COM_EASYBLOG_PUBLISH_POST'); ?>
        </a>

        <a class="eb-float-publish-btn btn btn-primary eb-composer-primary-button eb-composer-update-post-button" data-eb-composer-update-post-button>
            <i class="fa eb-loader-o size-sm color-white eb-composer-loader hide"></i>
            <?php echo JText::_('COM_EASYBLOG_UPDATE_POST'); ?>
        </a>

        <a class="eb-float-publish-btn btn btn-warning eb-composer-primary-button eb-composer-submit-post-button" data-eb-composer-submit-post-button>
            <i class="fa eb-loader-o size-sm color-white eb-composer-loader hide"></i>
            <?php echo JText::_('COM_EASYBLOG_SUBMIT_POST_FOR_APPROVAL'); ?>
        </a>

        <a class="eb-float-publish-btn btn btn-success eb-composer-primary-button eb-composer-approve-post-button" data-eb-composer-approve-post-button>
            <i class="fa eb-loader-o size-sm color-white eb-composer-loader hide"></i>
            <?php echo JText::_('COM_EASYBLOG_APPROVE_AND_PUBLISH_POST'); ?>
        </a>
    </div>

    <!-- div class="eb-float-btn-wrap pull-left" data-eb-composer-launcher-close-button>
        <label class="eb-float-btn eb-float-close">
            <i></i>
        </label>
    </div -->

    <div class="eb-float-btn-wrap pull-right">
        <button class="eb-float-btn eb-float-settings" data-eb-composer-show-drawer-button>
            <i class="fdi fa fa-cog"></i>
        </button>
    </div>

    <div class="eb-float-btn-wrap pull-right">
        <button class="eb-float-btn eb-float-toggle" for="menu-open" data-eb-composer-mobile-blip>
            <i></i>
        </button>

        <div class="eb-float-links">
            <div class="eb-float-link eb-link-block" data-eb-composer-add-block-button>
                <i class="fdi fa fa-cube"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_BLOCK');?></span>
            </div>

            <div class="eb-float-link eb-link-media" data-eb-composer-add-media-button>
                <i class="fdi fa fa-camera"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_MEDIA');?></span>
            </div>

            <div class="eb-float-link eb-link-post" data-eb-composer-add-post-button>
                <i class="fdi far fa-file-alt"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_POST');?></span>
            </div>

            <div class="eb-float-link eb-link-cover<?php echo $post->hasImage() ? ' has-cover' : '';?>" data-eb-composer-meta-button data-id="cover">
                <i class="fdi fa fa-image"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_COVER');?></span>
            </div>

            <?php if ($this->config->get('main_locations') && EB::location($this->config->get('location_service_provider'))->isSettingsComplete()) { ?>
            <div class="eb-float-link eb-link-location<?php echo $post->hasLocation() ? ' has-location' : '';?>" data-eb-composer-meta-button data-id="location">
                <i class="fdi fa fa-map-marker"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_ADD_LOCATION');?></span>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
