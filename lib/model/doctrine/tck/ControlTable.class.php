<?php

/**
 * ControlTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ControlTable extends PluginControlTable
{
  public function createListQuery()
  {
    $raw = new Doctrine_RawSql;
    $raw->select('{r.res}, {r.*}')
      ->from("(
        (SELECT false AS automatic, 'failed' as res, 'failed-'||id AS id, fc.sf_guard_user_id, fc.ticket_id, fc.checkpoint_id, fc.comment, fc.created_at, fc.updated_at, fc.version FROM failed_control fc)
        UNION
        (SELECT automatic, 'success' as res, id::varchar, c.sf_guard_user_id, c.ticket_id::varchar, c.checkpoint_id, c.comment, c.created_at, c.updated_at, c.version from control c)
      ) AS r")
      ->addComponent('r', 'Control');
    
    return $raw;
  }
  
  /**
   * Returns an instance of this class.
   *
   * @return object ControlTable
   */
  public static function getInstance()
  {
      return Doctrine_Core::getTable('Control');
  }
}
