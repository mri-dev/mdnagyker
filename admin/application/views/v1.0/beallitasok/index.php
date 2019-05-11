<script type="text/javascript">
	$(function(){
		$('.settings-change input[type=checkbox]').click( function(){

            var by  = $(this).attr('key');
            var v   = $(this).is(':checked');
            v = (v) ? 1 : 0;

            $('#'+by+'_response').stop().html('<strong style="color:red;">folyamatban...</strong>');

            $.post('<?=AJAX_POST?>',{
                type: 'changeSettings',
                key : by,
                val : v
            }, function(d){
                $('#'+by+'_response').stop().html('<strong style="color:green;">Mentve!</strong>');
                setTimeout(function(){
                   $('#'+by+'_response').stop().html('');
                }, 4500);
            }, "html");
        });

	})
     $(function(){

    })

    function responsive_filemanager_callback(field_id){
        var imgurl = $('#'+field_id).val();
        $('#logo_preview').attr('src',imgurl);
    }
</script>
<h1>Beállítások</h1>
<br><br>
<div class="settings-change">
    <a name="admins"></a>
    <? if( $this->err && $this->bmsg['admin'] ): ?>
        <?=$this->bmsg['admin']?>
    <? endif; ?>
    <div class="row np">
        <div class="col-md-4" style="padding-right:8px;">
            <form action="#admins" method="post">
                <div class="con <?=($this->gets[1] == 'admin_torles' ? 'con-del' : ($this->gets[1] == 'admin_szerkesztes'?'con-edit':''))?>">
                    <h2><?=($this->gets[1] == 'admin_torles' ? 'Adminisztrátor törlése' : ($this->gets[1] == 'admin_szerkesztes'?'Adminisztrátor szerkesztése':'Új Adminisztrátor'))?></h2>

                    <? if($this->gets[1] == 'admin_torles'): ?>
                        Biztos benne, hogy törli a(z) <strong><u><?=$this->admin->getUsername()?></u></strong> azonosítójú adminisztrátort? A művelet nem visszavonható!

                        <div class="row np">
                            <div class="col-md-12 right">
                                <a href="/beallitasok/#admins" class="btn btn-danger"><i class="fa fa-times"></i> Mégse</a>
                                <button name="delAdmin" value="1" class="btn btn-success">Igen, véglegesen törlöm <i class="fa fa-check"></i></button>
                            </div>
                        </div>
                    <? else: ?>
                    <div class="row np">
                        <div class="col-md-12">
                            <label for="admin_user">Belépő azonosító*</label>
                            <input type="text" id="admin_user" name="admin_user" value="<?= ( $this->err ? $_POST['admin_user'] : ($this->admin ? $this->admin->getUsername():'') ) ?>" class="form-control">
                        </div>
                    </div>
                    <br>
                    <div class="row np">
                        <div class="col-md-6" style="padding-right:5px;">
                            <label for="admin_pw1"><?=($this->gets[1] == 'admin_szerkesztes')?'Jelszó csere':'Jelszó*'?></label>
                            <input type="password" id="admin_pw1" name="admin_pw1" value="" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="admin_pw2"><?=($this->gets[1] == 'admin_szerkesztes')?'Jelszó csere (megerősít)':'Jelszó újra*'?></label>
                            <input type="password" id="admin_pw2" name="admin_pw2" value="" class="form-control">
                        </div>
                    </div>
                    <br>
                    <div class="row np">
                        <div class="col-md-4" style="padding-right:5px;">
                            <label for="admin_status">Engedélyezve<sup>1</sup></label>
                            <select name="admin_status" id="admin_status" class="form-control">
                                <option value="1" selected="selected">Igen</option>
                                <option value="0" <?=($this->admin && !$this->admin->getStatus() ? 'selected="selected"' : '')?>>Nem</option>
                            </select>
                         </div>
                         <div class="col-md-8">
                            <label for="admin_jog">Jogosultság<sup>2</sup></label>
                            <select name="admin_jog" id="admin_jog" class="form-control">
                                <option value="1" selected="selected">Adminisztrátor</option>
                                <option value="<?=\PortalManager\Admin::SUPER_ADMIN_PRIV_INDEX?>" <?=($this->gets[1] == 'admin_szerkesztes' && $this->admin->getPrivIndex() == \PortalManager\Admin::SUPER_ADMIN_PRIV_INDEX ? 'selected="selected"' : '')?>>Szuper Adminisztrátor</option>
                            </select>
                         </div>
                         <div class="row np">
                             <div class="col-md-12">
                                <em>
                                  <div class="info"><sup>1</sup>: az engedélyezett adminisztrátorok tudnak csak bejelentkezni!</div>
                                  <div class="info"><sup>2</sup>: <strong>Szuper Adminisztrátor</strong> jogosultsággal lehet a beállításokat megváltoztatni.</div>
                                </em>
                             </div>
                         </div>
                    </div>
                    <br>
                    <div class="row np">
                        <div class="col-md-12 right">
                             <? if($this->gets[1] == 'admin_szerkesztes'): ?>
                             <a href="/beallitasok/#admins" class="btn btn-danger"><i class="fa fa-times"></i> mégse</a>
                             <button name="saveAdmin" value="1" class="btn btn-success">Változások mentése <i class="fa fa-save"></i></button>
                             <? else: ?>
                             <button name="addAdmin" value="1" class="btn btn-primary">Létrehozás <i class="fa fa-plus"></i></button>
                             <? endif; ?>
                        </div>
                    </div>
                    <? endif; ?>
                </div>
            </form>
        </div>
        <div class="col-md-8">
             <div class="con">
                <h2>Adminisztrátorok</h2>
                <div class="info">Egy adott azonosítóval csak egy eszközről/böngészőből lehet bejelentkezni. Ha ugyan azzal az azonosítóval belépünk egy másik eszközön, minden más eszközről/böngészőből kiléptet a rendszer.</div>
                <table class="table termeklista table-bordered">
                    <thead>
                        <tr>
                            <th>Azonosító</th>
                            <th width="150">Jogosultság</th>
                            <th width="120">Utoljára aktív</th>
                            <th width="120">Utoljára belépett</th>
                            <th width="80">Engedélyezve</th>
                            <th width="50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach($this->admins as $admin):?>
                        <tr>
                            <td><strong><a title="szerkesztés" href="/beallitasok/admin_szerkesztes/<?=$admin['ID']?>#admins"><?=$admin['user']?></a></strong></td>
                            <td class="center"><?=($admin['jog'] == \PortalManager\Admin::SUPER_ADMIN_PRIV_INDEX)? 'Szuper Adminisztrátor':'Adminisztrátor'?></td>
                            <td class="center"><?=\PortalManager\Formater::distanceDate($admin['utolso_aktivitas'])?></td>
                            <td class="center"><?=\PortalManager\Formater::dateFormat($admin['utoljara_belepett'], $this->settings['date_format'])?></td>
                            <td class="center"><?=($admin['engedelyezve'] == 1)?'<span class="color-allow">Igen</span>':'<span class="color-disallow">Nem</span>'?></td>
                            <td class="actions center">
                                <a href="/beallitasok/admin_torles/<?=$admin['ID']?>#admins" title="Törlés"><i class="fa fa-times"></i></a>
                            </td>
                        </tr>
                        <? endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>
    <a name="basics"></a>
    <? if( $this->err && $this->bmsg['basics'] ): ?>
        <?=$this->bmsg['basics']?>
    <? endif; ?>
    <div class="row np">
        <form action="#basics" method="post">
            <div class="con">
                <h2>Változók</h2>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_page_title">Weboldal főcíme</label>
                        <input type="text" id="basics_page_title" name="page_title" class="form-control" value="<?=$this->settings['page_title']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_page_description">Weboldal alcíme</label>
                        <input type="text" id="basics_page_description" name="page_description" class="form-control" value="<?=$this->settings['page_description']?>">
                    </div>
                </div>
								<br>
								<div class="row np">
                    <div class="col-md-12">
                        <label for="basics_about_us">Weboldal bemutatkozás szövege</label>
                        <textarea name="about_us" id="basics_about_us" class="form-control no-editor" style="max-width: 100%;"><?=$this->settings['about_us']?></textarea>
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-5">
                        <label for="basics_logo">Alapértelmezz logó</label>
                        <div class="input-group">
                            <input type="text" id="basics_logo" name="logo" class="form-control" value="<?=$this->settings['logo']?>">
                            <div class="input-group-addon"><a title="Kép kiválasztása a galériából" href="<?=FILE_BROWSER_IMAGE?>&field_id=basics_logo" data-fancybox-type="iframe" class="iframe-btn" ><i class="fa fa-link"></i></a></div>
                        </div>
                        <div style="margin-top: 5px;
    background: #c5171e;
    padding: 10px;
    float: left;">
                            <img src="<?=$this->settings['logo']?>" id="logo_preview" alt="" style="max-width:180px;">
                        </div>
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_date_format">Dátum formátum</label>
                        <input type="text" id="basics_date_format" name="date_format" class="form-control" value="<?=$this->settings['date_format']?>">
                        <div class="info"><em><a href="http://php.net/manual/en/function.date.php" target="_blank">Dátum formátum struktúra (php.net)</a></em></div>
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_google_analitics"><i class="fa fa-pie-chart"></i> Google Analitics követőkód</label>
                        <textarea type="text" id="basics_google_analitics" name="google_analitics" class="form-control no-editor"><?=$this->settings['google_analitics']?></textarea>
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_recaptcha_private_key"><a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">reCaptcha</a> PRIVATE kulcs</label>
                        <input type="text" id="basics_recaptcha_private_key" name="recaptcha_private_key" class="form-control" value="<?=$this->settings['recaptcha_private_key']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_recaptcha_public_key"><a target="_blank" href="https://www.google.com/recaptcha/intro/index.html">reCaptcha</a> PUBLIC kulcs</label>
                        <input type="text" id="basics_recaptcha_public_key" name="recaptcha_public_key" class="form-control" value="<?=$this->settings['recaptcha_public_key']?>">
                    </div>
                </div>
                <br>
                <div class="divider"></div>
                <br>
                <h3>Tulajdon adatok</h3>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_page_author">Weboldal tulajdonos</label>
                        <input type="text" id="basics_page_author" name="page_author" class="form-control" value="<?=$this->settings['page_author']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_page_url">Weboldal elérhetősége</label>
                        <input type="text" id="basics_page_url" name="page_url" class="form-control" value="<?=$this->settings['page_url']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_page_author_phone">Központi telefonszám</label>
                        <input type="text" id="basics_page_author_phone" name="page_author_phone" class="form-control" value="<?=$this->settings['page_author_phone']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_mobile_number">Mobil telefonszám</label>
                        <input type="text" id="basics_mobile_number" name="mobile_number" class="form-control" value="<?=$this->settings['mobile_number']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_mobile_number_elerhetoseg">Mobil telefonszám elérhetőség</label>
                        <input type="text" id="basics_mobile_number_elerhetoseg" name="mobile_number_elerhetoseg" class="form-control" value="<?=$this->settings['mobile_number_elerhetoseg']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_page_author_address">Telephely</label>
                        <input type="text" id="basics_page_author_address" name="page_author_address" class="form-control" value="<?=$this->settings['page_author_address']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_primary_email">Elsődleges e-mail cím</label>
                        <input type="text" id="basics_primary_email" name="primary_email" class="form-control" value="<?=$this->settings['primary_email']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_alert_email">Adminisztratív e-mail cím, értesítő leveleknek <?=\PortalManager\Formater::tooltip('A rendszer erre az e-mail címre fogja kiküldeni a fontosabb értesítő e-mail üzeneteket. Pl.: új megrendelés, új üzenet, stb...')?></label>
                        <input type="text" id="basics_alert_email" name="alert_email" class="form-control" value="<?=(is_array($this->settings['alert_email'])) ? implode($this->settings['alert_email'],",") : $this->settings['alert_email']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_reply_email">Válasz e-mail cím</label>
                        <input type="text" id="basics_reply_email" name="reply_email" class="form-control" value="<?=$this->settings['reply_email']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_office_email">Iroda e-mail cím</label>
                        <input type="text" id="basics_office_email" name="office_email" class="form-control" value="<?=$this->settings['office_email']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_email_noreply_address">Inaktív (no-reply) e-mail cím, értesítő levelek válaszcímének</label>
                        <input type="text" id="basics_email_noreply_address" name="email_noreply_address" class="form-control" value="<?=$this->settings['email_noreply_address']?>">
                    </div>
                </div>
                <br>
                <div class="divider"></div>
                <br>
                <h3>Social</h3>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_social_facebook_link"><i class="fa fa-facebook-official"></i> Social - Facebook fiók link</label>
                        <input type="text" id="basics_social_facebook_link" name="social_facebook_link" class="form-control" value="<?=$this->settings['social_facebook_link']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_social_googleplus_link"><i class="fa fa-google-plus-square"></i> Social - Google+ link</label>
                        <input type="text" id="basics_social_googleplus_link" name="social_googleplus_link" class="form-control" value="<?=$this->settings['social_googleplus_link']?>">
                    </div>
                </div>

                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_social_youtube_link"><i class="fa fa-youtube"></i> Social - Youtube csatorna link</label>
                        <input type="text" id="basics_social_youtube_link" name="social_youtube_link" class="form-control" value="<?=$this->settings['social_youtube_link']?>">
                    </div>
                </div>

                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_social_twitter_link"><i class="fa fa-twitter"></i> Social - Twitter fiók link</label>
                        <input type="text" id="basics_social_twitter_link" name="social_twitter_link" class="form-control" value="<?=$this->settings['social_twitter_link']?>">
                    </div>
                </div>
                <br>
                <div class="divider"></div>
                <br>
                <h3>Linkek</h3>
                 <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_tudastar_url">TUDÁSTÁR elérhetősége</label>
                        <input type="text" id="basics_tudastar_url" name="tudastar_url" class="form-control" value="<?=$this->settings['tudastar_url']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_ASZF_URL">ÁSZF elérhetősége</label>
                        <input type="text" id="basics_ASZF_URL" name="ASZF_URL" class="form-control" value="<?=$this->settings['ASZF_URL']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_contact_url">"Kapcsolat" oldal elérhetősége</label>
                        <input type="text" id="basics_contact_url" name="contact_url" class="form-control" value="<?=$this->settings['contact_url']?>">
                    </div>
                </div>

                 <br>
                <div class="divider"></div>
                <br>
                <h3>Webáruház</h3>
								<div class="row np">
										<div class="col-md-3">
												<label for="basics_tuzvedo_order_pretext_wanted">Megrendelés előtti Szerződéses Feltétel elfogadása <?=\PortalManager\Formater::tooltip('A megrendelés összegzőnél a megrendelés elküldése előtt felugró ablak, ahol kötelezően el kell fogadni a meghatározott speciális feltételeket. Az elfogadás után lehet csak a megrendelést leadni.')?></label>
												<select name="tuzvedo_order_pretext_wanted" id="basics_tuzvedo_order_pretext_wanted" class="form-control">
														<option value="0" <?=($this->settings['tuzvedo_order_pretext_wanted'] == '0' ? 'selected="selected"' : '')?>>Nem</option>
														<option value="1" <?=($this->settings['tuzvedo_order_pretext_wanted'] == '1' ? 'selected="selected"' : '')?>>Igen</option>
												</select>
												<br>
												<label for="basics_tuzvedo_order_pretext_title">Felugró ablak fejéc szövege</label>
                        <input type="text" id="basics_tuzvedo_order_pretext_title" name="tuzvedo_order_pretext_title" class="form-control" value="<?=$this->settings['tuzvedo_order_pretext_title']?>">
										</div>
										<div class="col-md-9" style="padding-left: 35px;">
											<label for="basics_tuzvedo_order_pretext">Speciális feltételek szövege</label>
											<textarea type="text" id="basics_tuzvedo_order_pretext" name="tuzvedo_order_pretext" class="form-control"><?=$this->settings['tuzvedo_order_pretext']?></textarea>
										</div>
								</div>
								<br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_account_reseller_description">Viszonteladói regisztráció ismertető szöveg</label>
                        <textarea type="text" id="basics_account_reseller_description" name="account_reseller_description" class="form-control"><?=$this->settings['account_reseller_description']?></textarea>
                    </div>
                </div>
								<br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_round_price_5">Árak kerekítése 5-tel osztható számokra</label>
                        <select name="round_price_5" id="basics_round_price_5" class="form-control">
                            <option value="0" <?=($this->settings['round_price_5'] == '0' ? 'selected="selected"' : '')?>>Nem</option>
                            <option value="1" <?=($this->settings['round_price_5'] == '1' ? 'selected="selected"' : '')?>>Igen</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_stock_withdrawal">Készlet kivonás</label>
                        <select name="stock_withdrawal" id="basics_stock_withdrawal" class="form-control">
                            <option value="0" <?=($this->settings['stock_withdrawal'] == '0' ? 'selected="selected"' : '')?>>Nem</option>
                            <option value="1" <?=($this->settings['stock_withdrawal'] == '1' ? 'selected="selected"' : '')?>>Igen</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_order_min_price">Minimális rendelés érték (Forint)</label>
                        <input type="text" id="basics_order_min_price" name="order_min_price" class="form-control" value="<?=$this->settings['order_min_price']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_banktransfer_author">Banki adat - Tulajdonos</label>
                        <input type="text" id="basics_banktransfer_author" name="banktransfer_author" class="form-control" value="<?=$this->settings['banktransfer_author']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_banktransfer_number">Banki adat - Számlaszám</label>
                        <input type="text" id="basics_banktransfer_number" name="banktransfer_number" class="form-control" value="<?=$this->settings['banktransfer_number']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_banktransfer_bank">Banki adat - Bank neve</label>
                        <input type="text" id="basics_banktransfer_bank" name="banktransfer_bank" class="form-control" value="<?=$this->settings['banktransfer_bank']?>">
                    </div>
                </div>

                <br>
                <div class="divider"></div>
                <br>

								<h3>Készlethiány beállítások</h3>
                <div class="row np">
                    <div class="col-md-3">
											<label for="basics_stock_outselling">Készlet nélküli továbbértékesítés <?=\PortalManager\Formater::tooltip('A termékek továbbra is vásárolhatóak, ha elfogy a készletről.')?></label>
											<select name="stock_outselling" id="basics_stock_outselling" class="form-control">
													<option value="0" <?=($this->settings['stock_outselling'] == '0' ? 'selected="selected"' : '')?>>Nem</option>
													<option value="1" <?=($this->settings['stock_outselling'] == '1' ? 'selected="selected"' : '')?>>Igen</option>
											</select>
                    </div>
										<div class="col-md-3" style="padding-left:8px;">
											<label for="basics_stock_outselling_status">Termék állapot felülírás: <u>elfogyás esetén</u> <?=\PortalManager\Formater::tooltip('A terméknél beállított állapotot erre állítja be, ha elfogyott a készlet.')?></label>
											<select name="stock_outselling_status" id="basics_stock_outselling_status" class="form-control">
													<option value="" <?=($this->settings['stock_outselling_status'] == 0)?'selected="selected"':''?>>Ne írja felül</option>
													<option value="" disabled="disabled"></option>
													<? foreach( $this->termek_allapotok as $d ): ?>
													<option value="<?=$d['ID']?>" <?=($this->settings['stock_outselling_status'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
													<? endforeach; ?>
											</select>
										</div>
										<div class="col-md-3" style="padding-left:8px;">
											<label for="basics_stock_outselling_status_off">Termék állapot felülírás: <u>ha nincs továbbértékesítés</u> <?=\PortalManager\Formater::tooltip('A terméknél beállított állapotot erre állítja be, ha elfogyott a készlet és nincs továbbértékesítés.')?></label>
											<select name="stock_outselling_status_off" id="basics_stock_outselling_status_off" class="form-control">
													<option value="" <?=($this->settings['stock_outselling_status_off'] == 0)?'selected="selected"':''?>>Ne írja felül</option>
													<option value="" disabled="disabled"></option>
													<? foreach( $this->termek_allapotok as $d ): ?>
													<option value="<?=$d['ID']?>" <?=($this->settings['stock_outselling_status_off'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
													<? endforeach; ?>
											</select>
										</div>
                </div>
								<br>
								<div class="row">
									<div class="col-md-3 col-md-offset-3" style="padding-left:8px;">
										<label for="basics_stock_outselling_transport">Termék szállítási idő felülírás: <u>elfogyás esetén</u> <?=\PortalManager\Formater::tooltip('A terméknél beállított szállítási időt erre állítja be, ha elfogyott a készlet.')?></label>
										<select name="stock_outselling_transport" id="basics_stock_outselling_transport" class="form-control">
												<option value="" <?=($this->settings['stock_outselling_transport'] == 0)?'selected="selected"':''?>>Ne írja felül</option>
												<option value="" disabled="disabled"></option>
												<? foreach( $this->szallitasi_idok as $d ): ?>
												<option value="<?=$d['ID']?>" <?=($this->settings['stock_outselling_transport'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
												<? endforeach; ?>
										</select>
									</div>
									<div class="col-md-3" style="padding-left:8px;">
										<label for="basics_stock_outselling_transport_off">Termék szállítási idő felülírás: <u>ha nincs továbbértékesítés</u> <?=\PortalManager\Formater::tooltip('A terméknél beállított szállítási időt erre állítja be, ha elfogyott a készlet és nincs továbbértékesítés.')?></label>
										<select name="stock_outselling_transport_off" id="basics_stock_outselling_transport_off" class="form-control">
												<option value="" <?=($this->settings['stock_outselling_transport_off'] == 0)?'selected="selected"':''?>>Ne írja felül</option>
												<option value="" disabled="disabled"></option>
												<? foreach( $this->szallitasi_idok as $d ): ?>
												<option value="<?=$d['ID']?>" <?=($this->settings['stock_outselling_transport_off'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
												<? endforeach; ?>
										</select>
									</div>
								</div>

								<?php if (true): ?>
								<br>
                <h3>OTP Simple Kártyás fizetés</h3>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_payu_merchant">OTP Simple MERCHANT</label>
                        <input type="text" id="basics_payu_merchant" name="payu_merchant" class="form-control" value="<?=$this->settings['payu_merchant']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_payu_secret">OTP Simple SECRET</label>
                        <input type="text" id="basics_payu_secret" name="payu_secret" class="form-control" value="<?=$this->settings['payu_secret']?>">
                    </div>
                </div>
								<?php endif; ?>

								<?php if (true): ?>
								<br>
                <h3>BORGUN Kártyás fizetés</h3>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_borgun_setting_merchant">MERCHANT ID</label>
                        <input type="text" id="basics_borgun_setting_merchant" name="borgun_setting_merchant" class="form-control" value="<?=$this->settings['borgun_setting_merchant']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_borgun_setting_secret">SECRET KEY</label>
                        <input type="text" id="basics_borgun_setting_secret" name="borgun_setting_secret" class="form-control" value="<?=$this->settings['borgun_setting_secret']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_borgun_setting_gatewayid">PAYMENT GATEWAY ID</label>
                        <input type="text" id="basics_borgun_setting_gatewayid" name="borgun_setting_gatewayid" class="form-control" value="<?=$this->settings['borgun_setting_gatewayid']?>">
                    </div>
                </div>
								<?php endif; ?>

								<?php if (false): ?>
                <br>
                <div class="divider"></div>
                <br>
                <h3>Cetelem Áruhitel</h3>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_cetelem_shopcode">Shop Code (Boltkód)</label>
                        <input type="text" id="basics_cetelem_shopcode" name="cetelem_shopcode" class="form-control" value="<?=$this->settings['cetelem_shopcode']?>">
                    </div>
                </div>
                <br>
                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_cetelem_society">Society</label>
                        <input type="text" id="basics_cetelem_society" name="cetelem_society" class="form-control" value="<?=$this->settings['cetelem_society']?>">
                    </div>
                </div>
                <br>

                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_cetelem_username">Felhasználónév</label>
                        <input type="text" id="basics_cetelem_username" name="cetelem_username" class="form-control" value="<?=$this->settings['cetelem_username']?>">
                    </div>
                </div>
                <br>

                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_cetelem_userpassword">Felhasználó jelszó</label>
                        <input type="text" id="basics_cetelem_userpassword" name="cetelem_userpassword" class="form-control" value="<?=$this->settings['cetelem_userpassword']?>">
                    </div>
                </div>
                <br>

                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_cetelem_barem">Barem</label>
                        <input type="text" id="basics_cetelem_barem" name="cetelem_barem" class="form-control" value="<?=$this->settings['cetelem_barem']?>">
                    </div>
                </div>
                <br>

                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_cetelem_min_product_price">Minimális vásárlási limit (Ft)</label>
                        <input type="text" id="basics_cetelem_min_product_price" name="cetelem_min_product_price" class="form-control" value="<?=$this->settings['cetelem_min_product_price']?>">
                    </div>
                </div>
                <br>

                <div class="row np">
                    <div class="col-md-12">
                        <label for="basics_cetelem_max_product_price">Maximális vásárlási limit (Ft)</label>
                        <input type="text" id="basics_cetelem_max_product_price" name="cetelem_max_product_price" class="form-control" value="<?=$this->settings['cetelem_max_product_price']?>">
                    </div>
                </div>
                <br>
								<?php endif; ?>
								<br>
                <div class="divider"></div>
                <br>
                <h3>Megrendelés kulcsok</h3>
                <div class="row np">
                    <div class="col-md-3">
                        <label for="basics_flagkey_orderstatus_done"><u>Teljesített</u> megrendelés végpont</label>
                        <select name="flagkey_orderstatus_done" id="basics_flagkey_orderstatus_done" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->orderstatus as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_orderstatus_done'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_flagkey_orderstatus_delete"><u>Törölt</u> megrendelés végpont</label>
                        <select name="flagkey_orderstatus_delete" id="basics_flagkey_orderstatus_delete" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->orderstatus as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_orderstatus_delete'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
										<?php if (true): ?>
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_flagkey_invoice_orderstatus"><u>Cashman FX</u> számla generálás megrendelés állapot</label>
                        <select name="flagkey_invoice_orderstatus" id="basics_flagkey_invoice_orderstatus" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->orderstatus as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_invoice_orderstatus'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
										<?php endif; ?>
                </div>

								<?php if (false): ?>
                <br>
                <div class="divider"></div>
                <br>
                <h3>Szállítási kulcsok</h3>
                <div class="row np">
                    <div class="col-md-3">
                        <label for="basics_flagkey_pickpacktransfer_id"><u>Pick Pack Pont</u> szállítási kulcs ID</label>
                        <select name="flagkey_pickpacktransfer_id" id="basics_flagkey_pickpacktransfer_id" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->szallitas as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_pickpacktransfer_id'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                </div>
								<?php endif; ?>

                <br>
                <div class="divider"></div>
                <br>
                <h3>Fizetési kulcsok</h3>
                <div class="row np">
										<?php if (false): ?>
										<div class="col-md-3">
                        <label for="basics_flagkey_pay_payu"><u>Simple</u> fizetési kulcs ID</label>
                        <select name="flagkey_flagkey_pay_payu" id="basics_flagkey_pay_payu" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->fizetes as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_pay_payu'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
										<?php endif; ?>
										<?php if (false): ?>
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_flagkey_pay_cetelem"><u>Cetelem</u> fizetési kulcs ID</label>
                        <select name="flagkey_pay_cetelem" id="basics_flagkey_pay_cetelem" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->fizetes as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_pay_cetelem'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
										<?php endif; ?>
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_flagkey_pay_banktransfer"><u>Átutalás (bank)</u> fizetési kulcs ID</label>
                        <select name="flagkey_pay_banktransfer" id="basics_flagkey_pay_banktransfer" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->fizetes as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_pay_banktransfer'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_flagkey_pay_card_borgun"><u>BORGUN bannkártyás</u> fizetési kulcs ID</label>
                        <select name="flagkey_pay_card_borgun" id="basics_flagkey_pay_card_borgun" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->fizetes as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_pay_card_borgun'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_flagkey_pay_card_simple"><u>OTP Simple</u> fizetési kulcs ID</label>
                        <select name="flagkey_pay_card_simple" id="basics_flagkey_pay_card_simple" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->fizetes as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_pay_card_simple'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_flagkey_pay_ontransfer_id"><u>Utánvétel </u> fizetési kulcs ID</label>
                        <select name="fflagkey_pay_ontransfer_id" id="basics_flagkey_pay_ontransfer_id" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->fizetes as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_pay_ontransfer_id'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>


                </div>
								<?php if (false): ?>
                <br>
                <div class="divider"></div>
                <br>
                <h3>Termék importálás</h3>
                <div class="row np">
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_alapertelmezett_marka">Alapértelmezett <u>márka</u></label>
                        <select name="alapertelmezett_marka" id="basics_alapertelmezett_marka" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->markak as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['alapertelmezett_marka'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_alapertelmezett_termek_allapot">Alapértelmezett <u>termék állapot</u></label>
                        <select name="alapertelmezett_termek_allapot" id="basics_alapertelmezett_termek_allapot" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->termek_allapotok as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['alapertelmezett_termek_allapot'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_alapertelmezett_termek_szallitas">Alapértelmezett <u>szállítási idő</u></label>
                        <select name="alapertelmezett_termek_szallitas" id="basics_alapertelmezett_termek_szallitas" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->szallitasi_idok as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['alapertelmezett_termek_szallitas'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_flagkey_itemstatus_outofstock"><u>ELFOGYOTT</u> termék állapot kulcs</label>
                        <select name="flagkey_itemstatus_outofstock" id="basics_flagkey_itemstatus_outofstock" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->termek_allapotok as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_itemstatus_outofstock'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3" style="padding-left:8px;">
                        <label for="basics_flagkey_itemstatus_instock"><u>RAKTÁRON</u> termék állapot kulcs</label>
                        <select name="flagkey_itemstatus_instock" id="basics_flagkey_itemstatus_instock" class="form-control">
                            <option value="">-- válasszon --</option>
                            <option value="" disabled="disabled"></option>
                            <? foreach( $this->termek_allapotok as $d ): ?>
                            <option value="<?=$d['ID']?>" <?=($this->settings['flagkey_itemstatus_instock'] == $d['ID'])?'selected="selected"':''?>><?=$d['nev']?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                </div>
								<?php endif; ?>
                <br>
                <div class="divider"></div>
                <br>
                <div class="row np">
                    <div class="col-md-12 right">
                        <button name="saveBasics" value="1" class="btn btn-success">Változások mentése <i class="fa fa-save"></i></button>
                    </div>
                </div>
            </div>
            <br>
            <div class="con">
                <h3>Linkek / Elérhetőségek</h3>
                <div class="row np">
                    <div class="col-md-2"><em>Árgép XML lista</em></div>
                    <div class="col-md-10"><strong><a href="<?=HOMEDOMAIN?>app/argep" target="_blank"><?=HOMEDOMAIN?>app/argep</a></strong></div>
                </div>
                <div class="row np">
                    <div class="col-md-2"><em>Árukereső XML lista</em></div>
                    <div class="col-md-10"><strong><a href="<?=HOMEDOMAIN?>app/arukereso" target="_blank"><?=HOMEDOMAIN?>app/arukereso</a></strong></div>
                </div>
                <div class="row np">
                    <div class="col-md-2"><em>Webshop API</em></div>
                    <div class="col-md-10"><strong><a href="<?=HOMEDOMAIN?>gateway/api" target="_blank"><?=HOMEDOMAIN?>gateway/api</a></strong></div>
                </div>
            </div>
            <br>
            <a name="apilog"></a>
            <div class="con">
                <h3>Webshop API tranzakció log</h3>
                <? if($_GET['showApiLog'] == '1'): ?>
                <div class="api-log-cont overflowed">
                    <table class="table table-bordered termeklista">
                        <thead>
                            <tr>
                                <th width="150">Időpont</th>
                                <th>Parancs</th>
                                <th>JSON parancs</th>
                                <th>API válasz</th>
                            </tr>
                        </thead>
                         <tbody>
                        <? foreach ( $this->api_log[data] as $d ) { ?>
                            <tr>
                                <td class="center"><?=\PortalManager\Formater::dateFormat($d['idopont'], $this->settings['date_format'])?></td>
                                <td class="center"><?=$d[command]?></td>
                                <td><div class="jsoncommand"><span class="st"><?=wordwrap($d['parancs_json'], 150, '<br>', true)?></span><span class="nl"><?=nl2br($d['parancs_json'])?></span></div></td>
                                <td><div class="jsoncommand"><span class="st"><?=wordwrap($d['valasz_json'], 150, '<br>', true)?></span><span class="nl"><?=nl2br($d['valasz_json'])?></span></div></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
                <? else: ?>
                <a href="/beallitasok/?showApiLog=1#apilog"><strong>API log megtekintése</strong></a>
                <? endif; ?>
            </div>
        </form>
    </div>
</div>
