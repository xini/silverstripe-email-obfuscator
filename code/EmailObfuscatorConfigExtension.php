<?php
class EmailObfuscatorConfigExtension extends DataExtension {
    
    private static $has_one = array(
        'ContactPage' => 'SiteTree',
    );
 
    public function updateCMSFields(FieldList $fields) {
        if(!$fields->hasField('ContactPageID')){
            $fields->addFieldToTab(
                "Root.Contact",
                OptionalTreeDropdownField::create("ContactPageID", 'Global Contact Page', "SiteTree")
                    ->setEmptyString('- none -')
            );
        }
    }
    
    public function updateSiteCMSFields(FieldList $fields) {
        $this->updateCMSFields($fields);
    }
    
}
