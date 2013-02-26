<?php
namespace Ag\Email\Domain\Model;

class EmailAddress {

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