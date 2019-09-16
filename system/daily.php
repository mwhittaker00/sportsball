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
// If the game_day hit the max, we enter the "management" day
//
if ( $gameDay == $maxGameDay ){
  require_once("./management-day.php");
  // Now increment the game_day by 1
  //
  $stmt = $db->prepare("UPDATE system SET game_day = game_day + 1");
  $stmt->execute();
  $stmt->close();

  // decrease all contract lengths that aren't already 0 by 1
  $stmt = $db->prepare("UPDATE contract SET seasons_left = seasons_left - 1");
  $stmt->execute();
  $stmt->close();
}
// if we've passed the max game day, reset the game day
// and make a new schedule
else if ( $gameDay > $maxGameDay ){
  $stmt = $db->prepare(
    "UPDATE system
      SET game_day = 0,
      current_season = current_season + 1"
  );
  $stmt->execute();
  $stmt->close();

  // Any contracts at 0 seasons_left, and where the player is on a non-0 team, need to be updated to make that player a free agent. Set their player_team to 0 and add them to the bid table for 5 days
  $stmt = $db->prepare(
    "UPDATE player_team
      SET team_id = 0
      WHERE team_id != 0
      AND player_id IN (SELECT contract.player_id FROM contract WHERE seasons_left <= 0)"
    );
  $stmt->execute();
  $stmt->close();
  // add these players into the bid table
  $stmt = $db->prepare(
    "INSERT INTO bid (player_id, bid_amount, bid_show)
      SELECT contract.player_id, contract.base_cost, contract.base_cost
        FROM contract
        WHERE contract.player_id NOT IN (SELECT b.player_id FROM bid b WHERE days_left > 0)"
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
  // Now increment the game_day by 1
  //
  $stmt = $db->prepare("UPDATE system SET game_day = game_day + 1");
  $stmt->execute();
  $stmt->close();
}

// update trade tables to remove any offers older than 3 days, and change the being_offered status for those players
// reset "being_offered" first
$stmt = $db->prepare(
  "UPDATE player_team a
	 SET being_offered = 0
    WHERE a.player_id IN (
      SELECT p.player_id
      FROM player p
      JOIN player_team_trade ptt
      ON p.player_id = ptt.player_id_1
  	   OR p.player_id = ptt.player_id_2
       OR p.player_id = ptt.player_id_3
       OR p.player_id = ptt.player_id_4
       OR p.player_id = ptt.player_id_5
      WHERE trade_start_date <= DATE_SUB(SYSDATE(), INTERVAL 3 DAY) AND trade_open = 1
  )"
);
$stmt->execute();
$stmt->close();
// update player_team_trade to set these trades as inactive, status "declined"
$stmt = $db->prepare(
  "UPDATE player_team_trade
    SET trade_status = 0,
      trade_open = 0
    WHERE trade_start_date <= DATE_SUB(SYSDATE(), INTERVAL 3 DAY) AND trade_open = 1"
);
$stmt->execute();
$stmt->close();
?>
