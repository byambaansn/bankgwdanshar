<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HrmBranch
 *
 * @author duurenbayar
 */
class HrmBranch extends HrmCore
{
  const TYPE_TENANT = 4;
  protected $branch = array();
  protected static $_instance;

  public function __construct()
  {
    parent::__construct();

    self::$_instance = null;

    $this->branch = $this->getObject('branch');
  }

  /**
   *
   * @return HrmBranch
   */
  public static function getInstance()
  {
    if (!isset(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function hasBranch()
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
    $this->branch = $this->findOneBy(HrmCore::$_table_branch, $select, $where, $fetchStyle);

    return self::$_instance;
  }

  public function findAll($where, $select = "*", $fetchStyle = PDO::FETCH_ASSOC)
  {
    return $this->findAllBy(HrmCore::$_table_branch, $select, $where, $fetchStyle);
  }

  public function get($column)
  {
    return $this->branch[$column];
  }

}

?>
