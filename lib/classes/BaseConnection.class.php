<?php

/**
 * Description of BaseConnection
 *
 * @author Belbayar
 */
class BaseConnection
{

    private $link = null;
    private $mysqlHost;
    private $mysqlUsername;
    private $mysqlPassword;
    private $mysqlDatabase;

    /**
     * 
     * @param mixed array(host=>server_address, username=>connection_username, password=>connection_password)
     */
    public function __construct($param = null)
    {
        if (is_array($param)) {
            $this->mysqlHost = $param['host'];
            $this->mysqlUsername = $param['username'];
            $this->mysqlPassword = $param['password'];
            $this->mysqlDatabase = $param['database'];
        }
    }

    public function mysqlConnect()
    {
        $this->link = mysql_connect($this->mysqlHost, $this->mysqlUsername, $this->mysqlPassword) or BaseGateway::doStop(mysql_errno(), mysql_error());

        mysql_set_charset('utf8', $this->link) or BaseGateway::doStop(mysql_errno(), mysql_error());
        
        mysql_select_db($this->mysqlDatabase, $this->link) or BaseGateway::doStop(mysql_errno(), mysql_error());
    }

    public function mysqlClose()
    {
        mysql_close($this->link) or BaseGateway::doStop(mysql_errno(), mysql_error());
        
        $this->link = null;
    }

    public function mysqlFetchOne($query)
    {
        $this->mysqlConnect();
        
        $result = mysql_query($query, $this->link) or BaseGateway::doStop(mysql_errno(), mysql_error());

        $this->mysqlClose();

        return mysql_fetch_assoc($result);
    }

    public function mysqlResultset($query)
    {
        $this->mysqlConnect();
        
        $result = mysql_query($query, $this->link) or BaseGateway::doStop(mysql_errno(), mysql_error());

        $this->mysqlClose();

        return $result;
    }

    public function mysqlExecute($query, $getInsertedId = FALSE)
    {
        $this->mysqlConnect();
        
        mysql_query($query, $this->link) or BaseGateway::doStop(mysql_errno(), mysql_error());
        
        if ($getInsertedId === TRUE) {
            $return = mysql_insert_id($this->link) or BaseGateway::doStop(mysql_errno(), mysql_error());
        } else {
            $return = mysql_affected_rows($this->link);
        }

        $this->mysqlClose();

        return $return;
    }

}

?>
