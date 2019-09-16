<?php
  require_once('./includes/_init.inc.php');
  if(isset($_SESSION['user'])){
    header("location:./home.php");
  }
  require_once('./includes/head.inc');
?>

<title>SportsBall Manager</title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row'>

    <div class='col-sm-7'>
      <h1>SportsBall! Yeah!</h1>
        <p>The lights. The crowd. The players.</p>
        <p><em>The manager.</em></p>
        <p>This is SportsBall Manager, where you get to raise your very own SportsBall team from the bottom of Scrub Tier and take them all the way to the top. What exactly is SportsBall? Well... nobody knows for certain but we hear it's a blast!</p>
    </div>

    <div class='col-sm-5'>
      <h1>Start your team!</h1>
      <form class="form-horizontal" action='/process/register.php' method='post'>
        <div class="form-group">
          <label for="register-username" class="col-sm-3 control-label">Username</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name='username' id="register-username" placeholder="username" require />
          </div>
        </div>
        <div class="form-group">
          <label for="register-password" class="col-sm-3 control-label">Password</label>
          <div class="col-sm-9">
            <input type="password" class="form-control" name='password' id="register-password" placeholder="Password" required />
          </div>
        </div>
        <div class="form-group">
          <label for="confirm-password" class="col-sm-3 control-label">Confirm password</label>
          <div class="col-sm-9">
            <input type="password" class="form-control" name='confirm-password' id="confirm-password" placeholder="Confirm password" required />
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">Email</label>
          <div class="col-sm-9">
            <input type="email" class="form-control" name='email' id="email" placeholder="Email" required />
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <div class="checkbox">
              <label>
                <input name='terms' type="checkbox"> <a href='#'>I've read the rules and terms.</a>
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-default">Play some SportsBall</button>
          </div>
        </div>
      </form>
    </div>

  </div>

</div><!-- end content .container-fluid -->

<?php require_once('./includes/footer.inc');?>
</body>
</html>
