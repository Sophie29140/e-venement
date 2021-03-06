<?php
/**********************************************************************************
*
*	    This file is part of e-venement.
*
*    e-venement is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License.
*
*    e-venement is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with e-venement; if not, write to the Free Software
*    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
*    Copyright (c) 2006-2015 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2015 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php

/**
 * Organism filter form.
 *
 * @package    e-venement
 * @subpackage filter
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class OrganismFormFilter extends BaseOrganismFormFilter
{
  /**
   * @see AddressableFormFilter
   */
  public function configure()
  {
    $this->widgetSchema   ['district'] = new liWidgetFormDoctrineJQueryAutocompleter(array(
      'model' => 'GeoFrDistrictBase',
      'url'   => url_for('geo_fr_street_base/districts'),
    ));
    $this->validatorSchema['district'] = new sfValidatorDoctrineChoice(array(
      'model' => 'GeoFrDistrictBase',
      'required' => false,
    ));
    
    $this->widgetSchema['groups_list']->setOption(
      'order_by',
      array('u.id IS NULL DESC, u.username, name','')
    );
    
    $this->widgetSchema['organism_category_id']->setOption('order_by',array('name',''));
    $this->widgetSchema['organism_category_id']->setOption('multiple',true);
    $this->widgetSchema['organism_category_id']->setOption('add_empty',false);
    $this->validatorSchema['organism_category_id']->setOption('multiple',true);
    
    $this->widgetSchema   ['contacts_groups'] = new cxWidgetFormDoctrineJQuerySelectMany(array(
      'model' => 'Group',
      'url'   => cross_app_url_for('rp', 'group/ajax'),
      'config' => '{ max: 300 }',
    ));
    $this->validatorSchema['contacts_groups'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Group',
      'required' => false,
      'multiple' => true,
    ));
    $this->widgetSchema   ['not_groups_list'] = new cxWidgetFormDoctrineJQuerySelectMany(array(
      'model' => 'Group',
      'url'   => cross_app_url_for('rp', 'group/ajax'),
      'config' => '{ max: 300 }',
    ));
    $this->validatorSchema['not_groups_list'] = $this->validatorSchema['groups_list'];
    
    $this->validatorSchema['not_organisms_list'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Organism',
      'required' => false,
      'multiple' => true,
    ));
    
    $this->widgetSchema   ['has_close_contact'] = new sfWidgetFormChoice(array(
      'choices' => $arr = array('' => 'yes or no', 1 => 'yes', 2 => 'no'),
    ));
    $this->validatorSchema['has_close_contact'] = new sfValidatorChoice(array(
      'choices' => array_keys($arr),
      'required' => false,
    ));
    
    $this->widgetSchema['professional_meta_event_id'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'MetaEvent',
      'order_by' => array('name',''),
      'multiple' => true,
    ));
    $this->validatorSchema['professional_meta_event_id'] = new sfValidatorDoctrineChoice(array(
      'model' => 'MetaEvent',
      'required' => false,
      'multiple' => true,
    ));
    
    $this->widgetSchema   ['region_id'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'GeoFrRegion',
      'order_by' => array('name',''),
      'add_empty' => true,
    ));
    $this->validatorSchema['region_id'] = new sfValidatorDoctrineChoice(array(
      'model' => 'GeoFrRegion',
      'required' => false,
    ));
    
    $this->widgetSchema   ['email_newsletter'] = $this->widgetSchema['npai'];
    $this->validatorSchema['email_newsletter'] = $this->validatorSchema['npai'];
    
    $this->widgetSchema   ['duplicates'] = new sfWidgetFormInputCheckbox;
    $this->validatorSchema['duplicates'] = new sfValidatorBoolean(array('required' => false));
        
    parent::configure();
  }
  
  public function setup()
  {
    $this->noTimestampableUnset = true;
    parent::setup();
  }
  
  public function getFields()
  {
    $fields = parent::getFields();
    $fields['district']             = 'District';
    $fields['duplicates']           = 'Duplicates';
    $fields['postalcode']           = 'Postalcode';
    $fields['contacts_groups']      = 'ContactsGroups';
    $fields['has_close_contact']    = 'HasCloseContact';
    $fields['professional_meta_event_id'] = 'ProfessionalMetaEventId';
    $fields['not_groups_list']      = 'NotGroupsList';
    $fields['region']               = 'RegionId';
    $fields['email_newsletter']     = 'EmailNewsletter';
    $fields['not_organisms_list']   = 'NotOrganismsList';

    return $fields;
  }

  public function addDistrictColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $o = $q->getRootAlias();
    if ( $value )
      $q->addWhere("(SELECT count(district_sb.id) FROM GeoFrStreetBase district_sb WHERE district_sb.iris2008 = ? AND $o.address = district_sb.address AND $o.postalcode = district_sb.zip AND $o.city = district_sb.city) > 0",$value);
    return $q;
  }


  public function addEmailNewsletterColumnQuery(Doctrine_Query $q, $field, $value)
  {
    if ( $value === '' )
      return $q;
    
    $a = $q->getRootAlias();
    if ( $value )
      return $q->addWhere("$a.email_no_newsletter = FALSE OR p.contact_email_no_newsletter = FALSE");
    else
      return $q->addWhere("$a.email_no_newsletter = TRUE AND p.contact_email_no_newsletter = TRUE");
  }
  public function addEmailNpaiColumnQuery(Doctrine_Query $q, $field, $value)
  {
    if ( $value === '' )
      return $q;
    
    $a = $q->getRootAlias();
    if ( $value )
      return $q->addWhere("$a.email_npai = TRUE OR p.contact_email_npai IS NOT NULL AND p.contact_email_npai = TRUE");
    else
      return $q->addWhere("$a.email_npai = FALSE AND NOT (p.contact_email_npai IS NOT NULL AND p.contact_email_npai = TRUE)");
  }

  public function addRegionIdColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    
    if ( intval($value) > 0 )
      $q->andWhere("SUBSTRING($a.postalcode,1,2) IN (SELECT REGEXP_REPLACE(dpt.num, '[a-zA-Z]', '0') FROM GeoFrDepartment dpt LEFT JOIN dpt.Region reg WHERE reg.id = ?)",$value)
        ->andWhere("LOWER($a.country) = ? OR TRIM($a.country) = ? OR $a.country IS NULL",array('france',''));
  }

  public function addContactsGroupsColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $c = $q->getRootAlias();
    if ( is_array($value) )
    {
      $q->leftJoin('c.Groups gc')
        ->leftJoin('p.Groups gp')
        ->andWhere('(TRUE')
        ->andWhereIn("gp.id",$value)
        ->orWhereIn("gc.id",$value)
        ->andWhere('TRUE)');
    }
    
    return $q;
  }
  public function addHasCloseContactColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $c = $q->getRootAlias();
    if ( $value === '1' )
      $q->andWhere("$c.professional_id IS NOT NULL");
    if ( $value === '0' )
      $q->andWhere("$c.professional_id IS NULL");
    
    return $q;
  }
  public function addProfessionalMetaEventIdColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $c = $q->getRootAlias();
    if ( is_array($value) )
    {
      $q->leftJoin('p.Transactions tr')
        ->andWhere('tr.id IS NOT NULL')
        ->andWhere('p.id IS NOT NULL');
    }
    
    return $q;
  }
  public function addPostalcodeColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $c = $q->getRootAlias();
    if ( $value['text'] )
    {
      $q->andWhere("$c.postalcode LIKE ?",$value['text'].'%');
    }
    
    if ( $value['is_empty'] )
    {
      $q->andWhere("$c.postalcode IS NULL or $c.postalcode = ''");
    }
    
    return $q;
  }
  
  public function addDuplicatesColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $c = $q->getRootAlias();
    if ( $value )
    {
      $raw_q = new Doctrine_RawSql();
      $raw_q->select('c.id')
        ->from('Organism c')
        ->leftJoin('(select lower(name) as name, lower(city) as city, count(*) AS nb from organism group by lower(name), lower(city) order by lower(name), lower(city)) AS c2 ON c2.city ILIKE c.city AND c2.name ILIKE c.name')
        ->where('c2.nb > 1')
        ->orderBy('lower(c.name), lower(c.city), c.id')
        ->addComponent('c','Organism')
        ->addComponent('c2','Organism');
      $contact_ids = $raw_q->execute(array(),Doctrine::HYDRATE_ARRAY);
      
      $ids = array();
      foreach ( $contact_ids as $id )
        $ids[] = $id['id'];
      
      $q->andWhereIn("$c.id",$ids);
    }
    
    return $q;
  }
  
  public function addNotGroupsListColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    
    if ( is_array($value) )
    {
      $q1 = new Doctrine_Query();
      $q1->select('gotmp.organism_id')
        ->from('GroupOrganism gotmp')
        ->andWhereIn('gotmp.group_id',$value);
      
      $q->andWhere("$a.id NOT IN (".$q1.")",$value); // hack for inserting $value
    }
    
    return $q;
  }
  
  public function addNotOrganismsListColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    
    if ( is_array($value) )
    {
      $q1 = new Doctrine_Query();
      $q1->select('gotmp.organism_id')
        ->from('GroupOrganism gotmp')
        ->andWhereIn('gotmp.group_id',$value);
      
      $q->andWhereNotIn("$a.id",$value);
    }
    
    return $q;
  }
  
  public function addDescriptionColumnQuery(Doctrine_Query $q, $field, $value)
  {
    $a = $q->getRootAlias();
    
    if (!( $value && is_array($value)
      && (trim($value['text']) || isset($value['is_empty']) && $value['is_empty']) ))
      return $q;
    
    if ( isset($value['is_empty']) && $value['is_empty'] )
      return $q->andWhere("$a.description = ?", '');
    
    // includes a batch of OR clauses inside a AND context
    foreach ( explode(' ', str_replace('  ', ' ', trim($value['text']))) as $str )
    if ( $str )
      $this->addTextQuery($q, $field, array('text' => $str));
    
    return $q;
  }  
}
