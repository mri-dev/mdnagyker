<div class="account page-width">
 <div class="grid-layout">
    <div class="grid-row grid-row-20"><? $this->render('user/inc/account-side', true); ?></div>
    <div class="grid-row grid-row-80">
      <div class="login-form">
          <h1>Jelszócsere</h1>
          <br>
    		  <?=$this->msg?>
          <div class="form-rows">
              <form action="#password" method="post">
              	<div class="row np">
                      <div class="col-md-3 form-text-md"><strong>Régi jelszó:</strong></div>
                      <div class="col-md-9"><input name="old" type="password" class="form-control" /></div>
                  </div>
                  <div class="row np">
                      <div class="col-md-3 form-text-md"><strong>Új jelszó:</strong></div>
                      <div class="col-md-9"><input name="new" type="password" class="form-control" /></div>
                  </div>
                  <div class="row np">
                      <div class="col-md-3 form-text-md"><strong>Új jelszó újra:</strong></div>
                      <div class="col-md-9"><input name="new2" type="password" class="form-control" /></div>
                  </div>
                  <div class="row np">
                      <div class="col-md-12 center">
                        <button name="changePassword" class="btn btn-pr btn-sm">Jelszó lecserélése</button>
                      </div>
                  </div>
              </form>
          </div>
      </div>
	   </div>
  </div>
</div>
