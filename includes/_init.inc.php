<?php
session_start();
$db_server = 'localhost';
	$db_name = 'manager';
	$db_pass = '';
	$db_user = 'root';

$db = new mysqli($db_server,$db_user,$db_pass,$db_name);
if($db->connect_errno > 0){
 die('Unable to connect to database [' . $db->connect_error . ']');
}

//
// Need to select the current game_day from the `system` table
// SELECT game_day FROM system
//
$stmt = $db->prepare(
	"SELECT game_day, max_game_day, current_season
		FROM system
		LIMIT 1"
);
$stmt->execute();
$stmt->bind_result($gameDay,$maxGameDay,$currentSeason);
$stmt->fetch();
$stmt->close();
if ($gameDay >= $maxGameDay) {
	$gameDayText = "Off Season";
} else {
	$gameDayText = $gameDay+1;
}

if (isset($_COOKIE['userKey']) && isset($_COOKIE['user'])){

	$ck_user = $_COOKIE['user'];
	$ck_key = $_COOKIE['userKey'];
	$stmt = $db->prepare("SELECT user.user_id, user_name, user_joined, user_verified, team_name, team.team_id, team_color1, team_color2,team_elo, division.division_id, division_name, credits,
		(SELECT COUNT(win_team_id) FROM game
			JOIN schedule
			ON schedule.schedule_id = game.schedule_id
			WHERE schedule.season_number = ?
			AND win_team_id = team.team_id) as win,
		(SELECT COUNT(lose_team_id) FROM game
			JOIN schedule
			ON schedule.schedule_id = game.schedule_id
			WHERE schedule.season_number = ?
			AND lose_team_id = team.team_id) as loss,
		(SELECT COUNT(team_id_2)
			FROM player_team_trade
			WHERE team_id_2 = team.team_id
			AND trade_open = 1) as trade_offers
		FROM user
		LEFT JOIN user_team
		ON user.user_id = user_team.user_id
		LEFT JOIN team
		ON team.team_id = user_team.team_id
		LEFT JOIN team_division
		ON team.team_id = team_division.team_id
		LEFT JOIN division
		ON team_division.division_id = division.division_id
		JOIN team_money
		ON team_money.team_id = team.team_id
		WHERE user.user_id = ?
											LIMIT 1");
	$stmt->bind_param("iis", $currentSeason, $currentSeason, $_COOKIE['user']);
	$stmt->execute();
	$stmt->bind_result($user_id, $user_name, $user_joined, $user_verified, $team_name, $team_id, $pColor, $sColor, $team_elo, $division_id, $division_name,$credits,$win,$loss,$trade_offers);
	$stmt->fetch();
	$stmt->close();

	$check = $user_id.$user_joined.$user_name;
	$check = md5($check);


		if ($check == $ck_key){
			$_SESSION['user'] = [
				'id' => $user_id,
				'name' => $user_name,
				'joined' => $user_joined,
				'verified' => $user_verified,
				'login' => 1,
				'team_name' => $team_name,
				'team_id' => $team_id,
				'color1' => $pColor,
				'color2' => $sColor,
				'elo' => $team_elo,
				'division_id' => $division_id,
				'division_name' => $division_name,
				'win' => $win,
				'loss' => $loss,
				'credits' => $credits,
				'trade_offers' => $trade_offers,
				'game_day'	=> $gameDayText,
				'current_season'	=> $currentSeason
			];
		}
		else{}
	}
	else{}

?>
