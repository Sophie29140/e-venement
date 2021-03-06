<?php use_helper('Number') ?>
<?php if (! (isset($detail['fake']) && $detail['fake']) ): ?>
  <?php use_javascript('tck-touchscreen-dataloader?'.date('Ymd')) ?>
  <div class="families sample">
    <div class="family" id="li_transaction_<?php echo strtolower($detail['model']) ?>_" data-family-id="">
      <h3 class="ui-corner-all">
        <a target="_blank" class="event"></a>
        <a target="_blank" class="happens_at" title=""></a>
        <a target="_blank" class="location"></a>
        <a target="_blank" class="fg-button-mini fg-button ui-state-default fg-button-icon-left ui-priority-secondary delete" href="#">
          <span class="ui-icon ui-icon-trash"></span>
          <?php echo __('Delete', null, 'sf_admin') ?>
        </a>
        <?php if ( in_array(strtolower($detail['model']), array('manifestation', 'museum')) ): ?>
        <a target="_blank" class="fg-button-mini fg-button ui-state-default fg-button-icon-left ui-priority-secondary gauge" href="#" target="_blank">
          <span class="ui-icon ui-icon-help"></span>
          <?php echo __('Gauge', null, 'sf_admin') ?>
        </a>
        <?php endif ?>
      </h3>
      <div class="items">
        <div class="item ui-corner-all highlight" id="li_transaction_item_">
          <?php include_partial('form_field_content_item_sample', array('transaction' => $transaction)) ?>
        </div>
        <div class="item total">
          <?php include_partial('form_field_content_item_total') ?>
        </div>
      </div>
    </div>
    <div class="family total">
      <div class="items">
        <div class="item total">
          <?php include_partial('form_field_content_item_total') ?>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    if ( LI.urls == undefined )
      LI.urls = {};
    LI.urls['<?php echo $id ?>'] = '<?php echo url_for($detail['data_url'].'?id='.$transaction->id) ?>';
  </script>
<?php else: ?>
  <?php include_partial('form_field_content_fake') ?>
<?php endif ?>
