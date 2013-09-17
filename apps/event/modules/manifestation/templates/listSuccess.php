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
<?php
  use_helper('Colors');
  
  // STYLES OF CALENDAR ELEMENTS
  $css_around = array(
    'font-size' => '80%',
  );
  
  // JSON CONTENT
  
  $manifs = array();
  
  foreach ( $manifestations as $manif )
  {
    // case of big manifestations shown exceptionnally because of their size
    if ( $month_view && $manif->Event->MetaEvent->hide_in_month_calendars )
    {
      $raw_manif = $manif->getRawValue();
      if ( $raw_manif->reservation_begins_at < $raw_manif->happens_at )
        $raw_manif->happens_at = $raw_manif->reservation_begins_at;
      if ( $raw_manif->reservation_ends_at > $raw_manif->ends_at )
        $raw_manif->duration = strtotime($raw_manif->reservation_ends_at) - strtotime($raw_manif->happens_at);
    }
    
    // the manif itself
    $manifs[] = array(
      'id' => $manif->id,
      'title' => !isset($event_id) ? (string)$manif->Event : (string)$manif->Location,
      'start' => $manif->happens_at,
      'end' => $manif->ends_at,
      'allDay' => false,
      'hackurl' => url_for('manifestation/show?id='.$manif->id),
      'editable' => $sf_user->hasCredential('event-manif-edit'),
      'css' => array_merge($css_base = array(
          'border-style'  => $manif->reservation_confirmed ? 'solid' : 'dashed',
          'font-style'    => $manif->blocking ? 'normal' : 'italic',
        ), array(
          'opacity'       => !$manif->reservation_optional || $manif->reservation_confirmed  ? '1' : '0.7',
        ))
      );
    
    if ( $manif->color_id )
      $manifs[count($manifs)-1]['backgroundColor'] = '#'.$manif->Color->color;
    
    $css = array( 'opacity'       => !$manif->reservation_optional || $manif->reservation_confirmed  ? '0.7' : '0.5', );
    // preparation
    if ( $manif->reservation_begins_at < $manif->happens_at )
    {
      $manifs[] = $manifs[count($manifs)-1];
      $manifs[count($manifs)-1]['id'] = $manif->id.'-before';
      $manifs[count($manifs)-1]['start'] = $manif->reservation_begins_at;
      $manifs[count($manifs)-1]['end'] = $manif->happens_at;
      $manifs[count($manifs)-1]['editable'] = false;
      $manifs[count($manifs)-1]['css'] = array_merge($css_around, $css_base, $css, array(
        'border-bottom-left-radius' => '0',
        'border-bottom-right-radius' => '0',
        'border-bottom-width' => '0',
      ));
      if ( $manif->color_id )
        $manifs[count($manifs)-1]['backgroundColor'] = '#'.$manif->Color->color;
      unset($manifs[count($manifs)-1]['hackurl']);
    }
    
    // finition things
    if ( $manif->reservation_ends_at > $manif->ends_at )
    {
      $manifs[] = $manifs[count($manifs)-1];
      $manifs[count($manifs)-1]['id'] = $manif->id.'-after';
      $manifs[count($manifs)-1]['start'] = $manif->ends_at;
      $manifs[count($manifs)-1]['end'] = $manif->reservation_ends_at;
      $manifs[count($manifs)-1]['editable'] = false;
      $manifs[count($manifs)-1]['css'] = array_merge($css_around, $css_base, $css, array(
        'border-top-left-radius' => '0',
        'border-top-right-radius' => '0',
        'border-top-width' => '0',
      ));
      if ( $manif->color_id )
        $manifs[count($manifs)-1]['backgroundColor'] = '#'.$manif->Color->color;
      unset($manifs[count($manifs)-1]['hackurl']);
    }
  }
?>
<?php if ( $debug ): ?>
<pre><?php print_r($manifs) ?></pre>
<?php else: ?>
<?php echo json_encode($manifs) ?>
<?php endif ?>
