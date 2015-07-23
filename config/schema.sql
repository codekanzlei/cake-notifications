CREATE TABLE `notification_contents` (
  `id` char(36) NOT NULL,
  `notification_identifier` varchar(255) NOT NULL,
  `notes` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `notification_queue` (
  `id` char(36) NOT NULL,
  `locale` varchar(3) NOT NULL,
  `recipient_user_id` char(36) NOT NULL,
  `notification_identifier` varchar(255) DEFAULT NULL,
  `config` mediumtext NOT NULL,
  `transport_config` mediumtext,
  `transport` varchar(45) NOT NULL,
  `locked` tinyint(1) DEFAULT NULL,
  `sent` tinyint(1) DEFAULT NULL,
  `send_after` datetime DEFAULT NULL,
  `send_tries` int(2) unsigned NOT NULL DEFAULT '0',
  `seen` tinyint(1) NULL DEFAULT NULL,
  `debug` mediumtext,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
