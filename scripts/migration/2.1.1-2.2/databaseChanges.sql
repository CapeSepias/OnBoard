rename table media to applicantMedia;
alter table applicantMedia modify internalFilename varchar(128) not null;
update applicantMedia set internalFilename=concat(date_format(uploaded, '%Y/%m/%d/'), internalFilename);
alter table applicantMedia change uploaded updated timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;
alter table applicantMedia add created datetime not null default CURRENT_TIMESTAMP after mime_type;
update applicantMedia set created=updated;

alter table committees add calendarId varchar(128) after endDate;

create table meetingMedia(
	id               int unsigned not null primary key auto_increment,
    committee_id     int unsigned not null,
    meetingDate      date         not null,
    eventId          varchar(128),
    type             varchar(16)  not null,
	internalFilename varchar(128) not null,
	filename         varchar(128) not null,
	mime_type        varchar(128) not null,
	created          datetime     not null default CURRENT_TIMESTAMP,
	updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	foreign key (committee_id) references committees(id)
);
