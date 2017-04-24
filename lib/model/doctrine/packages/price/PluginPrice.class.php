<?php

/**
 * PluginPrice
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    e-venement
 * @subpackage model
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginPrice extends BasePrice
{
  public function actAs($tpl, array $options = array())
  {
    $options['table'] = $this->getTable();
    return parent::actAs($tpl, $options);
  }
  public function getIndexesPrefix()
  {
    return strtolower(get_class($this));
  }

  public function postInsert($event)
  {      
    $rank = new PriceRank;
    $rank->rank = $this->id;
    $rank->price_id = $this->id;
    $rank->domain = sfConfig::get('project_internals_users_domain', '');
    $rank->save();      
    
    $this->Ranks[0] = $rank;
    
    return parent::postInsert($event);
  }

  public function getUsers($load)
  {
    return liDoctrineRelationAssociationUsers::removeUpperUsersFromCollection($this->_get('Users', $load));
  }
}
