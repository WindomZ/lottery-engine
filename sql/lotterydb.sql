DROP SCHEMA IF EXISTS lotterydb;

CREATE SCHEMA lotterydb CHARACTER SET utf8 COLLATE utf8_general_ci;

create table lotterydb.le_play
(
	id char(36) not null
		primary key,
	post_time datetime default CURRENT_TIMESTAMP not null,
	put_time datetime default CURRENT_TIMESTAMP not null,
	name varchar(32) default '' not null,
	active tinyint(1) default '1' not null,
	`desc` varchar(256) default '' not null,
	level int default '0' not null,
	daily tinyint(1) default '1' not null,
	`limit` int default '0' not null,
	size int default '0' not null,
	count int default '0' not null,
	weights json,
	rule tinyint(1) default '0' not null,
	constraint le_play_id_uindex
		unique (id)
)
;

create index le_play_name_active_index
	on lotterydb.le_play (name, active)
;

create table lotterydb.le_record
(
	id char(36) not null
		primary key,
	post_time datetime default CURRENT_TIMESTAMP not null,
	put_time datetime default CURRENT_TIMESTAMP not null,
	user_id char(36) not null,
	play_id char(36) not null,
	reward_id char(36) not null,
	winning tinyint(1) default '1' not null,
	constraint le_record_id_uindex
		unique (id)
)
;

create index le_record_play_id_index
	on lotterydb.le_record (play_id)
;

create index le_record_reward_id_index
	on lotterydb.le_record (reward_id)
;

create index le_record_user_id_index
	on lotterydb.le_record (user_id)
;

create index le_record_play_id_reward_id_index
	on lotterydb.le_record (play_id, reward_id)
;

create table lotterydb.le_reward
(
	id char(36) not null
		primary key,
	post_time datetime default CURRENT_TIMESTAMP not null,
	put_time datetime default CURRENT_TIMESTAMP not null,
	name varchar(32) default '' not null,
	active tinyint(1) default '1' not null,
	`desc` varchar(256) default '' not null,
	level int default '0' not null,
	award_id char(36) not null,
	award_class int default '0' not null,
	award_kind int default '0' not null,
	size int default '0' not null,
	count int default '0' not null,
	constraint le_reward_id_uindex
		unique (id)
)
;

create index le_reward_award_id_index
	on lotterydb.le_reward (award_id)
;

create index le_reward_name_active_index
	on lotterydb.le_reward (name, active)
;

create index le_reward_award_class_award_kind_index
	on lotterydb.le_reward (award_class, award_kind)
;

create table lotterydb.le_rule
(
	id char(36) not null
		primary key,
	post_time datetime default CURRENT_TIMESTAMP not null,
	put_time datetime default CURRENT_TIMESTAMP not null,
	name varchar(32) default '' not null,
	active tinyint(1) default '1' not null,
	play_id char(36) not null,
	reward_id char(36) not null,
	weight int default '0' not null,
	constraint le_rule_id_uindex
		unique (id),
	constraint le_rule_play_id_reward_id_uindex
		unique (play_id, reward_id)
)
;

create index le_rule_play_id_index
	on lotterydb.le_rule (play_id)
;
