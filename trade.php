<?php
  require_once('./includes/_init.inc.php');
  if(!isset($_SESSION['user'])){
    header("location:/");
  }
  require_once('./control/_trade.php');
  require_once('./includes/head.inc');
?>

<link href="/resources/css/footable.core.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="/resources/css/footable.filtering.min.css" rel="stylesheet" type="text/css" />
<link href="/resources/css/footable.sorting.min.css" rel="stylesheet" type="text/css" />

<script src="/resources/js/footable.core.min.js" type="text/javascript"></script>
<script src="/resources/js/footable.filtering.min.js" type="text/javascript"></script>
<script src="/resources/js/footable.sorting.min.js" type="text/javascript"></script>

<script type="text/javascript">
  $(function () {
    $('.footable').footable();
  });
</script>

<title>SportsBall Manager</title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row'>
<?php require_once('./includes/left-column.inc');?>

  <div class='col-sm-9'>
    <h1>Trading Block</h1>
    <hr />
<?php require_once('./includes/nav-office.inc');?>

<table class='footable table' data-sorting="true" data-filtering="true">
  <thead>
    <tr>
      <th class='footable-first-column'>Name</th>
      <th data-type="text">Team</th>
      <th data-type="text">Pos</th>
      <th data-type="number">Avg</th>
      <th data-type="number">Age</th>
      <th></th>
      <th data-filterable="false" class='footable-last-column'><em>Trade</em></th>
    </tr>
  </thead>
  <tbody>
<?php
  $i = 0;
  $firstInRow = true;
  while($i < $player_num){
    $row = $player_result[$i];
 ?>
    <tr>
      <td>
        <a href="/player.php?player=<?=$row['id'];?>">
          <?=$row['name'];?>
        </a>
      </td>
      <td>
        <a href="/team.php?team=<?=$row['team_id'];?>">
          <?=$row['team'];?>
        </a>
      </td>
      <td><?=$row['position'];?></td>
      <td><?=$row['average'];?></td>
      <td><?=$row['age'];?></td>
<?php
// if this player belongs to the current manager,
// allow manager to remove them from the trade block
if ($_SESSION['user']['team_id'] == $row['team_id']) {
?>
        <td>
          <a class='btn btn-default' href='/trade-offer.php?player=<?=$row['id'];?>'>View Offers</a>
        </td>
        <td>
          <form class='form-inline bid-form' action='/process/set-trade-status.php' method='post'>
            <input type='hidden' name='player' value='<?=$row['id'];?>' required />
            <input type='submit' class='btn btn-danger' value='Remove from Trade Block' />
          </form>
        </td>
<?php } else { ?>
        <td>
          <a class='btn btn-default' href='/trade-offer.php?player=<?=$row['id'];?>'>Offer Trade</a>
        </td>
        <td>
        </td>
    </tr>
<?php
    }
  $i++;
  }
?>
  </tbody>
</table>

  </div><!-- end col-sm-9 -->

  </div>
</div><!-- end content .container-fluid -->


<?php require_once('./includes/footer.inc');?>
</body>
</html>
