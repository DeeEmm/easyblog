
EasyBlog.ready(function($) {

<?php if ($task->failed == false) { ?>
console.info('EasyBlog style sheet built successfully!');
<?php } else { ?>
try {
	<?php foreach ($task->subtasks as $subtask) { ?>
		<?php if ($subtask->failed) { ?>
			<?php foreach ($subtask->subtasks as $child) { ?>
				<?php if ($child->state == 'error') { ?>
					<?php foreach ($child->details as $result) { ?>
						console.error("<?php echo $result->message;?>");
					<?php } ?>
				<?php } ?>
			<?php } ?>
		<?php } ?>
	<?php } ?>

} catch(e) {
	// In case browser doesn't support it.
};

<?php } ?>

});