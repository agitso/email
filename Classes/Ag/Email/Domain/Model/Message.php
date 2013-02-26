<?php
namespace Ag\Email\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\ValueObject
 */
class Message {

	/**
	 * @var string
	 * @ORM\Id
	 */
	protected $persistenceId;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	protected $plain;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	protected $html;

	/**
	 * @param string $plain
	 * @param string $html
	 */
	public function __construct($plain, $html) {
		$this->persistenceId = \TYPO3\Flow\Utility\Algorithms::generateUUID(); // bug in value object handling
		$this->setPlain($plain);
		$this->setHtml($html);
	}

	/**
	 * @param string $plain
	 */
	protected function setPlain($plain) {
		$this->plain = trim($plain);
	}

	/**
	 * @param string $html
	 */
	protected function setHtml($html) {
		$this->html = trim($html);
	}

	/**
	 * @return string
	 */
	public function getHtml() {
		return $this->html;
	}

	/**
	 * @return string
	 */
	public function getPlain() {
		return $this->plain;
	}
}
?>