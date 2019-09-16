<?php

require_once('./functions/parsedown.php');
//check if GET['division'] is set. If not, default to current player's division
$division_id = '';
if(!isset($_GET['division'])){
  $division_id = $_SESSION['user']['division_id'];
}
else{
  $division_id = $_GET['division'];
}
// information for current division
$stmt = $db->prepare(
  "SELECT division_name
    FROM division
    WHERE division_id = ?");
$stmt->bind_param("s", $division_id);
$stmt->execute();
$stmt->bind_result($division_name);
$stmt->fetch();
$stmt->close();

$division = [
  'name' => $division_name,
  'id' => $division_id
];

//teams and records for current division
$stmt = $db->prepare(
  "SELECT team_name, team.team_id, team_color1,
  team_color2, division_name,
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
    ((SELECT COUNT(win_team_id) FROM game
      JOIN schedule
      ON schedule.schedule_id = game.schedule_id
      WHERE schedule.season_number = ?
      AND win_team_id = team.team_id)
    -
    (SELECT COUNT(lose_team_id) FROM game
      JOIN schedule
      ON schedule.schedule_id = game.schedule_id
      WHERE schedule.season_number = ?
      AND lose_team_id = team.team_id)
    ) as difference
	FROM team
    JOIN user_team
    ON team.team_id = user_team.team_id
    JOIN user
    ON user.user_id = user_team.user_id
    JOIN team_division
		ON team.team_id = team_division.team_id
		JOIN division
		ON team_division.division_id = division.division_id
	WHERE division.division_id = ?
  ORDER BY difference DESC"
);
$stmt->bind_param('iiiis',$currentSeason,$currentSeason,$currentSeason,$currentSeason,$division['id']);
$stmt->execute();
$stmt->store_result();
$team_num = $stmt->num_rows;
$stmt->bind_result($team_name, $team_id, $pColor, $sColor, $division_name,$win,$loss,$difference);
// store collected
$team_result = array();
while($stmt->fetch()){
	$tmp = array();
  $tmp['name'] = $team_name;
  $tmp['id'] = $team_id;
  $tmp['color1'] = $pColor;
  $tmp['color2'] = $sColor;
  $tmp['average'] = 0;
  $tmp['division_name'] = $division_name;
  $tmp['win'] = $win;
  $tmp['loss'] = $loss;
  $tmp['difference'] = $difference;

	array_push($team_result,$tmp);
}
$stmt->close();

// message posts made in the current division
$stmt = $db->prepare(
  "SELECT post.post_id, post.post_content, UNIX_TIMESTAMP(post.post_time), team.team_id, team.team_name,team_color1,team_color2
	FROM post
    JOIN team_post
    ON post.post_id = team_post.post_id
    LEFT JOIN team
    ON team_post.team_id = team.team_id
    JOIN division_post
    ON post.post_id = division_post.post_id
    WHERE division_post.division_id = ?
    ORDER BY post.post_time DESC LIMIT 10"
  );
  $stmt->bind_param('s',$division['id']);
  $stmt->execute();
  $stmt->store_result();
  $post_num = $stmt->num_rows;
  $stmt->bind_result($post_id, $post_content, $post_time, $team_id, $team_name, $pColor, $sColor);
  // store collected
  $post_result = array();
  while($stmt->fetch()){
    // if the player is null, it's a trade alert message
    if (!$team_id) {
      $team_id = 0;
      $team_name = "Roster Move";
      $pColor = "#000000";
      $sColor = "#000000";
    }
    // prepare post content for markdown
    $Parsedown = new Parsedown();
    $post_content = $Parsedown->text($post_content);
    $tmp = array();
    $newDateTime = date('M j, Y G:i',$post_time);
    $tmp['name'] = $team_name;
    $tmp['team_id'] = $team_id;
    $tmp['content'] = $post_content;
    $tmp['color1'] = $pColor;
    $tmp['color2'] = $sColor;
    $tmp['time'] = $newDateTime;
    $tmp['id'] = $post_id;
  	array_push($post_result,$tmp);
  }
  $stmt->close();
?>
