<?php
class EmailObfuscatorSubmittedFormFieldExtension extends DataExtension {
	
	public function getFormattedRawEmailValue() {
		return EmailObfuscator::encloseRawEmailLinks($this->owner->getFormattedValue());
	}
	
}