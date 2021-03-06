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
*    Copyright (c) 2006-2012 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2012 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
  // $active sets the current item to highlight and which ones are past or future
  // be careful for the login step if it comes before command
  $nb = 0;
?>
<div id="ariane">
  <div class="login choices <?php if ( $active == $nb ) echo 'active'; else echo 'past' ?> access">
    <ul>
      <li>
        <?php
        echo $sf_user->hasContact()
          ? link_to(__('My account'),'contact/index')
          : link_to(__('Login'),'login/index')
        ?>
      </li>
      <li>
        <?php
        echo $sf_user->hasContact()
          ? link_to(__('Logout'),'login/out')
          : link_to(__('Create an account'),'contact/new')
        ?>
      </li>
    </ul>
  </div>
  <?php $nb++ ?>
  <div class="event choices <?php if ( $active == $nb ) echo 'active'; else echo $active < $nb ? 'future' : 'past' ?> access <?php echo $sf_user->isStoreActive() ? 'with-store' : '' ?>">
    <ul>
      <li><?php echo link_to(pubConfiguration::getText('app_informations_index',__('Events')), '@homepage', array('class' => 'event')) ?></li>
      <?php if ( $sf_user->isStoreActive() ): ?>
      <li><?php echo link_to(pubConfiguration::getText('app_informations_store',__('Store')), 'store/index', array('class' => 'store')) ?></li>
      <?php endif ?>
      <?php if ( $sf_user->getGuardUser()->MemberCards->count() > 0 ): ?>
      <li><?php echo link_to(pubConfiguration::getText('app_member_cards_title', __('Member cards')), 'card/index', array('class' => 'mc')) ?></li>
      <?php endif ?>
    </ul>
  </div>
  <?php $nb++ ?>
  <div class="cart <?php if ( $active == $nb ) echo 'active'; else echo $active < $nb ? 'future' : 'past' ?> access">
    <ul>
      <li><?php echo link_to(__('Cart'),'cart/show') ?></li>
      <?php if ( count($cultures = sfConfig::get('project_internals_cultures',array('fr' => 'Français'))) > 1 ): ?>
      <?php endif ?>
    </ul>
  </div>
  <?php $nb++ ?>
  <div class="command <?php if ( $active == $nb ) echo 'active'; else echo $active < $nb ? 'future' : 'past' ?> access">
    <ul>
      <li><?php echo __('Command') ?></li>
    </ul>
    <?php if ( count($cultures) > 1 ): ?>
    <div id="translation">
      <?php foreach ( $cultures as $culture => $lang ): ?>
      <p class="i18n">
        <a href="<?php echo url_for('login/culture?lang='.$culture) ?>"
           class="culture-<?php echo $culture ?> <?php echo $culture === $sf_user->getCulture() ? 'current' : '' ?>"
           title="<?php echo $lang ?>">
          <?php echo $lang ?>
        </a>
      </p>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </div>
</div>
