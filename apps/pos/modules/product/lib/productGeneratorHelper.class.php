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

/**
 * product module helper.
 *
 * @package    e-venement
 * @subpackage product
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @author     Marcos Bezerra de Menezes <marcos.bezerra AT libre-informatique.fr>
 * @version    SVN: $Id: helper.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class productGeneratorHelper extends BaseProductGeneratorHelper
{
  public function linkToExtraAction($params)
  {
    if (!key_exists('ui-icon', $params)) $params['ui-icon'] = '';

    if ( isset($params['more-icon']) )
    {
      $icon = '<span class="ui-icon ui-icon-'.$params['more-icon'].'"></span>';
      unset($params['more-icon']);
    }

    $params['params'] = UIHelper::addClasses($params, '');
    if ( !isset($icon) )
    {
      $params['ui-icon'] = $this->getIcon($params['ui-icon'], $params);
      $icon = UIHelper::addIcon($params);
    }

    return '<li class="sf_admin_action_'.$params['action'].'">'.link_to($icon . __($params['label']), sfContext::getInstance()->getModuleName().'/'.$params['action'], $params['params']).'</li>';
  }
}
