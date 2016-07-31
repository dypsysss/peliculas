-- ------------------------------------------------------
-- Table structure for table `#__peliculas_genders`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__peliculas_genders` (
    `id` 	              INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(250) DEFAULT NULL,
    `alias`             VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
    `themoviedb_id`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `published` 	      TINYINT(1) unsigned NOT NULL DEFAULT '0',
    `params`            TEXT NOT NULL,
    `access`            INT(10) unsigned NOT NULL DEFAULT 0,
    `created_user_id`   INT(10) unsigned NOT NULL DEFAULT 0,
    `created_time`      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_user_id`  INT(10) unsigned NOT NULL DEFAULT 0,
    `modified_time`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_out` 	    INT(11) NOT NULL DEFAULT 0,
    `checked_out_time` 	DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering`          INT(11) NOT NULL ,
    `version`           INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `language`          CHAR(7) NOT NULL,
    `hits`              INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_access`    (`access`),
    KEY `idx_checkout`  (`checked_out`),
    KEY `idx_state`     (`published`),
    KEY `idx_language`  (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ------------------------------------------------------
-- Table structure for table `#__peliculas_persons`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__peliculas_persons` (
    `id` 	              INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `themoviedb_id`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `name`              VARCHAR(250) DEFAULT NULL,
    `alias`             VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
    `birthday`          VARCHAR(15) DEFAULT NULL,
    `deathday`          VARCHAR(15) DEFAULT NULL,
    `homepage`          VARCHAR(250) DEFAULT NULL,
    `place_of_birth`    VARCHAR(250) DEFAULT NULL,
    `imdb_id`           VARCHAR(15) DEFAULT NULL,
    `biography`         TEXT NOT NULL,
    `image`             VARCHAR(250) DEFAULT NULL,
    `published` 	      TINYINT(1) unsigned NOT NULL DEFAULT '0',
    `params`            TEXT NOT NULL,
    `access`            INT(10) unsigned NOT NULL DEFAULT 0,
    `created_user_id`   INT(10) unsigned NOT NULL DEFAULT 0,
    `created_time`      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_user_id`  INT(10) unsigned NOT NULL DEFAULT 0,
    `modified_time`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_out` 	    INT(11) NOT NULL DEFAULT 0,
    `checked_out_time` 	DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering`          INT(11) NOT NULL ,
    `version`           INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `language`          CHAR(7) NOT NULL,
    `hits`              INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_access`    (`access`),
    KEY `idx_checkout`  (`checked_out`),
    KEY `idx_state`     (`published`),
    KEY `idx_language`  (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ------------------------------------------------------
-- Table structure for table `#__peliculas_companies`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__peliculas_companies` (
    `id` 	              INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `themoviedb_id`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `name`              VARCHAR(250) DEFAULT NULL,
    `alias`             VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
    `image`             VARCHAR(250) DEFAULT NULL,
    `homepage`          VARCHAR(250) DEFAULT NULL,
    `headquarters`      VARCHAR(250) DEFAULT NULL,
    `published` 	      TINYINT(1) unsigned NOT NULL DEFAULT '0',
    `params`            TEXT NOT NULL,
    `access`            INT(10) unsigned NOT NULL DEFAULT 0,
    `created_user_id`   INT(10) unsigned NOT NULL DEFAULT 0,
    `created_time`      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_user_id`  INT(10) unsigned NOT NULL DEFAULT 0,
    `modified_time`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_out` 	    INT(11) NOT NULL DEFAULT 0,
    `checked_out_time` 	DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering`          INT(11) NOT NULL ,
    `version`           INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `language`          CHAR(7) NOT NULL,
    `hits`              INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_checkout` (`checked_out`),
    KEY `idx_state` (`published`),
    KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ------------------------------------------------------
-- Table structure for table `#__peliculas_movies`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__peliculas_movies` (
    `id` 	              INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(250) DEFAULT NULL,
    `alias`             VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
    `themoviedb_id`     INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `imdb_id`           VARCHAR(15) DEFAULT NULL,
    `description`       TEXT NOT NULL,
    `meta_title`        VARCHAR(255) NOT NULL,
    `meta_description`  TEXT NOT NULL,
    `meta_keyword`      TEXT NOT NULL,
    `homepage`          VARCHAR(250) DEFAULT NULL,
    `original_language` VARCHAR(2) DEFAULT NULL,
    `original_title`    VARCHAR(250) DEFAULT NULL,
    `production_companies`    VARCHAR(250) DEFAULT NULL,
    `production_countries`    VARCHAR(250) DEFAULT NULL,
    `release_date`      VARCHAR(250) DEFAULT NULL,
    `f_estreno`         DATE NOT NULL DEFAULT '0000-00-00',
    `interpretes`       VARCHAR(250) DEFAULT NULL,
    `productores`       VARCHAR(250) DEFAULT NULL,
    `directores`        VARCHAR(250) DEFAULT NULL,
    `guion`             VARCHAR(250) DEFAULT NULL,
    `poster`            VARCHAR(250) DEFAULT NULL,
    `published` 	      TINYINT(1) unsigned NOT NULL DEFAULT '0',
    `videos`            TEXT NOT NULL,
    `duracion`          INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `imagenes`          TEXT NOT NULL,
    `params`            TEXT NOT NULL,
    `access`            INT(10) unsigned NOT NULL DEFAULT 0,
    `created_user_id`   INT(10) unsigned NOT NULL DEFAULT 0,
    `created_time`      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_user_id`  INT(10) unsigned NOT NULL DEFAULT 0,
    `modified_time`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_out` 	    INT(11) NOT NULL DEFAULT 0,
    `checked_out_time` 	DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering`          INT(11) NOT NULL ,
    `version`           INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `language`          CHAR(7) NOT NULL,
    `featured`          TINYINT(1) NOT NULL default '0',
    `hits`              INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_checkout` (`checked_out`),
    KEY `idx_state` (`published`),
    KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ------------------------------------------------------
-- Table structure for table `#__peliculas_movies_gender`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__peliculas_movies_gender` (
    `id` 	              INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `movie_id`          INT(10) UNSIGNED NOT NULL DEFAULT 0,
    `gender_id`         INT(10) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_movie_gender` (`movie_id`, `gender_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ------------------------------------------------------
-- Table structure for table `#__peliculas_featured`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__peliculas_featured` (
	  `id` 			          INT(11) unsigned NOT NULL AUTO_INCREMENT,
	  `movie_id` 		      INT(11) unsigned NOT NULL,
	  `created_time` 			DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `created_user_id` 	INT(11) unsigned NOT NULL DEFAULT '0',
	  `modified_time` 		DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `modified_user_id` 	INT(11) unsigned NOT NULL DEFAULT '0',
	  `checked_out` 		  INT(11) NOT NULL DEFAULT '0',
	  `checked_out_time` 	DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `ordering`          INT(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ------------------------------------------------------
-- Table structure for table `#__peliculas_cinemas`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__peliculas_cinemas` (
    `id` 	              INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(250) DEFAULT NULL,
    `alias`             VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
    `informacion`       TEXT NOT NULL,
    `image`             VARCHAR(250) DEFAULT NULL,
    `published` 	      TINYINT(1) unsigned NOT NULL DEFAULT '0',
    `params`            TEXT NOT NULL,
    `access`            INT(10) unsigned NOT NULL DEFAULT 0,
    `created_user_id`   INT(10) unsigned NOT NULL DEFAULT 0,
    `created_time`      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_user_id`  INT(10) unsigned NOT NULL DEFAULT 0,
    `modified_time`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_out` 	    INT(11) NOT NULL DEFAULT 0,
    `checked_out_time` 	DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering`          INT(11) NOT NULL ,
    `version`           INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `language`          CHAR(7) NOT NULL,
    `hits`              INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_checkout` (`checked_out`),
    KEY `idx_state` (`published`),
    KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- ------------------------------------------------------
-- Table structure for table `#__peliculas_cinemas_rooms`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__peliculas_cinemas_rooms` (
    `id` 	              INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `cinema_id`         INT(11) unsigned NOT NULL DEFAULT 0,
    `name`              VARCHAR(250) DEFAULT NULL,
    `alias`             VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
    `informacion`       TEXT NOT NULL,
    `created_user_id`   INT(10) unsigned NOT NULL DEFAULT 0,
    `created_time`      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_user_id`  INT(10) unsigned NOT NULL DEFAULT 0,
    `modified_time`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_out` 	    INT(11) NOT NULL DEFAULT 0,
    `checked_out_time` 	DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering`          INT(11) NOT NULL ,
    `version`           INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `language`          CHAR(7) NOT NULL,
    `hits`              INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_checkout` (`checked_out`),
    KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


-- ------------------------------------------------------
-- Table structure for table `#__peliculas_events`
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__peliculas_events` (
    `id` 	              INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(250) DEFAULT NULL,
    `alias`             VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
    `description`       TEXT NOT NULL,
    `cinema_id`         INT(11) unsigned NOT NULL DEFAULT 0,
    `published` 	      TINYINT(1) unsigned NOT NULL DEFAULT '0',
    `params`            TEXT NOT NULL,
    `access`            INT(10) unsigned NOT NULL DEFAULT 0,
    `created_user_id`   INT(10) unsigned NOT NULL DEFAULT 0,
    `created_time`      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_user_id`  INT(10) unsigned NOT NULL DEFAULT 0,
    `modified_time`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_out` 	    INT(11) NOT NULL DEFAULT 0,
    `checked_out_time` 	DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `publish_up`        DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `publish_down`      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering`          INT(11) NOT NULL ,
    `version`           INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `language`          CHAR(7) NOT NULL,
    `hits`              INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_access` (`access`),
    KEY `idx_checkout` (`checked_out`),
    KEY `idx_state` (`published`),
    KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__peliculas_events_rooms` (
    `id` 	              INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `event_id`          INT(11) unsigned NOT NULL DEFAULT 0,
    `cinema_id`         INT(11) unsigned NOT NULL DEFAULT 0,
    `room_id`           INT(11) unsigned NOT NULL DEFAULT 0,
    `movie_id`          INT(11) unsigned NOT NULL DEFAULT 0,
    `informacion`       TEXT NOT NULL,
    `created_user_id`   INT(10) unsigned NOT NULL DEFAULT 0,
    `created_time`      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `modified_user_id`  INT(10) unsigned NOT NULL DEFAULT 0,
    `modified_time`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `checked_out` 	    INT(11) NOT NULL DEFAULT 0,
    `checked_out_time` 	DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ordering`          INT(11) NOT NULL ,
    `version`           INT(10) UNSIGNED NOT NULL DEFAULT '1',
    `language`          CHAR(7) NOT NULL,
    `hits`              INT(10) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `idx_checkout` (`checked_out`),
    KEY `idx_language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;