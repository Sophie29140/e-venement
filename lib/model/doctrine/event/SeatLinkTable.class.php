<?php

/**
 * SeatLinkTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SeatLinkTable extends PluginSeatLinkTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object SeatLinkTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('SeatLink');
    }
}