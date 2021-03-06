<td class="sf_admin_date sf_admin_list_td_happens_at" <?php if ( $manifestation->color_id ): ?>style="background-color: <?php echo $manifestation->Color ?>"<?php endif ?>>
  <a href="<?php echo url_for('manifestation/show?id='.$manifestation->id) ?>">
    <?php echo $manifestation->happens_at ? $manifestation->getShortenedDate() : '&nbsp;' ?>
    <?php if ( $sf_context->getConfiguration()->getApplication() == 'museum' ): ?>
    <br/>
    <?php echo $manifestation->ends_at ? $manifestation->getShortenedEndDate() : '&nbsp;' ?>
    <?php endif ?>
  </a>
</td>
<td class="sf_admin_text sf_admin_list_td_list_location">
  <?php echo get_partial('manifestation/list_location', array('type' => 'list', 'manifestation' => $manifestation)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_list_gauge">
  <?php echo get_partial('manifestation/list_gauge', array('type' => 'list', 'manifestation' => $manifestation)) ?>
</td>
<td class="sf_admin_text sf_admin_list_td_list_description">
  <?php echo get_partial('manifestation/list_description', array('type' => 'list', 'manifestation' => $manifestation)) ?>
</td>
<?php if ( sfConfig::get('app_manifestation_extra_informations_enable',true) ): ?>
<td class="sf_admin_text sf_admin_list_td_list_extra_informations_list">
  <?php echo get_partial('manifestation/list_extra_informations_list', array('type' => 'list', 'manifestation' => $manifestation)) ?>
</td>
<?php endif ?>
<?php if ( $sf_user->hasCredential('event-manif-edit') ): ?>
  <?php echo get_partial('manifestation/list_td_actions', array('manifestation' => $manifestation, 'helper' => $helper,)) ?>
<?php endif ?>
