<?php
  require_once('./includes/_init.inc.php');
  if(!isset($_SESSION['user'])){
    header("location:/");
  }
  require_once('./control/_home.php');
  require_once('./includes/head.inc');
?>

<title>SportsBall Manager</title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row'>
<?php require_once('./includes/left-column.inc');?>
  <div class='col-sm-9'>
    <h1>The Front Office</h1>
    <hr />
<?php require_once('./includes/nav-office.inc');?>

      <div class='col-sm-8'>
          <h2>News and updates...</h2>
          <p>This will be a summary of things happening right now. Your Free Agents you have bids on, the players on your trade block, and the players you're tracking.</p>
      </div>

      <div class='col-sm-4'>
        <h2>Your Schedule</h2>
          <div class='schedule-column'>
  <?php
  $i = 0;
  $firstInRow = true;
  while($i < $sched_count){
    $schedule_row = $schedule_result[$i];
    $team1 = '';
    $team2 = '';
    if (!$schedule_row['name1']){
      $team1 = "<em>bye</em>";
    }
    else{
      $team1 = "<a href='team.php?team=".$schedule_row['id1']."'>".$schedule_row['name1']."</a>";
    }

    if (!$schedule_row['name2']){
      $team2 = "<em>bye</em>";
    }
    else{
      $team2 = "<a href='team.php?team=".$schedule_row['id2']."'>".$schedule_row['name2']."</a>";
    }

    // if win_team_id is not null, the game has been played
    if ($schedule_row['win_team']){
      if ( $schedule_row['win_team'] == $_SESSION['user']['team_id']){
        $game_result = "winner";
      }
      else{
        $game_result = "loser";
      }

      $win_score = "<span class='pull-right'>".$schedule_row['win_score']."</span>";
      $lose_score = "<span class='pull-right'>".$schedule_row['lose_score']."</span>";

      if ($schedule_row['win_team'] == $schedule_row['id1']){
        $team1 = $team1.$win_score;
        $team2 = $team2.$lose_score;
      }
      else{
        $team2 = $team2.$win_score;
        $team1 = $team1.$lose_score;
      }
    }
    else{
      // set the $game_result to empty string
      $game_result = "";
    }
    // check if this game is the current game day. If so, add a class to highlight these games
    if ( $gameDay == $schedule_row['game_day'] ){
      $currentDay = 'current-day';
    }
    else {
      $currentDay = '';
    }
  ?>


      <div class='<?=$game_result;?> <?=$currentDay;?> well well-sm'>
        <strong>Game Day <?=$schedule_row['game_day']+1;?></strong>
        <br />
        <?=$team1;?>
        <br />
        <strong><em>at</em></strong>
        <br />
        <?=$team2;?>
      </div>

  <?php
    $i++;
  }
  ?>
</div>

      </div><!-- end col-sm-4 -->
    </div><!-- end row-fluid -->
  </div><!-- end col-sm-9 -->

  </div>
</div><!-- end content .container-fluid -->


<?php require_once('./includes/footer.inc');?>
</body>
</html>
