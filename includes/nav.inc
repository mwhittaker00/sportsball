</head>
<body>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav-collapse" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href='/'><img id="logo" src="/resources/images/logo.png" alt="Sports Ball Manager Home" /></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="main-nav-collapse">
      <ul class="nav navbar-nav">
        <li><a href="/division.php">Division</a></li>
        <li><a href="https://discord.gg/S944xyu" target="_blank">Community</a></li>
      </ul>

<?php if(!isset($_SESSION['user'])){ ?>
      <form class="navbar-form navbar-right" action="/process/login.php" method="post">
        <div class="form-group" >
          <label class='sr-only' for='username'>Username</label>
          <input type="text" name="username" class="form-control" placeholder="Username" required />
        </div>
        <div class="form-group" >
          <label class='sr-only' for='password'>Password</label>
          <input type="password" name="password" class="form-control" placeholder="Password" required />
        </div>
        <button type="submit" class="btn btn-default">Log in</button>
      </form>
<?php } else{ ?>

    <div class='user-link navbar-right'><a href="/home.php"><?=$_SESSION['user']['name'];?></a> | <a href="/process/logout.php">Log Out</a></div>
<?php } ?>

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<?php
// Status reporting

$statusMessage = '';
if (isset($_SESSION['status']) && strlen($_SESSION['status']) > 1){
  $statusMessage = $_SESSION['status'];
  unset($_SESSION['status']);
  echo "<div id='status-alert'>".$statusMessage."<br /><br /><a href='.' class='btn' id='close-status'>Close</a></div>";
}
?>
