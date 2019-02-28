<md-dialog aria-label="Tegye fel kérdését a termékkel kapcsolatban" class="ajaxdialog termekkerdes">
  <form ng-cloak>
    <md-toolbar>
      <div class="md-toolbar-tools">
        <h2>Tegye fel kérdését a termékkel kapcsolatban</h2>
        <span flex></span>
        <md-button class="md-icon-button" ng-click="closeDialog()">
          <md-icon md-svg-src="/src/images/ic_close_white_24px.svg" aria-label="Close dialog"></md-icon>
        </md-button>
      </div>
    </md-toolbar>

    <md-dialog-content>
      <div class="md-dialog-content" ng-show="!sending">
        <md-input-container class="md-icon-float md-block">
          <label>Az Ön teljes neve</label>
          <md-icon md-svg-src="/src/images/ic_person_grey_24px.svg"></md-icon>
          <input required ng-model="termekkerdes.name">
        </md-input-container>
        <md-input-container class="md-icon-float md-block">
          <label>Az Ön telefonszáma</label>
          <md-icon md-svg-src="/src/images/ic_phone_grey_24px.svg"></md-icon>
          <input required ng-model="termekkerdes.phone" ng-pattern="/^\d+$/">
          <div class="hint" ng-show="showHints">Minta: 061123456</div>
          <div ng-messages="termekkerdes.phone.$error" multiple md-auto-hide="false">
            <div ng-message="required">
              Telefonszám kitöltése kötelező!
            </div>
            <div ng-message="pattern">
              Nem megfelelő a telefonszám formátuma.
            </div>
          </div>
        </md-input-container>
        <md-input-container class="md-icon-float md-block">
          <label>Az Ön e-mail címe</label>
          <md-icon md-svg-src="/src/images/ic_email_grey_24px.svg"></md-icon>
          <input type="email" required ng-model="termekkerdes.email">
          <div class="hint" ng-show="showHints">Formátum: email@example.com</div>
        </md-input-container>
        <br>
        <md-input-container class="md-block">
          <label>Kérjük fogalmazza meg kérdését a termékkel kapcsolatban</label>
          <textarea required ng-model="termekkerdes.message" md-maxlength="1000" rows="4" md-select-on-focus></textarea>
        </md-input-container>

        <div class="validate-text" ng-show="!validateForm()">
          Kérjük, hogy töltse ki helyesen a fenti űrlapot.<br>A csillaggal megjelölt mezők kitöltése kötelező!<br>A telefonszámot a mintán szereplő formátumban adja meg.
        </div>
      </div>
      <div class="md-dialog-content sending-progress" ng-show="sending">
        <div class="message">
          Termék kérdés küldése folyamatban... <i class="fa fa-spin fa-spinner"></i>
        </div>
      </div>
    </md-dialog-content>

    <md-dialog-actions layout="row" ng-show="!sending" ng-hide="!validateForm()">
      <md-button ng-click="closeDialog()">
       Mégse
      </md-button>
      <span flex></span>
      <md-button class="md-primary md-raised" ng-click="sendModalMessage('termekkerdes')">
        Kérdés elküldése
      </md-button>
    </md-dialog-actions>
  </form>
</md-dialog>
