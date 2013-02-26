<?php
namespace Ag\Email\Factory;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EmailFactory {

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @param string $toName
	 * @param string $toEmail
	 * @param string $subject
	 * @param string $message
	 * @return \Ag\Email\Domain\Model\Email
	 */
	public function create($toName, $toEmail, $subject, $message) {
		$email = new \Ag\Email\Domain\Model\Email(
			new \Ag\Email\Domain\Model\EmailAddress($this->settings['from']['name'], $this->settings['from']['email']),
			new \Ag\Email\Domain\Model\EmailAddress($toName, $toEmail),
			$subject
		);

		$message = new \Ag\Email\Domain\Model\Message($message, $this->generateHtml($email, $message));

		$email->setMessage($message);

		return $email;
	}

	/**
	 * @param \Ag\Email\Domain\Model\Email $email
	 * @param string $message
	 * @return string
	 */
	protected function generateHtml($email, $message) {
		$url = str_replace('###ID###', $email->getEmailId(), $this->settings['tracking']['imageUrl']);

		$html = '<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<title>'.$email->getSubject().'</title>
		</head>
		<body>
			<p>'.str_replace(chr(10), '<br>', $message).'</p>
			<p><img src="'.$url.'" alt="" /></p>
		</body>
		</html>';

		return $html;
	}
}
?>