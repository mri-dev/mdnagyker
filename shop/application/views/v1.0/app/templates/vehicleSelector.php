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
          <div class="vehicle-group deep0">
            <div class="vehicle" ng-repeat="vehicle in vehicles">
              <div class="wrapper" ng-click="selectVehicleItem(vehicle.ID)" ng-class="(vehicles_selected.indexOf(vehicle.ID)!==-1)?'selected':''">
                <div class="logo" ng-hide="(vehicle.logo=='')">
                  <img src="<?=IMGDOMAIN?>{{vehicle.logo}}" alt="{{vehicle.title}}">
                </div>
                <div class="title">
                  {{vehicle.title}}
                </div>
              </div>
            </div>
          </div>
          <div class="child-list">
            <div class="child" ng-repeat="models in vehicle_childs" ng-show="models.data">
              <div class="title">
                <strong>{{models.title}}</strong> modellek:
              </div>
              <div class="vehicle-group">
                <div class="item" ng-repeat="model in models.data">
                  <div class="wrapper">
                    {{model.title}}
                  </div>
                </div>
              </div>
            </div>
          </div>
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
