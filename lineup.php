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

  <div class='row'>
<?php require_once('./includes/left-column.inc');?>
  <div class='col-sm-9'>
    <h1>Lineup</h1>
    <hr />
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
        <form class='form-inline' action="/process/make-captain.php" method="post">
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
        <form class='form-inline' action='/process/lineup-change.php' method='post'>
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
<p>You cannot promote or release a player who is on the trade block. You will need to remove them from the trade block before you can make them a starter or release them from your team.</p>
<table class='footable table' data-sorting="true">
  <thead>
    <tr>
      <th class='footable-first-column'>Name</th>
      <th>Pos</th>
      <th data-type="number">Avg</th>
      <th data-type="number">Age</th>
      <th data-type="number">Cha</th>
      <th><span class='sr-only'>Move to Bench</span></th>
      <th><span class='sr-only'>Release From Team</span></th>
      <th class='footable-last-column'><span class='sr-only'>Trade Player</span></th>
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
<?php
// don't allow promotion if player is being traded
if (!$row['being_traded'] && !$row['being_offered']) {
?>
        <form class='form-inline' action='/process/lineup-change.php' method='post'>
          <input type='hidden' name='player' value='<?=$row['id'];?>' required />
          <input type='hidden' name='active' value='<?=$row['active'];?>' required />
          <div class='form-group'>
            <input type='submit' class='btn btn-default' value='Make Starter' />
          </div>
        </form>
<?php } ?>
      </td>

      <td>
<?php
// don't allow release if player is being traded
// instead, show a link to their offer page
if (!$row['being_traded'] && !$row['being_offered']) {
?>
        <form class='form-inline' action='/process/release-player.php' method='post'>
          <input type='hidden' name='player' value='<?=$row['id'];?>' required />
          <div class='form-group'>
            <input type='submit' class='btn btn-danger' value='Release Player' />
          </div>
        </form>
<?php } else { ?>
      <a class='btn btn-default' href='/trade-offer.php?player=<?=$row['id'];?>'>View Trade Offers</a>
<?php } ?>
      </td>

      <td>
<?php
  // if the player is a backup, they can be traded
  if (!$row['being_traded'] && !$row['being_offered']) {
?>
        <form class='form-inline' action='/process/set-trade-status.php' method='post'>
          <input type='hidden' name='player' value='<?=$row['id'];?>' required />
          <input type='submit' class='btn btn-danger' value='Trade Player' />
        </form>
<?php
}
// if they're being offered, don't show a button.
if ($row['being_offered']) {
?>
        Offered in trade.
<?php }
// if they're being traded, we can remove them from the trade block
if ($row['being_traded'])
{ ?>
        <form class='form-inline' action='/process/set-trade-status.php' method='post'>
          <input type='hidden' name='player' value='<?=$row['id'];?>' required />
          <input type='submit' class='btn btn-danger' value='Remove from Trade Block' />
        </form>
<?php
  } // end trade checks
?>
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
