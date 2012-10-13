CREATE TABLE IF NOT EXISTS  `prefix_opinion` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `target_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `target_type` ENUM('topic', 'blog', 'user', 'comment') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'topic' NOT NULL,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `voter_id` INT(11) UNSIGNED NOT NULL,
  `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' NOT NULL,
PRIMARY KEY (`id`),
KEY (`target_id`, `target_type`, `user_id`),
KEY  `user_id` (`user_id`) );

CREATE TABLE `prefix_opinion_rating` (
  `user_id` INT NOT NULL,
  `user_rating` float(9,3) NOT NULL DEFAULT '0.000',
  `user_position` INT NOT NULL,
PRIMARY KEY (`user_id`) );

