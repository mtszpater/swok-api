<?php
include_once "DatabaseOperation.php";

class DatabaseManager implements DatabaseOperation {
	private $connection = null;

	public function __construct($host = 'localhost', $port = '5432', $dbname = 'springbootdb', $user = 'ja', $password = '' )
    {
		$this->connection = pg_connect("host=".$host." port=".$port." dbname=".$dbname." user=".$user." password=".$password."");
	}

    public function userExists($user_login, $user_password)
    {
		$query = pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password';");

		return pg_num_rows($query) == 1 ? true : false;
	}

	public function isAdmin($user_login, $user_password)
    {
        $query = pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password' AND admin='1';");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function createEvent($event_name, $start_timestamp, $end_timestamp)
    {
        return pg_query($this->connection, "insert into event (name, date_start, date_end) VALUES ('$event_name', '$start_timestamp', '$end_timestamp');") ? true : false;
    }

	public function registerUser($user_login, $user_password)
    {
        return pg_query($this->connection, "insert into member (login, password) VALUES ('$user_login', '$user_password')") ? true : false;
    }

    public function isLoginBusy($user_login)
    {
        $query = pg_query($this->connection, "select * from member WHERE login='$user_login'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function isEventNameBusy($event_name)
    {
        $query = pg_query($this->connection, "select * from event WHERE name='$event_name'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function existsProposalTalk($talk_id)
    {
        $query = pg_query($this->connection, "select * from talk_proposal WHERE id='$talk_id'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function existsTalk($talk_id)
    {
        $query = pg_query($this->connection, "select * from talk WHERE id='$talk_id'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function createTalk($user_login, $talk, $title, $start_timestamp, $room, $event_name = NULL)
    {
        return pg_query($this->connection, "insert into talk (id, title, login, date_start, room, event_name) VALUES('$talk', '$title', '$user_login', '$start_timestamp', '$room', '$event_name')") ? true : false;
    }

    public function registerUserForEvent($user_login, $event_name)
    {
        return pg_query($this->connection, "insert into registrations_on_events (login, event_name) VALUES ('$user_login', '$event_name')") ? true : false;
    }

    public function checkAttendance($user_login, $talk_id)
    {
        return pg_query($this->connection, "insert into attendance_on_talks (login, talk_id) VALUES ('$user_login', '$talk_id')") ? true : false;
    }

    public function evaluationTalk($user_login, $talk_id, $rate)
    {
        return pg_query($this->connection, "insert into rate (talk_id, login, rate) VALUES ('$talk_id', '$user_login', '$rate')") ? true : false;
    }

    public function rejectTalk($talk_id)
    {
        return pg_query($this->connection, "delete from talk_proposal where id = '$talk_id'") ? true : false;
    }

    public function proposalTalk($user_login, $talk_id, $title, $start_timestamp)
    {
        return pg_query($this->connection, "insert into talk_proposal (id, title, login, date_start) VALUES ('$talk_id', '$title', '$user_login', '$start_timestamp')") ? true : false;
    }

    public function registerOrganizer($user_login, $user_password)
    {
        return pg_query($this->connection, "insert into member (login, password, admin) VALUES ('$user_login', '$user_password', true)") ? true : false;
    }

    public function createProposalTalk($user_login, $talk, $title, $start_timestamp)
    {
        return pg_query($this->connection, "insert into talk_proposal (id, title, login, date_start) VALUES ('$talk', '$title', '$user_login', '$start_timestamp')") ? true : false;
    }

    public function addFriend($user_login, $user_login2)
    {
        return pg_query($this->connection, "insert into friendship (first_user, second_user) VALUES ('$user_login', '$user_login2')") ? true : false;
    }

    public function getStartTimeStampOfEvent($event_name)
    {
        $query = pg_query($this->connection, "select date_start from event where name = '$event_name'");

        return pg_fetch_result($query, 0, 0);
    }

    public function getUserPlan($user_login, $limit)
    {

        if($limit <= 0) {
            $query = pg_query($this->connection, "
                  SELECT registrations_on_events.login, id as talk, date_start as start_timestamp, title, room FROM registrations_on_events
                  JOIN talk ON registrations_on_events.event_name=talk.event_name
                  WHERE registrations_on_events.login = '$user_login'
                  ORDER BY talk.date_start ASC;");
        }
        else {
            $query = pg_query($this->connection, "
                  SELECT registrations_on_events.login, id as talk, date_start as start_timestamp, title, room FROM registrations_on_events
                  JOIN talk ON registrations_on_events.event_name=talk.event_name
                  WHERE registrations_on_events.login = '$user_login'
                  ORDER BY talk.date_start ASC
                  LIMIT $limit;");
        }

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();

    }

    public function getDayPlan($timestamp)
    {
        $query = pg_query($this->connection,
            "SELECT id as talk, date_start as start_timestamp, title, room FROM talk where date_trunc('day', talk.date_start) =  date_trunc('day', TIMESTAMP '$timestamp') ORDER BY room, start_timestamp ASC;");

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }


    public function getBestTalks($start_timestamp, $end_timestamp, $limit, $all)
    {
//        TODO: ALL

        if($limit == 0) {
            $query = pg_query($this->connection,
                "SELECT rate.talk_id as talk, talk.date_start as start_timestamp, talk.room, talk.title FROM talk
                    JOIN rate on talk.id = rate.talk_id
                    WHERE talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp'
                    GROUP BY rate.talk_id, talk.date_start, talk.room, talk.title
                    ORDER BY avg(rate) DESC;");
        }
        else {
            $query = pg_query($this->connection,
                    "SELECT rate.talk_id as talk, talk.date_start as start_timestamp, talk.room, talk.title FROM talk
                    JOIN rate on talk.id = rate.talk_id
                    WHERE talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp'
                    GROUP BY rate.talk_id, talk.date_start, talk.room, talk.title
                    ORDER BY avg(rate) DESC
                    LIMIT '$limit';");
        }

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    public function getMostPopularTalks($start_timestamp, $end_timestamp, $limit)
    {

        if($limit == 0){
            $query = pg_query($this->connection, "
            SELECT id as talk, date_start as start_timestamp, title, room FROM talk 
            WHERE talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp'
            ORDER BY members DESC; ");
        }
        else
        {
            $query = pg_query($this->connection, "
            SELECT id as talk, date_start as start_timestamp, title, room FROM talk 
            WHERE talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp'
            ORDER BY members DESC
            LIMIT '$limit';");
        }

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();

    }

    public function getAttendedTalks($user_login)
    {
        $query = pg_query($this->connection, "SELECT attendance_on_talks.talk_id as talk, talk.date_start as start_timestamp, title, room  FROM talk
JOIN attendance_on_talks on talk.id=attendance_on_talks.talk_id
WHERE attendance_on_talks.login = '$user_login';");

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    public function getProposalTalks()
    {
        $query = pg_query($this->connection, "SELECT id as talk, title, login as speakerlogin, date_start as start_timestamp FROM talk_proposal;");
        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }
}
?>