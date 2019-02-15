<div class="side-menu side-left">
  <div class="logged-as">
    Belépve, mint <br>
    <strong><?php echo $this->user['data']['nev']; ?></strong>
    <?php if ($this->user['data']['user_group'] == 'company' && !empty($this->user['data']['company_name'])): ?>
      <div class="company">
        <strong>(<?php echo $this->user['data']['company_name']; ?>)</strong>
      </div>
    <?php endif; ?>
    <div class="price-group">
      <?php echo $this->user['data']['price_group_title']; ?>
    </div>
  </div>
  <ul>
      <li class="<?=($this->gets[1] == '')?'active':''?>"><a href="/user/">Megrendeléseim</a></li>
      <li class="<?=($this->gets[1] == 'beallitasok')?'active':''?>"><a href="/user/beallitasok">Beállítások</a></li>
      <li class="<?=($this->gets[1] == 'jelszocsere')?'active':''?>"><a href="/user/jelszocsere">Jelszócsere</a></li>
      <li class="logout"><a href="/user/logout">Kijelentkezés <i class="fa fa-sign-out"></i> </a></li>
  </ul>
</div>
