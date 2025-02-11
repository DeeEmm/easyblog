<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-block-file eb-block-file--preview" data-responsive="400,300,200,100">
	<div class="t-d--flex t-align-items--c">
		<div class="t-flex-shrink--0 t-mr--lg" data-file-icon-wrapper>
			<div class="eb-file-thumb-wrapper">
				<div class="eb-file-thumb" >
					<i data-file-icon></i>
				</div>
			</div>
		</div>
		<div class="t-flex-grow--1 t-min-width--0">
			<div class="eb-file-details">
				<div class="t-d--flex sm:t-flex-direction--c">
					<div class="t-flex-grow--1 t-min-width--0 t-pr--lg sm:t-pr--no sm:t-pb--lg">
						<div class="t-text--truncate">
							<span class="eb-image-source-title t-text--truncate" data-file-name></span>
						</div>
						<div>
							<span class="text-muted" data-file-size></span>
						</div>
					</div>
					<div class="t-flex-shrink--0">
						<a href="" class="btn btn-eb-default-o" target="_blank" data-file-url><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_DOWNLOAD_BUTTON'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
