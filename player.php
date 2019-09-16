<?php
  require_once('./includes/_init.inc.php');
  if(!isset($_SESSION['user']) || !isset($_GET['player'])){
    header("location:/");
  }

  /* gets the player data for the page being viewed
  $position_id, $position, $player_id, $player_fname, $player_lname, $team_name, $team_id, $player_age, $speed, $endurance, $strength, $pass, $block, $shot, $catch, $aware, $charisma, $avg, $being_traded, $team_captain, $team_captain_str
  */
  require_once('./control/_player.php');
  require_once('./includes/head.inc');
?>

<title>SportsBall Manager</title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row'>
<?php require_once('./includes/left-column.inc');?>

<div class='col-sm-9'>
  <div class='row'>
    <div class='col-sm-6'>
      <h1>
        <canvas class='team-colors-box team-colors'></canvas> <?=$player['extra_info'];?> <?=$player['name'];?>
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
      <h3>Manage</h3>
    <div class='row player-row'>
      <div class='col-xs-4'>
        <strong>Contract length: </strong><?=$player['contract_time'];?> seasons.
        <br />
        <strong>Cost: </strong><?=$player['contract_cost'];?> / day

        <br /><br />
        <ul>
          <li>Only backups can be released from your team or placed on the trade block.</li>
          <li>You cannot promote players who are on the trade block.</li>
          <li>You cannot release players who are on the trade block.</li>
          <li>Manage backups and starters on <a href="/lineup.php">your lineup</a>.</li>
        </ul>

      </div>

      <div class='col-xs-3'>

      </div>

      <div class='col-xs-7'>
<?php if (!$player['captain'] && $player['team_id'] == $_SESSION['user']['team_id'] && $player['is_active'] == 1){ ?>
      <h4>Promote to Captain</h4>
      <p>The captain is your team's leader on the field. A charismatic captian can motivate their teammates to give a little bit extra during a game.</p>

      <form action="/process/make-captain.php" method="post">
        <input type='hidden' name='player' value='<?=$player['id'];?>' required />
        <input type='hidden' name='team' value='<?=$player['team_id'];?>' required />
        <input type='submit' value='Promote <?=$player['name'];?> to captain.' class='btn btn-default' />
      </form>
      <small>This will demote your current team captain.</small>

<?php }
  if ($player['team_id'] == $_SESSION['user']['team_id']
    && !$player['is_active']) {
    // if the player is a backup, they can be traded
    // if they are not being traded, they can be released and placed on the trade block
    if (!$player['being_traded']) {
?>
      <h4>Remove Player</h4>
      <p>Remove the player from your team and immediately make them a free agent?</p>
      <p><em>This cannot be undone!</em></p>
      <form action='/process/release-player.php' method='post'>
        <input type='hidden' name='player' value='<?=$player['id'];?>' required />
        <input type='submit' class='btn btn-danger' value='Release Player' />
      </form>
      <br />
      <hr />
      <h4>Trade Player</h4>

      <p>Make this player available to trade?</p>
      <form action='/process/set-trade-status.php' method='post'>
        <input type='hidden' name='player' value='<?=$player['id'];?>' required />
        <input type='submit' class='btn btn-danger' value='Trade Player' />
      </form>
<?php } else { ?>
  <h4>Trade Player</h4>
      <p>Remove this player from the trade block?</p>
      <form action='/process/set-trade-status.php' method='post'>
        <input type='hidden' name='player' value='<?=$player['id'];?>' required />
        <input type='submit' class='btn btn-danger' value='Remove from Trade Block' />
      </form>
      <br />
      <a class='btn btn-default' href='/trade-offer.php?player=<?=$player['id'];?>'>View Trade Offers</a>

<?php
  } // end trade checks
} // end roster backup check
?>
      </div>

    </div>

  </div>
</div>

</div><!-- end content .container-fluid -->

<?php require_once('./includes/footer.inc');?>
</body>
</html>
