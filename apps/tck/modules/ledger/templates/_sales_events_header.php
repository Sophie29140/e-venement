    <td class="event"><?php echo __('Event') ?></td>
    <td class="see-more"><a href="#">+</a></td>
    <td class="id-qty"><?php echo __('Qty') ?></td>
    <td class="value" title="<?php echo __('PIT').' = '.__('TEP').' + '.__('Tot.VAT') ?>"><?php echo __('PIT') ?></td>
    <td class="extra-taxes" title="<?php echo __('Incl. VAT') ?>"><?php echo __('Taxes', null, 'li_accounting') ?></td>
    <?php foreach ( $total['vat'] as $name => $arr ): ?>
    <td class="vat"><?php echo format_number(round($name*100,2)).'%'; ?></td>
    <?php endforeach ?>
    <td class="vat total"><?php echo __('Tot.VAT') ?></td>
    <td class="tep"><?php echo __('TEP') ?></td>
