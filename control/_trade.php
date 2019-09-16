<?php

// players on a team that are on the trade block
$stmt = $db->prepare(
  "SELECT position.position_id, position_name, p.player_id, first_name.name_text,
    last_name.last_name_text, player_age, team.team_name, team.team_id,
    ((`player_speed`+`player_end`+`player_str`+`player_pass`+`player_block`+`player_shot`+`player_catch`+`player_aware`+`player_charisma`)/9) as average_score
  FROM  player p
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
  LEFT JOIN team
  ON player_team.team_id = team.team_id
  WHERE being_traded
  ORDER BY position.position_id"
);
$stmt->execute();
$stmt->store_result();
$player_num = $stmt->num_rows;
$stmt->bind_result($position_id, $position, $player_id, $player_fname, $player_lname, $player_age, $team_name, $team_id, $average);
// store collected
$player_result = array();
$team_score = 0;
while($stmt->fetch()){
	$tmp = array();
	$tmp['position']=ucfirst($position);
	$tmp['name']=$player_fname." ".$player_lname;
  $tmp['id'] = $player_id;
	$tmp['age']=$player_age;
  $tmp['team']=$team_name;
  $tmp['team_id']=$team_id;
  $tmp['average']=substr($average,0,4);
	array_push($player_result,$tmp);
}
$stmt->close();
?>
