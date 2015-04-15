<?php

    class ContactsPresenter extends Presenter {
        
        public function main($args) {
        	$cContacts = new ContactCollection();
        	
        	$cContacts->bindEvent('LOAD_ERROR', function($args) {
        	    throw new Exception('Unable to fetch data from the DB.');
        	    return;
        	});
        	
        	$cContacts->load();
        	
        	plg('CRLaces')->setIntoContext('$contacts', $cContacts->toArray());
            plg('CRLaces')->loadAndRender('templates/contacts-all.ltp');
        }
        
        public function delete($args) {
            $contact = new ContactMap();
            $contact->load($args['id']);
            $name = $contact->name;
            $contact->destroy();
            
            plg('CRAlerts')->addAlert('Contact "'.$name.'" deleted successfully.');
            
            relocate( appRoot() . 'contacts/' );
        }
        
    }

?>