<?php
namespace Ag\Email\Domain\Model;

class Message {

	/**
	 * @var string
	 */
	protected $plain;

	/**
	 * @var string
	 */
	protected $html;

	/**
	 * @param string $plain
	 * @param string $html
	 */
	public function __construct($plain, $html) {
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