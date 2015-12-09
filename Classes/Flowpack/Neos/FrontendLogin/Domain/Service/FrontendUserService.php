<?php
namespace Flowpack\Neos\FrontendLogin\Domain\Service;

/*                                                                             *
 * This script belongs to the TYPO3 Flow package "Flowpack.Neos.FrontendLogin".*
 *                                                                             */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Security\AccountFactory;
use TYPO3\Flow\Security\AccountRepository;
use TYPO3\Flow\Security\Context;
use TYPO3\Flow\Security\Cryptography\HashService;
use TYPO3\Flow\Security\Policy\PolicyService;
use TYPO3\Flow\Utility\Now;
use Flowpack\Neos\FrontendLogin\Domain\Model\User;
use Flowpack\Neos\FrontendLogin\Domain\Repository\UserRepository;

/**
 * Central authority to deal with "frontend users"
 *
 * @Flow\Scope("singleton")
 */
class FrontendUserService
{

    /**
     * @const string
     */
    const ACCOUNT_AUTHENTICATION_PROVIDER = 'Flowpack.Neos.FrontendLogin:Frontend';

    /**
     * @Flow\Inject
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @Flow\Inject
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @Flow\Inject
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @Flow\Inject
     * @var PolicyService
     */
    protected $policyService;

    /**
     * @Flow\Inject
     * @var Context
     */
    protected $securityContext;
    /**
     * @Flow\Inject
     * @var HashService
     */
    protected $hashService;

    /**
     * @Flow\Inject(lazy = FALSE)
     * @var Now
     */
    protected $now;

    /**
     * Retrieves an existing user by the given username
     *
     * @param string $username The username
     * @return User The user, or NULL if the user does not exist
     */
    public function getUser($username)
    {
        $account = $this->accountRepository->findActiveByAccountIdentifierAndAuthenticationProviderName($username, self::ACCOUNT_AUTHENTICATION_PROVIDER);
        if ($account === NULL) {
            return NULL;
        }
        return $this->userRepository->findOneHavingAccount($account);
    }

    /**
     * Returns the currently logged in user, if any
     *
     * @return User The currently logged in user, or NULL
     */
    public function getCurrentUser()
    {
        $account = $this->securityContext->getAccount();
        if ($account === NULL) {
            return NULL;
        }
        return $this->userRepository->findOneHavingAccount($account);
    }

    /**
     * Creates a user based on the given information
     *
     * The created user and account are automatically added to their respective repositories and thus be persisted.
     *
     * @param User $user
     * @param string $username The username of the user to be created.
     * @param string $password Password of the user to be created
     * @param array $roleIdentifiers A list of role identifiers to assign
     * @return void
     */
    public function addUser(User $user, $username, $password, array $roleIdentifiers = array('Flowpack.Neos.FrontendLogin:User'))
    {
        $account = $this->accountFactory->createAccountWithPassword($username, $password, $roleIdentifiers, self::ACCOUNT_AUTHENTICATION_PROVIDER);
        $user->addAccount($account);

        $this->userRepository->add($user);
        $this->accountRepository->add($account);
    }

    /**
     * Deletes the specified user
     *
     * @param User $user The user to delete
     * @throws Exception
     */
    public function deleteUser(User $user)
    {
        foreach ($user->getAccounts() as $account) {
            $this->accountRepository->remove($account);
        }
        $this->userRepository->remove($user);
    }

    /**
     * Sets a new password for the given user
     *
     * @param User $user The user to set the password for
     * @param string $password A new password
     * @return void
     */
    public function setUserPassword(User $user, $password)
    {
        foreach ($user->getAccounts() as $account) {
            /** @var Account $account */
            $account->setCredentialsSource($this->hashService->hashPassword($password, 'default'));
            $this->accountRepository->update($account);
        }
    }

    /**
     * Updates the given user in the respective repository and potentially executes further actions depending on what
     * has been changed.
     *
     * @param User $user The modified user
     * @return void
     */
    public function updateUser(User $user)
    {
        $this->userRepository->update($user);
    }

}