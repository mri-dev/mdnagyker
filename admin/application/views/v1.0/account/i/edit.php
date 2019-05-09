<? $data = $this->data; ?>
<h1><?=$data[nev]?> <small>Fiók szerkesztése</small></h1>
<?=$this->msg?>
<form action="" method="post" enctype="multipart/form-data">
	<div style="margin: 0 -10px;">
		<div class="row">
			<div class="col-sm-6">
				<div class="con">
					<h3 style="margin: 0 0 5px 0;">Fiók alapadatok</h3>
					<div class="divider" style="margin-bottom: 10px;"></div>
					<div class="row">
						<div class="col-sm-6">
							<label for="data_felhasznalok_nev">Név*</label>
							<input type="text" id="data_felhasznalok_nev" class="form-control" name="data[felhasznalok][nev]" value="<?=$data[nev]?>" required>
						</div>
						<div class="col-sm-6">
							<label for="data_felhasznalok_email">E-mail cím*</label>
							<input type="text" id="data_felhasznalok_email" class="form-control" name="data[felhasznalok][email]" value="<?=$data[email]?>" required>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-6">
							<label for="data_felhasznalok_jelszo">új jelszó</label>
							<input type="text" id="data_felhasznalok_jelszo" class="form-control" name="data[felhasznalok][jelszo]">
						</div>
						<div class="col-sm-6">
							<label for="data_felhasznalok_cash">Virtuális egyenleg</label>
							<input type="text" id="data_felhasznalok_cash" class="form-control" name="data[felhasznalok][cash]" value="<?=$data[cash]?>" min="0">
						</div>
					</div>

					<br>
					<h3 style="margin: 0 0 5px 0;">Számlázási adatok</h3>
					<div class="divider" style="margin-bottom: 10px;"></div>
					<div class="row">
						<div class="col-sm-5">
							<label for="data_felhasznalo_adatok_szamlazas_nev">Számlázási név*</label>
							<input type="text" id="data_felhasznalok_nev" class="form-control" name="data[felhasznalo_adatok][szamlazas_nev]" value="<?=$data[szamlazas_nev]?>" required>
						</div>
						<div class="col-sm-2">
							<label for="data_felhasznalo_adatok_szamlazas_irsz">Irányítószám*</label>
							<input type="text" id="data_felhasznalo_adatok_szamlazas_irsz" class="form-control" name="data[felhasznalo_adatok][szamlazas_irsz]" value="<?=$data[szamlazas_irsz]?>" required>
						</div>
						<div class="col-sm-2">
							<label for="data_felhasznalo_adatok_szamlazas_kerulet">Kerület</label>
							<input type="text" id="data_felhasznalo_adatok_szamlazas_kerulet" class="form-control" name="data[felhasznalo_adatok][szamlazas_kerulet]" value="<?=$data[szamlazas_kerulet]?>">
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szamlazas_city">Város*</label>
							<input type="text" id="data_felhasznalo_adatok_szamlazas_city" class="form-control" name="data[felhasznalo_adatok][szamlazas_city]" value="<?=$data[szamlazas_city]?>" required>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-5">
							<label for="data_felhasznalo_adatok_szamlazas_kozterulet_nev">Közterület neve*</label>
							<input type="text" id="data_felhasznalo_adatok_szamlazas_kozterulet_nev" class="form-control" name="data[felhasznalo_adatok][szamlazas_kozterulet_nev]" value="<?=$data[szamlazas_kozterulet_nev]?>" required>
						</div>
						<div class="col-sm-4">
							<label for="data_felhasznalo_adatok_szamlazas_kozterulet_jelleg">Közterület jelleg*</label>
							<select name="data[felhasznalo_adatok][szamlazas_kozterulet_jelleg]" class="form-control" id="data_felhasznalo_adatok_szamlazas_kozterulet_jelleg">
									<option value="">* Közterület jellege</option>
									<option value="" disabled="disabled"></option>
									<? foreach( $this->kozterulet_jellege as $s ): ?>
									<option value="<?=$s?>" <?=( $data[szamlazas_kozterulet_jelleg] == $s ) ? 'selected="selected"' : ''?>><?=$s?></option>
									<? endforeach; ?>
								</select>
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szamlazas_hazszam">Házszám*</label>
							<input type="text" id="data_felhasznalo_adatok_szamlazas_hazszam" class="form-control" name="data[felhasznalo_adatok][szamlazas_hazszam]" value="<?=$data[szamlazas_hazszam]?>" required>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szamlazas_epulet">Épület</label>
							<input type="text" id="data_felhasznalo_adatok_szamlazas_epulet" class="form-control" name="data[felhasznalo_adatok][szamlazas_epulet]" value="<?=$data[szamlazas_epulet]?>" requied>
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szamlazas_lepcsohaz">Lépcsőház</label>
							<input type="text" id="data_felhasznalo_adatok_szamlazas_lepcsohaz" class="form-control" name="data[felhasznalo_adatok][szamlazas_lepcsohaz]" value="<?=$data[szamlazas_lepcsohaz]?>" >
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szamlazas_szint">Szint</label>
							<input type="text" id="data_felhasznalo_adatok_szamlazas_szint" class="form-control" name="data[felhasznalo_adatok][szamlazas_szint]" value="<?=$data[szamlazas_szint]?>" >
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szamlazas_ajto">Ajtó</label>
							<input type="text" id="data_felhasznalo_adatok_szamlazas_ajto" class="form-control" name="data[felhasznalo_adatok][szamlazas_ajto]" value="<?=$data[szamlazas_ajto]?>" >
						</div>
					</div>
					<br>
					<h3 style="margin: 0 0 5px 0;">Szállítási adatok</h3>
					<div class="divider" style="margin-bottom: 10px;"></div>
					<div class="row">
						<div class="col-sm-5">
							<label for="data_felhasznalo_adatok_szallitas_nev">Szállítási név*</label>
							<input type="text" id="data_felhasznalok_nev" class="form-control" name="data[felhasznalo_adatok][szallitas_nev]" value="<?=$data[szallitas_nev]?>" required>
						</div>
						<div class="col-sm-2">
							<label for="data_felhasznalo_adatok_szallitas_irsz">Irányítószám*</label>
							<input type="text" id="data_felhasznalo_adatok_szallitas_irsz" class="form-control" name="data[felhasznalo_adatok][szallitas_irsz]" value="<?=$data[szallitas_irsz]?>" required>
						</div>
						<div class="col-sm-2">
							<label for="data_felhasznalo_adatok_szallitas_kerulet">Kerület</label>
							<input type="text" id="data_felhasznalo_adatok_szallitas_kerulet" class="form-control" name="data[felhasznalo_adatok][szallitas_kerulet]" value="<?=$data[szallitas_kerulet]?>">
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szallitas_city">Város*</label>
							<input type="text" id="data_felhasznalo_adatok_szallitas_city" class="form-control" name="data[felhasznalo_adatok][szallitas_city]" value="<?=$data[szallitas_city]?>" required>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-5">
							<label for="data_felhasznalo_adatok_szallitas_kozterulet_nev">Közterület neve*</label>
							<input type="text" id="data_felhasznalo_adatok_szallitas_kozterulet_nev" class="form-control" name="data[felhasznalo_adatok][szallitas_kozterulet_nev]" value="<?=$data[szallitas_kozterulet_nev]?>" required>
						</div>
						<div class="col-sm-4">
							<label for="data_felhasznalo_adatok_szallitas_kozterulet_jelleg">Közterület jelleg*</label>
							<select name="data[felhasznalo_adatok][szallitas_kozterulet_jelleg]" class="form-control" id="data_felhasznalo_adatok_szallitas_kozterulet_jelleg">
									<option value="">* Közterület jellege</option>
									<option value="" disabled="disabled"></option>
									<? foreach( $this->kozterulet_jellege as $s ): ?>
									<option value="<?=$s?>" <?=( $data[szallitas_kozterulet_jelleg] == $s ) ? 'selected="selected"' : ''?>><?=$s?></option>
									<? endforeach; ?>
								</select>
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szallitas_hazszam">Házszám*</label>
							<input type="text" id="data_felhasznalo_adatok_szallitas_hazszam" class="form-control" name="data[felhasznalo_adatok][szallitas_hazszam]" value="<?=$data[szallitas_hazszam]?>" required>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szallitas_epulet">Épület</label>
							<input type="text" id="data_felhasznalo_adatok_szallitas_epulet" class="form-control" name="data[felhasznalo_adatok][szallitas_epulet]" value="<?=$data[szallitas_epulet]?>" requied>
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szallitas_lepcsohaz">Lépcsőház</label>
							<input type="text" id="data_felhasznalo_adatok_szallitas_lepcsohaz" class="form-control" name="data[felhasznalo_adatok][szallitas_lepcsohaz]" value="<?=$data[szallitas_lepcsohaz]?>" >
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szallitas_szint">Szint</label>
							<input type="text" id="data_felhasznalo_adatok_szallitas_szint" class="form-control" name="data[felhasznalo_adatok][szallitas_szint]" value="<?=$data[szallitas_szint]?>" >
						</div>
						<div class="col-sm-3">
							<label for="data_felhasznalo_adatok_szallitas_ajto">Ajtó</label>
							<input type="text" id="data_felhasznalo_adatok_szallitas_ajto" class="form-control" name="data[felhasznalo_adatok][szallitas_ajto]" value="<?=$data[szallitas_ajto]?>" >
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<label for="data_felhasznalo_adatok_szallitas_phone">Telefonszám</label>
							<input type="text" id="data_felhasznalok_phone" class="form-control" name="data[felhasznalo_adatok][szallitas_phone]" value="<?=$data[szallitas_phone]?>">
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<label for="data_felhasznalo_price_group">Ár csoport</label>
							<select name="data[felhasznalok][price_group]" class="form-control" id="data_felhasznalo_price_group" required>
                  <option value="" selected="selected">-- válasszon --</option>
                  <option value="" disabled="disabled"></option>
                  <? foreach( $this->price_groups as $key => $value ): ?>
                      <option value="<?=$value['ID']?>" <?=($value['ID']==$data[price_group])?'selected="selected"':''?>><?=$value['title']?></option>
                  <? endforeach; ?>
              </select>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-sm-12">
							<label for="data_felhasznalo_user_group">Vásárlói csoport</label>
							<select name="data[felhasznalok][user_group]" class="form-control" id="data_felhasznalo_user_group" required>
                  <option value="" selected="selected">-- válasszon --</option>
                  <option value="" disabled="disabled"></option>
                  <? foreach( $this->user_groupes as $key => $value ): ?>
                      <option value="<?=$key?>" <?=($key==$data[user_group])?'selected="selected"':''?>><?=$value?></option>
                  <? endforeach; ?>
              </select>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<? if($data[user_group] == \PortalManager\Users::USERGROUP_COMPANY): ?>
				<div class="con">
					<h3 style="margin:0;">Céges adatok</h3>
					<div class="clr"></div>
					<div id="reseller_v">
						<div class="divider" style="margin-bottom: 10px;"></div>
						<div class="row">
							<div class="col-sm-8">
								<label for="data_felhasznalo_adatok_company_name">Cég neve</label>
								<input type="text" id="data_felhasznalo_adatok_company_name" class="form-control" name="data[felhasznalo_adatok][company_name]" value="<?=$data[company_name]?>">
							</div>
							<div class="col-sm-4">
								<label for="data_felhasznalo_adatok_company_adoszam">Adószám</label>
								<input type="text" id="data_felhasznalo_adatok_company_adoszam" class="form-control" name="data[felhasznalo_adatok][company_adoszam]" value="<?=$data[company_adoszam]?>">
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_company_address">Cég postázási címe</label>
								<input type="text" id="data_felhasznalo_adatok_company_address" class="form-control" name="data[felhasznalo_adatok][company_address]" value="<?=$data[company_address]?>">
							</div>
							<div class="col-sm-6">
								<label for="data_felhasznalo_adatok_company_hq">Cég székhelye</label>
								<input type="text" id="data_felhasznalo_adatok_company_hq" class="form-control" name="data[felhasznalo_adatok][company_hq]" value="<?=$data[company_hq]?>">
							</div>
						</div>
					</div>
				</div>
				<? endif; ?>
				<div class="con">
					<div class="row np">
						<div class="col-sm-12 right">
							<button class="btn btn-success" name="saveUserByAdmin">Változások mentése <i class="fa fa-save"></i></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
