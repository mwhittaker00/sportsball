<?
// get the team_id for the people playing this game day.
$stmt = $db->prepare("SELECT schedule_id,
	(SELECT ds1.team_id FROM division_slot ds1
     	WHERE ds1.slot_id = s.slot_id_1
    	AND ds1.is_current_season = 1) AS team_1,
    (SELECT ds2.team_id FROM division_slot ds2
     	WHERE ds2.slot_id = s.slot_id_2
    	AND ds2.is_current_season = 1) AS team_2
	FROM schedule s
    WHERE game_day = ?");
	$stmt->bind_param('i',$gameDay);
	$stmt->execute();
	$stmt->bind_result($schedule_id,$team1,$team2);
	$stmt->store_result();
	$games_count = $stmt->num_rows();

	$teams = [];
	while($stmt->fetch()){
		array_push($teams,[$team1,$team2,$schedule_id]);
	}
	$stmt->close();

	for ($i=0;$i < $games_count; $i++){
		if ($teams[$i][0] && $teams[$i][1]){
			playGame($teams[$i][0],$teams[$i][1],$teams[$i][2],$db);
		}
	}

function playGame($team1,$team2,$schedule_id,$db){
	$stmt = $db->prepare(
		  "SELECT position.position_id, position_name, p.player_id,player_team.team_id,team_captain,`player_speed`,`player_end`,`player_str`,`player_pass`,`player_block`,`player_shot`,`player_catch`,`player_aware`,`player_charisma`
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
	  WHERE (player_team.team_id = ?
	  OR player_team.team_id = ?)
		AND player_team.is_active = 1");
	$stmt->bind_param('ii',$team1,$team2);
	$stmt->execute();
	$stmt->store_result();
	$player_num = $stmt->num_rows;
	$stmt->bind_result($position_id, $position, $player_id, $team_id, $team_captain, $speed, $endurance, $strength, $pass, $block, $shot, $catch, $aware, $charisma);

	$teams = [
		$team1 => ['totalShot'		=>	0,
		'totalEnd'			=>  0,
		'totalAw'			=> 	0,
		'totalStr'			=>  0,
		'totalBlock'		=> 	0,
		'totalSpeed'=>	0,
		'totalPass'		=>  0,
		'totalCatch'			=>  0,
		'chanceToTakeShot'=>	0,
		'score'	=>	0,
		'shotChance'	=>	0
	],

		$team2 => ['totalShot'		=>	0,
		'totalEnd'			=>  0,
		'totalAw'			=> 	0,
		'totalStr'			=>  0,
		'totalBlock'		=> 	0,
		'totalSpeed'=>	0,
		'totalPass'		=>  0,
		'totalCatch'			=>  0,
		'chanceToTakeShot'=>	0,
		'score'	=>	0,
		'shotChance'	=>	0
	]
	];

	$team_score = 0;
  $allIDs = [];
	while($stmt->fetch()){

		$temp = array();
		$temp = [$speed,$endurance,$strength,$pass,$block,$shot,$catch,$aware,$charisma];
		// find the player's highest skill
		$high = highSkill($temp);
		// 1/3 of the highest skill, times half of the potential
		// add 100 for the purpose of the really bad players
		$baseCost = floor(($high/3) * ((88+1)*.5) + 100);
		// update their base_cost in the contract table
		$qry = $db->prepare(
			"UPDATE contract SET base_cost = ? WHERE player_id = ? LIMIT 1"
		);
		$qry->bind_param('ss',$baseCost,$player_id);
		$qry->execute();
		$qry->close();

    //Improve player's skills
    $qry = $db->prepare(
      "UPDATE player SET
      	player_speed = player_speed + ((player_speed/215)+(player_potential/215)),
        player_end = player_end + ((player_end/215)+(player_potential/215)),
        player_str = player_str + ((player_str/215)+(player_potential/215)),
        player_pass = player_pass + ((player_pass/215)+(player_potential/215)),
        player_block = player_block + ((player_block/215)+(player_potential/215)),
        player_shot = player_shot + ((player_shot/215)+(player_potential/215)),
        player_catch = player_catch + ((player_catch/215)+(player_potential/215)),
        player_aware = player_aware + ((player_aware/215)+(player_potential/215))
      WHERE player_id = ?"
  );
    $qry->bind_param('i',$player_id);
    $qry->execute();
    $qry->close();

		//these are the skills we want to count from EVERYBODY
		$teams[$team_id]['totalAw']+=$aware;
		$teams[$team_id]['totalEnd']+=$endurance;
		$teams[$team_id]['totalPass']+=$pass;
		$teams[$team_id]['totalBlock']+=$block;
		$teams[$team_id]['totalSpeed']+=$speed;
		$teams[$team_id]['totalStr']+=$strength;
		// We only care about the keeper's catch
		if ( $position == 'keeper' ){
			$teams[$team_id]['totalCatch']+=$catch;
		}
		// We don't care about the keeper's shot
		else{
			$teams[$team_id]['totalShot']+=$shot;
		}
	}
	$stmt->close();

	// set the probability for each shot on goal. (AVG shot/totalCatch)/10
	$teams[$team1]['shotChance'] = (($teams[$team1]['totalShot']/5)/$teams[$team2]['totalCatch'])/5;
	$teams[$team2]['shotChance'] = (($teams[$team2]['totalShot']/5)/$teams[$team1]['totalCatch'])/5;

	//Endurance protects against strength. Divide Srength by Endurance to get success of Defenders against approaching Forwads. Divide Pass by Block for success of passing game. Divide team1 awareness against team2 awareness
	$teams[$team1]['chanceToTakeShot'] += (($teams[$team1]['totalStr']/$teams[$team2]['totalEnd']) + ($teams[$team1]['totalPass']/$teams[$team2]['totalBlock']) + ($teams[$team1]['totalAw']/$teams[$team2]['totalAw']) + ($teams[$team1]['totalSpeed']/$teams[$team2]['totalSpeed']))*1.5;

	$teams[$team2]['chanceToTakeShot'] += (($teams[$team2]['totalStr']/5/$teams[$team1]['totalEnd']) + ($teams[$team2]['totalPass']/$teams[$team1]['totalBlock']) + ($teams[$team2]['totalAw']/$teams[$team1]['totalAw']) + ($teams[$team2]['totalSpeed']/$teams[$team1]['totalSpeed']))*1.5;

	// give an extra shot to whoever has the highest chanceToTakeShot
	if ($teams[$team1]['chanceToTakeShot'] > $teams[$team2]['chanceToTakeShot']){
		$teams[$team1]['chanceToTakeShot'] = round($teams[$team1]['chanceToTakeShot']) + 1;

		$teams[$team2]['chanceToTakeShot'] = round($teams[$team2]['chanceToTakeShot']);
	} else{
		$teams[$team2]['chanceToTakeShot'] = round($teams[$team2]['chanceToTakeShot']) + 1;

		$teams[$team1]['chanceToTakeShot'] = round($teams[$team1]['chanceToTakeShot']);
	}

	// set the scores
	while ($teams[$team1]['score'] == $teams[$team2]['score']){
		$teams[$team1]['score'] = takeShot($teams[$team1]['chanceToTakeShot'],$teams[$team2]['shotChance']);
		$teams[$team2]['score'] = takeShot($teams[$team2]['chanceToTakeShot'],$teams[$team2]['shotChance']);
	}

	// find out who won
	if ( $teams[$team1]['score'] > $teams[$team2]['score']){
		$winner = $team1;
		$winScore = $teams[$team1]['score'];
		$loser = $team2;
		$loseScore = $teams[$team2]['score'];
	}
	else if ( $teams[$team2]['score'] > $teams[$team1]['score']){
		$winner = $team2;
		$winScore = $teams[$team2]['score'];
		$loser = $team1;
		$loseScore = $teams[$team1]['score'];
	}
	$finalResults = ["team1"=>$team1,"team2"=>$team2,"score1"=>$teams[$team1]['score'],"score2"=>$teams[$team2]['score'],"winner"=>$winner,"loser"=>$loser,"schedule_id"=>$schedule_id];
	$stmt = $db->prepare(
		"INSERT INTO game
		(schedule_id, win_team_id, lose_team_id, win_team_score, lose_team_score)
		VALUES
		(?,?,?,?,?)"
	);
	$stmt->bind_param('iiiii',$schedule_id,$winner,$loser,$winScore,$loseScore);
	$stmt->execute();
	$stmt->close();
}

function takeShot($shots,$chance){
	$chance = $chance * 100;
	$score = 0;
	if ($chance > 50 ){
		$chance = 50;
	}
	for ($i=0; $i < $shots; $i++ ){
		if (rand(0,100) <= $chance){
			$score += 1;
		}
	}
	return $score;
}
?>
