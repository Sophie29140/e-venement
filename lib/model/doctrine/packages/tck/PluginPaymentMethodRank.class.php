<?php

/**
 * PluginPaymentMethodRank
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    symfony
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginPaymentMethodRank extends BasePaymentMethodRank
{
  public function preSave($event)
  {
    $this->domain = sfConfig::get('project_internals_users_domain', '');
  
    return parent::preSave($event);
  }
}
