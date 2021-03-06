<?php

/**
 * Event filter form.
 *
 * @package    e-venement
 * @subpackage filter
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class EventFormFilter extends BaseEventFormFilter
{
  public function configure()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('CrossAppLink'));
    $this->widgetSchema['companies_list'] = new cxWidgetFormDoctrineJQuerySelectMany(array(
      'model' => 'Organism',
      'url'   => cross_app_url_for('rp','organism/ajax'),
    ));
    
    $this->widgetSchema   ['meta_event_id']->setOption('multiple',true);
    $this->widgetSchema   ['meta_event_id']->setOption('query', Doctrine::getTable('MetaEvent')->createQuery('me')
      ->andWhereIn('me.id',array_keys(sfContext::getInstance()->getUser()->getMetaEventsCredentials()))
    );
    $this->widgetSchema   ['meta_event_id']
      ->setOption('add_empty',false)
      ->setOption('order_by',array('translation.name',''));
    $this->validatorSchema['meta_event_id']->setOption('multiple',true);
    $this->widgetSchema   ['workspaces_list'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'Workspace',
      'query' => $q = Doctrine::getTable('Workspace')->createQuery('ws')
        ->andWhereIn('ws.id', array_keys(sfContext::getInstance()->getUser()->getWorkspacesCredentials())),
      'multiple' => true,
      'order_by' => array('name',''),
    ));
    $this->validatorSchema['workspaces_list'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Workspace',
      'query' => $q,
      'required' => false,
      'multiple' => true,
    ));
    
    $this->widgetSchema['event_category_id']
      ->setOption('order_by',array('name',''))
      ->setOption('multiple', true)
      ->setOption('add_empty', false);
    $this->validatorSchema['event_category_id']
      ->setOption('multiple', true);
    
    $this->widgetSchema   ['day_of_the_week'] = new sfWidgetFormChoice(array(
      'choices' => $choices = array(
        '' => '',
        1  => __('Monday', null, 'generic'),
        2  => __('Tuesday', null, 'generic'),
        3  => __('Wednesday', null, 'generic'),
        4  => __('Thrusday', null, 'generic'),
        5  => __('Friday', null, 'generic'),
        6  => __('Saturday', null, 'generic'),
        0  => __('Sunday', null, 'generic'),
      )
    ));
    $this->validatorSchema['day_of_the_week'] = new sfValidatorChoice(array(
      'choices' => array_keys($choices),
      'required' => false,
    ));
    $this->widgetSchema   ['location_id'] = new sfWidgetFormDoctrineChoice(array(
      'add_empty' => true,
      'model'     => 'Location',
      'order_by'  => array('place DESC, rank, name',''),
      'method'    => '__toStringWithPrefix',
    ));
    $this->validatorSchema['location_id'] = new sfValidatorDoctrineChoice(array(
      'required'  => false,
      'model'     => 'Location',
    ));
    
    sfContext::getInstance()->getConfiguration()->loadHelpers(array('CrossAppLink'));
    $this->widgetSchema   ['contact_id'] = new liWidgetFormDoctrineJQueryAutocompleter(array(
      'model'     => 'Contact',
      'url'       => cross_app_url_for('rp', 'contact/ajax'),
    ));
    $this->validatorSchema['contact_id'] = new sfValidatorDoctrineChoice(array(
      'required'  => false,
      'model'     => 'Contact',
    ));
    
    $this->widgetSchema   ['age'] = $this->widgetSchema   ['age_min'];
    $this->validatorSchema['age'] = $this->validatorSchema['age_min'];
    
    $this->widgetSchema   ['manif_confirmed'] =
    $this->widgetSchema   ['manif_optional'] =
    $this->widgetSchema   ['manif_conflict'] =
    $this->widgetSchema   ['manif_blocking'] = new sfWidgetFormChoice(array(
      'choices' => $choices = array('' => "doesn't matter", 1 => 'only them', 0 => 'exclude them'),
    ));
    $this->validatorSchema['manif_confirmed'] =
    $this->validatorSchema['manif_optional'] =
    $this->validatorSchema['manif_conflict'] =
    $this->validatorSchema['manif_blocking'] = new sfValidatorChoice(array(
      'choices' => array_keys($choices),
      'required' => false,
    ));
    
    $this->widgetSchema   ['colors_list'] = new liWidgetFormDoctrineChoice(array(
      'model'       => 'Color',
      'add_empty'   => array('-1', 'No color'),
      'method'      => 'getName',
      'multiple'    => true,
    ));
    $this->validatorSchema['colors_list'] = new liValidatorDoctrineChoice(array(
      'model'       => 'Color',
      'multiple'    => true,
      'null_value'  => '-1',
      'required'    => false,
    ));
    $this->widgetSchema   ['voucherized'] = new sfWidgetFormChoice(array(
      'choices' => $arr = array('0' => 'yes or no', 1 => 'yes', -1 => 'no'),
    ));
    $this->validatorSchema['voucherized'] = new sfValidatorChoice(array(
      'choices'       => array_keys($arr),
      'required'      => false,
    ));
    $this->widgetSchema   ['dates_range'] = new sfWidgetFormFilterDate(array(
      'from_date' => new liWidgetFormJQueryDateText(array('culture' => sfContext::getInstance()->getUser()->getCulture())),
      'to_date'   => new liWidgetFormJQueryDateText(array('culture' => sfContext::getInstance()->getUser()->getCulture())),
      'template'  => '<span class="from">'.__('From %from_date%').'</span> <span class="to">'.__('to %to_date% excluded').'</span>',
      'with_empty'=> false,
    ));
    $this->validatorSchema['dates_range'] = new sfValidatorDateRange(array(
      'from_date'     => new sfValidatorDate(array(
        'required'    => false,
        'date_output' => 'Y-m-d',
        'with_time'   => false,
      )),
      'to_date'       => new sfValidatorDate(array(
        'required'    => false,
        'date_output' => 'Y-m-d',
        'with_time'   => false,
      )),
      'required' => false,
    ));
    
    $this->widgetSchema   ['participants_list'] = new sfWidgetFormDoctrineChoice($arr = array(
      'model' => 'Contact',
      'query' => Doctrine::getTable('Contact')->createQuery('c')
        ->leftJoin('c.InvolvedIn m')
        ->andWhere('m.id IS NOT NULL')
        ->andWhere('c.confirmed = ?', true)
        ->orderBy('m.happens_at DESC')
        ->limit(50),
      'multiple' => true,
    ));
    $this->validatorSchema['participants_list'] = new sfValidatorDoctrineChoice($arr + array(
      'required' => false,
    ));

    $this->validatorSchema['id'] = new SfValidatorPass();
  }
  public function buildQuery(array $values)
  {
    return $this->addCredentialsQueryPart(
      parent::buildQuery($values)
    );
  }
  
  public function getFields()
  {
    return array_merge(parent::getFields(),array(
      'workspaces_list'   => 'WorkspacesList',
      'location_id'       => 'LocationId',
      'colors_list'       => 'ColorsList',
      'dates_range'       => 'DatesRange',
      'participants_list' => 'ParticipantsList',
      'voucherized'       => 'Voucherized',
      'id'                => 'Id'    
    ));
  }
  protected function getTranslatedFields($fieldName = NULL)
  {
    $fields = array(
      'manif_optional'  => 'm.reservation_optional',
      'manif_confirmed' => 'm.reservation_confirmed',
      'manif_blocking'  => 'm.blocking',
    );
    
    return !is_null($fieldName)
      ? $fields[$fieldName]
      : $fields;
  }
  
  public static function addCredentialsQueryPart(Doctrine_Query $query, $me = 'me')
  {
    return $query
      ->andWhere('(TRUE')
      ->andWhere("$me.id IS NULL")
      ->orWhereIn("$me.id",array_keys(sfContext::getInstance()->getUser()->getMetaEventsCredentials()))
      ->andWhere('TRUE)');
  }
  
  // I18N
  public function addNameColumnQuery(Doctrine_Query $q, $field, $values)
  { return $this->addI18nTextQuery($q, $field, $values, 'translation'); }
  public function addShortNameColumnQuery(Doctrine_Query $q, $field, $values)
  { return $this->addI18nTextQuery($q, $field, $values, 'translation'); }
  public function addDescriptionColumnQuery(Doctrine_Query $q, $field, $values)
  { return $this->addI18nTextQuery($q, $field, $values, 'translation'); }
  public function addExtradescColumnQuery(Doctrine_Query $q, $field, $values)
  { return $this->addI18nTextQuery($q, $field, $values, 'translation'); }
  public function addExtraspecColumnQuery(Doctrine_Query $q, $field, $values)
  { return $this->addI18nTextQuery($q, $field, $values, 'translation'); }
  
  public function addDayOfTheWeekColumnQuery(Doctrine_Query $q, $field, $values)
  {
    if ( $values === '' )
      return;
    
    $a = $q->getRootAlias();
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    
    $q->andWhere('extract(dow from m.happens_at) = ?', $values);
    return $q;
  }
  public function addDatesRangeColumnQuery(Doctrine_Query $q, $field, $values)
  {
    if ( !$values )
      return $q;
    if (!( $values && is_array($values) && ($values['from'] || $values['to']) ))
      return $q;
    
    $a = $q->getRootAlias();
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    
    if ( $values['from'] )
      $q->andWhere('m.reservation_begins_at >= ? OR m.happens_at >= ?', array($values['from'], $values['from']));
    if ( $values['to'] )
      $q->andWhere("m.reservation_ends_at < ?", array($values['to']));
    
    return $q;
  }
  public function addIdColumnQuery(Doctrine_Query $q, $field, $values)
  {
    if ( $values === '' )
      return $q;
    
    $q->andWhereIn('id', $values);
    return $q;
  }
  public function addVoucherizedColumnQuery(Doctrine_Query $q, $field, $values)
  {
    if ( is_null($values) || $values == '0' )
      return $q;
    
    $a = $q->getRootAlias();
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    
    if ( $values['from'] )
      $q->andWhere('m.voucherized = ?', $values == '1');
    
    return $q;
  }
  public function addColorsListColumnQuery(Doctrine_Query $q, $field, $values)
  {
    if ( !$values )
      return $q;
    
    $a = $q->getRootAlias();
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    
    if ( ($i = array_search($this->validatorSchema['colors_list']->getOption('null_value'), $values)) !== false )
    {
      if ( count($values) > 1 )
      {
        $q->andWhere('(TRUE');
        unset($values[$i]);
      }
      $q->andWhere('m.color_id IS NULL');
      if ( count($values) > 1 )
      {
        $q->orWhereIn('m.color_id', $values)
          ->andWhere('TRUE)');
      }
    }
    else
      $q->andWhereIn('m.color_id', $values);
    
    return $q;
  }
  
  public function addLocationIdColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    
    if ( intval($value) > 0 )
      return $q
        ->leftJoin('m.Booking b')
        ->andWhere('(TRUE')
        ->andWhere('m.location_id = ?', intval($value))
        ->orWhere('b.id = ?', intval($value))
        ->andWhere('TRUE)');
    return $q;
  }
  public function addWorkspacesListColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    if ( !$q->contains("LEFT JOIN m.Gauges g") )
      $q->leftJoin("m.Gauges g");
    
    if ( $value )
      return $q->andWhereIn('g.workspace_id', $value);
    return $q;
  }
  public function addContactIdColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    
    if ( intval($value) > 0 )
      return $q->andWhere('m.contact_id = ?',intval($value));
    return $q;
  }
  public function addManifConflictColumnQuery(Doctrine_Query $q, $field, $value)
  {
    if ( !in_array($value,array(0,1)) )
      return $q;
    
    $a = $q->getRootAlias();
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    
    $conflicts = Doctrine::getTable('Manifestation')->getConflicts(array('potentially' => true));
    return $q->andWhereIn('m.id', array_keys($conflicts));
  }
  public function addManifOptionalColumnQuery(Doctrine_Query $q, $field, $value)
  { return $this->addTranslatedBooleanQuery($q, $field, $value); }
  public function addManifConfirmedColumnQuery(Doctrine_Query $q, $field, $value)
  { return $this->addTranslatedBooleanQuery($q, $field, $value); }
  public function addManifBlockingColumnQuery(Doctrine_Query $q, $field, $value)
  { return $this->addTranslatedBooleanQuery($q, $field, $value); }
  public function addTranslatedBooleanQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    
    if ( !in_array($value,array(0,1)) )
      return $q;
    return $q->andWhere($this->getTranslatedFields($field).' = ?', $value == 1);
  }
  
  public function addAgeMinColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    if ( isset($value['text']) && $value['text'] )
      $q->andWhere("$a.$field <= ?",$value['text']);
  }
  public function addAgeMaxColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    if ( isset($value['text']) && $value['text'] )
      $q->andWhere("$a.$field >= ?",$value['text']);
  }
  public function addAgeColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    if ( isset($value['text']) && $value['text'] )
      $q->andWhere("$a.age_min <= ?",$value['text'])
        ->andWhere("$a.age_max >= ?",$value['text']);
  }
  
  public function addParticipantsListColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    if ( $value && !is_array($value) )
      $value = array($value);
    if ( count($value) == 0 )
      return $q;
    
    if ( !$q->contains("LEFT JOIN $a.Manifestations m") )
      $q->leftJoin("$a.Manifestations m");
    if ( !$q->contains("LEFT JOIN m.Participants participant") )
      $q->leftJoin("m.Participants participant");
    $q->andWhereIn('participant.id',$value);
    
    return $q;
  }
}
