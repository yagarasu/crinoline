<?php

    class HomePresenter extends Presenter {
        
        public function main($args) {
        	plg('CRLaces')->setIntoContext('$welcome', app()->softconf->get('welcome', 'Please set one'));
            plg('CRLaces')->loadAndRender('templates/home.ltp');
        }

        public function about($args) {
            plg('CRLaces')->loadAndRender('templates/about.ltp');
        }
        
    }

?>