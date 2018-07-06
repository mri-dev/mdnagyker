<? require "head.php"; ?>
  <style media="screen">
    a.green-btn{
      background: #63b363;
      border-radius: 3px;
      font-size: 1em;
      border: none;
      padding: 12px 20px;
      text-transform: uppercase;
      color: white;
      font-weight: bold;
      display: block;
      margin: 15px 0;
      float:left;
      text-decoration: none;
    }

    .center{
      text-align: center;
    }

    a{
      text-decoration: none;
    }

    .clr {
      clear:both;
    }
  </style>
  <h1>Tisztelt <?=$name?>!</h1>
  <p>Előfoglalását sikeresen leadta. A foglalt termékeket <strong><?=$foglal_ora?> óráig</strong> félretettük Önnek, melyet ez időn belül megvásárolhat!</p>
  <p>A foglalás lejáratának ideje: <strong><?=$expire_at?></strong>.</p>

  <h2>Lefoglalt termékek</h2>
  <div class="products">
    <table class="if" width="100%" border="1" style="border-collapse:collapse;" cellpadding="10" cellspacing="0">
      <thead>
      	<tr>
      		<th align="center">Termék</th>
        	<th align="center">Me.</th>
      		<th align="center">Egység ár</th>
      		<th align="center">Ár</th>
      	</tr>
      </thead>
      <tbody style="color:#888;">
      	<?
      	foreach($cart as $d){
      	$total += round($d[prices]['current_each']*$d[me]);
      	?>
      	<tr>
      		<td><a href="<?=$d[url]?>"><?=$d[termekNev]?></a></td>
        	<td align="center"><?=$d[me]?>x</td>
      		<td align="center"><?=round($d[prices]['current_each'])?> Ft</td>
      		<td align="center"><?=round($d[prices]['current_each']*$d[me])?> Ft</td>
      	</tr>
      	<? } ?>
      	<tr>
      		<td colspan="3" align="right">Összesen:</td>
      		<td align="center"><?=$total?> Ft</td>
      	</tr>
      </tbody>
    </table>
  </div>

  <a class="green-btn" href="<?=$settings['page_url']?>/elofoglalasok/?session=<?=$hash?>&a=<?=$mid?>">Előfoglalás megtekintése</a>
  <div class="clr"></div>

  <small>Előfoglalás azonosító kulcs: <em><?=$hash?></em></small>

<? require "footer.php"; ?>
