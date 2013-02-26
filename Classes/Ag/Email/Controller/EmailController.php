<?php
namespace Ag\Email\Controller;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EmailController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @var \Ag\Email\Service\EmailService
	 * @Flow\Inject
	 */
	protected $emailService;

	/**
	 * @param string $emailId
	 * @return string
	 */
	public function readAction($emailId) {

		$this->emailService->read($emailId, $this->request->getHttpRequest()->getClientIpAddress(), $this->request->getHttpRequest()->getHeader('User-Agent'));

		$resource = 'resource://' . $this->settings['tracking']['resource'];

		$extension = explode('.', $resource);
		$extension = array_pop($extension);

		switch(strtolower($extension)) {
			case 'jpg':
			case 'jpeg':
				$this->response->setHeader('Content-Type', 'image/jpeg');
				break;

			case 'gif':
				$this->response->setHeader('Content-Type', 'image/gif');
				break;

			case 'png':
				$this->response->setHeader('Content-Type', 'image/png');
				break;

			default:
				$this->response->setHeader('Content-Type', 'image/jpeg');
				break;
		}

		$this->response->sendHeaders();
		readfile($resource);

		return '';
	}
}
?>