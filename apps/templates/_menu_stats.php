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
*    Copyright (c) 2006-2011 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2011 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php if ( $sf_user->hasCredential('stats-attendance')
        || $sf_user->hasCredential('stats-activity')
        || $sf_user->hasCredential('stats-prices')
        || $sf_user->hasCredential('stats-byGroup')
        || $sf_user->hasCredential('stats-pr-social')
        || $sf_user->hasCredential('stats-pr-cards') ): ?>
      <li class="menu-stats">
        <ul class="second">
          <?php if ( $sf_user->hasCredential('stats-attendance') ): ?>
          <li><a href="<?php echo cross_app_url_for('stats','attendance/index') ?>"><?php echo __('Attendance',array(),'menu') ?></a></li>
          <?php endif ?>
          <?php if ( $sf_user->hasCredential('stats-prices') ): ?>
          <li><a href="<?php echo cross_app_url_for('stats','prices/index') ?>"><?php echo __('Tickets by price',array(),'menu') ?></a></li>
          <?php endif ?>
          <?php if ( $sf_user->hasCredential('stats-geo') ): ?>
          <li><a href="<?php echo cross_app_url_for('stats', 'geo/index') ?>"><?php echo __('Geographical approach', null, 'menu') ?></a></li>
          <?php endif ?>
          <li class="spaced"></li>
          <?php if ( $sf_user->hasCredential('stats-activity') ): ?>
          <li><a href="<?php echo cross_app_url_for('stats','activity/index') ?>"><?php echo __('Ticketing activity',array(),'menu') ?></a></li>
          <li><a href="<?php echo cross_app_url_for('stats','debts/index') ?>"><?php echo __('Debts evolution',array(),'menu') ?></a></li>
          <?php endif ?>
          <?php if ( $sf_user->hasCredential('tck-reports') ): ?>
          <li><a href="<?php echo cross_app_url_for('stats','tickets/index') ?>"><?php echo __('Ticketing',array(),'menu') ?></a></li>
          <?php endif ?>
          <?php if ( $sf_user->hasCredential('stats-pub') ): ?>
          <li><a href="<?php echo cross_app_url_for('stats','web_origin/index') ?>"><?php echo __('Online sales',array(),'menu') ?></a></li>
          <?php endif ?>
          <?php if ( $sf_user->hasCredential('stats-control') ): ?>
          <li class="spaced"></li>
          <li><a href="<?php echo cross_app_url_for('stats','control/index') ?>"><?php echo __('Ticket controls', array(),'menu') ?></a></li>
          <?php endif ?>
          <?php if ( $sf_user->hasCredential('stats-pr-cards') || $sf_user->hasCredential('stats-pr-social') || $sf_user->hasCredential('stats-pr-groups') ): ?>
          <li class="spaced"></li>
          <?php endif ?>
          <?php if ( $sf_user->hasCredential('stats-pr-groups') ): ?>
          <li><a href="<?php echo cross_app_url_for('stats','groups/index') ?>"><?php echo __('Evolution of groups',array(),'menu') ?></a></li>
          <?php endif ?>
          <?php if ( $sf_user->hasCredential('stats-pr-cards') ): ?>
          <li><a href="<?php echo cross_app_url_for('stats','cards/index') ?>"><?php echo __('Member cards',array(),'menu') ?></a></li>
          <?php endif ?>
          <?php if ( $sf_user->hasCredential('stats-pr-social') ): ?>
          <li><a href="<?php echo cross_app_url_for('stats','social/index') ?>"><?php echo __('Social statistics',array(),'menu') ?></a></li>
          <?php endif ?>
          <?php include_partial('global/menu_extra', array('name' => 'stats')) ?>
        </ul>
        <span class="title"><?php echo __('Stats',array(),'menu') ?></span>
      </li>
<?php endif ?>
