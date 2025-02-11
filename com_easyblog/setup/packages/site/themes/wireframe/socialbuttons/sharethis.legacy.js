var switchTo5x = true;

EasyBlog.require()
.script('https://ws.sharethis.com/button/buttons.js')
.done(function($) {
    stLight.options({
        publisher: "<?php echo $this->config->get('social_sharethis_publishers');?>",
        doNotHash: false, 
        doNotCopy: false, 
        hashAddressBar: false
    });
});