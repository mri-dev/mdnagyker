<? require "head.php"; ?>
<h1>Termék kérdés érkezett!</h1>
<strong>Tájékoztatjuk, hogy a(z) <?=$settings[page_title]?> rendszerén keresztül <u><?=$targy?></u> érkezett.</strong>

<h3>Az igénylő által beírt adatok:</h3>
<table class="if">
  <tr>
    <th><strong>Név:</strong></th>
    <td><?=$name?></td>
  </tr>
  <tr>
    <th><strong>Telefonszám:</strong></th>
    <td><?=$phone?></td>
  </tr>
  <tr>
    <th><strong>E-mail cím:</strong></th>
    <td><?=$email?></td>
  </tr>
  <?php if ($message != ''): ?>
  <tr>
    <th><strong>Üzenete / Kérdése:</strong></th>
    <td><?=$message?></td>
  </tr>
  <?php endif; ?>
</table>

<?=$termek?>

<p>A kérés <strong><?=NOW?></strong> időponttal lett rögzítve a rendszerbe.</p>

<p style="color: red;">
  A kérésre válaszolhat az adminisztrációs felületen keresztül a beérkező üzeneteknél, melyet az Üzenetek menüpont alatt talál meg:
</p>
<p><a href="<?=$settings['admin_url']?>/uzenetek"><?=$settings['admin_url']?>/uzenetek</a></p>

<? require "footer.php"; ?>
