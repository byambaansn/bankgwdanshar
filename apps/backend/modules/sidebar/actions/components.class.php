<?php

class sidebarComponents extends sfComponents
{

    /**
     * @uses layout
     *
     */
    public function executeMainmenu(sfWebRequest $request)
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers('Menu');

        // current tab
        $this->tab = $request->getParameter('tab', '');

        // main menus
        $this->menus = getMenu();
    }

    /**
     * @uses layout
     *
     */
    public function executeSubmenu(sfWebRequest $request)
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers('Menu');

        // current tab
        $tab = $request->getParameter('tab', 'user');
        $this->sub_tab = $request->getParameter('sub_tab', '');
        // sub menus
        $menus = getMenu();
        $menus = $menus[$tab];
        $menus = $menus['sub'];

        $this->menus = $menus;
    }

}
