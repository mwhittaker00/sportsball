<?php
  require_once('./includes/_init.inc.php');
  if(!isset($_SESSION['user']) || !isset($_GET['player'])){
    header("location:/");
  }

  /* gets the player data for the page being viewed
  $position_id, $position, $player_id, $player_fname, $player_lname, $team_name, $team_id, $player_age, $speed, $endurance, $strength, $pass, $block, $shot, $catch, $aware, $charisma, $avg
  */
  require_once('./control/_player.php');
  require_once('./includes/head.inc');
?>

<title>SportsBall Manager</title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row-fluid'>
<?php require_once('./includes/left-column.inc');?>

    <h1>
      <canvas class='team-colors-box team-colors'></canvas> <?=$player['name'];?>
      <a href='/team.php?team=<?=$player['team_id'];?>' class='small'>
        <?=$player['team_name'];?>
      </a>
    </h1>

    <hr />
    <div class='col-sm-9'>

      <div class='row'>

        <div class='col-xs-2'>
          <strong>AVG</strong><br />
          <?=$player['average'];?>
        </div>

        <div class='col-xs-2'>
          <strong>AGE</strong><br />
          <?=$player['age'];?>
        </div>

        <div class='col-xs-2'>
          <strong>POS</strong><br />
          <?=substr($player['position'],0,1);?>
        </div>

        <div class='col-xs-2'>
          <strong>CPT</strong><br />
          <?=$player['captain'];?>
        </div>

      </div>
      <br />
      <h4>Skills</h4>
      <div class='row player-row'>
        <div class='col-xs-1'>
          <strong>Sp</strong><br />
          <?=$player['speed'];?>
        </div>
        <div class='col-xs-1'>
          <strong>En</strong><br />
          <?=$player['endurance'];?>
        </div>
        <div class='col-xs-1'>
          <strong>St</strong><br />
          <?=$player['strength'];?>
        </div>
        <div class='col-xs-1'>
          <strong>Pa</strong><br />
          <?=$player['pass'];?>
        </div>
        <div class='col-xs-1'>
          <strong>Bl</strong><br />
          <?=$player['block'];?>
        </div>
        <div class='col-xs-1'>
          <strong>Sh</strong><br />
          <?=$player['shot'];?>
        </div>
        <div class='col-xs-1'>
          <strong>Ca</strong><br />
          <?=$player['catch'];?>
        </div>
        <div class='col-xs-1'>
          <strong>Aw</strong><br />
          <?=$player['aware'];?>
        </div>
        <div class='col-xs-1'>
          <strong>Ch</strong><br />
          <?=$player['charisma'];?>
        </div>
    </div>
    <br />
      <h4>Manage</h4>
    <div class='row player-row'>
      <div class='col-xs-4'>
        <strong>Contract length: </strong><?=$player['contract_time'];?> seasons.
        <br />
        <strong>Cost: </strong><?=$player['contract_cost'];?> / day
      </div>

      <div class='col-xs-1'>

      </div>

      <div class='col-xs-7'>
<?php if (!$player['cpt_bool'] && $player['team_id'] == $_SESSION['user']['team_id'] && $player['is_active'] == 1){ ?>

      <p>The captain is your team's leader on the field. A charismatic captian can motivate their teammates to give a little bit extra during a game.</p>

      <form action="/process/make-captain.php" method="post">
        <input type='hidden' name='player' value='<?=$player['id'];?>' required />
        <input type='hidden' name='team' value='<?=$player['team_id'];?>' required />
        <input type='submit' value='Promote <?=$player['name'];?> to captain.' class='btn btn-default' />
      </form>
      <small>This will demote your current team captain.</small>

<?php }
  if ($player['team_id'] == $_SESSION['user']['team_id'] && $player['is_active'] == 0){
?>
      <p>Remove the player from your team and immediately make them a free agent?</p>
      <p><em>This cannot be undone!</em></p>
      <form action='/process/release-player.php' method='post'>
        <input type='hidden' name='player' value='<?=$player['id'];?>' required />
        <input type='submit' class='btn btn-danger' value='Release Player' />
      </form>


<?php } ?>
      </div>

    </div>

  </div>
</div>

</div><!-- end content .container-fluid -->

<?php require_once('./includes/footer.inc');?>
</body>
</html>
