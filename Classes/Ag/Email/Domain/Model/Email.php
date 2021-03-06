<?php
namespace Ag\Email\Domain\Model;

use TYPO3\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Email {

	/**
	 * @var string
	 * @ORM\Id
	 */
	protected $emailId;

	/**
	 * @var int
	 * @ORM\Version
	 */
	protected $version = 0;

	/**
	 * @var \DateTime
	 */
	protected $creationDate;

	/**
	 * @var \DateTime
	 * @ORM\Column(nullable=true)
	 */
	protected $sentDate;

	/**
	 * @var string
	 */
	protected $fromName;

	/**
	 * @var string
	 */
	protected $fromEmail;

	/**
	 * @var string
	 */
	protected $toName;

	/**
	 * @var string
	 */
	protected $toEmail;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * Ensure content can only be set once
	 *
	 * @var bool
	 */
	protected $contentHasBeenSet = FALSE;

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
	 * This to ease querying and make sure that the version flag is increased
	 * If only child to collection is added, parent version is not raised by doctrine
	 *
	 * @var int
	 */
	protected $readTimes = 0;

	/**
	 * @var \Doctrine\Common\Collections\Collection<\Ag\Email\Domain\Model\Read>
	 * @ORM\OneToMany(mappedBy="email")
	 */
	protected $reads;

	/**
	 * @param \Ag\Email\Domain\Model\EmailAddress $from
	 * @param \Ag\Email\Domain\Model\EmailAddress $to
	 * @param string $subject
	 */
	public function __construct($from, $to, $subject) {
		$this->emailId = \TYPO3\Flow\Utility\Algorithms::generateUUID(); // bug in value object handling
		$this->reads = new \Doctrine\Common\Collections\ArrayCollection();
		$this->creationDate = new \DateTime();
		$this->setFrom($from);
		$this->setTo($to);
		$this->setSubject($subject);
	}

	/**
	 * @param \Ag\Email\Domain\Model\EmailAddress $from
	 * @throws \InvalidArgumentException
	 */
	protected function setFrom($from) {
		if($from === NULL) {
			throw new \InvalidArgumentException('From email address is required.');
		}

		$this->fromName = $from->getName();
		$this->fromEmail = $from->getEmail();
	}

	/**
	 * @param \Ag\Email\Domain\Model\EmailAddress $to
	 * @throws \InvalidArgumentException
	 */
	protected function setTo($to) {
		if($to === NULL) {
			throw new \InvalidArgumentException('To email address is required.');
		}

		$this->toName = $to->getName();
		$this->toEmail = $to->getEmail();
	}

	/**
	 * @param string $subject
	 * @throws \InvalidArgumentException
	 */
	protected function setSubject($subject) {
		$subject = trim($subject);

		if(empty($subject)) {
			throw new \InvalidArgumentException('Subject is required.');
		}

		$this->subject = $subject;
	}

	/**
	 * @param \Ag\Email\Domain\Model\Message $message
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function setMessage($message) {
		if($message === NULL) {
			throw new \InvalidArgumentException('Message is required.');
		}

		if($this->contentHasBeenSet) {
			throw new \Exception('Message can only be set one time.');
		}

		$this->contentHasBeenSet = TRUE;

		$this->plain = $message->getPlain();
		$this->html = $message->getHtml();
	}

	/**
	 * @param string $ip
	 * @param string $userAgent
	 * @return Read
	 */
	public function read($ip, $userAgent) {
		$this->readTimes++;
		$this->reads->add(new Read($ip, $userAgent, $this));
	}


	/**
	 * @throws \Exception
	 * @return void
	 */
	public function send() {
		if($this->sentDate !== NULL) {
			throw new \Exception('Email can only be sent once.');
		}

		$this->sentDate = new \DateTime();
	}

	/**
	 * @return \DateTime
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	/**
	 * @return string
	 */
	public function getEmailId() {
		return $this->emailId;
	}

	/**
	 * @return \Ag\Email\Domain\Model\EmailAddress
	 */
	public function getFrom() {
		return new EmailAddress($this->fromName, $this->fromEmail);
	}

	/**
	 * @return \Ag\Email\Domain\Model\Message
	 */
	public function getMessage() {
		return new Message($this->plain, $this->html);
	}

	/**
	 * @return \DateTime
	 */
	public function getSentDate() {
		return $this->sentDate;
	}

	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @return \Ag\Email\Domain\Model\EmailAddress
	 */
	public function getTo() {
		return new EmailAddress($this->toName, $this->toEmail);
	}

	/**
	 * @return bool
	 */
	public function isSent() {
		return $this->sentDate !== NULL;
	}

	/**
	 * @return bool
	 */
	public function isRead() {
		return $this->isRead > 0;
	}
}
?>