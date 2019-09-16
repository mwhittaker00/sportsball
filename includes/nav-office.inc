<?php
if ($_SESSION['user']['trade_offers']) {
  $offerStr = "<span class='badge label label-pill label-danger'>".$_SESSION['user']['trade_offers']." <span class='sr-only'>open trade offers.</span></span>";
} else {
  $offerStr = '';
}
?>
<ul class="nav nav-tabs">
  <li role="presentation" id="home"><a href="/home.php">Office</a></li>
  <li role="presentation" id="freeagent"><a href="freeagent.php">Free Agents</a></li>
  <li role="presentation" id="trade"><a href="/trade.php">Trades <?=$offerStr;?></a></li>
  <!--<li role="presentation" id="stadium"><a href="#">Stadium</a></li>
  <li role="presentation" id="finance"><a href="#">Finances</a></li>-->
</ul><br />
