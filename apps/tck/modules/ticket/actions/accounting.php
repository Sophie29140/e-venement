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
    if ( !isset($this->transaction) )
    $this->transaction = $this->getRoute()->getObject();
    $this->nocancel = $request->hasParameter('nocancel');
    
    $this->totals = array('pet' => 0, 'tip' => 0, 'extra-taxes' => 0, 'vat' => array('total' => 0));
    
    // retrieve tickets
    $q = Doctrine_Query::create()->from('Ticket t')
      ->leftJoin('t.Manifestation m')
      ->leftJoin('m.Event e')
      ->leftJoin("e.Translation et WITH et.lang = '".$this->getUser()->getCulture()."'")
      ->leftJoin('t.Price p')
      ->leftJoin("p.Translation pt WITH pt.lang = '".$this->getUser()->getCulture()."'")
      ->andWhere('t.transaction_id = ?',$this->transaction->id)
      ->andWhere('t.duplicating IS NULL')
      ->orderBy('m.happens_at, et.name, pt.description, t.value');
    if ( $printed )
      $q->andWhere('t.printed_at IS NOT NULL OR t.integrated_at IS NOT NULL OR t.cancelling IS NOT NULL');
    if ( intval($manifestation_id) > 0 )
    {
      $request->setParameter('manifestation_id', $manifestation_id);
      $q->andWhere('t.manifestation_id = ?',intval($manifestation_id));
    }
    $this->tickets = $q->execute();
    
    // remove cancelled tickets
    foreach ( $this->tickets as $ticket )
    if ( !$this->nocancel || $ticket->Cancelling->count() == 0 )
    {
      $this->totals['tip'] += $tmp = $ticket->value + $ticket->taxes;
      
      $local_vat = $ticket->printed_at || $ticket->integrated_at || $ticket->cancelling
        ? $ticket->vat
        : $ticket->Manifestation->Vat->value;
      if ( !isset($this->totals['vat'][$local_vat]) )
        $this->totals['vat'][$local_vat] = array($ticket->manifestation_id => 0);
      if ( !isset($this->totals['vat'][$local_vat][$ticket->manifestation_id]) )
        $this->totals['vat'][$local_vat][$ticket->manifestation_id] = 0;
      $this->totals['vat'][$local_vat][$ticket->manifestation_id] += $tmp = round($tmp - $tmp/(1+$local_vat), 2);
      $this->totals['vat']['total'] += $tmp;
    }
    
    // retrieve products
    $q = Doctrine_Query::create()->from('BoughtProduct bp')
      ->leftJoin('bp.Price p')
      ->andWhere('bp.transaction_id = ?',$this->transaction->id)
      ->orderBy('bp.name, bp.code, bp.declination, bp.price_name, bp.value');
    if ( $printed )
      $q->andWhere('bp.integrated_at IS NOT NULL');
    $this->products = $q->execute();
    
    foreach ( $this->products as $pdt )
    {
      $this->totals['tip'] += $pdt->shipping_fees + $pdt->value;
      
      $tmp = 0;
      foreach ( array('vat' => 'value', 'shipping_fees_vat' => 'shipping_fees') as $v => $t )
      {
        if ( !isset($this->totals['vat'][$pdt->$v]) )
          $this->totals['vat'][$pdt->$v] = array($pdt->product_declination_id => 0);
        if ( !isset($this->totals['vat'][$pdt->$v][$pdt->product_declination_id]) )
          $this->totals['vat'][$pdt->$v][$pdt->product_declination_id] = 0;
        
        $tmp += $buf = round($pdt->$t - $pdt->$t/(1+$pdt->$v), 2);
        $this->totals['vat'][$pdt->$v][$pdt->product_declination_id] += $buf;
      }
      $this->totals['vat']['total'] += $tmp;
    }
    
    foreach ( $this->totals['vat'] as $vat => $manifs )
    if ( is_array($manifs) )
    foreach ( $manifs as $manif )
    {
      if ( is_array($this->totals['vat'][$vat]) )
        $this->totals['vat'][$vat] = 0;
      $this->totals['vat'][$vat] += round($manif,2);
    }
    
    $this->setLayout('empty');
