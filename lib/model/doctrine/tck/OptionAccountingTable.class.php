<?php

/**
 * OptionAccountingTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class OptionAccountingTable extends PluginOptionAccountingTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object OptionAccountingTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('OptionAccounting');
    }
}