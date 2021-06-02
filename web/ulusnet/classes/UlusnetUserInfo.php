<?php

/**
 * Description of UlusnetUserInfo
 *
 * @author Belbayar
 */
class UlusnetUserInfo extends Basic
{

    public function setXmlRequest($username)
    {
        $this->xmlRequest = '<Ulusnet>
                                <Username>976' . $username . '</Username>
                                <Action>userinfo</Action>
                            </Ulusnet>';

        $this->defaultResponse = true;
        $this->number = $username;
        parent::setNumber($username);
    }

    public function setAttr()
    {
        $xml = parent::getXmlResponse();
    }

}
