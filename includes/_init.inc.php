<?
session_start();
$db_server = 'localhost';
	$db_name = 'manager';
	$db_pass = '';
	$db_user = 'root';

$db = new mysqli($db_server,$db_user,$db_pass,$db_name);
if($db->connect_errno > 0){
 die('Unable to connect to database [' . $db->connect_error . ']');
}

if (isset($_COOKIE['userKey']) && isset($_COOKIE['user'])){

	$ck_user = $_COOKIE['user'];
	$ck_key = $_COOKIE['userKey'];
	$stmt = $db->prepare("SELECT user.user_id, user_name, user_joined, user_verified, team_name, team.team_id, team_color1, team_color2, division.division_id, division_name, credits,
		(SELECT COUNT(win_team_id) FROM game
			JOIN division_slot
			ON division_slot.team_id = win_team_id
			WHERE is_current_season = 1
			AND win_team_id = team.team_id) as win,
		(SELECT COUNT(lose_team_id) FROM game
			JOIN division_slot
			ON division_slot.team_id = lose_team_id
			WHERE is_current_season = 1
			AND lose_team_id = team.team_id) as loss
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
	$stmt->bind_param("s", $_COOKIE['user']);
	$stmt->execute();
	$stmt->bind_result($user_id, $user_name, $user_joined, $user_verified, $team_name, $team_id, $pColor, $sColor, $division_id, $division_name,$credits,$win,$loss);
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
				'division_id' => $division_id,
				'division_name' => $division_name,
				'win' => $win,
				'loss' => $loss,
				'credits' => $credits
			];
		}
		else{}
	}
	else{}

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
?>
