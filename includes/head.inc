<!DOCTYPE html>
<head>
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600|Oswald' rel='stylesheet' type='text/css'>

<link href="/resources/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="/resources/css/main.css" rel="stylesheet">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script><script type="text/javascript" src="/resources/bootstrap/js/bootstrap.min.js"></script>

<script>
  $('#main-nav-collapse').collapse({
    toggle: false
  })
</script>
<script type="text/javascript" src="/resources/js/main.js"></script>

<?php
// set the styles for the team colors if the team variable is set
  if (isset($team) || isset($_SESSION['user']) || isset($player)){
    if(isset($player)){
      $primaryColor = $player['color1'];
      $secondColor = $player['color2'];
    }

    else if(isset($team)){
      $primaryColor = $team['color1'];
      $secondColor = $team['color2'];
    }

    else if(isset($_SESSION['user'])){
      $primaryColor = $_SESSION['user']['color1'];
      $secondColor = $_SESSION['user']['color2'];
    }


?>
<style>
.team-colors{
  background: linear-gradient(to right, <?=$primaryColor;?> 0%, <?=$primaryColor;?> 50%, <?=$secondColor;?> 50%, <?=$secondColor;?> 100%);
}
</style>
<?php
  }
?>
