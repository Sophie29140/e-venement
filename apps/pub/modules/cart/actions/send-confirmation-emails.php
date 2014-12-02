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
*    Copyright (c) 2006-2012 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2012 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
    // sending emails to contact and organizators
    if ( !sfConfig::has('app_texts_email_confirmation') )
      throw new liOnlineSaleException('You need to configure app_texts_email_confirmation in your apps/pub/config/app.yml file');
    
    sfApplicationConfiguration::getActive()->loadHelpers(array('Date','Number','I18N', 'Url'));
    
    // command is not yet i18n, only french
    $command = 'Commande #'.$transaction->id;
    if ( $transaction->Contact )
      $command .= ' pour '.$transaction->Contact;
    $command .= "\n";
    
    // tickets
    if ( $transaction->Tickets->count() > 0 )
    {
      $events = array();
      foreach ( $transaction->Tickets as $ticket )
      {
        if ( !isset($events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id]) ) $events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id] = array();
        if ( !isset($events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id][$ticket->Manifestation->id]) ) $events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id][$ticket->Manifestation->id] = array();
        
        $events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id]['event'] = $ticket->Manifestation->Event;
        $events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id][$ticket->Manifestation->id]['manif'] = $ticket->Manifestation;
        if ( !isset($events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id][$ticket->Manifestation->id][$ticket->price_id]) )
          $events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id][$ticket->Manifestation->id][$ticket->price_id] = array(
            'qty' => 0,
            'price' => $ticket->Price,
            'value' => 0,
          );
        $events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id][$ticket->Manifestation->id][$ticket->price_id]['qty']++;
        $events[$ticket->Manifestation->happens_at.' -- '.$ticket->Manifestation->event_id][$ticket->Manifestation->id][$ticket->price_id]['value'] += $ticket->value;
      }
      ksort($events);
      
      foreach ( $events as $event )
      {
        $command .= "\n".$event['event'].": \n";
        unset($event['event']);
        foreach ( $event as $manif )
        {
          $command .= "&nbsp;&nbsp;Le ".format_datetime($manif['manif']->happens_at)." à ".$manif['manif']->Location.(($sp = $ticket->Manifestation->Location->getWorkspaceSeatedPlan($ticket->Gauge->workspace_id)) ? '*' : '')."\n";
          unset($manif['manif']);
          foreach ( $manif as $tickets )
            $command .= "&nbsp;&nbsp;&nbsp;&nbsp;".($tickets['price']->description ? $tickets['price']->description : $tickets['price'])." x ".$tickets['qty']." = ".format_currency($tickets['value'],'€')."\n";
        }
      }
    }
    
    // member cards
    if ( $transaction->MemberCards->count() > 0 )
    {
      $command .= "\n";
      $command .= __("Member cards")."\n";
      foreach ( $transaction->MemberCards as $mc )
      $command .= $mc."\n";
    }
    
    // footer
    $command .= "\n";
    $command .= __('Total')."\n";
    if ( $amount = $transaction->getMemberCardPrice(true) )
    $command .= '  '.__('Member cards').": ".format_currency($amount,'€')."\n";
    $command .= '  '.__('Tickets').": ".format_currency($transaction->getPrice(true),'€')."\n";
    if ( sfConfig::get('app_payment_type', 'paybox') != 'onthespot' )
    {
      $command .= "\n";
      $command .= "Paiements\n";
      if ( $amount = $transaction->getTicketsLinkedToMemberCardPrice(true) )
      $command .= "  ".__('Member cards').": ".format_currency($amount,'€')."\n";
      $command .= "  ".__('Credit card').": ".format_currency($transaction->getPrice(true,true),'€')."\n";
    }
    
    $replace = array(
      '%%DATE%%' => format_date(date('Y-m-d')),
      '%%CONTACT%%' => (string)$transaction->Contact,
      '%%TRANSACTION_ID%%' => $transaction->id,
      '%%SELLER%%' => sfConfig::get('app_informations_title'),
      '%%COMMAND%%' => $command,
      //'%%COMMAND%%' => '<pre>'.$command.'</pre>',
      '%%TICKETS%%' => $transaction->renderSimplifiedTickets(), // HTML tickets w/ barcode
    );
    
    $email = new Email;
    if ( sfConfig::get('app_contact_professional', false) )
      $email->Professionals[] = $transaction->Contact->Professionals[0];
    else
      $email->Contacts[] = $transaction->Contact;
    $email->field_bcc = sfConfig::get('app_informations_email','admin@libre-informatique.fr');
    $email->field_subject = sfConfig::get('app_informations_title').': '.__('your order #').$transaction->id;
    $email->field_from = sfConfig::get('app_informations_email','contact@libre-informatique.fr');
    $email->content = nl2br(str_replace(array_keys($replace),$replace,sfConfig::get('app_texts_email_confirmation')));
    $email->content .= nl2br("\n\n  * ".sfConfig::get('app_text_email_seated_tickets',
      __('All lines marked with an wildcard concern a seated venue. You will receive a new email as soon as we allocate seats for your tickets.')
    ));
    $email->content .= nl2br("\n\n".sfConfig::get('app_text_email_footer',<<<EOF
--
<a href="http://www.e-venement.net/">e-venement</a> est le système de billetterie informatisée développé par <a href="http://www.libre-informatique.fr/">Libre Informatique</a>. 
Ces logiciels sont distribués sous <a href="http://fr.wikipedia.org/wiki/Licences_libres">licences libres</a>

Libre Informatique
<a href="mailto:contact@libre-informatique.fr">contact@libre-informatique.fr</a>
<a href="http://www.libre-informatique.fr">http://www.libre-informatique.fr</a>
EOF
    ));
    
    $email->not_a_test = true;
    $email->setNoSpool();
    return $email->save();
