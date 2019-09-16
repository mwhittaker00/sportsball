<?php
// Check if the player is in GET and the user is logged in
$player_id = '';
if(!isset($_GET['player'])
  && !isset($_SESSION['user'])) {
  header('location:/');
}
else{
  $player_id = $_GET['player'];
  $user_team_id = $_SESSION['user']['team_id'];
}

// player's information, make sure
// player is being traded
$stmt = $db->prepare(
  "SELECT position.position_id, position_name, p.player_id,
    first_name.name_text, last_name.last_name_text, team_name, team.team_id, team_color1, team_color2, being_traded, player_age,
    `player_speed`,`player_end`,`player_str`,`player_pass`,`player_block`,
    `player_shot`,`player_catch`,`player_aware`,`player_charisma`,
    ((`player_speed`+`player_end`+`player_str`+`player_pass`+`player_block`+
      `player_shot`+`player_catch`+`player_aware`+`player_charisma`)/9)
        as average_score,
    daily_cost, seasons_left, player_team.is_active
  FROM player p
  LEFT JOIN contract
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
  AND player_team.being_traded = 1
  LIMIT 1");
$stmt->bind_param('i', $player_id);
$stmt->execute();
$stmt->bind_result($position_id, $position, $player_id, $player_fname, $player_lname, $team_name, $team_id, $pColor, $sColor, $being_traded, $player_age, $speed, $endurance, $strength, $pass, $block, $shot, $catch, $aware, $charisma, $avg, $contract_cost, $contract_length, $is_active);
$stmt->fetch();
$stmt->close();

// only run the rest if the player is on the trade block
if ($being_traded) {

  $player = [
    'position_id' =>  $position_id,
    'position'    =>  ucfirst($position),
    'id'          =>  $player_id,
    'name'        =>  $player_fname." ".$player_lname,
    'team_name'   =>  $team_name,
    'team_id'     =>  $team_id,
    'color1'      =>  $pColor,
    'color2'      =>  $sColor,
    'being_traded' => $being_traded,
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
    'contract_cost'=> $contract_cost,
    'is_active'   =>  $is_active
  ];

  // Does this team already have an offer on this player?
  $stmt = $db->prepare(
    "SELECT trade_id
      FROM player_team_trade
      WHERE team_id_1 = ?
      AND team_id_2 = ?
      AND trade_player_id = ?
      AND trade_open = 1");
  $stmt->bind_param('iii', $user_team_id, $player['team_id'], $player['id']);
  $stmt->execute();
  $stmt->store_result();
  $trade_num = $stmt->num_rows;
  $stmt->close();
  // if the user is viewing a player they control, set $trade_num to 1 so they can see any offers made on their player
  if ($user_team_id == $player['team_id']) {
    $trade_num = 1;
  }
  // BENCHED players who are NOT BEING TRADED and are NOT IN A TRADE OFFER for current user's team
  $stmt = $db->prepare(
    "SELECT trade_id, position.position_id, position_name, p.player_id, first_name.name_text,
      last_name.last_name_text, player_age, team_name, team_id_1, team_id_2, credits,  ((`player_speed`+`player_end`+`player_str`+`player_pass`+`player_block`+`player_shot`+`player_catch`+`player_aware`+`player_charisma`)/9) as average_score
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
    LEFT JOIN player_team_trade ptt
    ON (player_team.team_id = ptt.team_id_1
    	OR player_team.team_id = ptt.team_id_2)
      AND ptt.trade_player_id = ?
      AND ptt.trade_open = 1
    	AND (p.player_id = ptt.player_id_1
             OR p.player_id = ptt.player_id_2
             OR p.player_id = ptt.player_id_3
             OR p.player_id = ptt.player_id_4
             OR p.player_id = ptt.player_id_5)
    JOIN team
    ON player_team.team_id = team.team_id
    WHERE (player_team.team_id = ?
      OR player_team.team_id = ptt.team_id_1)
    AND player_team.is_active = 0
    AND player_team.being_traded = 0
    AND player_team.being_offered = ?
    ORDER BY position.position_id, average_score DESC"
  );
  // we use $trade_num from above for being_offered. If we aren't offering anything for this trade it would be 0, or false, otherwise we are selecting players we are offering and we can show that.
  $stmt->bind_param('iii', $player_id, $user_team_id, $trade_num);
  $stmt->execute();
  $stmt->store_result();
  $player_num = $stmt->num_rows;
  $stmt->bind_result($trade_id, $position_id, $position, $player_id, $player_fname, $player_lname, $player_age, $team_name, $team_id_1, $team_id_2, $credits, $average);
  // store collected
  $team_result = array();
  while($stmt->fetch()){
  	$tmp = array();
    $tmp['trade_id']=$trade_id;
  	$tmp['position_id']=$position_id;
  	$tmp['position']=ucfirst($position);
  	$tmp['name']=$player_fname." ".$player_lname;
    $tmp['id'] = $player_id;
  	$tmp['age']=$player_age;
    $tmp['average']=substr($average,0,4);
    $tmp['team_name']=$team_name;
    $tmp['team1']=$team_id_1; // the team that submits the offers
    $tmp['team2']=$team_id_2; // the team controlling the trade block player
    $tmp['credits']=$credits;
  	array_push($team_result,$tmp);
  }

} else {
  $msg = "Not able to find a valid trade.";
  $_SESSION['status'] = $msg;
  header("location:/");
}
?>
