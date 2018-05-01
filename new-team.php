<?php
  require_once('./includes/_init.inc.php');
  // go back to index if they aren't logged in or if they have a team id
  if(!isset($_SESSION['user']) || $_SESSION['user']['team_id']){
    header("location:/");
  }
  require_once('./includes/head.inc');
?>

<title>Create a Team | SportsBall Manager</title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row-fluid'>

    <h1>Create Your Team</h1>
    <hr />
    <div class='col-sm-6'>
        <h2>Let's get started.</h2>
        <p>Welcome to the league, <span class='username'><?=$_SESSION['user']['name'];?></span>! We're happy to inform you that your expansion application has been fully approved and you're all set to create your very own SportsBall team! SportsBall fans across the globe are excited about this new addition and folks who don't follow the sport are already lined up to protest your arrival to their city. Things are already looking great!</p>
    </div>

    <div class='col-sm-6'>
      <h2>Team Information</h2>
      <p>It looks like our unpaid interns misplaced a portion of your application. Don't worry - we'll figure out who was responsible and get them right back to coffee duty.</p>
      <p>In the meantime, please provide some additional information about your team so we can complete the expansion process and add your new team!</p>
      <br />
      <form class="form-horizontal" action='/process/new-team.php' method='post'>
        <div class="form-group">
          <label for="team-name" class="col-sm-3 control-label">Team Name</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name='name' id="team-name" placeholder="Aquabats" required />
          </div>
          <p class="help-block">Don't add a "the" in front of your team name - we can take care of that.</p>
        </div>
        <div class="form-group">
          <label for="primary-color" class="col-sm-3 control-label">Primary Color</label>
          <div class="col-sm-3">
            <input type="color" class="form-control" name='primary-color' id="primary-color" value='#0000ff' required />
          </div>
          <label for="secondary-color" class="col-sm-3 control-label">Secondary Color</label>
          <div class="col-sm-3">
            <input type="color" class="form-control" name='secondary-color' id="secondary-color" value='#ff0000' required />
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Create your team!</button>
          </div>
        </div>
      </form>

    </div>

  </div>

</div><!-- end content .container-fluid -->

<?php require_once('./includes/footer.inc');?>
</body>
</html>
