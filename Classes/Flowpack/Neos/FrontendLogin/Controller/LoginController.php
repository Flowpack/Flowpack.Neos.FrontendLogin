<?php
namespace Flowpack\Neos\FrontendLogin\Controller;

/*                                                                             *
 * This script belongs to the TYPO3 Flow package "Flowpack.Neos.FrontendLogin".*
 *                                                                             */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Security\Authentication\Controller\AbstractAuthenticationController;

/**
 * Controller for displaying login/logout forms and a profile for authenticated users
 */
class LoginController extends AbstractAuthenticationController
{

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->view->assign('account', $this->securityContext->getAccount());
    }

    /**
     * return void
     */
    public function logoutAction()
    {
        parent::logoutAction();
        $this->addFlashMessage('Successfully logged out', 'Logged out', Message::SEVERITY_NOTICE);
        $this->redirect('index');
    }

    /**
     * @param ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
     * @return string
     */
    protected function onAuthenticationSuccess(ActionRequest $originalRequest = NULL)
    {
        $this->addFlashMessage('Successfully logged in', 'Logged in');
        $this->redirect('index');
    }

    /**
     * Disable the technical error flash message
     *
     * @return boolean
     */
    protected function getErrorFlashMessage()
    {
        return FALSE;
    }
}