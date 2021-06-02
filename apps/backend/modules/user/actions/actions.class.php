<?php

/**
 * user actions.
 *
 * @package    sf_sandbox
 * @subpackage user
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class userActions extends sfActions
{

    /**
     * Info
     *
     * @param sfWebRequest $request
     */
    public function executeInfo(sfWebRequest $request)
    {
        $this->getRequest()->setParameter('tab', 'user');
        $this->permissions = $this->getUser()->getAttribute('permissions');
    }

    /**
     * Login
     *
     * @param sfWebRequest $request
     */
    public function executeLogin(sfWebRequest $request)
    {
        $this->form = new FormLogin();

        if ($request->isMethod('POST')) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $this->getUser()->setFlash('success', 'Сайн байна уу? Та амжилттай нэвтэрлээ!');
                $this->redirect($request->getParameter('referer', '@homepage'));
            }
        }

        if ($this->getUser()->isAuthenticated()) {
            $this->redirect('@homepage');
        }

        $this->referer = $request->getUri();
    }

    /**
     * Logout
     *
     * @param sfWebRequest $request
     */
    public function executeLogout(sfWebRequest $request)
    {
        $this->getUser()->signOut();

        $this->redirect('@login');
    }

    /**
     * Error
     *
     * @param sfWebRequest $request
     */
    public function executeError(sfWebRequest $request)
    {
        
    }

}
