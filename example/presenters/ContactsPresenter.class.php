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

        public function create_form($args) {
            plg('CRLaces')->loadAndRender('templates/header.ltp');
            plg('CRLaces')->loadAndRender('templates/contacts-new.ltp');
        }

        public function create_save($args) {
            $contact = new ContactMap();
            $contact->name = $args['name'];
            $contact->email = $args['email'];
            $contact->phone = $args['phone'];
            $contact->save();
            plg('CRAlerts')->addAlert('Contact "'.$args['name'].'" created successfully.');
            relocate( appRoot() . 'contacts/' );
        }

        public function edit($args) {
            # code...
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