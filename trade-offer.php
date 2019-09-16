<?php
  require_once('./includes/_init.inc.php');
  if(!isset($_GET['player'])
    && !isset($_SESSION['user'])) {
    //header('location:/');
  }

  /* gets the player data for the page being viewed
  $position_id, $position, $player_id, $player_fname, $player_lname, $team_name, $team_id, $player_age, $speed, $endurance, $strength, $pass, $block, $shot, $catch, $aware, $charisma, $avg, $being_traded, $team_captain, $team_captain_str

  Also get the backups for the current player so they can offer them in the trade.
  */
  require_once('./control/_trade-offer.php');
  require_once('./includes/head.inc');
?>

<title>SportsBall Manager | Trade Offer</title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row'>
<?php require_once('./includes/left-column.inc');?>

<div class='col-sm-9'>
  <div class='row'>
    <div class='col-sm-6'>
      <h1>
        <canvas class='team-colors-box team-colors'></canvas> <?=$player['name'];?>
        <a href='/team.php?team=<?=$player['team_id'];?>' class='small'>
          <?=$player['team_name'];?>
        </a>

      </h1>
    </div>
    <div class='col-xs-1'></div>
    <div class='col-xs-2'>
      <strong>Age</strong><br />
      <?=$player['age'];?>
    </div>

    <div class='col-xs-2'>
      <strong>Position</strong><br />
      <?=$player['position'];?>
    </div>
    <div class='col-xs-1'></div>
  </div>

    <hr />
      <h3>Skills</h3>
      <div class='row player-row'>
        <div class='col-xs-2'>
          <strong>Speed</strong><br />
          <?=$player['speed'];?>
        </div>
        <div class='col-xs-2'>
          <strong>Endurance</strong><br />
          <?=$player['endurance'];?>
        </div>
        <div class='col-xs-2'>
          <strong>Strength</strong><br />
          <?=$player['strength'];?>
        </div>
        <div class='col-xs-2'>
          <strong>Awareness</strong><br />
          <?=$player['aware'];?>
        </div>
        <div class='col-xs-2'>
          <strong>Charisma</strong><br />
          <?=$player['charisma'];?>
        </div>
      </div>
      <br />
      <div class='row'>
        <div class='col-xs-2'>
          <strong>Pass</strong><br />
          <?=$player['pass'];?>
        </div>
        <div class='col-xs-2'>
          <strong>Block</strong><br />
          <?=$player['block'];?>
        </div>
        <div class='col-xs-2'>
          <strong>Shot</strong><br />
          <?=$player['shot'];?>
        </div>
        <div class='col-xs-2'>
          <strong>Catch</strong><br />
          <?=$player['catch'];?>
        </div>
        <div class='col-xs-3'>
          <strong>AVERAGE</strong><br />
          <?=$player['average'];?>
        </div>
    </div>
    <br />
