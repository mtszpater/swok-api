<?php
include_once "DatabaseManagerInterface.php";

class DatabaseManager implements DatabaseManagerInterface {
	private $connection = null;

	public function __construct($dbname = 'springbootdb', $user = 'ja', $password = '', $host = 'localhost', $port = '5432')
    {
		$this->connection = pg_connect("host=".$host." port=".$port." dbname=".$dbname." user=".$user." password=".$password."");
	}

    public function userExists($user_login, $user_password)
    {
		$query = @pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password';");

		return pg_num_rows($query) == 1 ? true : false;
	}

	public function isAdmin($user_login, $user_password)
    {
        $query = @pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password' AND admin='1';");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function createEvent($event_name, $start_timestamp, $end_timestamp)
    {
        return @pg_query($this->connection, "insert into event (name, date_start, date_end) VALUES ('$event_name', '$start_timestamp', '$end_timestamp');") ? true : false;
    }

	public function registerUser($user_login, $user_password)
    {
        return @pg_query($this->connection, "insert into member (login, password) VALUES ('$user_login', '$user_password')") ? true : false;
    }

    public function isLoginBusy($user_login)
    {
        $query = @pg_query($this->connection, "select * from member WHERE login='$user_login'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function isEventNameBusy($event_name)
    {
        $query = @pg_query($this->connection, "select * from event WHERE name='$event_name'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function existsProposalTalk($talk_id)
    {
        $query = @pg_query($this->connection, "select * from talk_proposal WHERE id='$talk_id'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function existsTalk($talk_id)
    {
        $query = @pg_query($this->connection, "select * from talk WHERE id='$talk_id'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    public function createTalk($user_login, $talk, $title, $start_timestamp, $room, $event_name = NULL)
    {
        return @pg_query($this->connection, "insert into talk (id, title, login, date_start, room, event_name) VALUES('$talk', '$title', '$user_login', '$start_timestamp', '$room', '$event_name')") ? true : false;
    }

    public function registerUserForEvent($user_login, $event_name)
    {
        return @pg_query($this->connection, "insert into registrations_on_events (login, event_name) VALUES ('$user_login', '$event_name')") ? true : false;
    }

    public function checkAttendance($user_login, $talk_id)
    {
        return @pg_query($this->connection, "insert into attendance_on_talks (login, talk_id) VALUES ('$user_login', '$talk_id')") ? true : false;
    }


    public function evaluationTalk($user_login, $talk_id, $rate, $initial_evaluation = false)
    {
        if( $initial_evaluation )
            return @pg_query($this->connection, "insert into rate (talk_id, login, rate, initial_evaluation) VALUES ('$talk_id', '$user_login', '$rate', 'TRUE')") ? true : false;
        else
            return @pg_query($this->connection, "insert into rate (talk_id, login, rate, initial_evaluation) VALUES ('$talk_id', '$user_login', '$rate', 'FALSE')") ? true : false;
    }

    public function rejectTalk($talk_id)
    {
        return @pg_query($this->connection, "update talk_proposal SET rejected = TRUE where id = '$talk_id'") ? true : false;
    }

    public function proposalTalk($user_login, $talk_id, $title, $start_timestamp)
    {
        return @pg_query($this->connection, "insert into talk_proposal (id, title, login, date_start) VALUES ('$talk_id', '$title', '$user_login', '$start_timestamp')") ? true : false;
    }

    public function registerOrganizer($user_login, $user_password)
    {
        return @pg_query($this->connection, "insert into member (login, password, admin) VALUES ('$user_login', '$user_password', true)") ? true : false;
    }

    public function createProposalTalk($user_login, $talk, $title, $start_timestamp)
    {
        return @pg_query($this->connection, "insert into talk_proposal (id, title, login, date_start) VALUES ('$talk', '$title', '$user_login', '$start_timestamp')") ? true : false;
    }

    public function addFriend($user_login, $user_login2)
    {
        return @pg_query($this->connection, "insert into friendship (first_user, second_user) VALUES ('$user_login', '$user_login2')") ? true : false;
    }

    public function getStartTimeStampOfEvent($event_name)
    {
        $query = @pg_query($this->connection, "select date_start from event where name = '$event_name'");

        return pg_fetch_result($query, 0, 0);
    }

    public function getUserPlan($user_login, $limit)
    {

        $query = "SELECT registrations_on_events.login, id as talk, date_start as start_timestamp, title, room FROM registrations_on_events
                  JOIN talk ON registrations_on_events.event_name=talk.event_name
                  WHERE registrations_on_events.login = '$user_login'
                  ORDER BY talk.date_start ASC";

        if($limit != 0){
            $query = $query . " LIMIT '$limit'";
        }

        $query = @pg_query($this->connection, $query);

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();

    }

    public function getDayPlan($timestamp)
    {
        $query = @pg_query($this->connection,
            "SELECT id as talk, date_start as start_timestamp, title, room FROM talk where date_trunc('day', talk.date_start) =  date_trunc('day', TIMESTAMP '$timestamp') ORDER BY room, start_timestamp ASC;");

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }


    public function getBestTalks($start_timestamp, $end_timestamp, $limit, $all)
    {
        if($all == 1)
        {
            $query = "SELECT rate.talk_id as talk, talk.date_start as start_timestamp, talk.room, talk.title FROM talk
                    JOIN rate on talk.id = rate.talk_id
                    WHERE talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp'
                    GROUP BY rate.talk_id, talk.date_start, talk.room, talk.title
                    ORDER BY avg(rate) DESC";

        }
        else
        {
                $query = "SELECT avg(rate), rate.talk_id as talk, talk.date_start as start_timestamp, talk.room, talk.title  from attendance_on_talks p
                                                        JOIN rate on rate.talk_id = p.talk_id
                                                        JOIN talk on p.talk_id = talk.id
                                                        WHERE talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp'
                                                        GROUP BY rate.talk_id, talk.date_start, talk.room, talk.title
                                                        ORDER BY avg(rate) DESC";

        }

        if($limit != 0)
            $query = $query . " LIMIT '$limit'";

        $query = @pg_query($this->connection, $query);

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    public function getMostPopularTalks($start_timestamp, $end_timestamp, $limit)
    {

        $query = " SELECT id as talk, date_start as start_timestamp, title, room FROM talk WHERE talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp' ORDER BY members DESC";

        if($limit != 0)
            $query = $query . " LIMIT '$limit'";

        $query = @pg_query($this->connection, $query);

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();

    }

    public function getAttendedTalks($user_login)
    {
        $query = @pg_query($this->connection, "SELECT attendance_on_talks.talk_id as talk, talk.date_start as start_timestamp, title, room  FROM talk
                                                    JOIN attendance_on_talks on talk.id=attendance_on_talks.talk_id
                                                    WHERE attendance_on_talks.login = '$user_login';");

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    public function getProposalTalks()
    {
        $query = @pg_query($this->connection, "SELECT id as talk, title, login as speakerlogin, date_start as start_timestamp FROM talk_proposal;");
        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    public function getFriendsTalks($user_login, $start_timestamp, $end_timestamp, $limit)
    {
            $query = "Select talk.id as talk, talk.login as speakerlogin, talk.date_start as start_timestamp, title, room from friendship f1
                                                    JOIN friendship f2 on f1.first_user = f2.second_user
                                                    JOIN talk on f1.second_user = talk.login
                                                    WHERE f1.first_user = '$user_login'
                                                    AND f1.second_user = f2.first_user
                                                    AND talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp'
                                                    ORDER BY date_start ASC";

        if($limit != 0)
            $query = $query . " LIMIT '$limit'";

        $query = @pg_query($this->connection, $query);

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();

    }

    public function getFriendsEvents($user_login, $event_name)
    {
        $query = @pg_query($this->connection, "Select f1.first_user as login, r.event_name as event, r.login as friendlogin from friendship f1
                                                    JOIN friendship f2 on f1.first_user = f2.second_user
                                                    JOIN registrations_on_events r on f1.second_user = r.login
                                                    WHERE f1.first_user = '$user_login'
                                                    AND f1.second_user = f2.first_user
                                                    AND r.event_name = '$event_name';");

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    public function getAllRejectedTalks()
    {
        $query = @pg_query($this->connection, "Select t.id as talk, t.login as speakerlogin, t.date_start as start_timestamp, t.title FROM talk_proposal t WHERE t.rejected = 't';");
        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    public function getAllRejectedTalksForUser($user_login)
    {
        $query = @pg_query($this->connection, "Select t.id as talk, t.login as speakerlogin, t.date_start as start_timestamp, t.title FROM talk_proposal t WHERE t.rejected = 't' AND t.login = '$user_login';");
        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    public function getAbandonedTalks($limit)
    {
        $query = "SELECT t.id as talk, t.title, count(r.*) - t.members as number, t.room, t.date_start as start_timestamp from registrations_on_events r
                                                        JOIN talk t on r.event_name = t.event_name
                                                        GROUP by r.event_name, t.id, t.title, t.members
                                                        ORDER by number DESC";

        if($limit != 0)
            $query = $query . " LIMIT '$limit'";

        $query = @pg_query($this->connection, $query);


        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }
}
?>