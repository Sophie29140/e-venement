<?php

/**
 * OptionLabelsTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class OptionLabelsTable extends PluginOptionLabelsTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object OptionLabelsTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('OptionLabels');
    }
}