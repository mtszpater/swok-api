DROP TRIGGER IF EXISTS update_talk_after_insert ON attendance_on_talks;
DROP TRIGGER IF EXISTS update_event_after_insert ON talk;


CREATE TABLE IF NOT EXISTS member (
  login text UNIQUE PRIMARY KEY,
  password text NOT NULL,
  admin bool default false
);

CREATE TABLE IF NOT EXISTS talk_proposal (
  id text PRIMARY KEY,
  title text NOT NULL,
  login text NOT NULL REFERENCES member(login),
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
  login text NOT NULL REFERENCES member(login),
  members int default 0,
  date_start TIMESTAMP NOT NULL,
  room int NOT NULL,
  talk_added TIMESTAMP default now(),
  event_name text NULL  REFERENCES event(name)
);


CREATE TABLE IF NOT EXISTS registrations_on_events(
  login text NOT NULL REFERENCES member(login),
  event_name text REFERENCES event(name),
  PRIMARY KEY (login, event_name)
);

CREATE TABLE IF NOT EXISTS attendance_on_talks(
  login text NOT NULL REFERENCES member(login),
  talk_id text NOT NULL REFERENCES talk(id),
  PRIMARY KEY (login, talk_id)
);

CREATE TABLE IF NOT EXISTS friendship (
  first_user TEXT NOT NULL REFERENCES member(login),
  second_user TEXT NOT NULL REFERENCES member(login),
  PRIMARY KEY (first_user, second_user),
  check ( second_user != first_user )
);

CREATE TABLE IF NOT EXISTS rate (
  talk_id text NOT NULL  REFERENCES talk(id),
  login text NOT NULL  REFERENCES member(login),
  rate int NOT NULL,
  initial_evaluation bool default false,
  PRIMARY KEY (talk_id, login),
  check ( rate >= 0 AND rate <= 10 )
);

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