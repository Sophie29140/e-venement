<?php

/**
 * PluginColorPriceTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginColorPriceTable extends ColorTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object PluginColorPriceTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PluginColorPrice');
    }
}