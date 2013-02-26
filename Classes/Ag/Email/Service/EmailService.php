<?php
namespace Ag\Email\Service;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EmailService {

	/**
	 * @Flow\Inject
	 * @var \Ag\Email\Factory\EmailFactory
	 */
	protected $emailFactory;

	/**
	 * @Flow\Inject
	 * @var \Ag\Email\Domain\Repository\EmailRepository
	 */
	protected $emailRepository;

	/**
	 * @Flow\Inject
	 * @var \Ag\Email\Domain\Repository\EmailRepository
	 */
	protected $readRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 * @Flow\Inject
	 */
	protected $systemLogger;

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
	 */
	public function send($toName, $toEmail, $subject, $message) {
		try {
			$email = $this->emailFactory->create($toName, $toEmail, $subject, $message);

			$this->sendEmail($email);

			$this->emailRepository->add($email);
			$this->persistenceManager->persistAll();
		} catch(\Exception $e) {
			$this->systemLogger->log('Failed to send email.', LOG_CRIT, array(
				'toName'=>$toName,
				'toEmail'=>$toEmail,
				'subject'=>$subject,
				'message'=>$message,
				'exception'=>$this->renderException($e)
			));
		}
	}

	/**
	 * @param string $emailId
	 * @param string $ip
	 * @param string $userAgent
	 * @return void
	 */
	public function read($emailId, $ip, $userAgent) {
		$completed = FALSE;
		for ($i = 0; $i < 10 && !$completed; $i++) {
			try {
				$email = $this->emailRepository->findByIdentifier($emailId);

				if(empty($email)) {
					return;
				}

				$email->read($ip, $userAgent);

				$this->emailRepository->update($email);
				$this->persistenceManager->persistAll();

				$completed = TRUE;
			} catch (\Doctrine\ORM\OptimisticLockException $e) {
				sleep(1);
			}
		}

		if (!$completed) {
			$this->systemLogger->log('Could not save read on email after 10 tries.');
		}
	}

	/**
	 * @param \Ag\Email\Domain\Model\Email $email
	 * @return void
	 */
	protected function sendEmail($email) {
		if($this->settings['disableActualSending']) {
			$email->send();
			return;
		}

		$message = new \TYPO3\SwiftMailer\Message();
		$message
				->setFrom($email->getFrom()->getEmail(), $email->getFrom()->getName())
				->setTo($email->getTo()->getEmail(), $email->getTo()->getName())
				->setSubject($email->getSubject())
				->setBody($email->getMessage()->getPlain());

		$message->addPart($email->getMessage()->getHtml(), 'text/html');

		$result = $message->send();

		if($result === 1) {
			$email->send();
		}
	}

	/**
	 * @param \Exception $exception
	 * @return string
	 */
	protected function renderException($exception) {
		$exceptionMessage = '';
		$exceptionReference = "\n<b>More Information</b>\n";
		$exceptionReference .= "  Exception code      #" . $exception->getCode() . "\n";
		$exceptionReference .= "  Exception type      " . get_class($exception) . "\n";
		$exceptionReference .= "  File                " . $exception->getFile() . ($exception->getLine() ? ' line ' . $exception->getLine() : '') . "\n";
		$exceptionReference .= ($exception instanceof \TYPO3\Flow\Exception ? "  Exception reference #" . $exception->getReferenceCode() . "\n" : '');
		foreach (explode(chr(10), wordwrap($exception->getMessage(), 73)) as $messageLine) {
			 $exceptionMessage .= "  $messageLine\n";
		}

		return sprintf("Uncaught Exception\n%s%s\n", $exceptionMessage, $exceptionReference);
	}


}
?>