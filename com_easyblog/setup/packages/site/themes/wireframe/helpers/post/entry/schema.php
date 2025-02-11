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
<script type="application/ld+json">
{
	"@context": "http://schema.org",
	"mainEntityOfPage": "<?php echo $post->getPermalink(true, true); ?>",
	"@type": ["BlogPosting", "Organization"],
	"name": "<?php echo FH::getSiteName(); ?>",
	"headline": "<?php echo $this->fd->html('str.escape', $post->getTitle());?>",
	"image": "<?php echo $post->getImage(EB::getCoverSize('cover_size_entry'), true, true);?>",
	"editor": "<?php echo $post->getAuthor()->getName();?>",
	"genre": "<?php echo $post->getPrimaryCategory()->title;?>",
	"wordcount": "<?php echo $post->getTotalWords();?>",
	"publisher": {
		"@type": "Organization",
		"name": "<?php echo FH::getSiteName(); ?>",
		"logo": <?php echo $post->getSchemaLogo(); ?>
	},
	"datePublished": "<?php echo $post->getPublishDate(true)->format('Y-m-d');?>",
	"dateCreated": "<?php echo $post->getCreationDate(true)->format('Y-m-d');?>",
	"dateModified": "<?php echo $post->getModifiedDate()->format('Y-m-d');?>",
	"description": "<?php echo EB::jconfig()->get('MetaDesc'); ?>",
	"articleBody": "<?php echo EB::normalizeSchema($schemaContent); ?>",
	"author": {
		"@type": "Person",
		"url": "<?php echo $post->getAuthor()->getExternalPermalink(); ?>",
		"name": "<?php echo $post->getAuthor()->getName();?>",
		"image": "<?php echo $post->creator->getAvatar();?>"
	}<?php if ($ratings) { ?>,
		"aggregateRating": {
			"@type": "http://schema.org/AggregateRating",
			"ratingValue": "<?php echo round($ratings->ratings / 2, 2); ?>",
			"worstRating": "0.5",
			"bestRating": "5",
			"ratingCount": "<?php echo $ratings->total; ?>"
		}
	<?php } ?>
}
</script>
