<div class="nextOrded cart-elofoglalas">
  <h2 class="title">Termékek előfoglalása</h2>
  <div class="desc">
    Az előfoglalással Ön lefoglalhatja a kosárba tett termékeket anélkül, hogy megvásárolná. A lefoglalt termékeket <?=$this->settings['elofoglalas_ora']?> óráig tartjuk fent Önnek!
  </div>
  <div class="info-box">
    <div class="ico"><i class="fa fa-info"></i></div>
    <div class="text">
      Az előfoglalást kérjük, hogy akkor használja, ha meg szeretné vásárolni a termékeket, de nincs most ideje végig menni a megrendelés folyamatán!
    </div>
  </div>
  <div class="datas">
    <h2>Foglalási adatok megadása</h2>
    <div class="input-line">
      <label class="ilb" for="preorder_name">Az Ön neve *</label>
      <input type="text" id="preorder_name" name="preorder[name]" value="<?=(isset($_POST['preorder']['name'])) ? $_POST['preorder']['name'] : $this->user['data']['nev']?>" class="form-control" required="required">
    </div>
    <div class="input-line">
      <label class="ilb" for="preorder_email">Az Ön e-mail címe *</label>
      <input type="email" id="preorder_email" name="preorder[email]" value="<?=(isset($_POST['preorder']['email'])) ? $_POST['preorder']['email'] : $this->user['data']['email']?>" class="form-control" required="required">
    </div>
    <div class="input-line">
      <div class="lb-check">
        <input type="checkbox" name="preorder[go]" id="preorder_go" required="required"> <label for="preorder_go">Igen, le szeretném foglalni a kosárban található termékeket max. <?=$this->settings['elofoglalas_ora']?> órára.</label>
      </div>
      <div class="lb-check">
        <input type="checkbox" name="preorder[aszf]" id="preorder_aszf" required="required"> <label for="preorder_aszf">Elfogadom és egyetértek az <a href="/p/aszf" target="_blank">Általános Szerződési Feltételek</a>kel és <a href="/p/adatvedelmi-tajekoztato" target="_blank">Adatvédelmi Tájékoztató</a>val!</label>
      </div>
    </div>
    <div class="input-line">
      <button type="submit" value="1" name="doPreorder">Előfoglalás rögzítése</button> <a class="back-order" href="/kosar/">vissza a megrendeléshez <i class="fa fa-arrow-right"></i></a>
    </div>
  </div>
</div>
