<div class="eb-entry-authors">
	<?php if ($params->get('post_author', true)) { ?>
	<div>
		<span class="text-muted"><?php echo JText::_('COM_EB_WRITTEN_BY'); ?></span> 

		<a href="<?php echo $authorPermalink; ?>" rel="author"><?php echo $authorName; ?></a>
	</div>
	<?php } ?>

	<?php if ($reviewerName) { ?>
	<div class="mt-5">
		<span class="text-muted"><?php echo JText::_('COM_EB_REVIEWED_BY'); ?></span> 

		<?php if ($reviewerLink) { ?>
			<a href="<?php echo $reviewerLink; ?>" target="_blank">
		<?php } ?>

			<?php echo $reviewerName; ?>

		<?php if ($reviewerLink) { ?>
			</a>
		<?php } ?>
	</div>
	<?php } ?>

	<?php if ($factCheckerName) { ?>
	<div class="mt-5">
		<span class="text-muted"><?php echo JText::_('COM_EB_FACT_CHECKED_BY'); ?></span> 

		<?php if ($factCheckerLink) { ?>
			<a href="<?php echo $factCheckerLink; ?>" target="_blank">
		<?php } ?>

			<?php echo $factCheckerName; ?>

		<?php if ($factCheckerLink) { ?>
			</a>
		<?php } ?>
	</div>
	<?php } ?>
</div>
