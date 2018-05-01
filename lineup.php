<?php
  require_once('./includes/_init.inc.php');
  if(!isset($_SESSION['user'])){
    header("location:/");
  }
  require_once('./control/_lineup.php');
  require_once('./includes/head.inc');
?>

<link href="/resources/css/footable.core.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="/resources/css/footable.sorting.min.css" rel="stylesheet" type="text/css" />

<script src="/resources/js/footable.core.min.js" type="text/javascript"></script>
<script src="/resources/js/footable.sorting.min.js" type="text/javascript"></script>

<script type="text/javascript">
  $(function () {
    $('.footable').footable();
  });
</script>

<title>SportsBall Manager - Team Lineup</title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row-fluid'>
<?php require_once('./includes/left-column.inc');?>

    <h1>Free Agents</h1>
    <hr />
    <div class='col-sm-9'>
<?php require_once('./includes/nav-team.inc');?>

<h3>Starters</h3>
<table class='footable table' data-sorting="true">
  <thead>
    <tr>
      <th class='footable-first-column'>Name</th>
      <th>Pos</th>
      <th data-type="number">Avg</th>
      <th data-type="number">Age</th>
      <th data-type="number">Cha</th>
      <th><span class='sr-only'>Promote to Captain</span></th>
      <th class='footable-last-column'><span class='sr-only'>Move to Bench</span></th>
    </tr>
  </thead>
  <tbody>
<?php
  $i = 0;
  $firstInRow = true;
  while($i < $player_num){
    $row = $player_result[$i];
    if ( $row['active'] == 1 ){
 ?>
    <tr>
      <td>
        <a href="/player.php?player=<?=$row['id'];?>">
          <?=$row['name'];?>
        </a>
      </td>
      <td><?=$row['position'];?></td>
      <td><?=$row['average'];?></td>
      <td><?=$row['age'];?></td>
      <td><?=$row['charisma'];?></td>
      <td>
<?php
if ( $row['captain'] > 0 ){
  echo "Captain";
} else{
?>
        <form class='form-inline bid-form' action="/process/make-captain.php" method="post">
          <label for='captain-<?=$i;?>' class='sr-only'>Promote to Captain</label>
          <input type='hidden' name='player' value='<?=$row['id'];?>' required />
          <input type='hidden' name='team' value='<?=$_SESSION['user']['team_id'];?>' required />
          <input type='submit' value='Promote to captain.' class='btn btn-default' />
        </form>
<?php
}// end else of if captain check
?>
      </td>
      <td>
        <form class='form-inline bid-form' action='/process/lineup-change.php' method='post'>
          <input type='hidden' name='player' value='<?=$row['id'];?>' required />
          <input type='hidden' name='active' value='<?=$row['active'];?>' required />
          <div class='form-group'>
            <input type='submit' class='btn btn-default' value='Make Backup' />
          </div>
        </form>
      </td>
    </tr>
<?php
  } // end if is_active check
  $i++;
} // end while loop

reset($player_result);
?>
  </tbody>
</table>
<hr/>
<h3>Backups</h3>
<p>
</p>
<p>
Promoting a backup to starter will replace the lowest average starter for that position. If the position only has one available spot like Keeper or Center, the backup will always replace the startup.
</p>
<table class='footable table' data-sorting="true">
  <thead>
    <tr>
      <th class='footable-first-column'>Name</th>
      <th>Pos</th>
      <th data-type="number">Avg</th>
      <th data-type="number">Age</th>
      <th data-type="number">Cha</th>
      <th><span class='sr-only'>Move to Bench</span></th>
      <th class='footable-last-column'><span class='sr-only'>Release From Team</span></th>
    </tr>
  </thead>
  <tbody>
<?php
$i = 0;
$firstInRow = true;
while($i < $player_num){
  $row = $player_result[$i];
  if ( $row['active'] == 0 ){
?>
    <tr>
      <td>
        <a href="/player.php?player=<?=$row['id'];?>">
          <?=$row['name'];?>
        </a>
      </td>
      <td><?=$row['position'];?></td>
      <td><?=$row['average'];?></td>
      <td><?=$row['age'];?></td>
      <td><?=$row['charisma'];?></td>

      <td>
        <form class='form-inline bid-form' action='/process/lineup-change.php' method='post'>
          <input type='hidden' name='player' value='<?=$row['id'];?>' required />
          <input type='hidden' name='active' value='<?=$row['active'];?>' required />
          <div class='form-group'>
            <input type='submit' class='btn btn-default' value='Make Starter' />
          </div>
        </form>
      </td>

      <td>
        <form class='form-inline bid-form' action='/process/release-player.php' method='post'>
          <input type='hidden' name='player' value='<?=$row['id'];?>' required />
          <div class='form-group'>
            <input type='submit' class='btn btn-danger' value='Release Player' />
          </div>
        </form>
      </td>

    </tr>
<?php
} // end if is_active check
$i++;
} // end while loop
?>
  </tbody>
</table>

  </div><!-- end col-sm-9 -->

  </div>
</div><!-- end content .container-fluid -->


<?php require_once('./includes/footer.inc');?>
</body>
</html>
