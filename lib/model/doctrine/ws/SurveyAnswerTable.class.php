<?php

/**
 * SurveyAnswerTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SurveyAnswerTable extends PluginSurveyAnswerTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object SurveyAnswerTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('SurveyAnswer');
    }
}