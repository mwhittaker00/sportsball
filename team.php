<?php
  require_once('/includes/_init.inc.php');
  if(!isset($_SESSION['user'])){
    header("location:/");
  }

  require_once('/control/_team.php');
  require_once('/includes/head.inc');
?>

<title>SportsBall Manager</title>

<?php require_once('/includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row-fluid'>
<?php require_once('/includes/left-column.inc');?>

    <h1><canvas class='team-colors-box team-colors'></canvas> <?=$team['name'];?> </h1>

    <hr />
    <div class='col-sm-9'>

<?php require_once('/includes/nav-team.inc');?>

      <div class='row-fluid'>

        <div class='col-sm-9'>

    <!-- DISPLAY TEAM PLAYERS -->
        <div class='player-cards'>
    <?php
    $i = 0;
    $firstInRow = true;
    while($i < $player_num){
      $player_row = $player_result[$i];

      //set team captain symbol
      if ($player_row['captain'] == 1){
        $cpt = "<span class='glyphicon glyphicon-star pull-right'><span class='sr-only'>Captain</span></span>";
      }
      else{
        $cpt = "";
      }
      // set the glyphicon for each position
      if ( $firstInRow ){
        echo "<div class='row'><strong>".$player_row['position']."s</strong><br />";
        $firstInRow = false;
      }
      echo "<div class='col-sm-4'><div class='well well-sm'>";
      echo "<strong><a href='/player.php?player=".$player_row['id']."'>".$player_row['name']."</a></strong>".$cpt."<hr />";
      echo "Avg: ".$player_row['average'];
      echo " <span class='pull-right small'>Age: ".$player_row['age']."</span>";
      echo "</div></div>";

      if ( $i+1 == $player_num  || $player_row['position_id'] < $player_result[$i+1]['position_id']){
        echo "</div>";
        $firstInRow = true;
      }
      $i++;
    }

    ?>

          </div>
        </div>

        <div class='col-sm-3'>
          <div class='well well-sm small'>
            <strong>Team Average: </strong><?=$team['average'];?>
            <br />
            <a href="/division.php?division=<?=$team['division_id'];?>">
              <?=$team['division_name'];?>
            </a>
            <br />
            <strong>Record: </strong><?=$team['win'];?> - <?=$team['loss'];?>

            <br /><br />
            <small>Created on <?=date('F j, Y',strtotime($team['created']));?></small>
        </div>

      </div>
    </div>
  </div>

</div><!-- end content .container-fluid -->

<?php require_once('/includes/footer.inc');?>
</body>
</html>
