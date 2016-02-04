<?php

/**
 * PluginGroup
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    e-venement
 * @subpackage model
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginGroup extends BaseGroup
{
  public function preSave($event)
  {
    parent::preSave($event);
    
    // adding every active user to the permission if 0 given
    if ( $this->isModified() && $this->Users->count() == 0 && is_null($this->sf_guard_user_id) )
    {
      foreach ( Doctrine::getTable('sfGuardUser')->createQuery('u')
        ->andWhere('u.is_active = TRUE')
        ->leftJoin('u.Permissions up')
        ->leftJoin('u.Groups ug')
        ->leftJoin('ug.Permissions gp')
        ->andWhere('(gp.name = ? OR up.name = ?)', array('pr-group-common', 'pr-group-common'))
        ->execute() as $user )
      $this->Users[] = $user;
    }
  }
}
