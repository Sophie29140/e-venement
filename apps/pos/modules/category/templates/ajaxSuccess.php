<?php if ( sfConfig::get('sf_debug') ): ?>
<pre><?php print_r($sf_data->getRaw('cats')) ?></pre>
<?php else: ?>
<?php echo json_encode($cats) ?>
<?php endif ?>
