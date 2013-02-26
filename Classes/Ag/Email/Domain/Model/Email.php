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
	 * @var \Ag\Email\Domain\Model\EmailAddress
	 * @ORM\OneToOne(cascade={"all"})
	 */
	protected $from;

	/**
	 * @var \Ag\Email\Domain\Model\EmailAddress
	 * @ORM\OneToOne(cascade={"all"})
	 */
	protected $to;

	/**
	 * @var string
	 */
	protected $subject;

	/**
	 * @var \Ag\Email\Domain\Model\Message
	 * @ORM\OneToOne(cascade={"all"})
	 */
	protected $message;

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

		$this->from = $from;
	}

	/**
	 * @param \Ag\Email\Domain\Model\EmailAddress $to
	 * @throws \InvalidArgumentException
	 */
	protected function setTo($to) {
		if($to === NULL) {
			throw new \InvalidArgumentException('To email address is required.');
		}

		$this->to = $to;
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

		if($this->message !== NULL) {
			throw new \Exception('Message can only be set one time.');
		}

		$this->message = $message;
	}

	/**
	 * @param string $ip
	 * @param string $userAgent
	 * @return Read
	 */
	public function read($ip, $userAgent) {
		$this->reads->add(new Read($ip, $userAgent, $this));
	}


	/**
	 * @throws \Exception
	 * @return void
	 */
	public function send() {
		if($this->sendDate !== NULL) {
			throw new \Exception('Email can only be sent once.');
		}

		$this->sendDate = new \DateTime();
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
		return $this->from;
	}

	/**
	 * @return \Ag\Email\Domain\Model\Message
	 */
	public function getMessage() {
		return $this->message;
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
		return $this->to;
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
		return $this->reads->count() > 0;
	}
}
?>