<?php

/**
 * SurveyTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SurveyTable extends PluginSurveyTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object SurveyTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Survey');
    }
}