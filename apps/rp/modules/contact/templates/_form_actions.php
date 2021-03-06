<ul class="sf_admin_actions_form">
<?php if ($form->isNew()): ?>
  <?php echo $helper->linkToDelete($form->getObject(), array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',  'ui-icon' => '',)) ?>
  <?php echo $helper->linkToList(array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'list',  'label' => 'Back to list',  'ui-icon' => '',)) ?>
  <?php echo $helper->linkToSave($form->getObject(), array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'save',  'label' => 'Save',  'ui-icon' => '',)) ?>
  <?php echo $helper->linkToSaveAndAdd($form->getObject(), array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'save_and_add',  'label' => 'Save and add',  'ui-icon' => '',)) ?>
<?php else: ?>
  <?php if ( sfConfig::get('app_cards_enable') ): ?>
  <?php echo $helper->linkToExtraAction(array(  'params' => 'class= fg-button ui-state-default', 'more-icon' => 'print', 'action' => 'card', 'label' => 'Card',),$contact) ?>
  <?php endif ?>
  <?php echo $helper->linkToShow($form->getObject(), array(  'params' => 'class=fg-button-mini fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'show',  'label' => 'Show',  'ui-icon' => 'person',)) ?>
  <?php echo $helper->linkToDelete($form->getObject(), array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',  'label' => 'Delete',  'ui-icon' => '',)) ?>
  <?php echo $helper->linkToList(array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'list',  'label' => 'Back to list',  'ui-icon' => '',)) ?>
  <?php echo $helper->linkToSave($form->getObject(), array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'save',  'label' => 'Save',  'ui-icon' => '',)) ?>
  <?php echo $helper->linkToSaveAndAdd($form->getObject(), array(  'params' => 'class= fg-button ui-state-default fg-button-icon-left ',  'class_suffix' => 'save_and_add',  'label' => 'Save and add',  'ui-icon' => '',)) ?>
<?php endif; ?>
</ul>
