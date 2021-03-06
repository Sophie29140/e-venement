<?php

/**
 * SeatedPlanTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SeatedPlanTable extends PluginSeatedPlanTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object SeatedPlanTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('SeatedPlan');
    }
    
    public function retreiveOrderedList()
    {
      return $this->createQuery('sp')
        ->leftJoin('sp.Location l')
        ->leftJoin('sp.Workspaces w')
        ->orderBy('l.name, w.name');
    }
}
