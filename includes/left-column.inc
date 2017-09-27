<div class='col-sm-3'>

  <div class='row-fluid'>

    <div class='team-info-box small'>
      <span class='team-name h4'>
        <a href="/team.php">
          <?=$_SESSION['user']['team_name'];?>
        </a>
      </span><br />

        <a href="/division.php?division=<?=$_SESSION['user']['division_id'];?>">
          <?=$_SESSION['user']['division_name'];?>
        </a>
        <br />
        <strong>Record: </strong><?=$_SESSION['user']['win'];?> - <?=$_SESSION['user']['loss'];?>
        <br />
        <?=$_SESSION['user']['credits'];?> EuroBucks
    </div>

    <div class='team-colors team-colors-bar'
      style="background: linear-gradient(to right,
        <?=$_SESSION['user']['color1'];?> 0%,
        <?=$_SESSION['user']['color1'];?> 50%,
        <?=$_SESSION['user']['color2'];?> 50%,
        <?=$_SESSION['user']['color2'];?> 100%);">
    </div>
  </div>
  <div class='row-fluid'>
    <div class='col-sm-12'>

      <div class='left-nav'>
        <ul class="nav nav-pills nav-stacked">

          <li role="presentation">
            <a href='home.php' role='button'>
              <span class='glyphicon glyphicon-briefcase' aria-hidden='true'></span>
              Office
            </a>
          </li>

          <li role="presentation">
            <a href='/team.php' role='button'>
              <span class='glyphicon glyphicon-user' aria-hidden='true'></span>
              Team
            </a>

          </li>

          <li role="presentation">
            <a href='#' role='button'>
              <span class='glyphicon glyphicon-stats' aria-hidden='true'></span> Stats
            </a>
          </li>

          <li role="presentation" class=""><a href="#">
            <span class='glyphicon glyphicon-comment' aria-hidden='true'></span>
            Mail</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>
