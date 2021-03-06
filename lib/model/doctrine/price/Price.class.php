<?php

/**
 * Price
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    e-venement
 * @subpackage model
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Price extends PluginPrice implements liUserAccessInterface
{
  public function getDescriptionName()
  {
    return $this->description
      ? $this->description
      : $this->name;
  }
  public function getFullName()
  {
    sfApplicationConfiguration::getActive()->loadHelpers(array('Number'));
    return $this->description.' ('.$this->name.'), '.format_currency($this->value,sfContext::hasInstance() ? sfContext::getInstance()->getConfiguration()->getCurrency() : '€');
  }
  
  public function getWorkspaceIds()
  {
    return array_keys($this->Workspaces->getPrimaryKeys());
  }
  
  public function isAccessibleBy(sfSecurityUser $user, $option = NULL)
  {
    if ( $this->isNew() )
      return false;
    
    if ( Doctrine::getTable('UserPrice')->createQuery('up')
      ->andWhere('up.price_id = ?', $this->id)
      ->andWhere('up.sf_guard_user_id = ?', $user->getId())
      ->count() == 0 )
      return false;
    if ( ! $user instanceof pubUser )
      return true;
    
    // continue after this comment if we are in an online sales context
    
    // not linked to any member card
    if ( !$this->member_card_linked || !$this->isNew() )
      return true;
    
    $manifestation = NULL;
    if ( isset($option['manifestation']) && $option['manifestation'] instanceof Manifestation )
      $manifestation = $option['manifestation'];
    $mcp = $user->getAvailableMCPrices($manifestation);
    
    if ( !isset($mcp[$this->id]) )
      return false;
    if ( isset($mcp[$this->id]['']) && $mcp[$this->id][''] > 0 )
      return true;
    if ( $manifestation && isset($mcp[$this->id][$manifestation->event_id]) && $mcp[$this->id][$manifestation->event_id] > 0 )
      return true;
    return false;
  }
}
