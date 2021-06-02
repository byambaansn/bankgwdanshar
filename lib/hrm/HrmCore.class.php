<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HrmCore
 *
 * @author duurenbayar
 */
class HrmCore
{

    const BOT_USER = 17624;
    const BOT_BRANCH = 130;
    # KHAAN bank
    const BOT_USER_KHAAN = 45294;
    const BOT_BRANCH_KHAAN = 155;
    # GOLOMT bank
    const BOT_USER_GOLOMT = 50525;
    const BOT_BRANCH_GOLOMT = 162;
    # XAC bank
    const BOT_USER_XAC = 9;
    const BOT_BRANCH_XAC = 163;
    # TDB bank
    const BOT_USER_TDB = 58237;
    const BOT_BRANCH_TDB = 170;
    # CAPITRON
    const BOT_USER_CAPITRON = 69016;
    const BOT_BRANCH_CAPITRON = 193;
    # CAPITAL
    const BOT_USER_CAPITAL = 75342;
    const BOT_BRANCH_CAPITAL = 226;
    const GROUP_DEALER = 12;

    protected static $_database = 'bankgw_login';
    protected static $_table_branch = 'branch';
    protected static $_table_user = 'user_new';
    protected static $_table_user_organization = 'user_organization';
    protected static $_table_group = 'user_group';
    protected static $_table_group_join = 'user_group_join';
    protected static $_table_permission = 'permission';
    protected static $_table_permission_option = 'permission_option';
    protected static $_pdo;
    protected static $_instance;

    public function __construct()
    {
        //self::$_pdo = new Doctrin
        self::$_pdo = Doctrine_Manager::connection(new PDO('mysql:host=192.168.9.190;dbname=hrm_db', 'bankgw', 'B4H3sAWyDvsjC382'))->getDbh();
        self::$_pdo->query("SET NAMES 'utf8'");
    }

    /**
     * HrmCore object
     *
     * @return HrmCore
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getObject($table)
    {
        $query = "SELECT column_name
              FROM information_schema.columns
              WHERE table_schema = '" . self::$_database . "'
              AND table_name = '" . $table . "'";

        return array_fill_keys($this->fetchAll($query, PDO::FETCH_COLUMN), null);
    }

    public function fetch($query, $style = PDO::FETCH_ASSOC)
    {
        return self::$_pdo->query($query)->fetch($style);
    }

    public function fetchAll($query, $style = PDO::FETCH_ASSOC)
    {
        return self::$_pdo->query($query)->fetchAll($style);
    }

    public function execute($query)
    {
        return self::$_pdo->exec($query);
    }

    public function count($table, $where = 1)
    {
        $query = "SELECT COUNT(*)
              FROM " . $table . "
              WHERE $where";

        $row = $this->fetch($query, PDO::FETCH_NUM);

        return $row[0];
    }

    public function findOneBy($table, $select, $where, $fetchStyle = PDO::FETCH_ASSOC)
    {
        $query = "SELECT $select
              FROM " . $table . "
              WHERE $where
              LIMIT 1";

        return $this->fetch($query, $fetchStyle);
    }

    public function findAllBy($table, $select, $where, $fetchStyle = PDO::FETCH_ASSOC)
    {
        $query = "SELECT $select
              FROM " . $table . "
              WHERE $where";

        return $this->fetchAll($query, $fetchStyle);
    }

    public function insert($table, $data)
    {
        $query = "INSERT INTO " . $table . " (`" . implode("`,`", array_keys($data)) . "`)
                VALUES ('" . implode("','", array_values($data)) . "')";

        $this->execute($query);

        return self::$_pdo->lastInsertId();
    }

    public function update($table, $data)
    {
        $query = "UPDATE " . $table . " SET ";
        $set = array();
        foreach ($data as $column => $value) {
            $set[] = "`" . $column . "` = '" . $value . "'";
        }

        $query .= implode(",", $set) . " WHERE id = " . $data['id'];

        return $this->execute($query);
    }

}

?>
