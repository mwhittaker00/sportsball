<?php
  require_once('./includes/_init.inc.php');
  if(!isset($_SESSION['user'])){
    header("location:/");
  }
  require_once('./control/_freeagent.php');
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

  <div class='row-fluid'>
<?php require_once('./includes/left-column.inc');?>

    <h1>Free Agents</h1>
    <hr />
    <div class='col-sm-9'>
<?php require_once('./includes/nav-office.inc');?>

<table class='footable table' data-sorting="true" data-filtering="true">
  <thead>
    <tr>
      <th class='footable-first-column'>Name</th>
      <th>Pos</th>
      <th data-type="number">Avg</th>
      <th data-type="number">Age</th>
      <th data-type="number">Bid</th>
      <th data-type="number">Days Left</th>
      <th>High Bid</th>
      <th data-filterable="false" class='footable-last-column'><em>Bid</em></th>
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
      <td><?=$row['position'];?></td>
      <td><?=$row['average'];?></td>
      <td><?=$row['age'];?></td>
      <td><?=$row['cost'];?>s</td>
      <td><?=$row['days_left'];?></td>
      <td><?=$row['team'];?></td>
      <td>
        <form class='form-inline bid-form' action='/process/add-bid.php' method='post'>
          <div class='form-group'>
            <label for='bid-<?=$i;?>' class='sr-only'>Bid</label>
            <input type='hidden' name='player' value='<?=$row['id'];?>' required />
            <input type='text' class='bid-input form-control' name='bid' id='bid-<?=$i;?>' maxlength='7' required />
          </div>
          <div class='form-group'>
            <input type='submit' class='btn btn-default' value='Bid' />
          </div>
        </form>
    </tr>
<?php
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
