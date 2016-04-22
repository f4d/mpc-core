<?php 
class GenericFormNotification {
	public function __construct($formId,$ownerFieldId,$confirmationFieldId,$notificationTemplate) {
		$this->formId = $formId;
		$this->ownerFieldId = $ownerFieldId;
		$this->confirmationFieldId = $confirmationFieldId;
		$this->notificationTemplate = $notificationTemplate;
	}
}