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
     * return void
     */
    public function logoutAction()
    {
        parent::logoutAction();

        $uri = $this->request->getInternalArgument('__redirectAfterLogoutUri');
        $this->redirectToUri($uri);
    }

    /**
     * @param ActionRequest $originalRequest The request that was intercepted by the security framework, NULL if there was none
     * @return void
     * @throws UnsupportedRequestTypeException
     */
    protected function onAuthenticationSuccess(ActionRequest $originalRequest = null)
    {
        $uri = $this->request->getInternalArgument('__redirectAfterLoginUri');
        $this->redirectToUri($uri);
    }

    /**
     * Create add a validation error and send the request back to the referrer
     *
     * @param AuthenticationRequiredException $exception
     * @return void
     */
    protected function onAuthenticationFailure(AuthenticationRequiredException $exception = null)
    {
        $referringRequest = $this->request->getReferringRequest();
        if ($referringRequest === null) {
            return;
        }

        $validationResults = new Error\Result();
        $validationResults->addError(new Error\Error('authenticationFailure'));

        $packageKey = $referringRequest->getControllerPackageKey();
        $subpackageKey = $referringRequest->getControllerSubpackageKey();
        if ($subpackageKey !== null) {
            $packageKey .= '\\' . $subpackageKey;
        }

        $argumentsForNextController = $referringRequest->getArguments();
        $argumentsForNextController['__submittedArguments'] = [];
        $argumentsForNextController['__submittedArgumentValidationResults'] = $validationResults;

        $this->forward($referringRequest->getControllerActionName(), $referringRequest->getControllerName(), $packageKey , $argumentsForNextController);
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
}
