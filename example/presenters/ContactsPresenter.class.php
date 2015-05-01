<?php

    class ContactsPresenter extends Presenter {
        
        public function main($args) {
            // Relocate if can't do
            if(!plg('CRRoles')->userCan('contacts-admin')) {
                plg('CRAlerts')->addAlert('You are not allowed to do that.', 3);
                relocate(approot());
            }

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
            // Relocate if can't do
            if(!plg('CRRoles')->userCan('contacts-admin')) {
                plg('CRAlerts')->addAlert('You are not allowed to do that.', 3);
                relocate(approot());
            }

            plg('CRLaces')->loadAndRender('templates/contacts-edit.ltp');
        }

        public function create_save($args) {
            // Relocate if can't do
            if(!plg('CRRoles')->userCan('contacts-admin')) {
                plg('CRAlerts')->addAlert('You are not allowed to do that.', 3);
                relocate(approot());
            }

            $contact = new ContactMap();
            $contact->name = $args['name'];
            $contact->email = $args['email'];
            $contact->phone = $args['phone'];
            $contact->save();
            plg('CRAlerts')->addAlert('Contact "'.$args['name'].'" created successfully.',1);
            relocate( appRoot() . 'contacts/' );
        }

        public function edit_form($args) {
            // Relocate if can't do
            if(!plg('CRRoles')->userCan('contacts-admin')) {
                plg('CRAlerts')->addAlert('You are not allowed to do that.', 3);
                relocate(approot());
            }

            $contact = new ContactMap();
            $contact->bindEvent('LOAD_ERROR', function($args) {
                plg('CRAlerts')->addAlert('Contact can not be found.', 3);
                relocate( appRoot() . 'contacts/' );
            });
            $contact->load($args['id']);
            plg('CRLaces')->setIntoContext('$contact', $contact->toArray());
            plg('CRLaces')->loadAndRender('templates/header.ltp');
            plg('CRLaces')->loadAndRender('templates/contacts-edit.ltp');
        }

        public function edit_save($args) {
            // Relocate if can't do
            if(!plg('CRRoles')->userCan('contacts-admin')) {
                plg('CRAlerts')->addAlert('You are not allowed to do that.', 3);
                relocate(approot());
            }

            $contact = new ContactMap();
            $contact->id = $args['id'];
            $contact->name = $args['name'];
            $contact->email = $args['email'];
            $contact->phone = $args['phone'];
            $contact->save();
            plg('CRAlerts')->addAlert('Contact "'.$args['name'].'" saved successfully.',1);
            relocate( appRoot() . 'contacts/' );
        }
        
        public function delete($args) {
            // Relocate if can't do
            if(!plg('CRRoles')->userCan('contacts-admin')) {
                plg('CRAlerts')->addAlert('You are not allowed to do that.', 3);
                relocate(approot());
            }
            
            $contact = new ContactMap();
            $contact->load($args['id']);
            $name = $contact->name;
            $contact->destroy();
            
            plg('CRAlerts')->addAlert('Contact "'.$name.'" deleted successfully.',1);
            
            relocate( appRoot() . 'contacts/' );
        }
        
    }

?>