<?php
namespace Ag\Email\Domain\Factory;

use TYPO3\Flow\Annotations as Flow;
use \TYPO3\Flow\Configuration\ConfigurationManager as ConfigurationManager;

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
	 * @var \TYPO3\Flow\Mvc\Routing\UriBuilder
	 */
	protected $uriBuilder;

	/**
	 * @param \TYPO3\Flow\Configuration\ConfigurationManager $configurationManager
	 */
	public function injectUriBuilder(\TYPO3\Flow\Configuration\ConfigurationManager $configurationManager) {
		$flowSettings = $configurationManager->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'TYPO3.Flow');
		$httpRequest = \TYPO3\Flow\Http\Request::create(new \TYPO3\Flow\Http\Uri('http://localhost'));
		$httpRequest->injectSettings($flowSettings);
		$request = new \TYPO3\Flow\Mvc\ActionRequest($httpRequest);

		$uriBuilder = new \TYPO3\Flow\Mvc\Routing\UriBuilder();
		$uriBuilder->setRequest($request);
		$uriBuilder->setCreateAbsoluteUri(TRUE);

		$this->uriBuilder = $uriBuilder;
	}

	/**
	 * @param string $toName
	 * @param string $toEmail
	 * @param string $subject
	 * @param string $message
	 * @param string $htmlContent
	 * @return \Ag\Email\Domain\Model\Email
	 */
	public function create($toName, $toEmail, $subject, $message, $htmlContent) {
		$email = new \Ag\Email\Domain\Model\Email(
			new \Ag\Email\Domain\Model\EmailAddress($this->settings['from']['name'], $this->settings['from']['email']),
			new \Ag\Email\Domain\Model\EmailAddress($toName, $toEmail),
			$subject
		);

		$htmlContent = trim($htmlContent);

		if(empty($htmlContent)) {
			$htmlContent = '<p>'.str_replace(chr(10), '<br>', trim($message)).'</p>';
		}

		$message = new \Ag\Email\Domain\Model\Message($message, $this->generateHtml($email, $htmlContent));

		$email->setMessage($message);

		return $email;
	}



	/**
	 * @param \Ag\Email\Domain\Model\Email $email
	 * @param string $htmlContent
	 * @return string
	 */
	protected function generateHtml($email, $htmlContent) {
		$url = $this->uriBuilder->uriFor('read', array('emailId'=>$email->getEmailId()), 'Email', 'Ag.Email');

		$html = '<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>'.$email->getSubject().'</title>
	</head>
	<body>
		'.$htmlContent.'
		<div><img src="'.$url.'" alt="" /></div>
	</body>
</html>';

		return $html;
	}
}
?>