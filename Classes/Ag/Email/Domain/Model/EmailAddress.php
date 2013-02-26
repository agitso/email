<?php
namespace Ag\Email\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\ValueObject
 */
class EmailAddress {

	/**
	 * @var string
	 * @ORM\Id
	 */
	protected $persistenceId;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @param string $name
	 * @param string $email
	 */
	public function __construct($name, $email) {
		$this->persistenceId = \TYPO3\Flow\Utility\Algorithms::generateUUID(); // bug in value object handling
		$this->setName($name);
		$this->setEmail($email);
	}

	/**
	 * @param string $name
	 */
	protected function setName($name) {
		$this->name = trim($name);
	}

	/**
	 * @param string $email
	 * @throws \InvalidArgumentException
	 */
	protected function setEmail($email) {
		$validator = new \TYPO3\Flow\Validation\Validator\EmailAddressValidator();
		if($validator->validate($email)->hasErrors()) {
			throw new \InvalidArgumentException('"' . $email . '" is not a valid email.');
		}

		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		if(!empty($this->name)) {
			return $this->name .' <'.$this->email.'>';
		}
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
}

?>