<?php
/**********************************************************************************
*
*	    This file is part of e-venement.
*
*    e-venement is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License.
*
*    e-venement is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with e-venement; if not, write to the Free Software
*    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
*    Copyright (c) 2006-2013 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2013 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
      <script type="text/javascript"><!--
        $(document).ready(function(){
          $('#ledger_move').selectmenu({
            style: 'dropdown',
            width: 200
          })
          .change(function(){
            if ( $(this).val() )
              window.location = $(this).val();
          });
        });
      --></script>
      <select name="move" id="ledger_move">
        <option value=""><?php echo __('Actions') ?></option>
        <option value="<?php echo url_for('ledger/reset?ledger='.$ledger) ?>"><?php echo __('Reset', null, 'sf_admin') ?></option>
      <?php if ( $sf_user->hasCredential('tck-ledger-'.($ledger == 'cash' ? 'sales' : 'cash')) ): ?>
        <?php if ( in_array($ledger, array('cash', 'both')) ): ?>
        <option value="<?php echo url_for('ledger/sales') ?>">
          <?php echo __('Sales Ledger',NULL,'menu') ?>
        </option>
        <?php endif ?>
        <?php if ( in_array($ledger, array('sales', 'both')) ): ?>
        <option value="<?php echo url_for('ledger/cash') ?>">
          <?php echo __('Cash Ledger',NULL,'menu') ?>
        </option>
        <?php endif ?>
        <?php if ( in_array($ledger, array('sales', 'cash')) ): ?>
        <option value="<?php echo url_for('ledger/both') ?>">
          <?php echo __('Detailed Ledger',array(),'menu') ?>
        </option>
        <option value="<?php echo url_for('ledger/extract') ?>?type=<?php echo $ledger ?>">
          <?php echo __('Extract (%%format%%)',array('%%format%%' => 'CSV')) ?>
        </option>
        <option value="<?php echo url_for('ledger/extract') ?>?type=<?php echo $ledger ?>&with_totals=1">
          <?php echo __('Extract (%%format%%)',array('%%format%%' => 'CSV, '.__('with totals'))) ?>
        </option>
        <?php endif ?>
        <?php if ( $ledger == 'cash' ): ?>
        <option value="<?php echo url_for('ledger/extract') ?>?type=lineal">
          <?php echo __('Extract (%%format%%)',array('%%format%%' => 'Lineal, Sigma')) ?>
        </option>
        <?php endif ?>
      <?php endif ?>
      </select>
