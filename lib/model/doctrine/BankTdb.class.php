<?php

/**
 * BankTdb
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class BankTdb extends BaseBankTdb
{

    public function canReCharge()
    {
        if (in_array($this->status, array(
                    BankTdbTable::STAT_FAILED,
                    BankTdbTable::STAT_PROCESS,
                    BankTdbTable::STAT_FAILED_CHARGE,
                    BankTdbTable::STAT_FAILED_DEALER)) &&
                $this->try_count < BankTdbTable::MAX_TRY_COUNT) {
            return TRUE;
        }

        return FALSE;
    }

    public function canReOutcome()
    {
        if (in_array($this->status, array(
                    BankTdbTable::STAT_FAILED_OUTCOME
                ))) {
            return TRUE;
        }

        return FALSE;
    }

}
