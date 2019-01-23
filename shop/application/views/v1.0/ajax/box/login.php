<script type="text/javascript">
	$(function(){
		$('#loginBtn').loginUser();
	})
</script>
<div class="recall ajaxBox">
	<h3><i class="fa fa-lock"></i> Bejelentkezés</h3>
    <form action="" method="post" id="login" onsubmit="$('#loginBtn').click() return false;">
    <div class="cont" style="padding:15px;">
     	<div class="row np">
        	<div class="col-md-3 form-text"><strong>E-mail cím:</strong></div>
            <div class="col-md-9"><input type="text" value="<?=$_COOKIE['ajx_login_usr']?>" autocomplete="on" required="required" name="email" excCode="1001" id="email" class="form-control" /></div>
        </div>
        <div class="divider"></div>
        <div class="row np">
        	<div class="col-md-3 form-text"><strong>Jelszó:</strong></div>
            <div class="col-md-9"><input type="password"  value="<?=base64_decode( $_COOKIE['ajx_login_pw'])?>"  autocomplete="on" required="required" name="pw" id="pw" class="form-control" /></div>
        </div>
        <div class="divider"></div>
        <div class="row np">
        	<div class="col-md-2 form-btn" align="left"><a href="javascript:void(0);" fill="resetPassword">Elfelejtett jelszó</a></div>
            <div class="col-md-10 form-btn" align="right">
                <label>emlékezz rám <input type="checkbox" name="remember_me" <?=($_COOKIE['ajx_login_usr'])?'checked="checked"':''?>></label> &nbsp;
                <button id="loginBtn" class="btn btn-warning btn-md">Bejelentkezés <i class="fa fa-arrow-circle-right"></i></button>
            </div>
        </div>
    </div>
    </form>
</div>