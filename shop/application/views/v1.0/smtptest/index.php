<? if(true): ?>
<br>
<h3>GMAIL SMTP teszt üzenet küldése:</h3>
<form method="post">
	<input type="text" style="padding:10px;" name="cimzett" placeholder="címzett e-mail" value="<?=$_POST[cimzett]?>"><br>
    <input type="text" style="padding:10px;" name="tema" placeholder="Téma" value="<?=$_POST[tema]?>"><br>
    <div>
        <div><em>SSL:</em> Be</div>
        <div><em>SMTP Auth:</em> Be</div>
        <div><em>SMTP Secure:</em> 
            <input type="radio" name="smtp_mode" value="tls" checked>TLS <input type="radio" name="smtp_mode" value="ssl">SSL
        </div>
        <div><em>SMTP Port:</em> 
            <input type="text" name="smtp_port" value="587">
        </div>
    </div>
 <input type="submit" name="sendSmtpMail" value="Küldés"><br>
</form>
<? endif; ?>