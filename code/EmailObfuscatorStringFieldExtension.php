<?php
class EmailObfuscatorStringFieldExtension extends Extension {
	
    public function RAWEMAIL() {
        return EmailObfuscator::encloseRawEmailLinks($this->owner->RAW());
    }
    
}