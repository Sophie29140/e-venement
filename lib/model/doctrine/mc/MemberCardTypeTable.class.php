<?php

/**
 * MemberCardTypeTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class MemberCardTypeTable extends PluginMemberCardTypeTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object MemberCardTypeTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('MemberCardType');
    }
}