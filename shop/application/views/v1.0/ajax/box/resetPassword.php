<script type="text/javascript">
	$(function(){
		$('#resetBtn').resetPassword();
	})
</script>
<div class="recall ajaxBox">
	<h3><i class="fa fa-lock"></i> Új jelszó generálása</h3>
    <form action="" method="post" id="resetPassword" onsubmit="$('#resetBtn').click() return false;">
    <div class="cont" style="padding:15px;">
     	<div class="row np">
        	<div class="col-md-3 form-text"><strong>E-mail cím:</strong></div>
            <div class="col-md-9"><input type="text" required="required" name="email" excCode="1001" id="email" class="form-control" /></div>
        </div>
        <div class="divider"></div>
        <div class="row np">
        	<div class="col-md-4 form-btn" align="left"><a href="javascript:void(0);" fill="register">Regisztráció</a> | <a href="javascript:void(0);" fill="login">Bejelentkezés</a></div>
            <div class="col-md-8 form-btn" align="right"><button id="resetBtn" class="btn btn-warning btn-md">Új jelszó generálása <i class="fa fa-arrow-circle-right"></i></button></div>
        </div>
    </div>
    </form>
</div>