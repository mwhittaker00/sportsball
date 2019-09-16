<?php
//check if GET['team'] is set. If not, default to current player's team
$team_id = '';
if(!isset($_GET['team'])){
  $team_id = $_SESSION['user']['team_id'];
}
else{
  $team_id = $_GET['team'];
}
// information for current team
$stmt = $db->prepare(
  "SELECT user.user_id, user_name, team_name, team.team_id, team_color1,
  team_color2, team_created, division.division_id, division_name,
  (SELECT COUNT(win_team_id) FROM game
    JOIN schedule
    ON schedule.schedule_id = game.schedule_id
    WHERE schedule.season_number = ?
    AND win_team_id = team.team_id) as win,
  (SELECT COUNT(lose_team_id) FROM game
    JOIN schedule
    ON schedule.schedule_id = game.schedule_id
    WHERE schedule.season_number = ?
    AND lose_team_id = team.team_id) as loss
	FROM team
    JOIN user_team
    ON team.team_id = user_team.team_id
    JOIN user
    ON user.user_id = user_team.user_id
    JOIN team_division
		ON team.team_id = team_division.team_id
		JOIN division
		ON team_division.division_id = division.division_id
    LEFT JOIN player_team
    ON player_team.team_id = team.team_id
	WHERE team.team_id = ?");
$stmt->bind_param("iis", $currentSeason, $currentSeason, $team_id);
$stmt->execute();
$stmt->bind_result($user_id, $user_name, $team_name, $team_id, $pColor, $sColor, $team_created, $division_id, $division_name,$win,$loss);
$stmt->fetch();
$stmt->close();

$team = [
  'name' => $team_name,
  'id' => $team_id,
  'owner_id' => $user_id,
  'owner_name'=>$user_name,
  'color1' => $pColor,
  'color2' => $sColor,
  'created' => $team_created,
  'average' => 0,
  'division_id' => $division_id,
  'division_name' => $division_name,
  'win' => $win,
  'loss' => $loss
];

// players for current team
$stmt = $db->prepare(
  "SELECT position.position_id, position_name, p.player_id, first_name.name_text,
    last_name.last_name_text, player_age, is_active, team_captain, being_traded, being_offered,
    ((`player_speed`+`player_end`+`player_str`+`player_pass`+`player_block`+`player_shot`+`player_catch`+`player_aware`+`player_charisma`)/9) as average_score
  FROM player p
  JOIN player_position
  ON p.player_id = player_position.player_id
  JOIN position
  ON position.position_id = player_position.position_id
  JOIN first_name
  ON first_name.name_id = p.first_name
  JOIN last_name
  ON last_name.last_name_id = p.last_name
  JOIN player_team
  ON player_team.player_id = p.player_id
  WHERE player_team.team_id = ?
  ORDER BY position.position_id, being_traded ASC, being_offered ASC, average_score DESC"
);
$stmt->bind_param('s',$team['id']);
$stmt->execute();
$stmt->store_result();
$player_num = $stmt->num_rows;
$stmt->bind_result($position_id, $position, $player_id, $player_fname, $player_lname, $player_age, $is_active, $team_captain, $being_traded, $being_offered, $average);
// store collected
$player_result = array();
$team_score = 0;
while($stmt->fetch()){
  $extra_info_str = '';
  if ($team_captain){
    $extra_info_str = $extra_info_str.' <span title="Captain" class="glyphicon glyphicon-star"></span>';
  } if ($is_active) {
    $extra_info_str = $extra_info_str.' <span title="Starter" class="glyphicon glyphicon-ok-circle"></span>';
  } if (!$is_active) {
    $extra_info_str = $extra_info_str.' <span title="Bench" class="glyphicon glyphicon-remove-circle"></span>';
  } if ($being_traded || $being_offered){
    $extra_info_str = $extra_info_str.' <span title="Trade Block" class="glyphicon glyphicon-transfer"></span>';
  }

	$tmp = array();
	$tmp['position_id']=($position_id);
	$tmp['position']=ucfirst($position);
	$tmp['name']=$player_fname." ".$player_lname;
  $tmp['id'] = $player_id;
	$tmp['age']=$player_age;
  $tmp['captain'] = $team_captain;
  $tmp['being_traded'] = $being_traded;
  $tmp['being_offered'] = $being_offered;
  $tmp['extra_info'] = $extra_info_str;
  $tmp['average']=substr($average,0,4);
  $team_score = $team_score + $average;
	array_push($player_result,$tmp);
}

$team['average'] = "n/a";
if ($player_num){
  $team['average'] = substr(($team_score/$player_num),0,2);
}
$stmt->close();
?>
