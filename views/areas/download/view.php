<?php if ($this->editmode) { ?>
    <?= \Toolbox\Tool\ElementBuilder::buildElementConfig('download', $this) ?>
<?php } ?>
<div class="toolbox-element toolbox-download <?= $this->select('downloadAdditionalClasses')->getData(); ?>">
    <?= $this->template('toolbox/download.php') ?>
</div>