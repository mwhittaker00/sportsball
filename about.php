<?php
  require_once('./includes/_init.inc.php');
  require_once('./includes/head.inc');
?>

<title>SportsBall Manager | About SBM</title>

<?php require_once('./includes/nav.inc');?>

<div id='main-container' class='container-fluid'>

  <div class='row'>

<h1>About SportsBall Manager</h1>
<hr />
<p>SportsBall manager is a free, web-based sports simulation and management game that puts you in charge of a professional sports team. It's up to you to manage your team's roster, train your players, negotiate contracts, maintain a stadium, and a lot more.</p>
<p>SBM is also a multiplayer game - you're competing against other managers to put your team to the top of the division. You can communicate with other managers on Discord or your division's message board, and you can even trade players with other managers.</p>
<p>SBM was inspired by Quidditch Manager and borrows a lot of ideas from that game. Unfortunately, the game's developer backed away from the project a couple of years ago and took it offline.
</p>
<hr />
<div class='row'>
  <div class='col-sm-4'>
<h2>How to Play</h2>
<div class="panel-group" id="accordionOne">
    <div class="panel panel-default">
    <div class="panel-heading" id="a-headingOne">
      <h3 class='panel-title'>
        <a class='collapsed'  data-toggle="collapse" data-target="#a-collapseOne" aria-expanded="true" aria-controls="a-collapseOne">
          Registration
        </a>
      </h3>
    </div>

    <div id="a-collapseOne" class="panel-collapse collapse" aria-labelledby="a-headingOne" data-parent="#accordionOne">
      <div class="panel-body">
        <p>You must first <a href="/">register for an account.</a></p>

      </div>
    </div>
  </div>
    <div class="panel panel-default">
    <div class="panel-heading" id="a-headingTwo">
      <h3 class='panel-title'>
        <a class="collapsed"  data-toggle="collapse" data-target="#a-collapseTwo" aria-expanded="false" aria-controls="a-collapseTwo">
          Team Creation
        </a>
      </h3>
    </div>
    <div id="a-collapseTwo" class="panel-collapse collapse" aria-labelledby="a-headingTwo" data-parent="#accordionOne">
      <div class="panel-body">
        <p>After you create your account and log in, you'll be directed to a team creation page. Here you can pick your two team colors and your team name.</p>

      </div>
    </div>
  </div>
    <div class="panel panel-default">
    <div class="panel-heading" id="a-headingThree">
      <h3 class='panel-title'>
        <a class="collapsed"  data-toggle="collapse" data-target="#a-collapseThree" aria-expanded="false" aria-controls="a-collapseThree">
          Divisions
        </a>
      </h3>
    </div>
    <div id="a-collapseThree" class="panel-collapse collapse" aria-labelledby="a-headingThree" data-parent="#accordionOne">
      <div class="panel-body">
        <p>You will play against teams in your division. At the start of every season, a round-robin style tournament is set up that gives every team the opportunity to play each opponent twice.
        </p>
        <p>
          Each division can have up to 12 teams. If there are fewer than 12 teams when a schedule is created, teams may have "bye" games. If a new team is created in the middle of a season they will take up one of the open slots in the schedule.
        </p>
      </div>
    </div>
  </div>

    <div class="panel panel-default">
    <div class="panel-heading" id="a-headingFour">
      <h3 class='panel-title'>
        <a class="collapsed"  data-toggle="collapse" data-target="#a-collapseFour" aria-expanded="false" aria-controls="a-collapseFour">
          Team Management
        </a>
      </h3>
    </div>
    <div id="a-collapseFour" class="panel-collapse collapse" aria-labelledby="a-headingFour" data-parent="#accordionOne">
      <div class="panel-body">
        <h3>The Front Office</h3>
        <p>
          The front office serves as the administrative center for the manager. This is where you will manage team finances, improve the stadium, purchase equipment, and get a general overview of what's happening with your team right now.
        </p>
        <h3>Team</h3>
        <p>
          The team page is where you will manage your roster, change your lineup, explore free agents, trade players, and train your team.
        </p>
        <h3>Division</h3>
        <p>
          Here you can few the current standings in your division as well as communicate with the other managers using the divisional message board.
        </p>
        <h3>Stats <small>- Coming Soon!</small></h3>
        <p>
          View information about your time as a SportsBall Manager, including things like points scored by players and overall win/loss records.
        </p>
        <h3>Mail <small>- Coming Soon!</small></h3>
        <p>
          Communicate directly with other managers, or receive in-game notifications about events or updates to the status of bids or trade deals.
        </p>
      </div>
    </div>
  </div>

