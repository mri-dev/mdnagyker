<div class="side-menu side-left">
	<ul>
	    <li class="head <?=($this->gets[1] == 'belepes')?'active':''?>"><a href="/user/belepes">Bejelentkezés</a></li>
	    <li class=" <?=($this->gets[1] == 'jelszoemlekezteto')?'active':''?>"><a href="/user/jelszoemlekezteto">Elfelejtett jelszó</a></li>
	    <li class="head <?=($this->gets[1] == 'regisztracio' && !$_GET['group'])?'active':''?>"><a href="/user/regisztracio">Regisztráció</a></li>
	</ul>
</div>
