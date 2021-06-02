<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HrmOrganization
 *
 * @author duurenbayar
 */
class HrmOrganization extends HrmCore
{
  public static function getOrganizationIds($userId)
  {
    $userId = intval($userId);
    
    $query = "SELECT o.*
              FROM " . HrmCore::$_table_user_organization . " o
              WHERE o.id = '" . $userId . "'
              LIMIT 1";
    
    $row = HrmCore::getInstance()->fetch($query, PDO::FETCH_ASSOC);
    if ($row)
    { 
      unset($row['id']);
      return array_filter(array_values($row));
    }
    
    return null;
  }
}

?>