</div>
</div>
<div class='col-sm-4'>
<h2>Scoring and Positions</h2>

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">    <div class="panel panel-default">
    <div class="panel-heading" id="headingOne">
      <h3 class='panel-title'>
        <a class='collapsed'  data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          The Rules of SportsBall
        </a>
      </h3>
    </div>

    <div id="collapseOne" class="panel-collapse collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
      <div class="panel-body">
        <p>
          The first rule of SportsBall is, well, nobody actually knows the rules. We pay players, they play a game, and someone wins. That's all we know so far.
        </p>
        <p>
          Wait! There actually is <em>one</em> thing we know! SportsBall games <strong>cannot end in a tie.</strong> The game will continue to be played until someone has definitely scored more points than the other team. Points are scored one at a time.
        </p>
      </div>
    </div>
  </div>
    <div class="panel panel-default">
    <div class="panel-heading" id="headingTwo">
      <h3 class='panel-title'>
        <a class="collapsed"  data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Player Attributes
        </a>
      </h3>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
      <div class="panel-body">
        <p>
          Every player has a series of attributes that determines their ability to do certain actions, may trigger certain events, or may modify other attributes. These attributes can be modified by some other attributes, training, equipment being used, and typically increase after every played game.
        </p>
        <ul>
          <li>
            <strong>Age</strong> - How old a player is. Most players enter the game in their late teens or early 20s. As they get older they are more likely to retire, and some stats may not increase as much.
          </li>
          <li>
            <strong>Speed</strong> - How fast a player moves. Speed is important in SportsBall, and allows both offenses and defenses to be more effective.
          </li>
          <li>
            <strong>Endurance</strong> - A player's ability to maintain their stamina. Important for mobile players or gameplans that depend on a lot of movement.
          </li>
          <li>
            <strong>Strength</strong> - How well a player can use physical force to impose their will on other players. This is an important attribute for plans that rely on close confrontations.
          </li>
          <li>
            <strong>Pass</strong> - A player's ability to accurately move the ball around the playing field.
          </li>
          <li>
            <strong>Block</strong> - A player's ability to stop a ball in motion. A successfully blocked ball can still be picked up by the opposing team.
          </li>
          <li>
            <strong>Shot</strong> - Determines how well a player makes a shot on goal.
          </li>
          <li>
            <strong>Catch</strong> - A player's ability to catch a ball in play. Unlike blocking, the team maintains possession of the ball.
          </li>
          <li>
            <strong>Awareness</strong> - Determines how well a player can follow the game. More aware players will generally perform better against less aware players if the other attributes are a fair match.
          </li>
          <li>
            <strong>Charisma</strong> - The strength of a player's personality. The team camptain's charisma can provide a boost to every player's attributes during a game. Players and teams with high charisma also draw more people to games, earning more money for managers.
          </li>
          <li>
            <strong>Potential</strong> - A hidden attribute used to determine how much a player develops over their career. A player with low potential will not gain as much from training or playing matches, while a player with high potential will improve quickly from experience.
          </li>
        </ul>

      </div>
    </div>
  </div>
    <div class="panel panel-default">
    <div class="panel-heading" id="headingThree">
      <h3 class='panel-title'>
        <a class="collapsed"  data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          Player Positions
        </a>
      </h3>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
      <div class="panel-body">
        <p>
          There are four key positions in SportsBall: Forward, defender, center, and keeper. Additionally, one player on the team can be named the captain.
        </p>
        <p>
          The captain can improve the abilities of the team depending on the captain's charisma rating, as well as their physical location on the field. A forward who is a captain will make a bigger impact on the forwards and center than they would on the defenders and keeper, while a center who is the captain would impact all of the team equally.
        </p>
        <ul>
          <li>
            <strong>Forward</strong> - The team's main scorer. They are usually fast players who are always trying to get into position for the open shot.
            <br /><br />
            <strong>Key Attributes</strong> - Speed, Endurance, Shot.
            <br /><br />
            <strong>Weak Attributes</strong> - Strength, Block, Catch.
          </li>

          <li>
            <strong>Center</strong> - A player who often plays both sides of the ball and is usually well-rounded.
            <br /><br />
            <strong>Key Attributes</strong> - Speed, Pass, Aware.
            <br /><br />
            <strong>Weak Attributes</strong> - Shot, Catch.
          </li>

          <li>
            <strong>Defender</strong> - The first line of defense against attacking opponents. Defenders are strong and sturdy, and usually spend most of the game in the backfield.
            <br /><br />
            <strong>Key Attributes</strong> - Strength, Block, Pass.
            <br /><br />
            <strong>Weak Attributes</strong> - Shot, Speed, Catch.
          </li>

          <li>
            <strong>Keeper</strong> - The player who tries to prevent the ball from reaching the goal. A good keeper can maintain possession of the ball after preventing a goal attempt and get the ball back to a teammate.
            <br /><br />
            <strong>Key Attributes</strong> - Catch, Awareness, Pass.
            <br /><br />
            <strong>Weak Attributes</strong> - Shot, Endurance, Strength.
          </li>
        </ul>
      </div>
    </div>
  </div>


