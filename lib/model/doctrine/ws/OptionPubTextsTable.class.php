<?php

/**
 * OptionPubTextsTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class OptionPubTextsTable extends PluginOptionPubTextsTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object OptionPubTextsTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('OptionPubTexts');
    }
}