<div class="tudastar-page" ng-controller="Tudastar" ng-init="init()">
  <div class="header">
    <i class="fa fa-lightbulb-o"></i>
    <h1>Keresés a tudástárban</h1>
  </div>
  <div class="searcher">
    <div class="insider">
      <div layout="row">
        <div flex="90">
          <md-chips
            ng-model="searchKeys"
            md-removable="true"
            placeholder="Kulcsszavak megadása"
            delete-button-label="kulcsó törlése"
            delete-hint="Nyomjon törlést a kulcsszó törléséhez"
            secondary-placeholder="+kulcsszó"></md-chips>
        </div>
        <div flex>
          <md-button class="md-raised md-warn md-hue-5" ng-click="doSearch(false)">Keresés <i class="fa fa-search"></i></md-button>
        </div>
      </div>
      <div class="filtered-categories" ng-hide="emptyCatFilters()">
        <strong>Témakörre szűrve:</strong> <span ng-repeat="ct in catFilters">{{ct.cim}}</span>
      </div>
    </div>
  </div>
  <div class="loading-screen" ng-show="loading">
    Tudástár cikkeinek betöltése folyamatban <i class="fa fa-spin fa-spinner"></i>
  </div>
  <div class="row">
    <div class="col-md-3">
      <div class="categories" ng-show="loaded && !loading">
        <h2>Témakörök</h2>
        <div class="cat" ng-repeat="c in categories" ng-click="filterCategory(c.ID)" ng-class="(catInFilter(c.ID))?'infilter':''">
          {{c.cim}} <span class="badge">{{c.articles.length}}</span>
        </div>
      </div>
    </div>
    <div class="col-md-9">
      <div ng-show="loaded && !loading">
        <div class="picked-article" ng-show="picked_article">
          <span class="pick-label"><i class="fa fa-thumb-tack"></i> Kiválasztott bejegyzés</span> <a class="closer" href="javascript:void(0);" ng-click="removeHighlightArticle()">bezárás <i class="fa fa-times"></i> </a>
          <h3 class="title">{{picked_article.cim}}</h3>
          <div class="description">
            <div ng-bind-html="picked_article.szoveg|unsafe"></div>
            <div class="clr"></div>
            <div class="metas">
              <span class="date" title="Cikk utolsó frissítésének ideje"><i class="fa fa-clock-o"></i> {{picked_article.idopont}}</span> <span class="keywords"><i class="fa fa-tags"></i> <span class="tag" ng-class="(inSearchTag(k))?'filtered':''" ng-click="putTagToSearch(k)" ng-repeat="k in picked_article.kulcsszavak">{{k}}</span></span>
            </div>
            <div class="clr"></div>
          </div>
        </div>
        <h2>Találatok</h2>
        <div class="found-articles">
          Összesen {{found_items}} darab bejegyzést találtunk
        </div>
        <div class="article-groups">
          <div class="article-group" ng-repeat="c in categories" ng-show="(c.articles.length != 0)">
            <h3 class="title">{{c.cim}}</h3>
            <div class="article-number" ng-show="(c.articles.length != 0)">
              <strong>{{c.articles.length}} db</strong> bejegyzés:
            </div>
            <div class="articles">
              <article id="tudastar{{a.ID}}" ng-class="(selected_article == a.ID)?'picked':''" ng-show="(c.articles.length != 0)" ng-repeat="a in c.articles">
                <div class="question" ng-click="pickArticle(a.ID)">
                  <i class="fa fa-plus" ng-hide="selected_article == a.ID"></i><i class="fa fa-minus" ng-show="selected_article == a.ID"></i> <strong>{{a.cim}}</strong>
                </div>
                <div class="description" ng-show="selected_article == a.ID"?'picked':''>
                  <div ng-bind-html="a.szoveg|unsafe"></div>
                  <div class="clr"></div>
                  <div class="metas">
                    <span class="date" title="Cikk utolsó frissítésének ideje"><i class="fa fa-clock-o"></i> {{a.idopont}}</span> <span class="keywords"><i class="fa fa-tags"></i> <span class="tag" ng-class="(inSearchTag(k))?'filtered':''" ng-click="putTagToSearch(k)" ng-repeat="k in a.kulcsszavak">{{k}}</span></span> <span class="ids"><i class="fa fa-link"></i> <a href="javascript:void(0);" ng-click="highlightArticle(a.ID)" title="Bejegyzés hivatkozása">#{{a.ID}}</a></span>
                    <span class="totop" ng-click="toTop()">lap tetejére <i class="fa fa-arrow-up"></i> </span>
                  </div>
                  <div class="clr"></div>
                </div>
              </article>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
