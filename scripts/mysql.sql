-- @copyright 2006-2016 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
create table races (
	id int unsigned not null primary key auto_increment,
	name varchar(50) not null unique
);
insert races set name='Caucasion';
insert races set name='Hispanic';
insert races set name='African American';
insert races set name='Native American';
insert races set name='Asian';
insert races set name='Other';

create table departments (
    id int unsigned not null primary key auto_increment,
    name  varchar(128) not null unique
);

create table people (
	id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	lastname  varchar(128) not null,
	email     varchar(128) unique,
	phone     varchar(32),
	address   varchar(128),
	city      varchar(32),
	state     varchar(8),
	zip       varchar(8),
	about text,
	gender enum('male','female'),
	race_id int unsigned,
	username varchar(40) unique,
	password varchar(40),
	authenticationMethod varchar(40),
	role varchar(30),
	foreign key (race_id) references races(id)
);

create table committees (
	id            int unsigned  not null primary key auto_increment,
	type enum('seated', 'open') not null default 'seated',
	name          varchar(128)  not null,
	statutoryName varchar(128),
	code          varchar(8),
	yearFormed    year(4),
	endDate       date,
	calendarId    varchar(128),
	website       varchar(128),
	videoArchive  varchar(128),
	email         varchar(128),
	phone         varchar(128),
	address       varchar(128),
	city          varchar(128),
	state         varchar(32),
	zip           varchar(32),
    description     text,
	meetingSchedule text,
	termEndWarningDays  tinyint unsigned not null default 0,
	applicationLifetime tinyint unsigned not null default 90
);

create table committeeStatutes(
    id           int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    citation varchar(128) not null,
    url      varchar(128) not null,
    foreign key (committee_id) references committees(id)
);

create table committee_departments (
    committee_id  int unsigned not null,
    department_id int unsigned not null,
    primary key (committee_id, department_id),
    foreign key (committee_id)  references committees (id),
    foreign key (department_id) references departments(id)
);

create table appointers (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null unique
);
insert appointers values(1,'Elected');

create table seats (
    id int unsigned not null primary key auto_increment,
    type enum('termed', 'open') not null default 'termed',
    code varchar(16),
    name varchar(128) not null,
	committee_id int unsigned not null,
	appointer_id int unsigned,
    startDate date,
    endDate   date,
    requirements text,
    termLength varchar(32),
    voting boolean not null default 1,
	foreign key (committee_id) references committees(id),
	foreign key (appointer_id) references appointers(id)
);

create table terms (
    id      int unsigned not null primary key auto_increment,
	seat_id int unsigned,
	startDate date not null,
	endDate   date not null,
	foreign key (seat_id) references seats(id)
);

create table members (
	id int unsigned not null primary key auto_increment,
	committee_id int unsigned not null,
	seat_id      int unsigned,
	term_id      int unsigned,
	person_id    int unsigned not null,
	startDate date,
	endDate   date,
	foreign key (committee_id) references committees(id),
	foreign key (seat_id)      references seats     (id),
	foreign key (term_id)      references terms     (id),
	foreign key (person_id)    references people    (id)
);

create table liaisons (
    id int unsigned not null primary key auto_increment,
    type enum('legal', 'departmental') not null default 'departmental',
    committee_id int unsigned not null,
    person_id    int unsigned not null,
    foreign key (committee_id) references committees(id),
    foreign key (person_id)    references people(id)
);

create table applicants (
    id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	lastname  varchar(128) not null,
	email     varchar(128),
	phone     varchar(32),
	address   varchar(128),
	city      varchar(128),
	zip       varchar(5),
	citylimits     boolean,
	occupation     varchar(128),
	referredFrom   varchar(128),
	referredOther  varchar(128),
	interest       text,
	qualifications text,
	created  datetime,
	modified timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
);

create table applications (
    id int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    applicant_id int unsigned not null,
    created  timestamp not null default CURRENT_TIMESTAMP,
    archived datetime,
    foreign key (committee_id) references committees(id),
    foreign key (applicant_id) references applicants(id)
);

create table applicantFiles (
	id int unsigned not null primary key auto_increment,
	internalFilename varchar(128) not null,
	filename         varchar(128) not null,
	mime_type        varchar(128) not null,
	created          datetime     not null /*!50700 default CURRENT_TIMESTAMP */,
	updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	applicant_id     int unsigned not null,
	foreign key (applicant_id) references applicants(id)
);

create table offices (
	id int unsigned not null primary key auto_increment,
	committee_id int unsigned not null,
	person_id int unsigned not null,
	title varchar(128) not null,
	startDate date not null,
	endDate date,
	foreign key (committee_id) references committees(id),
	foreign key (person_id) references people(id)
);

create table siteContent (
    label varchar(128) not null primary key,
    content text
);

create table meetingFiles(
	id               int unsigned not null primary key auto_increment,
    committee_id     int unsigned not null,
    meetingDate      date         not null,
    eventId          varchar(128),
    type             varchar(16)  not null,
	internalFilename varchar(128) not null,
	filename         varchar(128) not null,
	mime_type        varchar(128) not null,
	created          datetime     not null /*!50700 default CURRENT_TIMESTAMP */,
	updated          timestamp    not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
	foreign key (committee_id) references committees(id)
);

create table committeeHistory(
    id           int unsigned not null primary key auto_increment,
    committee_id int unsigned not null,
    person_id    int unsigned not null,
    date         timestamp    not null default CURRENT_TIMESTAMP,
    tablename    varchar(32)  not null,
    action       varchar(32)  not null,
    changes      text,
    foreign key (committee_id) references committees(id),
    foreign key (person_id)    references people    (id)
);
