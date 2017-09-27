<?php
//check if GET['team'] is set. If not, default to current player's team


// information for current team
$stmt = $db->prepare("SELECT s.schedule_id,
	(SELECT t1.team_name FROM team t1
    	JOIN division_slot ds1
    	ON ds1.team_id = t1.team_id
    	WHERE ds1.slot_id = s.slot_id_1) AS name1,

    (SELECT t2.team_name FROM team t2
    	JOIN division_slot ds2
    	ON ds2.team_id = t2.team_id
    	WHERE ds2.slot_id = s.slot_id_2) AS name2,

    (SELECT ds3.team_id FROM division_slot ds3
    	WHERE ds3.slot_id = s.slot_id_1) AS id1,

    (SELECT ds4.team_id FROM division_slot ds4
    	WHERE ds4.slot_id = s.slot_id_2) AS id2, win_team_score, lose_team_score, win_team_id, game_day
    FROM `schedule` s
		LEFT JOIN game
		ON s.schedule_id = game.schedule_id
    JOIN division_slot ds
    ON slot_id_1 = ds.slot_id OR slot_id_2 = ds.slot_id
    JOIN division
    ON division.division_id = ds.division_id
    JOIN team
    ON ds.team_id = team.team_id
    WHERE team.team_id = ?
		ORDER BY s.schedule_id");
$stmt->bind_param("s", $_SESSION['user']['team_id']);
$stmt->execute();
$stmt->store_result();
$sched_count = $stmt->num_rows();
$stmt->bind_result($schedule_id, $team_name1, $team_name2, $team_id1, $team_id2, $win_score, $lose_score, $win_team, $game_day);

$schedule_result = array();
while($stmt->fetch()){

	$tmp = array();
	$tmp['schedule_id']=$schedule_id;
	$tmp['name1']=$team_name1;
	$tmp['name2']=$team_name2;
  $tmp['id1']=$team_id1;
  $tmp['id2']=$team_id2;
	$tmp['win_score']=$win_score;
	$tmp['lose_score']=$lose_score;
	$tmp['win_team'] = $win_team;
	$tmp['game_day'] = $game_day;
	array_push($schedule_result,$tmp);
}

$stmt->close();
?>
