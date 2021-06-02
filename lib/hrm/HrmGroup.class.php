<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HrmGroup
 *
 * @author duurenbayar
 */
class HrmGroup extends HrmCore
{

    public static function getGroupIds($userId, $projectId = null)
    {
        $userId = intval($userId);
        $projectId = intval($projectId);

        if ($projectId) {
            $query = "SELECT j.group_id
                FROM " . HrmCore::$_table_group_join . " j
                INNER JOIN " . HrmCore::$_table_group . " g ON g.id = j.group_id
                WHERE j.user_id = '" . $userId . "'
                AND g.project_id = '" . $projectId . "'";
        } else {
            $query = "SELECT j.group_id
                FROM " . HrmCore::$_table_group_join . " j
                WHERE j.user_id = '" . $userId . "'";
        }

        return HrmCore::getInstance()->fetchAll($query, PDO::FETCH_COLUMN);
    }

}

?>
