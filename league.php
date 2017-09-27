<?php
  require_once('/includes/_init.inc.php');
  if(!isset($_SESSION['user'])){
    header("location:/");
  }

  require_once('/control/_league.php');
  require_once('/includes/head.inc');
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

<title>SportsBall Manager</title>

<?php require_once('/includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row-fluid'>
<?php require_once('/includes/left-column.inc');?>

    <h1><?=$division_name;?></h1>

    <hr />
    <div class='col-sm-9'>
<table class='footable table' data-sorting="true">
  <thead>
    <tr>
      <th class='footable-first-column' data-type="number">Rank</th>
      <th>Team</th>
      <th data-type="number">Win</th>
      <th data-type="number">Loss</th>
      <th class='footable-last-column' data-type="number">Diff</th>
    </tr>
  </thead>
  <tbody>

    <!-- DISPLAY TEAM standings -->
    <?php
    $i = 0;
    while($i < $team_num){
      $row = $team_result[$i];
?>
      <tr>
        <td><?=$i+1;?></td>
        <td><canvas class='team-colors-box team-colors sm-team-colors-box' style="background: linear-gradient(to right, <?=$row['color1'];?> 0%, <?=$row['color1'];?> 50%, <?=$row['color2'];?> 50%, <?=$row['color2'];?> 100%);"></canvas><a href='/team.php?team=<?=$row['id'];?>'><?=$row['name'];?></a></td>
        <td><?=$row['win'];?></td>
        <td><?=$row['loss'];?></td>
        <td><?=$row['difference'];?></td>
      </tr>
<?
$i++;
} // end while
?>
    </tbody>
  </table>
  </div>

</div><!-- end content .container-fluid -->

<?php require_once('/includes/footer.inc');?>
</body>
</html>
