<md-dialog aria-label="Gépjármű szerinti szűrés">
  <form ng-cloak>
    <md-toolbar>
      <div class="md-toolbar-tools">
        <h2>Gépjármű szerinti szűrés</h2>
        <span flex></span>
        <md-button class="md-icon-button" ng-click="cancel()">
          <md-icon md-svg-src="img/icons/ic_close_24px.svg" aria-label="Close dialog"></md-icon>
        </md-button>
      </div>
    </md-toolbar>

    <md-dialog-content>
      <div class="md-dialog-content">
        <div class="vehicle-filter-list">
          {{vehicles}}
        </div>
      </div>
    </md-dialog-content>

    <md-dialog-actions layout="row">
      <md-button ng-click="saveVehicleFilter()">
        Szűrő mentése
      </md-button>
    </md-dialog-actions>
  </form>
</md-dialog>
