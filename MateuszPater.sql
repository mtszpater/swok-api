drop table if exists attendance_on_talks;
drop table if exists registrations_on_events;
drop table if exists friendship;
drop table if exists rate;
drop table if exists talk_proposal;
drop table if exists talk;
drop table if exists event;
drop table if exists member;

CREATE TABLE IF NOT EXISTS member (
  login text UNIQUE PRIMARY KEY,
  password text NOT NULL,
  admin bool default false
);

CREATE TABLE IF NOT EXISTS talk_proposal (
  id text PRIMARY KEY,
  title text NOT NULL,
  login text NOT NULL,
  rejected bool default false,
  date_start TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS event (
  name text PRIMARY KEY,
  date_start TIMESTAMP NOT NULL,
  date_end TIMESTAMP NOT NULL,
  talks int default 0,
  CHECK ( date_start < date_end ),
  UNIQUE (name)
);

CREATE TABLE IF NOT EXISTS talk (
  id text PRIMARY KEY,
  title text NOT NULL,
  login text NOT NULL,
  members int default 0,
  date_start TIMESTAMP NOT NULL,
  room int NOT NULL,
  event_name text NULL
);

CREATE TABLE IF NOT EXISTS registrations_on_events(
  login text NOT NULL,
  event_name text,
  PRIMARY KEY (login, event_name)
);

CREATE TABLE IF NOT EXISTS attendance_on_talks(
  login text NOT NULL,
  talk_id text NOT NULL,
  PRIMARY KEY (login, talk_id)
);

CREATE TABLE IF NOT EXISTS friendship (
  first_user TEXT NOT NULL,
  second_user TEXT NOT NULL,
  PRIMARY KEY (first_user, second_user),
  check ( second_user != first_user )
);

CREATE TABLE IF NOT EXISTS rate (
  talk_id text NOT NULL,
  login text NOT NULL,
  rate int NOT NULL,
  initial_evaluation bool default false,
  PRIMARY KEY (talk_id, login),
  check ( rate >= 0 AND rate <= 10 )
);

ALTER TABLE ONLY talk_proposal ADD CONSTRAINT fk_talk_proposal_login FOREIGN KEY (login) REFERENCES member(login);
ALTER TABLE ONLY talk ADD CONSTRAINT fk_talk_login FOREIGN KEY (login) REFERENCES member(login);
ALTER TABLE ONLY talk ADD CONSTRAINT fk_talk_event FOREIGN KEY (event_name) REFERENCES event(name);
ALTER TABLE ONLY registrations_on_events ADD CONSTRAINT fk_ron_login FOREIGN KEY (login) REFERENCES member(login);
ALTER TABLE ONLY registrations_on_events ADD CONSTRAINT fk_ron_event FOREIGN KEY (event_name) REFERENCES event(name);
ALTER TABLE ONLY attendance_on_talks ADD CONSTRAINT fk_aon_login FOREIGN KEY (login) REFERENCES member(login);
ALTER TABLE ONLY attendance_on_talks ADD CONSTRAINT fk_aon_talk FOREIGN KEY (talk_id) REFERENCES talk(id);
ALTER TABLE ONLY friendship ADD CONSTRAINT fk_friendship_fu FOREIGN KEY (first_user) REFERENCES member(login);
ALTER TABLE ONLY friendship ADD CONSTRAINT fk_friendship_su FOREIGN KEY (second_user) REFERENCES member(login);
ALTER TABLE ONLY rate ADD CONSTRAINT fk_rate_login FOREIGN KEY (login) REFERENCES member(login);
ALTER TABLE ONLY rate ADD CONSTRAINT fk_rate_talk_id FOREIGN KEY (talk_id) REFERENCES talk(id);
--
--  WYZWALACZE DLA AKTUALIZOWANIA LICZBY UZYTKOWNIKOW, KTORZY WZIELI UDZIAŁ W REFERACIE
--

CREATE OR REPLACE FUNCTION get_all_events_members(text) RETURNS bigint
AS 'select count(*) from attendance_on_talks WHERE talk_id = $1;' LANGUAGE SQL IMMUTABLE RETURNS NULL ON NULL INPUT;

CREATE OR REPLACE FUNCTION update_all_events_members() RETURNS TRIGGER AS
$$
BEGIN
  UPDATE talk SET members = (Select * from get_all_events_members(talk.id));
  return new;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_talk_after_insert
AFTER INSERT OR UPDATE OR DELETE ON attendance_on_talks
FOR EACH ROW EXECUTE PROCEDURE update_all_events_members();

--
--  WYZWALACZE DLA AKTUALIZOWANIA LICZBY REFERATÓW
--

CREATE OR REPLACE FUNCTION get_all_talks_count(text) RETURNS bigint
AS 'select count(*) from talk WHERE event_name = $1;' LANGUAGE SQL IMMUTABLE RETURNS NULL ON NULL INPUT;

CREATE OR REPLACE FUNCTION update_talks_count() RETURNS TRIGGER AS
$$
BEGIN
  UPDATE event SET talks = (Select * from get_all_talks_count(event.name));
  return new;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_event_after_insert
AFTER INSERT OR UPDATE OR DELETE ON talk
FOR EACH ROW EXECUTE PROCEDURE update_talks_count();