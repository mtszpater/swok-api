<?php
include_once "DatabaseManagerInterface.php";

/**
 * Class DatabaseManager
 */
class DatabaseManager implements DatabaseManagerInterface {
    /**
     * @var null|resource
     */
    private $connection = null;

    /**
     * DatabaseManager constructor.
     * @param string $dbname
     * @param string $user
     * @param string $password
     * @param string $host
     * @param string $port
     */
    public function __construct($dbname = 'springbootdb', $user = 'ja', $password = '', $host = 'localhost', $port = '5432')
    {
		$this->connection = @pg_connect("host=".$host." port=".$port." dbname=".$dbname." user=".$user." password=".$password."") or die("Niepoprawne dane\n");
	}

    public function loadDatabase(){
        $filename = __DIR__ . '/db.sql';

        $templine = '';
        $lines = file($filename);
        foreach ($lines as $line)
        {
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            $templine .= $line;
        }

        @pg_query($this->connection, $templine);
    }

    /**
     * @param $user_login
     * @param $user_password
     * @return bool
     */
    public function userExists($user_login, $user_password)
    {
		$query = @pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password';");

		return pg_num_rows($query) == 1 ? true : false;
	}

    /**
     * @param $user_login
     * @param $user_password
     * @return bool
     */
    public function isAdmin($user_login, $user_password)
    {
        $query = @pg_query($this->connection, "select * from member WHERE login='$user_login' AND password='$user_password' AND admin='1';");

        return pg_num_rows($query) == 1 ? true : false;
    }

    /**
     * @param $event_name
     * @param $start_timestamp
     * @param $end_timestamp
     * @return bool
     */
    public function createEvent($event_name, $start_timestamp, $end_timestamp)
    {
        return @pg_query($this->connection, "insert into event (name, date_start, date_end) VALUES ('$event_name', '$start_timestamp', '$end_timestamp');") ? true : false;
    }

    /**
     * @param $user_login
     * @param $user_password
     * @return bool
     */
    public function registerUser($user_login, $user_password)
    {
        return @pg_query($this->connection, "insert into member (login, password) VALUES ('$user_login', '$user_password')") ? true : false;
    }

