<script type="text/javascript">
<?php if (!isset($init) || $init) { ?>
EasyBlog.module("init", function($) {

	this.resolve();

	<?php echo $contents; ?>
}).done();

<?php } else { ?>
	<?php echo $contents; ?>
<?php } ?>
</script>