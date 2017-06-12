DROP SCHEMA IF EXISTS lotterydb;

CREATE SCHEMA lotterydb;

create table lotterydb.le_play
(
	id char(36) not null,
	post_time datetime default CURRENT_TIMESTAMP not null,
	put_time datetime default CURRENT_TIMESTAMP not null,
	name varchar(32) default '' not null,
	active tinyint(1) default '1' not null,
	level int default '0' not null,
	`desc` varchar(256) default '' not null,
	rule varchar(256) default '' not null,
	daily tinyint(1) default '1' not null,
	`limit` int default '0' not null,
	size int default '0' not null,
	count int default '0' not null,
	weights json not null,
	constraint le_play_id_uindex
		unique (id)
)
;

create table lotterydb.le_reward
(
	id char(36) not null,
	post_time datetime default CURRENT_TIMESTAMP not null,
	put_time datetime default CURRENT_TIMESTAMP not null,
	name varchar(32) default '' not null,
	active tinyint(1) default '1' not null,
	level int default '0' not null,
	`desc` varchar(256) default '' not null,
	award_id char(36) not null,
	size int default '0' not null,
	count int default '0' not null,
	constraint le_reward_id_uindex
		unique (id)
)
;