<?php
// if a trade is offered, don't show options to offer a trade
if (!$trade_num && $player['team_id'] != $_SESSION['user']['team_id']) {
?>
      <h3>Your Tradable Assets</h3>
    <div class='player-row tradable-assets'>

<?php
  if (!$player_num) {
?>
        <p>Valid offers require at least one player. Money is not required. Money cannot be the only item offered in a trade.</p>

        <p>No players available to trade.</p>
<?php } else { ?>
        <form class='form-inline add-credits' action='#'>
          <input class='form-control' name='credits' type='number' min=1 max=<?=$_SESSION['user']['credits'];?> required />
          <input type='submit' value='Add Credits' class='btn btn-default' />
        </form>
        <br />
<?php
        $i = 0;
        $firstInRow = true;
        while($i < $player_num){
          $player_row = $team_result[$i];
          // set the glyphicon for each position
          if ( $firstInRow ){
            echo "<div class='row assets position-".strtolower($player_row['position'])."'>";
            $firstInRow = false;
          }
          echo "<div class='col-sm-4 trade-player-card'><div class='well well-sm' data-position='".$player_row['position']."' data-id='".$player_row['id']."'>";
          echo "<strong class='trade-player-name'><a href='/player.php?player=".$player_row['id']."'>".$player_row['name']."</a> - ".$player_row['position']."</strong>
          <span class='pull-right'>
          <a href='#' class='add-to-offer'>
            <span title='Add to Offer' class='glyphicon glyphicon-plus'></span>
          </a>
          <a href='#' class='remove-from-offer'>
            <span title='Remove Offer' class='glyphicon glyphicon-minus'></span>
          </a>
          </span>
          <hr />";
          echo "Avg: ".$player_row['average'];
          echo " <span class='pull-right small'>Age: ".$player_row['age']."</span>";
          echo "</div></div>";

          if ( $i+1 == $player_num  || $player_row['position_id'] < $team_result[$i+1]['position_id']){
            echo "</div>";
            $firstInRow = true;
          }
          $i++;
        }
      }
      ?>

    </div>
    <br /><br />
    <h3 class='new-offer'>New Offer</h3>
      <div class='player-row new-offer'>
        <div class='offer-credits'>
          <span class='credits-value'></span> <span class='credits-text'>Credits</span>
          <form class='form-inline remove-credits'>
            <input type='hidden' value='0' name='credits' />
            <input type='submit' class='btn btn-danger' class='credits-clear' value='Clear Credits' />
          </form>
        </div>
        <br />
        <div class='row position-forward'></div>
        <div class='row position-defender'></div>
        <div class='row position-center'></div>
        <div class='row position-keeper'></div>
        <br />
        <hr />
        <p>You cannot modify an offer once it has been submitted. Any money that is a part of your offer will be removed from your funds until the offer is closed. Any players that are a part of this offer cannot be moved to the trade block, promoted to starter, or be used in any other trades until this offer is closed. The offer will close either when the trade period has ended or when the other manager accepts or declines this trade.</p>
        <form class='submit-offer' action='/process/submit-trade-offer.php' method='post'>
          <input type='hidden' value='0' name='credits' requied />
          <input type='hidden' name='players' requied />
          <input type='hidden' value='<?=$player['id'];?>' name='player' required />
          <input type='hidden' value='<?=$player['team_id'];?>' name='team' required />
          <input type='submit' value='Submit Offer' class='btn btn-success' />
        </form>
      </div>
<?php
// a trade has been offered, so just show the current trade
} else { ?>
    <h3>Current Offers</h3>
      <div class='player-row current-offer'>
<?php
// only show if there are offers
  if ($player_num) {
    $i = 0;
    $firstInRow = true;
    $showCredits = true;
    // only show the team names if the user is looking at a player they control
    if ($player['team_id'] == $_SESSION['user']['team_id']) {
      $thisTeam = $team_result[0]['team_name'];
      echo "<h4><a href='/team.php?team=".$team_result[0]['team1']."'>".$thisTeam."</a></h4>";
    }
    while($i < $player_num){
      $player_row = $team_result[$i];
      // show the players being offered for this player
      // if the page player belongs to this user, show all of the offers. Otherwise only show offers made by the current user
      if (($player_row['team2'] == $player['team_id']
        && $player['team_id'] == $_SESSION['user']['team_id'])
      ||
        ($player_row['team1'] == $_SESSION['user']['team_id'])
        ) {
        if ($showCredits) {
          $crdStr = "Credits";
          if ($player_row['credits'] == 1) {
            $crdStr = "EuroBuck";
          }
          echo "<p><strong>".$player_row['credits']." ".$crdStr."</strong></p>";
          $showCredits = false;
        }
        // set the glyphicon for each position
        if ($firstInRow) {
          echo "<div class='row assets position-".strtolower($player_row['position'])."'>";
          $firstInRow = false;
        }
        echo "<div class='col-sm-4 trade-player-card'><div class='well well-sm' data-position='".$player_row['position']."' data-id='".$player_row['id']."'>";
        echo "<strong class='trade-player-name'><a href='/player.php?player=".$player_row['id']."'>".$player_row['name']."</a> - ".$player_row['position']."</strong>
        <hr />";
        echo "Avg: ".$player_row['average'];
        echo " <span class='pull-right small'>Age: ".$player_row['age']."</span>";
        echo "</div></div>";

        // buttons to accept/decline trade
        $decisionForm = "<br /><form class='form-inline' method='post' action='/process/submit-trade-decision.php'> <input type='hidden' name='id' value='".$player_row['trade_id']."' required /> <input class='btn btn-success' type='submit' name='accept' value='Accept' /> &nbsp; <input class='btn btn-danger' type='submit' name='decline' value='Decline' /> </form>";
        // see if we switch to a new position or a new team

        if ( $i+1 == $player_num
          || $player_row['position_id'] < $team_result[$i+1]['position_id']){
          echo "</div>";
          $firstInRow = true;
        }
        // show team names and the decision form, but only if the current user is looking at their own player
        if ($player['team_id'] == $_SESSION['user']['team_id']) {
          if ($i+1 < $player_num
          && $team_result[$i+1]['team_name'] != $thisTeam) {
            $thisTeam = $team_result[$i+1];
            echo "</div>";
            $firstInRow = true;
            $showCredits = true;
            echo $decisionForm;
            echo "<br /><hr /><h4><a href='/team.php?team=".$team_result[$i+1]['team1']."'>".$team_result[$i+1]['team_name']."</a></h4>";
          }
          if ($i + 1 == $player_num) {
            echo $decisionForm;
          }
        }
      }
      $i++;
    }
  } else {
    echo "<p>There are no offers on this player.</p>";
  }
?>
      </div>
<?php } ?>
  </div>
</div>

</div><!-- end content .container-fluid -->

<?php require_once('./includes/footer.inc');?>
</body>
</html>
