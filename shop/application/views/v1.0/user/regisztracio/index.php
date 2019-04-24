<div class="account page-width">
    <div class="grid-layout">
        <div class="grid-row grid-row-20"><? $this->render('user/inc/account-offline', true); ?></div>
        <div class="grid-row grid-row-80">
          <?php if (isset($_GET['group'])): ?>
            <div class="">
              <h1><?=($_GET['group'] == 'company')?'Viszonteladói regisztráció':'Regisztráció'?></h1>
                  <div class="center">
                    <?php if ($_GET['group'] == 'company'): ?>
                      <?php if ($this->settings['account_reseller_description'] != ''): ?>
                        <div class="reseller-description">
                          <?php echo $this->settings['account_reseller_description']; ?>
                        </div>
                      <?php endif; ?>
                      <a href="/user/regisztracio?group=user">Magánszemélyként regisztrálok inkább >></a>
                    <?php else: ?>
                    <?php endif; ?>
                  </div>
                  <?=$this->msg?>
                  <br><br>
                  <form autocomplete="off" action="/user/regisztracio/<?=($_GET['group'] == 'company')?'?group=company':''?>" method="post" id="register" onsubmit="$('#registerBtn').click() return false;">
                  <input type="hidden" name="group" value="<?=$_GET['group']?>">
                  <div class="" style="padding:0;">
                      <div class="stack">
                          <h3>Alapadatok</h3>
                          <div>
                              <div class="row">
                                  <div class="col-md-6 col-pright"><label for="nev">Teljes név:</label><input required="required" type="text" id="nev" name="nev" value="<?=($this->msg)?$_POST['nev']:''?>" class="form-control"/></div>
                                  <div class="col-md-6 col-pleft"><label for="email">E-mail cím:</label><input required="required" type="text" id="email" name="email" value="<?=($this->msg)?$_POST['email']:''?>" class="form-control <?=($this->err == 1002)?'input-text-error':''?>" excCode="1002" /></div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6 col-pright"><label for="pw1">Jelszó</label><input required="required" type="password" id="pw1" name="pw1" class="form-control"/></div>
                                  <div class="col-md-6 col-pleft"><label for="pw2">Jelszó újra</label><input required type="password" id="pw2" name="pw2" class="form-control"/></div>
                              </div>
                          </div>
                      </div>
                      <? if($_GET['group'] == 'company'): ?>
                      <div class="stack">
                          <h3>Cégadatok megadása</h3>
                          <div>
                              <div class="row">
                                  <div class="col-md-6 col-pright"><label for="company_name">Cégnév:</label><input required="required" type="text" id="company_name" name="company[company_name]" value="<?=($this->msg)?$_POST['company']['company_name']:''?>" class="form-control <?=($this->err == 2001)?'input-text-error':''?>" excCode="2001"/></div>
                                  <div class="col-md-6 col-pleft"><label for="company_hq">Cég székhelye:</label><input required="required" type="text" id="company_hq" name="company[company_hq]" value="<?=($this->msg)?$_POST['company']['company_hq']:''?>" class="form-control <?=($this->err == 2002)?'input-text-error':''?>" excCode="2002" /></div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6 col-pright"><label for="company_adoszam">Adószám:</label><input required="required" type="text" id="company_adoszam" name="company[company_adoszam]" value="<?=($this->msg)?$_POST['company']['company_adoszam']:''?>" class="form-control <?=($this->err == 2003)?'input-text-error':''?>" excCode="2003"/></div>
                                  <div class="col-md-6 col-pleft"><label for="company_address">Cég postacím:</label><input required="required" type="text" id="company_address" name="company[company_address]" value="<?=($this->msg)?$_POST['company']['company_address']:''?>" class="form-control <?=($this->err == 2004)?'input-text-error':''?>" excCode="2004" /></div>
                              </div>
                              <div class="row">
                                  <div class="col-md-6 col-pright"><label for="company_bankszamlaszam">Bankszámlaszám:</label><input required="required" type="text" id="company_bankszamlaszam" name="company[company_bankszamlaszam]" value="<?=($this->msg)?$_POST['company']['company_bankszamlaszam']:''?>" class="form-control <?=($this->err == 2005)?'input-text-error':''?>" excCode="2005"/></div>
                              </div>
                          </div>
                      </div>
                      <? endif; ?>
                      <div class="stack">
                          <h3>Vásárláshoz szükséges adatok</h3>
                          <div>
                              <div class="row">
                                  <div class="col-md-6 col-pright">
                                      <h4>Számlázási adatok</h4>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Név</strong> *</div>
                                          <div class="col-md-8"><input required="required" type="text" id="szam_nev" name="szam_nev" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Irányítószám</strong> *</div>
                                          <div class="col-md-8"><input autocomplete="off" required="required" type="text" ng-keyup="findCityByIrsz($event, 'szam_city')" id="szam_irsz" name="szam_irsz" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Település</strong> *</div>
                                          <div class="col-md-8 hint-holder-col"><input required="required" placeholder="Irányítószám megadása..." readonly="readonly" type="text" id="szam_city" name="szam_city" class="form-control"/><div class="hint-holder" ng-show="findedCity['szam_city'] && findedCity['szam_city'].length != 0" id="szam_city">
                                            <div class="hint-list">
                                              <div class="cityhint" ng-click="fillCityHint('szam_city', city)" ng-repeat="city in findedCity['szam_city']">{{city.varos}} <span ng-show="city.megye" class="megye">({{city.megye}} megye)</span></div>
                                            </div>
                                          </div></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Kerület</strong></div>
                                          <div class="col-md-8"><input type="text" id="szam_kerulet" name="szam_kerulet" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Közterület neve</strong> *</div>
                                          <div class="col-md-8"><input required="required" type="text" id="szam_kozterulet_nev" name="szam_kozterulet_nev" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Közterület jellege</strong></div>
                                          <div class="col-md-8">
                                              <select name="szam_kozterulet_jelleg" class="form-control" id="szam_kozterulet_jelleg">
                                                  <option value="" selected="selected">-- válasszon --</option>
                                                  <? foreach( $this->kozterulet_jellege as $kj ): ?>
                                                  <option value="<?=$kj?>" <?=(isset($_POST['szam_kozterulet_jellege']) && $_POST['szam_kozterulet_jellege'] == $kj) ? 'selected="selected"' : ( ($kj == 'utca') ? 'selected="selected"':'' )?>><?=$kj?></option>
                                                  <? endforeach; ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Házszám</strong> *</div>
                                          <div class="col-md-8"><input required="required" type="text" id="szam_hazszam" name="szam_hazszam" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Épület</strong></div>
                                          <div class="col-md-8"><input type="text" id="szam_epulet" name="szam_epulet" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Lépcsőház</strong></div>
                                          <div class="col-md-8"><input type="text" id="szam_lepcsohaz" name="szam_lepcsohaz" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Szint</strong></div>
                                          <div class="col-md-8"><input type="text" id="szam_szint" name="szam_szint" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Ajtó</strong></div>
                                          <div class="col-md-8"><input type="text" id="szam_ajto" name="szam_ajto" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-12 form-text right" style="font-size:0.85em;"><a href="javascript:void(0);" id="copySzamToSzall">számlázási adatok másolása szállítási adatokhoz <i class="fa fa-arrow-right"></i> </a></div>
                                      </div>
                                  </div>
                                  <div class="col-md-6 col-pleft">
                                      <h4>Szállítási adatok</h4>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Név</strong> *</div>
                                          <div class="col-md-8"><input required="required" type="text" id="szall_nev" name="szall_nev" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Irányítószám</strong> *</div>
                                          <div class="col-md-8"><input autocomplete="off" required="required" type="text" id="szall_irsz" ng-keyup="findCityByIrsz($event, 'szall_city')" name="szall_irsz" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Település</strong> *</div>
                                          <div class="col-md-8 hint-holder-col"><input required="required" placeholder="Irányítószám megadása..." type="text" id="szall_city" readonly="readonly" name="szall_city" class="form-control"/><div class="hint-holder" ng-show="findedCity['szall_city'] && findedCity['szall_city'].length != 0" id="szall_city">
                                            <div class="hint-list">
                                              <div class="cityhint" ng-click="fillCityHint('szall_city', city)" ng-repeat="city in findedCity['szall_city']">{{city.varos}} <span ng-show="city.megye" class="megye">({{city.megye}} megye)</span></div>
                                            </div>
                                          </div></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Kerület</strong></div>
                                          <div class="col-md-8"><input type="text" id="szall_kerulet" name="szall_kerulet" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Közterület neve</strong> *</div>
                                          <div class="col-md-8"><input required="required" type="text" id="szall_kozterulet_nev" name="szall_kozterulet_nev" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Közterület jellege</strong></div>
                                          <div class="col-md-8">
                                              <select name="szall_kozterulet_jelleg" class="form-control" id="szall_kozterulet_jelleg">
                                                  <option value="" selected="selected">-- válasszon --</option>
                                                  <? foreach( $this->kozterulet_jellege as $kj ): ?>
                                                  <option value="<?=$kj?>" <?=(isset($_POST['szall_kozterulet_jelleg']) && $_POST['szall_kozterulet_jelleg'] == $kj) ? 'selected="selected"' : ( ($kj == 'utca') ? 'selected="selected"':'' )?>><?=$kj?></option>
                                                  <? endforeach; ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Házszám</strong> *</div>
                                          <div class="col-md-8"><input required="required" type="text" id="szall_hazszam" name="szall_hazszam" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Épület</strong></div>
                                          <div class="col-md-8"><input type="text" id="szall_epulet" name="szall_epulet" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Lépcsőház</strong></div>
                                          <div class="col-md-8"><input type="text" id="szall_lepcsohaz" name="szall_lepcsohaz" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Szint</strong></div>
                                          <div class="col-md-8"><input type="text" id="szall_szint" name="szall_szint" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                          <div class="col-md-4 form-text"><strong>Ajtó</strong></div>
                                          <div class="col-md-8"><input type="text" id="szall_ajto" name="szall_ajto" class="form-control"/></div>
                                      </div>
                                      <div class="divider-sm"></div>
                                      <div class="row">
                                        <div class="col-md-4 form-text"><strong>Körzetszám</strong></div>
                                        <div class="col-md-2"><input required="required" type="text" id="szall_phone_korzet" autocomplete="off" name="szall_phone_korzet" class="form-control"/></div>
                                        <div class="col-md-2 form-text"><strong>Telefonszám</strong></div>
                                        <div class="col-md-4"><input required="required" type="text" id="szall_phone" autocomplete="off" name="szall_phone" class="form-control"/></div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="stack">
                          <div class="row">
                              <div class="col-md-8 form-btn" style="line-height:42px;font-size: 0.8em;"><input required="required" type="checkbox" id="aszfOk" name="aszfOk" /> <label for="aszfOk">Elolvastam és tudomásul vettem az <a href="<?=$this->settings['ASZF_URL']?>" target="_blank">ÁSZF</a>-ben és az <a href="/p/adatvedelmi-tajekoztato">Adatvédelmi tájékoztató</a>ban foglaltakat!</label></div>
                              <div class="col-md-4" align="right">
                                  <button name="registerUser" value="1" class="btn btn-pr">Regisztráció <i class="fa fa-arrow-circle-right"></i></button>
                              </div>
                          </div>
                      </div>
                  </div>
                  </form>
              </div>
          <?php elseif($_GET['successreg'] == 1): ?>
            <div class="success-reg-msg">
              <h1><i class="fa fa-check-circle-o"></i><br>Sikeresen rögzítette regisztrációját!</h1>
              <p><?php echo $_GET['msg']; ?></p>
            </div>
          <?php else: ?>
            <div class="group-selector">
              <h1>Ön hogy szeretne regisztrálni?</h1>
              <div class="groups">
                <div class="">
                  <a href="/user/regisztracio?group=user">Magánszemélyként</a>
                </div>
                <div class="">
                  <a href="/user/regisztracio?group=company">Cégként</a>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $('#copySzamToSzall').click(function(){
            $('#register input[name^=szam_]').each(function(){
                var e = $(this).attr('name');
                 $('#register input[name=szall_'+e.replace('szam_','')+']').val($(this).val());
            });

            $('#register select#szall_kozterulet_jelleg option[value="'+$('#register select#szam_kozterulet_jelleg').val()+'"]').prop('selected', true);
        });
    })
</script>
