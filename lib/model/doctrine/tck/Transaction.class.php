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
*    Copyright (c) 2006-2014 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2014 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php

/**
 * Transaction
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    e-venement
 * @subpackage model
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Transaction extends PluginTransaction
{
  public function getItemables()
  {
    $r = new Doctrine_Collection('Itemable');
    foreach ( $this->getTable()->getRelations() as $rel => $fk )
    if ( is_a($fk->getClass(), 'Itemable', true) )
    foreach ( $this->$rel as $obj )
      $r[] = $obj;
    return $r;
  }
  
  /**
    * Retrieve the applyable surveys
    *
    * @return Doctrine_Collection
    *
    **/
  public function getSurveys()
  {
    $surveys = array();
    
    // surveys linked to any transaction's related component
    foreach ( $this->Tickets as $ticket )
    foreach ( $ticket->Manifestation->SurveysToApply as $sat )
    if ( ($sat->date_from ? $sat->date_from <= date('Y-m-d H:i:s') : true) && ($sat->date_to ? $sat->date_to > date('Y-m-d H:i:s') : true) )
      $surveys[$sat->Survey->id] = $sat->Survey;
    foreach ( $this->Contact->SurveysToApply as $sat )
    if ( ($sat->date_from ? $sat->date_from <= date('Y-m-d H:i:s') : true) && ($sat->date_to ? $sat->date_to > date('Y-m-d H:i:s') : true) )
      $surveys[$sat->Survey->id] = $sat->Survey;
    foreach ( $this->Contact->Groups as $group )
    foreach ( $group->SurveysToApply as $sat )
    if ( ($sat->date_from ? $sat->date_from <= date('Y-m-d H:i:s') : true) && ($sat->date_to ? $sat->date_to > date('Y-m-d H:i:s') : true) )
      $surveys[$sat->Survey->id] = $sat->Survey;
    
    // surveys applyable everywhere
    foreach ( Doctrine::getTable('Survey')->createQuery('s')
      ->leftJoin('s.ApplyTo sat')
      ->andWhere('sat.everywhere = ?', true)
      ->andWhere('sat.date_from <= NOW() OR sat.date_from IS NULL')
      ->andWhere('sat.date_to > NOW() OR sat.date_to IS NULL')
      ->execute()
      as $survey )
      $surveys[$survey->id] = $survey;
    
    ksort($surveys);
    $collection = Doctrine_Collection::create('Survey');
    $collection->setData($surveys);
    return $collection;
  }
  
  /**
    * Retrieve the applyable surveys that need to be answered
    *
    * @return Doctrine_Collection
    *
    **/
  public function getSurveysToFillIn()
  {
    $surveys = $this->getSurveys();
    
    foreach ( $surveys as $key => $survey )
    foreach ( $survey->AnswersGroups as $group )
    if ( $group->transaction_id == $this->id )
    if ( in_array($key, $surveys->getKeys()) )
      $surveys->remove($key);
    
    return $surveys;
  }
  
  public function getNotPrinted()
  {
    $toprint = 0;
    foreach ( $this->Tickets as $ticket )
    if ( $ticket->Duplicatas->count() == 0 && !$ticket->printed_at && !$ticket->integrated_at && is_null($ticket->cancelling) )
      $toprint++;
    return $toprint;
  }
  public function getTicketsPrice($all = false)
  {
    $price = 0;
    foreach ( $this->Tickets as $ticket )
    if ( $all && $ticket->Duplicatas->count() == 0
      || $ticket->Duplicatas->count() == 0 && ($ticket->printed_at || $ticket->integrated_at || !is_null($ticket->cancelling)) )
      $price += $ticket->value + $ticket->taxes;
    return $price;
  }
  public function getProductsPrice($all = false)
  {
    $price = 0;
    foreach ( $this->BoughtProducts as $product )
    if ( $all || $product->integrated_at )
      $price += $product->value;
    return $price;
  }
  public function getPrice($all = false, $all_inclusive = false)
  {
    if ( $all_inclusive )
    {
      return $this->getPrice($all)
        + $this->getMemberCardPrice($all)
        - $this->getTicketsLinkedToMemberCardPrice($all);
    }
    
    $price = $this->getTicketsPrice($all) + $this->getProductsPrice($all);
    return $price;
  }
  public function getMemberCardPrice($all = false)
  {
    $price = 0;
    foreach ( $this->MemberCards as $mc )
    if ( $all || $mc->activated )
      $price += $mc->MemberCardType->value;
    return $price;
  }
  public function getTicketsLinkedToMemberCardPrice($all = false)
  {
    $prices = array();
    
    // linked directly to this transaction
    foreach ( $this->MemberCards as $mc )
    if ( $all || $mc->activated )
    foreach ( $mc->MemberCardPrices as $mcp )
      if ( isset($prices[$mcp->price_id]) )
        $prices[$mcp->price_id]++;
      else
        $prices[$mcp->price_id] = 1;
    
    // linked to the transaction's contact
    if ( !is_null($this->contact_id) )
    foreach ( $this->Contact->MemberCards as $mc )
    if ( $mc->active && $mc->transaction_id != $this->id )
    foreach ( $mc->MemberCardPrices as $mcp )
      if ( isset($prices[$mcp->price_id]) )
        $prices[$mcp->price_id]++;
      else
        $prices[$mcp->price_id] = 1;
    
    $price = 0;
    foreach ( $this->Tickets as $ticket )
    if ( $ticket->printed_at && $ticket->member_card_id )
      $price += $ticket->value;
    elseif ( $all && $ticket->Price->member_card_linked && !$ticket->printed_at
          && isset($prices[$ticket->price_id]) && $prices[$ticket->price_id] > 0 )
    {
      $prices[$ticket->price_id]--;
      $price += $ticket->value;
    }
    return $price;
  }
  public function getPaid()
  {
    $paid = 0;
    foreach ( $this->Payments as $payment )
      $paid += $payment->value;
    return $paid;
  }
  
  public function renderSimplifiedTickets(array $with = array())
  {
    foreach ( array('css' => true, 'tickets' => true, 'barcode' => 'html') as $field => $value )
    if ( !isset($with[$field]) )
      $with[$field] = $value;
    
    sfApplicationConfiguration::getActive()->loadHelpers(array('I18N'));
    $tickets_html = '';
    
    // tickets w/ barcode
    if (!( isset($with['css']) && !$with['css'] ))
    {
      $tickets_html .= '<div style="clear: both"></div>';
      $tickets_html .= '<style type="text/css" media="all">'.file_get_contents(sfConfig::get('sf_web_dir').'/css/print-simplified-tickets.css').'</style>';
      if ( file_exists(sfConfig::get('sf_web_dir').'/private/print-simplified-tickets.css') )
        $tickets_html .= '<style type="text/css" media="all">'.file_get_contents(sfConfig::get('sf_web_dir').'/private/print-simplified-tickets.css').'</style>';
    }
    
    $content = array();
    if (!( isset($with['tickets']) && !$with['tickets'] ))
    foreach ( $this->Tickets as $ticket )
      $content[] = $ticket->renderSimplified($with['barcode']);
    
    return $tickets_html."\n".implode("\n", $content);
  }
  public function renderSimplifiedProducts(array $with = array())
  {
    $conf = sfConfig::get('app_transaction_email', array());
    $conf = isset($conf['products']) ? $conf['products'] : sfConfig::get('app_store_email_products', 'never');
    if ( in_array($conf, array('never', false)) )
      return false;
    
    foreach ( array('css' => true, 'products' => true, 'barcode' => 'png', 'qrcode_only_id' => false) as $field => $value )
    if ( !isset($with[$field]) )
      $with[$field] = $value;
    
    sfApplicationConfiguration::getActive()->loadHelpers(array('I18N'));
    $products_html = '';
    
    // tickets w/ barcode
    if (!( isset($with['css']) && !$with['css'] ))
    {
      $products_html .= '<div style="clear: both"></div>';
      $products_html .= '<style type="text/css" media="all">'.file_get_contents(sfConfig::get('sf_web_dir').'/css/print-simplified-tickets.css').'</style>';
      if ( file_exists(sfConfig::get('sf_web_dir').'/private/print-simplified-tickets.css') )
        $products_html .= '<style type="text/css" media="all">'.file_get_contents(sfConfig::get('sf_web_dir').'/private/print-simplified-tickets.css').'</style>';
    }
    
    $content = array();
    if (!( isset($with['products']) && !$with['products'] ))
    foreach ( $this->BoughtProducts as $product )
    {
      if ( $conf === 'e-product' && !$product->description_for_buyers )
        continue;
      $content[] = $product->renderSimplified($with['barcode'], $with['qrcode_only_id']);
    }
    
    return $products_html."\n".implode("\n", $content);
  }
}
