<?php

require_once dirname(__FILE__).'/../lib/locationGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/locationGeneratorHelper.class.php';

/**
 * location actions.
 *
 * @package    e-venement
 * @subpackage location
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class locationActions extends autoLocationActions
{
  public function executeCalendar(sfWebRequest $request)
  {
    $this->executeEdit($request);
  }
  public function executeEdit(sfWebRequest $request)
  {
    if ( !$this->getRoute()->getObject()->place )
      throw new sfError404Exception(sprintf('Unable to find the %s object, it is not a location.', $this->options['model']));
    parent::executeEdit($request);
  }
  public function executeUpdate(sfWebRequest $request)
  {
    if ( !$this->getRoute()->getObject()->place )
      throw new sfError404Exception(sprintf('Unable to find the %s object, it is not a location.', $this->options['model']));
    parent::executeUpdate($request);
  }
  public function executeDelete(sfWebRequest $request)
  {
    if ( !$this->getRoute()->getObject()->place )
      throw new sfError404Exception(sprintf('Unable to find the %s object, it is not a location.', $this->options['model']));
    parent::executeDelete($request);
  }
  
  public function executeNewManif(sfWebRequest $request)
  {
    // preconditions
    $this->executeEdit($request);
    if ( !$request->getParameter('event_name',false) && !$request->getParameter('event_id',false) )
      throw new liEvenementException('Bad request.');
    
    if ( $eid = $request->getParameter('event_id',false) )
      $event = Doctrine::getTable('Event')->createQuery('e')->andWhere('e.id = ?',$eid)->fetchOne();
    else
    {
      $event = new Event;
      $event->name = $request->getParameter('event_name');
      $me = array_keys($this->getUser()->getMetaEventsCredentials());
      $event->meta_event_id = $me[0];
      $event->save();
    }
    
    $this->redirect('manifestation/new?event='.$event->slug.'&location='.$this->location->slug);
  }

  public function executeSearch(sfWebRequest $request)
  {
    self::executeIndex($request);
    $table = Doctrine::getTable('Location');
    
    $search = $this->sanitizeSearch($request->getParameter('s'));
    $transliterate = sfConfig::get('software_internals_transliterate',array());
    
    $a = $this->pager->getQuery()->getRootAlias();
    $this->pager->setQuery($table->search($search.'*',$this->pager->getQuery()->andWhere("$a.place = ?",true)));
    $this->pager->setPage($request->getParameter('page') ? $request->getParameter('page') : 1);
    $this->pager->init();
    
    $this->setTemplate('index');
  }
  public static function sanitizeSearch($search)
  {
    $nb = mb_strlen($search);
    $charset = sfConfig::get('software_internals_charset');
    $transliterate = sfConfig::get('software_internals_transliterate',array());
    
    $search = str_replace(preg_split('//u', $transliterate['from'], -1), preg_split('//u', $transliterate['to'], -1), $search);
    $search = str_replace(MySearchAnalyzer::$cutchars,' ',$search);
    $search = preg_replace(array('!^[\d\w] !', '! [\d\w] !'), ' ', $search);
    $search = mb_strtolower(iconv($charset['db'],$charset['ascii'], mb_substr($search,$nb-1,$nb) == '*' ? mb_substr($search,0,$nb-1) : $search));
    return $search;
  }
}
