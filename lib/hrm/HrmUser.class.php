<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HrmUser
 *
 * @author duurenbayar
 */
class HrmUser extends HrmCore
{

    protected $user = array();
    protected static $_instance;

    public function __construct()
    {
        parent::__construct();

        self::$_instance = null;

        $this->user = $this->getObject('user_new');
        $this->users = null;
    }

    /**
     *
     * @return HrmUser
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function hasUser()
    {
        if (self::$_instance) {
            if (!self::$_instance->get('id')) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    public function findOne($where, $select = "*", $fetchStyle = PDO::FETCH_ASSOC)
    {
        $this->user = $this->findOneBy(HrmCore::$_table_user, $select, $where, $fetchStyle);

        return self::$_instance;
    }

    public function findAll($where, $select = "*", $fetchStyle = PDO::FETCH_ASSOC)
    {
        return $this->findAllBy(HrmCore::$_table_user, $select, $where, $fetchStyle);
    }

    public function set($column, $value)
    {
        $this->user[$column] = $value;
    }

    public function get($column)
    {
        return $this->user[$column];
    }

    public function save()
    {
        $this->user['updated_at'] = date('Y-m-d H:i:s');

        if (!$this->user['id']) {
            $this->user['created_at'] = date('Y-m-d H:i:s');

            $this->user['id'] = $this->insert(HrmCore::$_table_user, $this->user);
        } else if ($this->count(HrmCore::$_table_user, "id = " . $this->user['id'])) {
            $this->update(HrmCore::$_table_user, $this->user);
        } else {
            $this->user['created_at'] = date('Y-m-d H:i:s');

            $this->user['id'] = $this->insert(HrmCore::$_table_user, $this->user);
        }

        return true;
    }

    public static function getUserPermissions($userId, $projectId = null)
    {
        $userId = intval($userId);
        $user = self::getInstance()->findOne('id = ' . $userId);

        if (!$user->get('id')) {
            return null;
        }

        $groupIds = HrmGroup::getGroupIds($userId, $projectId);
        $orgIds = HrmOrganization::getOrganizationIds($userId);
        $statusId = $user->get('status_id');
        $positionId = $user->get('position_id');
        $front = $user->get('front');
        $branchId = $user->get('branch_id');

        $query = "SELECT o.view, o.view_name, p.object_ref AS ref
                    FROM " . HrmCore::$_table_permission . " p
                    INNER JOIN " . HrmCore::$_table_permission_option . " o ON o.id = p.option_id
                    WHERE ((p.object_id = '" . $userId . "' AND p.object_type = 1)
                        " . (count($groupIds) ? " OR (p.object_id IN (" . implode(",", $groupIds) . ") AND p.object_type = 2) " : "") . "
                        " . (count($orgIds) ? " OR (p.object_id IN (" . implode(",", $orgIds) . ") AND p.object_type = 3) " : "") . "
                        OR (p.object_id = '" . $statusId . "' AND p.object_type = 4)
                        OR (p.object_id = '" . $positionId . "' AND p.object_type = 5)
                        OR (p.object_id = '" . $front . "' AND p.object_type = 6)
                        OR (p.object_id = '" . $branchId . "' AND p.object_type = 7))
                        " . ($projectId ? " AND (o.project_id = '" . $projectId . "')" : "") . "
                    ORDER BY o.view";

        return HrmCore::getInstance()->fetchAll($query);
    }

}

?>
