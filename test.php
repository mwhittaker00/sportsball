<?php
require_once('/includes/_init.inc.php');
require_once('/functions/init.func.php');

$stmt = $db->prepare(
  "SELECT bid_id, player_id, bid_amount, bid_show, bid.team_id, credits
    FROM bid
    JOIN team_money
    ON bid.team_id = team_money.team_id
    WHERE bid.team_id <> 0
    AND days_left = 0"
  );
$stmt->execute();
$stmt->store_result();
$bid_num = $stmt->num_rows;
$stmt->bind_result($bid_id, $player_id, $bid_amount, $bid_show, $team_id, $credits);
// store collected
$bid_result = array();
while($stmt->fetch()){
  $tmp = array();
  $tmp['player']=$player_id;
  $tmp['bid_show']=$bid_show;
  $tmp['bid_amount']=$bid_amount;
  $tmp['team']=$team_id;
  $tmp['credits']=$credits;
  array_push($bid_result,$tmp);

}
$stmt->close();

for ( $i=0; $i < count($bid_num); $i++){
    $row = $bid_result[$i];
    // Apply credit charges to bid winning teams
    // Find the difference : Total bid - show bid = refund added to credits
        $refund = $row['credits'] + ($row['bid_amount'] - $row['bid_show']);
        $sql = "UPDATE team_money
          SET credits = ?
          WHERE team_id = ?";
        $qry = $db->prepare($sql);
        $qry->bind_param("ii",$refund,$row['team']);
        $qry->execute();
        $qry->close();
    //
    // Add the player to their new team
        $qry = $db->prepare(
          "UPDATE player_team
            SET team_id = ?
            WHERE player_id = ?"
          );
        $qry->bind_param("ii",$row['team'],$row['player']);
        $qry->execute();
        $qry->close();
}
?>
