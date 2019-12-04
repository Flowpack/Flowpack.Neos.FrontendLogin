<?php
declare(strict_types=1);

namespace Flowpack\Neos\FrontendLogin\Controller;

/*
 * This file is part of the Flowpack.Neos.FrontendLogin package.
 *
 * (c) Contributors of the Flowpack Team - flowpack.org
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Error\Messages as Error;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Translator;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Mvc\Exception\UnsupportedRequestTypeException;
use Neos\Flow\Mvc\FlashMessage\FlashMessageService;
use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use Neos\Flow\Security\Exception\AuthenticationRequiredException;
use Neos\Fusion\View\FusionView;

/**
 * Controller for displaying a login/logout form and authenticating/logging out "frontend users"
 */
class AuthenticationController extends AbstractAuthenticationController
{

    /**
     * @var FusionView
     */
    protected $defaultViewObjectName = FusionView::class;

    /**
     * @var Translator
     * @Flow\Inject
     */
    protected $translator;

    /**
     * @Flow\InjectConfiguration(package="Flowpack.Neos.FrontendLogin", path="translation.packageKey")
     * @var string
     */
    protected $translationPackageKey;

    /**
     * @Flow\InjectConfiguration(package="Flowpack.Neos.FrontendLogin", path="translation.sourceName")
     * @var string
     */
    protected $translationSourceName;

    /**
     * @Flow\Inject
     * @var FlashMessageService
     */
    protected $flashMessageService;

    /**
     * @return void
     */
    public function indexAction(): void
    {
        $this->view->assignMultiple([
            'account' => $this->securityContext->getAccount(),
            'flashMessages' => $this->flashMessageService->getFlashMessageContainerForRequest($this->request)->getMessagesAndFlush(),
        ]);
    }

    /**
     * return void
     */
    public function logoutAction()
    {
        parent::logoutAction();

        $uri = $this->request->getInternalArgument('__redirectAfterLogoutUri');

        if (empty($uri)) {
            $this->redirect('index');
        } else {
            $this->redirectToUri($uri);
        }
    }

    /**
     * @param ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
     * @return void
     * @throws UnsupportedRequestTypeException
     */
    protected function onAuthenticationSuccess(ActionRequest $originalRequest = null)
    {
        $uri = $this->request->getInternalArgument('__redirectAfterLoginUri');

        if (empty($uri)) {
            $this->redirect('index');
        } else {
            $this->redirectToUri($uri);
        }
    }

    /**
     * Create translated FlashMessage and add it to flashMessageContainer
     *
     * @param AuthenticationRequiredException $exception
     * @return void
     */
    protected function onAuthenticationFailure(AuthenticationRequiredException $exception = null)
    {
        $title = $this->getTranslationById('authentication.failure.title');
        $message = $this->getTranslationById('authentication.failure.message');
        $this->addFlashMessage($message, $title, Error\Message::SEVERITY_ERROR, [], $exception === null ? 1496914553 : $exception->getCode());
    }

    /**
     * Get translation by label id for configured source name and package key
     *
     * @param string $labelId Key to use for finding translation
     * @return string Translated message or NULL on failure
     */
    protected function getTranslationById($labelId): string
    {
        return $this->translator->translateById($labelId, [], null, null, $this->translationSourceName, $this->translationPackageKey);
    }

    /**
     * Disable the technical error flash message
     *
     * @return boolean
     */
    protected function getErrorFlashMessage()
    {
        return false;
    }

    /**
     * Sets the Fusion path pattern on the view.
     *
     * @param ViewInterface $view
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);
        /** @var FusionView $view */
        $view->setFusionPathPattern('resource://Flowpack.Neos.FrontendLogin/Private/Fusion/Backend');
    }
}
