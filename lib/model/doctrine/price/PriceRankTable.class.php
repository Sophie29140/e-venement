<?php

/**
 * PriceRankTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PriceRankTable extends PluginPriceRankTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object PriceRankTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PriceRank');
    }
}