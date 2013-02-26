<?php
namespace Ag\Email\Domain\Repository;

use TYPO3\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class EmailRepository extends \TYPO3\Flow\Persistence\Repository {

	/**
	 * @param string $emailId
	 * @return \Ag\Email\Domain\Model\Email
	 */
	public function findByIdentifier($emailId) {
		parent::findByIdentifier($emailId);
	}


}

?>