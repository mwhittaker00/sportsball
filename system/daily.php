<?php
require_once('../includes/_init.inc.php');
require_once('../functions/init.func.php');

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

//
// If the game_day hit the max, we need to start next season
//

// if we've passed the max game day, reset the game day
// and make a new schedule
if ( $gameDay == $maxGameDay ){
  $stmt = $db->prepare(
    "UPDATE system
      SET game_day = 0,
      current_season = current_season + 1"
  );
  $stmt->execute();
  $stmt->close();
  require_once("./make-schedule.php");
}
else{
  // Play today's game
  require_once("./play-game.php");
  //
  // END GAME PLAY FOR THE DAY
  //

  //
  // HANDLE FREE AGENTS
  //
  require_once("./free-agents.php");
  //
  // END FREE AGENT HANDLING
  //
  //
  // Now increment the game_day by 1
  //
  $stmt = $db->prepare("UPDATE system SET game_day = game_day + 1");
  $stmt->execute();
  $stmt->close();
}

?>
