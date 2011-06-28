<?php

/**
 * ledger actions.
 *
 * @package    e-venement
 * @subpackage ledger
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ledgerActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->redirect('ledger/sales');
  }
  
  protected function formatCriterias(sfWebRequest $request)
  {
    $this->form = new LedgerCriteriasForm();
    $criterias = $request->getParameter($this->form->getName());
    
    if ( !$criterias['users'][0] && count($criterias['users']) == 1 )
      unset($criterias['users']);
    
    $this->form->bind($criterias, $request->getFiles($this->form->getName()));
    if ( !$this->form->isValid() )
    {
      $user->setFlash('error','Submitted values are invalid');
    }
    
    $dates = array(
      $criterias['dates']['from']['day']
        ? strtotime($criterias['dates']['from']['year'].'-'.$criterias['dates']['from']['month'].'-'.$criterias['dates']['from']['day'])
        : strtotime('1 month ago 0:00'),
      $criterias['dates']['to']['day']
        ? strtotime($criterias['dates']['to']['year'].'-'.$criterias['dates']['to']['month'].'-'.$criterias['dates']['to']['day'])
        : strtotime('tomorrow 0:00'),
    );
    
    if ( $dates[0] > $dates[1] )
    {
      $buf = $dates[1];
      $dates[1] = $dates[0];
      $dates[0] = $buf;
    }
    $criterias['dates'] = $dates;
    
    return $criterias;
  }
  
  public function executeSales(sfWebRequest $request)
  {
    $criterias = $this->formatCriterias($request);
    $dates = $criterias['dates'];
    
    $q = Doctrine::getTable('Event')->createQuery('e')
      ->leftJoin('e.Manifestations m')
      ->leftJoin('m.Location l')
      ->leftJoin('m.Tickets tck')
      ->leftJoin('tck.Transaction t')
      ->leftJoin('t.Contact c')
      ->leftJoin('t.Professional pro')
      ->leftJoin('pro.Organism o')
      ->leftJoin('tck.User u')
      ->andWhere('tck.created_at >= ? AND tck.created_at < ?',array(
        date('Y-m-d',$dates[0]),
        date('Y-m-d',$dates[1]),
      ))
      ->andWhere('tck.duplicate IS NULL')
      ->andWhere('tck.printed = TRUE')
      ->orderBy('e.name, m.happens_at, l.name, tck.price_name, tck.created_at');
    
    $q->andWhereIn('t.type',array('normal', 'cancellation'));
    
    if ( isset($criterias['users']) && is_array($criterias['users']) && $criterias['users'][0] )
      $q->andWhereIn('t.sf_guard_user_id',$criterias['users']);
    
    $this->events = $q->execute();
    $this->dates = $dates;
  }
  
  public function executeCash(sfWebRequest $request)
  {
    $criterias = $this->formatCriterias($request);
    $dates = $criterias['dates'];
    
    $q = $this->buildCashQuery($criterias);
    $this->methods = $q->execute();
    $this->dates = $dates;
  }
  
  protected function buildCashQuery($criterias)
  {
    $dates = $criterias['dates'];
    
    $q = Doctrine::getTable('PaymentMethod')->createQuery('m')
      ->leftJoin('m.Payments p')
      ->leftJoin('p.Transaction t')
      ->leftJoin('t.Contact c')
      ->leftJoin('t.Professional pro')
      ->leftJoin('pro.Organism o')
      ->leftJoin('p.User u')
      ->leftJoin('u.MetaEvents')
      ->leftJoin('u.Workspaces')
      ->andWhere('p.created_at >= ? AND p.created_at < ?',array(
        date('Y-m-d',$dates[0]),
        date('Y-m-d',$dates[1]),
      ))
      ->orderBy('m.name, m.id, t.id, p.value, p.created_at');
    
    if ( isset($criterias['users']) && is_array($criterias['users']) && $criterias['users'][0] )
      $q->andWhereIn('p.sf_guard_user_id',$criterias['users']);
    
    return $q;
  }
  
  public function executeBoth(sfWebRequest $request)
  {
    // filtering criterias
    $criterias = $this->formatCriterias($request);
    $dates = $criterias['dates'];
    
    // by payment-type
    $q = $this->buildCashQuery($criterias);
    $this->byPaymentMethod = $q->execute();
    
    // by price
    $q = Doctrine::getTable('Price')->createQuery('p')
      ->leftJoin('p.Tickets t')
      ->andWhere('t.printed')
      ->andWhere('t.duplicate IS NULL')
      ->andWhere('t.created_at >= ? AND t.created_at < ?',array(
        date('Y-m-d',$dates[0]),
        date('Y-m-d',$dates[1]),
      ))
      ->orderBy('p.name');
    $this->byPrice = $q->execute();
    
    // by price's value
    $pdo = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
    $q = "SELECT value, count(id) AS nb, sum(value) AS total
          FROM ticket
          WHERE created_at >= :date0
            AND created_at < :date1
            AND id NOT IN (SELECT cancelling FROM ticket WHERE created_at >= :date0 AND created_at < :date1 AND cancelling IS NOT NULL AND printed)
            AND (cancelling IS NULL OR cancelling NOT IN (SELECT id FROM ticket WHERE created_at >= :date0 AND created_at < :date1 AND printed))
            AND printed
            AND duplicate IS NULL
          GROUP BY value
          ORDER BY value DESC";
    $stmt = $pdo->prepare($q);
    $stmt->execute(array('date0' => date('Y-m-d',$dates[0]),'date1' => date('Y-m-d',$dates[1])));
    $this->byValue = $stmt->fetchAll();
    
    // synthesis by user
    $q = new Doctrine_Query();
    $q->from('SfGuardUser u')
      ->leftJoin('u.Tickets t')
      ->select('u.id, u.last_name, u.first_name, u.username')
      ->addSelect('count(t.id) AS nb')
      ->addSelect('sum(case when t.value < 0 then 0 else t.value end)/sum(case when t.value < 0 then 0 else 1 end) AS average')
      ->addSelect('sum(t.value = 0 AND cancelling IS NULL) AS nb_free')
      ->addSelect('sum(t.value > 0) AS nb_paying')
      ->addSelect('sum(t.value <= 0 AND cancelling IS NOT NULL) AS nb_cancelling')
      ->addSelect('sum(case when t.value < 0 then 0 else t.value end)/sum(value > 0) AS average_paying')
      ->addSelect('sum(case when t.value < 0 then 0 else t.value end) AS income')
      ->addSelect('sum(case when t.value > 0 then 0 else t.value end) AS outcome')
      ->andWhere('t.created_at >= ? AND t.created_at < ?',array(
        date('Y-m-d',$dates[0]),
        date('Y-m-d',$dates[1]),
      ))
      ->andWhere('t.duplicate IS NULL')
      ->andWhere('t.printed')
      ->orderBy('u.last_name, u.first_name, u.username')
      ->groupBy('u.id, u.last_name, u.first_name, u.username');
    $this->byUser = $q->execute();
  }
}
