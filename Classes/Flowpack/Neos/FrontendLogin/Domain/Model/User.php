<?php
namespace Flowpack\Neos\FrontendLogin\Domain\Model;

/*                                                                             *
 * This script belongs to the TYPO3 Flow package "Flowpack.Neos.FrontendLogin".*
 *                                                                             */

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\Account;

/**
 * A simple "user" model (Note: I don't extend AbstractParty or \TYPO3\Neos\..\User to keep this flexible and easier to maintain)
 *
 * @Flow\Entity
 */
class User
{

    /**
     * A unidirectional OneToMany association (done with ManyToMany and a unique constraint) to accounts.
     *
     * @var Collection<Account>
     * @ORM\ManyToMany
     * @ORM\JoinTable(inverseJoinColumns={@ORM\JoinColumn(unique=true)})
     */
    protected $accounts;

    /**
     * @var string
     */
    protected $givenName;

    /**
     * @var string
     */
    protected $familyName;

    /**
     * @var string
     * @ORM\Column(nullable = true)
     */
    protected $emailAddress;

    /**
     * @param string $givenName
     * @param string $familyName
     */
    function __construct($givenName, $familyName)
    {
        $this->givenName = $givenName;
        $this->familyName = $familyName;
        $this->accounts = new ArrayCollection();
    }

    /**
     * Assigns the given account to this party.
     *
     * @param Account $account The account
     * @return void
     */
    public function addAccount(Account $account)
    {
        $this->accounts->add($account);
    }

    /**
     * Remove an account from this party
     *
     * @param Account $account The account to remove
     * @return void
     */
    public function removeAccount(Account $account)
    {
        $this->accounts->removeElement($account);
    }

    /**
     * Returns the accounts of this party
     *
     * @return Collection<Account>|Account[] All assigned Account instances
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * @return string
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * @param string $givenName
     * @return void
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * @param string $familyName
     * @return void
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return sprintf('%s %s', $this->givenName, $this->familyName);
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     * @return void
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

}