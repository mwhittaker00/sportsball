<?php
//check if GET['team'] is set. If not, default to current player's team
$player_id = '';
if(!isset($_GET['player'])){
  header('location:/');
}
else{
  $player_id = $_GET['player'];
}

//players for current team
$stmt = $db->prepare(
  "SELECT position.position_id, position_name, p.player_id,
    first_name.name_text, last_name.last_name_text, team_name,
    team.team_id, team_color1, team_color2, team_captain, player_age,
    `player_speed`,`player_end`,`player_str`,`player_pass`,`player_block`,
    `player_shot`,`player_catch`,`player_aware`,`player_charisma`,
    ((`player_speed`+`player_end`+`player_str`+`player_pass`+`player_block`+
      `player_shot`+`player_catch`+`player_aware`+`player_charisma`)/9)
        as average_score,
    daily_cost, seasons_left
  FROM player p
  JOIN contract
  ON p.player_id = contract.player_id
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
  WHERE p.player_id = ?
  LIMIT 1");
$stmt->bind_param('s',$player_id);
$stmt->execute();
$stmt->bind_result($position_id, $position, $player_id, $player_fname, $player_lname, $team_name, $team_id, $pColor, $sColor, $team_captain, $player_age, $speed, $endurance, $strength, $pass, $block, $shot, $catch, $aware, $charisma, $avg, $contract_cost, $contract_length);
$stmt->fetch();
$stmt->close();

if ($team_captain == 0){
  $team_captain = 'No';
  $team_cpt_bool = false;
}
else{
  $team_captain = 'Yes';
  $team_cpt_bool = true;
}

$player = [
  'position_id' =>  $position_id,
  'position'    =>  ucfirst($position),
  'id'          =>  $player_id,
  'name'        =>  $player_fname." ".$player_lname,
  'team_name'   =>  $team_name,
  'team_id'     =>  $team_id,
  'color1'      =>  $pColor,
  'color2'      =>  $sColor,
  'captain'     =>  $team_captain,
  'cpt_bool'    =>  $team_cpt_bool,
  'age'         =>  $player_age,
  'speed'       =>  substr($speed,0,2),
  'endurance'   =>  substr($endurance,0,2),
  'strength'    =>  substr($strength,0,2),
  'pass'        =>  substr($pass,0,2),
  'block'       =>  substr($block,0,2),
  'shot'        =>  substr($shot,0,2),
  'catch'       =>  substr($catch,0,2),
  'aware'       =>  substr($aware,0,2),
  'charisma'    =>  substr($charisma,0,2),
  'average'     =>  substr($avg,0,4),
  'contract_time'=> $contract_length,
  'contract_cost'=> $contract_cost
];

?>
