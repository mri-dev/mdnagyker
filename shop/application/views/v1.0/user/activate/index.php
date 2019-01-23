<div class="activator page-width">
	<div style="padding:50px;" align="center">
		<? if ($this->err): ?>			
			<h1><?=$this->msg?></h1>
			<a href="/user/belepes" class="btn btn-pr btn-hd">Bejelentkezés</a>
		<? else: ?>
    	<h1>Sikeresen aktiválta fiókját</h1>
    	<br>
        <div class="sub">Jelentkezzen be fiókjába. Jó vásálást kívánunk!</div>
        <div>
        	<br>
        	<a href="/user/belepes" class="btn btn-pr btn-md">Bejelentkezés</a>
        </div>
    	<? endif; ?>
    </div>
</div>