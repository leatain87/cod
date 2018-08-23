ALTER TABLE `#__redj_redirects` ADD COLUMN `placeholders` text NOT NULL AFTER `decode_url`;

ALTER TABLE `#__redj_redirects` ADD COLUMN `comment` varchar(255) DEFAULT NULL AFTER `decode_url`;
