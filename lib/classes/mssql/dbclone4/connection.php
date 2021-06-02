<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
#const SERVER = 'dbclone';   #host
        const SERVER = 'dbclone4';   #host
#const SERVER = ' 192.168.9.219:1433';   #host
        const DB = 'BSCSTMP';   #database
        const USERNAME = 'a_bank_gw'; #username
        const PASSWORD = 'UUldTWD6wJef'; #password

    /**
     * Description of connection
     *
     * @author khishigdelger.b
     */
    class connection
    {
        function execute($query)
    {
        $link = mssql_connect(SERVER, USERNAME, PASSWORD) or die('Could not connect to database!');
        ini_set('mssql.charset', 'utf8');
        mssql_select_db(DB, $link) or die('Could not select to database!');
        $result = mssql_query($query) or die('Could not query to database');

        mssql_free_result($result);
        mssql_close($link);

        return $result;
    }

    function queryToArray($query)
    {
        $res = array();

        $link = mssql_connect(SERVER, USERNAME, PASSWORD);
        ini_set('mssql.charset', 'UTF-8');
        if ($link) {
            if (mssql_select_db(DB, $link)) {
                mssql_query('SET NAMES "utf8"', $link);
                $result = mssql_query($query);
                if (mssql_num_rows($result)) {
                    while ($row = mssql_fetch_array($result, MSSQL_ASSOC)) {
                        $res[] = $row;
                    }
                } else {
                    $err = 'No records found: ' . mssql_get_last_message();
                }

                mssql_free_result($result);
                mssql_close($link);
            } else {
                $err = 'Could not select to the database: ' . mssql_get_last_message();
            }
        } else {
            $err = 'Could not connect to the database: ' . mssql_get_last_message();
        }
        $isSuccessful = 1;
        if (count($err)) {
            $isSuccessful = 0;
        }
        return array('res' => $res, 'err' => $err, 'isSuccessful' => $isSuccessful);
    }
}
