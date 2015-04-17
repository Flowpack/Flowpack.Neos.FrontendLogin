<?php
namespace Flowpack\Neos\FrontendLogin\Domain\Repository;

/*                                                                             *
 * This script belongs to the TYPO3 Flow package "Flowpack.Neos.FrontendLogin".*
 *                                                                             */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Repository;
use TYPO3\Flow\Security\Account;
use Flowpack\Neos\FrontendLogin\Domain\Model\User;

/**
 * @Flow\Scope("singleton")
 */
class UserRepository extends Repository {

	/**
	 * @param Account $account
	 * @return User
	 */
	public function findOneHavingAccount(Account $account) {
		$query = $this->createQuery();
		return
			$query->matching(
				$query->contains('accounts', $account)
			)
			->execute()
			->getFirst();
	}

	/**
	 * Return all users with at least one account with role $roleIdentifier
	 *
	 * @param string $roleIdentifier
	 * @return query result?
	 */
	public function findWithRole($roleIdentifier) {
		$query = $this->createQuery();
		return
			$query->matching(
				$query->like('accounts.roleIdentifiers', '%' . $roleIdentifier . '%')
			)
			->execute();
	}
}
