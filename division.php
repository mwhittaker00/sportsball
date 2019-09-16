<?php
  require_once('./includes/_init.inc.php');
  if(!isset($_SESSION['user'])){
    header("location:/");
  }

  require_once('./control/_division.php');
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

<title>SportsBall Manager - <?=$division['name'];?></title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row'>
<?php require_once('./includes/left-column.inc');?>
  <div class='col-sm-9'>
    <h1> <?=$division['name'];?> </h1>

    <hr />
      <h2>Standings</h2>
      <table class='footable table' data-sorting="true">
        <thead>
          <tr>
            <th class='footable-first-column' data-type="number">Rank</th>
            <th>Team</th>
            <th data-type="number">Win</th>
            <th data-type="number">Lose</th>
            <th class='footable-last-column' data-type="number">Diff</th>
          </tr>
        </thead>
        <tbody>
    <!-- DISPLAY TEAM PLAYERS -->
<?php
$i = 0;
while($i < $team_num){
  $team_row = $team_result[$i];
?>
      <tr>
        <td>
          <strong><?=$i+1;?></strong>
        </td>
        <td>
          <a href="/team.php?team=<?=$team_row['id'];?>">
            <canvas class='team-colors-box team-colors team-colors-short' style='background: linear-gradient(to right, <?=$team_row['color1'];?> 0%, <?=$team_row['color1'];?> 50%, <?=$team_row['color2'];?> 50%, <?=$team_row['color2'];?> 100%);'></canvas>
            <?=$team_row['name'];?>
          </a>
        </td>
        <td>
          <?=$team_row['win'];?>
        </td>
        <td>
          <?=$team_row['loss'];?>
        </td>
        <td>
          <?=$team_row['difference'];?>
        </td>
      </tr>
<?php
      $i++;
    }
?>
        </tbody>
        </table>
          <h2>Communications</h2>
<?php
// only start posts well if there are posts
if ($post_num) {
?>
      <div class='well well-sm'>
<?php
$i = 0;
while($i < $post_num){
  $post = $post_result[$i];
  // if the team id is 0, this is a system message so we don't need the team name to be a link
  if ($post['team_id'] == 0) {
    $team_link = "<span class='h4'>".$post['name']."</span>";
  } else {
    $team_link = "<a class='h4' href='/team.php?team=".$team_row['id']."'>".$post['name']."</a>";
  }
?>
          <div class='division-comm'>
            <?=$team_link;?>
            <small class='text-muted comm-timestamp'><?=$post['time'];?></small>
            <div class='team-colors team-colors-bar team-colors-bar-small' style='background: linear-gradient(to right, <?=$post['color1'];?> 0%, <?=$post['color1'];?> 50%, <?=$post['color2'];?> 50%, <?=$post['color2'];?> 100%);'></div>
            <div class='division-comm-content'>
              <?=$post['content'];?>
            </div>
          </div>
<?php
  $i++;
}
// close post_num check and div
?>
          </div>
<?php } ?>

          <br />
          <form id='division-message' action='/process/post-message.php' method='post'>
            <label for='comm'><h3>Leave a message.</h3></label>
            <br />
            <textarea name='comm' maxlength='1500' required></textarea>
            <br />
            <span class='small pull-left'>You can format your posts using  <a class='light-link' target='_blank' href='https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet'>Markdown</a>.</span>
            <input type='submit' class='btn btn-default pull-right' value='Submit' />
          </form>
        </div>
      </div>
  </div>

</div><!-- end content .container-fluid -->

<?php require_once('./includes/footer.inc');?>
</body>
</html>
