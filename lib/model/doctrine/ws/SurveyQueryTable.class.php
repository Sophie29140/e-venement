<?php

/**
 * SurveyQueryTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SurveyQueryTable extends PluginSurveyQueryTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object SurveyQueryTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('SurveyQuery');
    }
}