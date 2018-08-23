ALTER TABLE `#__redj_redirects` ADD COLUMN `skip_usergroups` text NOT NULL AFTER `skip`;
ALTER TABLE `#__redj_errors` ADD COLUMN `redirect_url` varchar(255) NOT NULL DEFAULT '' AFTER `error_code`;
