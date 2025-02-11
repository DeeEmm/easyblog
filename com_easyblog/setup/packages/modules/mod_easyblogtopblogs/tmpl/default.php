<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-mod mod_easyblogtopblogs<?php echo $modules->getWrapperClass();?>" data-eb-module-topblogs>
    <?php if ($posts) { ?>
    <div class="eb-mod<?php echo $layout == 'horizontal' ? ' mod-items-grid clearfix' : '';?>">
    	<?php $column = 1; ?>
    	
        <?php foreach ($posts as $post) { ?>
            <?php require(JModuleHelper::getLayoutPath('mod_easyblogtopblogs', 'default_' . $layout)); ?>
        <?php } ?>
    </div>
    <?php } ?>
</div>

<?php if ($config->get('main_ratings')) { ?>
<script type="text/javascript">
EasyBlog.require()
.script('site/vendors/ratings')
.done(function($) {
    $('[data-eb-module-topblogs] [data-rating-form]').implement(EasyBlog.Controller.Ratings);
});
</script>
<?php } ?>