</div>
</div>
<div class='col-sm-4'>
<h2>FAQs!</h2>
<div class="panel-group" id="accordion-faq">
    <div class="panel panel-default">
    <div class="panel-heading" id="faq-headingOne">
      <h3 class='panel-title'>
        <a class='collapsed'  data-toggle="collapse" data-target="#faq-collapseOne" aria-expanded="true" aria-controls="faq-collapseOne">
          How are matches played/calculated?
        </a>
      </h3>
    </div>

    <div id="faq-collapseOne" class="panel-collapse collapse" aria-labelledby="faq-headingOne" data-parent="#accordion-faq">
      <div class="panel-body">
        <p>Matches are played every 24 hours. League matches are counted towards your division rank, earn revenue for your team, and give experience to your players.</p>
        <p>
            Matches are calculated by comparing the skills of competing teams at certain positions. For example, a team whose shooters have very high <strong>shot</strong> values are likely to score a lot against a team whose defenders and keeper have low <strong>block</strong> and <strong>catch</strong> values. The simulation attempts to determine how many shots each team would likely take, and then calculates how many are successful. If there is a tie, the simulation continues to run until a round ends and one team has scored more goals than the other team.
        </p>
      </div>
    </div>
  </div>
    <div class="panel panel-default">
    <div class="panel-heading" id="faq-headingTwo">
      <h3 class='panel-title'>
        <a class="collapsed"  data-toggle="collapse" data-target="#faq-collapseTwo" aria-expanded="false" aria-controls="faq-collapseTwo">
          How do I make money?
        </a>
      </h3>
    </div>
    <div id="faq-collapseTwo" class="panel-collapse collapse" aria-labelledby="faq-headingTwo" data-parent="#accordion-faq">
      <div class="panel-body">
        <p>Money can be made through ticket revenue, earning a small amount per ticket sold. You can increase ticket sales by upgrading your stadium, having a high team popularity, and having charismatic players. You can also earn money through sponsorships.</p>
        <p>
          Funds are spent on stadium upgrades, player contracts, training, and equipment.
        </p>
      </div>
    </div>
  </div>
    <div class="panel panel-default">
    <div class="panel-heading" id="faq-headingThree">
      <h3 class='panel-title'>
        <a class="collapsed"  data-toggle="collapse" data-target="#faq-collapseThree" aria-expanded="false" aria-controls="faq-collapseThree">
          How do sponsors work?
        </a>
      </h3>
    </div>
    <div id="faq-collapseThree" class="panel-collapse collapse" aria-labelledby="faq-headingThree" data-parent="#accordion-faq">
      <div class="panel-body">
        <p>Sponsors will provide resources for your team. You can unlock sponsors as your team's popularity increases.</p>
        <p>Some sponsors will give you revenue, while others will give you equipment.</p>
      </div>
    </div>
  </div>

    <div class="panel panel-default">
    <div class="panel-heading" id="faq-headingFour">
      <h3 class='panel-title'>
        <a class="collapsed"  data-toggle="collapse" data-target="#faq-collapseFour" aria-expanded="false" aria-controls="faq-collapseFour">
          How do I increase team popularity?
        </a>
      </h3>
    </div>
    <div id="faq-collapseFour" class="panel-collapse collapse" aria-labelledby="faq-headingFour" data-parent="#accordion-faq">
      <div class="panel-body">
        <p>
          Popularity is a measure of the seasons completed by your club, player values, win record and charisma, with your captain receiving more weighting for their charisma score.
        </p>
      </div>
    </div>
  </div>

</div>
</div>
</div>

</div>

</div><!-- end content .container-fluid -->

<?php require_once('./includes/footer.inc');?>
</body>
</html>
