<?php

/**
 * PluginBoughtProduct
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    e-venement
 * @subpackage model
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginBoughtProduct extends BaseBoughtProduct
{
  public function preSave($event)
  {
    // if the item is not being bought or is bought already, modifications are not allowed
    $mods = $this->getModified();
    if ( $this->integrated_at && !isset($mods['integrated_at']) )
      throw new liEvenementException('Trying to modify the #'.$this->id.' item which has been bought already.');
    
    parent::preSave($event);
    
    if ( !$this->vat_id )
      $this->Vat = $this->Declination->Product->Vat;
    if ( !$this->vat )
      $this->vat = $this->Vat->value ? $this->Vat->value : 0;
    if ( !$this->price_name )
      $this->price_name = (string)$this->Price;
    
    if ( !$this->value )
    foreach ( $this->Declination->Product->PriceProducts as $p )
    if ( $this->price_id == $p->price_id )
      $this->value = $p->value ? $p->value : 0; // free price here
    
    if ( !$this->name )
      $this->name = (string)$this->Declination->Product;
    if ( !$this->declination )
      $this->declination = (string)$this->Declination;
    
    if ( !$this->code )
      $this->code = $this->Declination->code;
    if ( !$this->description_for_buyers && $this->Declination->description_for_buyers )
      $this->description_for_buyers = $this->Declination->description_for_buyers;
  }
  
  public function isSold()
  {
    return !$this->integrated_at;
  }
  
  public function getIndexesPrefix()
  {
    return strtolower(get_class($this));
  }
}
