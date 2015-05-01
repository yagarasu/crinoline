-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`) VALUES
(1, 'Carmen Crinoline', 'carmen@crinoline.com', '1234567890'),
(2, 'Pedro Piedra', 'pedro@piedra.com', '1234567890'),
(4, 'Pancho Pistolas', 'pancho@pistolas.com', '1234567890'),
(5, 'Juan Camaney', 'juan@camaney.com', '1234567890');
