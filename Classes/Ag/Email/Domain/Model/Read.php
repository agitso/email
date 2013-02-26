<?php
namespace Ag\Email\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Read {

	/**
	 * @var \DateTime
	 */
	protected $readOn;

	/**
	 * @var string
	 */
	protected $ip;

	/**
	 * @var string
	 */
	protected $userAgent;

	/**
	 * @var \Ag\Email\Domain\Model\Email
	 */
	protected $email;

	/**
	 * @param string $ip
	 * @param string $userAgent
	 * @param \Ag\Email\Domain\Model\Email $email
	 */
	public function __construct($ip, $userAgent, $email) {
		$this->persistenceId = \TYPO3\Flow\Utility\Algorithms::generateUUID(); // bug in value object handling
		$this->readOn = new \DateTime();
		$this->setIp($ip);
		$this->setUserAgent($userAgent);
		$this->setEmail($email);
	}

	/**
	 * @param string $ip
	 */
	protected function setIp($ip) {
		$this->ip = trim($ip);
	}

	/**
	 * @param string $userAgent
	 */
	protected function setUserAgent($userAgent){
		$this->userAgent = trim($userAgent);
	}

	/**
	 * @param \Ag\Email\Domain\Model\Email $email
	 * @throws \InvalidArgumentException
	 */
	protected function setEmail($email) {
		if($email === NULL) {
			throw new \InvalidArgumentException('Email must be set.');
		}

		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getIp() {
		return $this->ip;
	}

	/**
	 * @return \DateTime
	 */
	public function getReadOn() {
		return $this->readOn;
	}

	/**
	 * @return string
	 */
	public function getUserAgent() {
		return $this->userAgent;
	}
}
?>