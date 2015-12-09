<?php
namespace Flowpack\Neos\FrontendLogin\Controller;

/*                                                                             *
 * This script belongs to the TYPO3 Flow package "Flowpack.Neos.FrontendLogin".*
 *                                                                             */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\Controller\ActionController;
use Flowpack\Neos\FrontendLogin\Domain\Model\User;
use Flowpack\Neos\FrontendLogin\Domain\Service\FrontendUserService;

/**
 * Controller for displaying a simple user profile for frontend users
 */
class UserController extends ActionController
{

    /**
     * @Flow\Inject
     * @var FrontendUserService
     */
    protected $userService;

    /**
     * @return void
     */
    public function showAction()
    {
        $this->view->assign('user', $this->userService->getCurrentUser());
    }

    /**
     * @param User $user
     * @return void
     */
    public function updateAction(User $user)
    {
        $this->userService->updateUser($user);
        $this->addFlashMessage('Successfully updated user data', 'Success');

        $this->redirect('show');
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