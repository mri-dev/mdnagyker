<div class="wp mh ep404">
	<div style="padding:25px;">
        <div class="code">404</div>
		<h1>A keresett oldal nem található!</h1>
        <br />
        	<div style="color:#ec5c5c;" align="center">
            	<strong>Hibás oldal:</strong> <br />
				<?=DOMAIN.substr($_SERVER['REQUEST_URI'],1)?>
            </div>
		<br />
        <br />
        	<table width="500">
        		<tr>
        			<td colspan="2" align="left">
                    	<strong>A következő okok egyike miatt:</strong>
                        <div class="light">
                        	<ul>
                        		<li><i class="fa fa-angle-right"></i> A keresett oldal nem létezik.</li>
                                <li><i class="fa fa-angle-right"></i> A keresett oldal elérhetősége hibás.</li>
                                <li><i class="fa fa-angle-right"></i> A keresett oldal korábban létezett, de azóta el lett távolítva.</li>
                        	</ul>
                        </div>
                    </td>
        		</tr>
                <tr>
                	<td align="left"><a href="<?=$_SERVER['HTTP_REFERER']?>">&larr; vissza az előző oldalra</a></td>
                	<td align="right"><a href="/">Főoldal</a> &nbsp;|&nbsp; <a href="/kapcsolat">Kapcsolat &rarr;</a></td>
                </tr>
        	</table>
        <br />
		<strong><?=CLR_DOMAIN?></strong><br>
        <?=date('Y')?>
    </div>
</div>