    /**
     * @param $user_login
     * @return bool
     */
    public function isLoginBusy($user_login)
    {
        $query = @pg_query($this->connection, "select * from member WHERE login='$user_login'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    /**
     * @param $event_name
     * @return bool
     */
    public function isEventNameBusy($event_name)
    {
        $query = @pg_query($this->connection, "select * from event WHERE name='$event_name'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    /**
     * @param $talk_id
     * @return bool
     */
    public function existsProposalTalk($talk_id)
    {
        $query = @pg_query($this->connection, "select * from talk_proposal WHERE id='$talk_id'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    /**
     * @param $talk_id
     * @return bool
     */
    public function existsTalk($talk_id)
    {
        $query = @pg_query($this->connection, "select * from talk WHERE id='$talk_id'");

        return pg_num_rows($query) == 1 ? true : false;
    }

    /**
     * @param $user_login
     * @param $talk
     * @param $title
     * @param $start_timestamp
     * @param $room
     * @param null $event_name
     * @return bool
     */
    public function createTalk($user_login, $talk, $title, $start_timestamp, $room, $event_name = NULL)
    {
        if(is_null($event_name))
            return @pg_query($this->connection, "insert into talk (id, title, login, date_start, room, event_name) VALUES('$talk', '$title', '$user_login', '$start_timestamp', '$room', NULL)") ? true : false;
        else
            return @pg_query($this->connection, "insert into talk (id, title, login, date_start, room, event_name) VALUES('$talk', '$title', '$user_login', '$start_timestamp', '$room', '$event_name')") ? true : false;
    }

    /**
     * @param $user_login
     * @param $event_name
     * @return bool
     */
    public function registerUserForEvent($user_login, $event_name)
    {
        return @pg_query($this->connection, "insert into registrations_on_events (login, event_name) VALUES ('$user_login', '$event_name')") ? true : false;
    }

    /**
     * @param $user_login
     * @param $talk_id
     * @return bool
     */
    public function checkAttendance($user_login, $talk_id)
    {
        return @pg_query($this->connection, "insert into attendance_on_talks (login, talk_id) VALUES ('$user_login', '$talk_id')") ? true : false;
    }

    /**
     * @param $user_login
     * @param $talk_id
     * @param $rate
     * @param bool $initial_evaluation
     * @return bool
     */
    public function evaluationTalk($user_login, $talk_id, $rate, $initial_evaluation = false)
    {
        if( $initial_evaluation )
            return @pg_query($this->connection, "insert into rate (talk_id, login, rate, initial_evaluation) VALUES ('$talk_id', '$user_login', '$rate', 'TRUE')") ? true : false;
        else
            return @pg_query($this->connection, "insert into rate (talk_id, login, rate, initial_evaluation) VALUES ('$talk_id', '$user_login', '$rate', 'FALSE')") ? true : false;
    }

    /**
     * @param $talk_id
     * @return bool
     */
    public function rejectTalk($talk_id)
    {
        return @pg_query($this->connection, "update talk_proposal SET rejected = TRUE where id = '$talk_id'") ? true : false;
    }

    /**
     * @param $talk_id
     * @return bool
     */
    public function deleteProposalTalk($talk_id)
    {
        return @pg_query($this->connection, "delete from talk_proposal where id = '$talk_id'") ? true : false;
    }

    /**
     * @param $user_login
     * @param $talk_id
     * @param $title
     * @param $start_timestamp
     * @return bool
     */
    public function proposalTalk($user_login, $talk_id, $title, $start_timestamp)
    {
        return @pg_query($this->connection, "insert into talk_proposal (id, title, login, date_start) VALUES ('$talk_id', '$title', '$user_login', '$start_timestamp')") ? true : false;
    }

    /**
     * @param $user_login
     * @param $user_password
     * @return bool
     */
    public function registerOrganizer($user_login, $user_password)
    {
        return @pg_query($this->connection, "insert into member (login, password, admin) VALUES ('$user_login', '$user_password', true)") ? true : false;
    }

    /**
     * @param $user_login
     * @param $talk
     * @param $title
     * @param $start_timestamp
     * @return bool
     */
    public function createProposalTalk($user_login, $talk, $title, $start_timestamp)
    {
        return @pg_query($this->connection, "insert into talk_proposal (id, title, login, date_start) VALUES ('$talk', '$title', '$user_login', '$start_timestamp')") ? true : false;
    }

    /**
     * @param $user_login
     * @param $user_login2
     * @return bool
     */
    public function addFriend($user_login, $user_login2)
    {
        return @pg_query($this->connection, "insert into friendship (first_user, second_user) VALUES ('$user_login', '$user_login2')") ? true : false;
    }

    /**
     * @param $event_name
     * @return string
     */
    public function getStartTimeStampOfEvent($event_name)
    {
        $query = @pg_query($this->connection, "select date_start from event where name = '$event_name'");

        return pg_fetch_result($query, 0, 0);
    }

    /**
     * @param $user_login
     * @param $limit
     * @return array
     */
    public function getUserPlan($user_login, $limit)
    {

        $query = "SELECT talk.login as login, id as talk, date_start as start_timestamp, title, room FROM registrations_on_events
                  JOIN talk ON registrations_on_events.event_name=talk.event_name
                  WHERE registrations_on_events.login = '$user_login' AND NOW() <= date_start
                  ORDER BY talk.date_start ASC";

        if($limit != 0){
            $query = $query . " LIMIT '$limit'";
        }

        $query = @pg_query($this->connection, $query);

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();

    }

    /**
     * @param $timestamp
     * @return array
     */
    public function getDayPlan($timestamp)
    {
        $query = @pg_query($this->connection,
            "SELECT id as talk, date_start as start_timestamp, title, room FROM talk where date_trunc('day', talk.date_start) =  date_trunc('day', TIMESTAMP '$timestamp') ORDER BY room, start_timestamp ASC;");

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    /**
     * @param $start_timestamp
     * @param $end_timestamp
     * @param $limit
     * @param $all
     * @return array
     */
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
            $query = "SELECT  talk_id as talk, talk.title, talk.date_start as start_timestamp, talk.room FROM rate
                        JOIN talk on rate.talk_id = talk.id
                        WHERE rate.login||'~'||talk_id IN (SELECT login||'~'||talk_id from rate where initial_evaluation = true union ( SELECT login||'~'||talk_id from rate intersect SELECT login||'~'||talk_id from attendance_on_talks ) )
                        AND  talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp'
                        GROUP BY talk_id,  talk.title, talk.date_start, talk.room
                        ORDER BY avg(rate) DESC";

        }

        $query = $this->addLimit($limit, $query);

        $query = @pg_query($this->connection, $query);

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    /**
     * @param $start_timestamp
     * @param $end_timestamp
     * @param $limit
     * @return array
     */
    public function getMostPopularTalks($start_timestamp, $end_timestamp, $limit)
    {

        $query = " SELECT id as talk, date_start as start_timestamp, title, room FROM talk WHERE talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp' ORDER BY members DESC";

        $query = $this->addLimit($limit, $query);

        $query = @pg_query($this->connection, $query);

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();

    }

    /**
     * @param $user_login
     * @return array
     */
    public function getAttendedTalks($user_login)
    {
        $query = @pg_query($this->connection, "SELECT attendance_on_talks.talk_id as talk, talk.date_start as start_timestamp, title, room  FROM talk
                                                    JOIN attendance_on_talks on talk.id=attendance_on_talks.talk_id
                                                    WHERE attendance_on_talks.login = '$user_login';");

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    /**
     * @return array
     */
    public function getProposalTalks()
    {
        $query = @pg_query($this->connection, "SELECT id as talk, title, login as speakerlogin, date_start as start_timestamp FROM talk_proposal where rejected = FALSE;");
        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    /**
     * @param $user_login
     * @param $start_timestamp
     * @param $end_timestamp
     * @param $limit
     * @return array
     */
    public function getFriendsTalks($user_login, $start_timestamp, $end_timestamp, $limit)
    {
            $query = "Select talk.id as talk, talk.login as speakerlogin, talk.date_start as start_timestamp, title, room from friendship f1
                                                    JOIN friendship f2 on f1.first_user = f2.second_user
                                                    JOIN talk on f1.second_user = talk.login
                                                    WHERE f1.first_user = '$user_login'
                                                    AND f1.second_user = f2.first_user
                                                    AND talk.date_start >= '$start_timestamp' AND talk.date_start <= '$end_timestamp'
                                                    ORDER BY date_start ASC";

        $query = $this->addLimit($limit, $query);

        $query = @pg_query($this->connection, $query);

        return pg_fetch_all($query) ? pg_fetch_all($query) : array();

    }

    /**
     * @param $user_login
     * @param $event_name
     * @return array
     */
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

    /**
     * @return array
     */
    public function getAllRejectedTalks()
    {
        $query = @pg_query($this->connection, "Select t.id as talk, t.login as speakerlogin, t.date_start as start_timestamp, t.title FROM talk_proposal t WHERE t.rejected = 't';");
        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    /**
     * @param $user_login
     * @return array
     */
    public function getAllRejectedTalksForUser($user_login)
    {
        $query = @pg_query($this->connection, "Select t.id as talk, t.login as speakerlogin, t.date_start as start_timestamp, t.title FROM talk_proposal t WHERE t.rejected = 't' AND t.login = '$user_login';");
        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    /**
     * @param $limit
     * @return array
     */
    public function getAbandonedTalks($limit)
    {
        $query = "SELECT t.id as talk, q2.s as number, t.title, t.date_start as start_timestamp, t.room from (
                  SELECT count(*) as s, q1.talk_id from 
                        (SELECT t.id as talk_id, s.login from talk t JOIN registrations_on_events s on s.event_name = t.event_name except SELECT b.talk_id, b.login from attendance_on_talks b) q1 
                        group by q1.talk_id  
                    UNION DISTINCT select 0 as s, g.id as talk_id from talk g
                    WHERE g.id NOT IN ( SELECT q1.talk_id from (SELECT t.id as talk_id, s.login from talk t JOIN registrations_on_events s on s.event_name = t.event_name except SELECT b.talk_id, b.login from attendance_on_talks b) q1 )) q2
                  JOIN talk t on q2.talk_id = t.id
                  ORDER by number DESC";

        $query = $this->addLimit($limit, $query);

        $query = @pg_query($this->connection, $query);


        return pg_fetch_all($query) ? pg_fetch_all($query) : array();
    }

    /**
     * @param $limit
     * @param $query
     * @return string
     */
    private function addLimit($limit, $query)
    {
        if ($limit != 0)
            $query = $query . " LIMIT '$limit'";
        return $query;
    }
}
?>