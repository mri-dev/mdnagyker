/**
* Dokumentumok
**/
var docs = angular.module('Documents', ['ngMaterial']);

docs.controller("List", ['$scope', '$http', '$mdToast', function($scope, $http, $mdToast)
{
	$scope.docs = [];
	$scope.docs_inserted_ids = [];
	$scope.searchdocs = [];
	$scope.selectedItem = null;
	$scope.searcher = null;
	$scope.loading = false;
	$scope.termid = 0;
	$scope.error = false;
	$scope.docs_in_sync = false;

	$scope.init = function( id ){
		$scope.termid = id;
		$scope.loadDocsList( function( docs ){
			$scope.searchdocs = docs;
			$scope.loadList();
		} );
	}

	$scope.findSearchDocs = function( src ) {
		var result = src ? $scope.searchdocs.filter( $scope.filterForSearch( src ) ) : $scope.searchdocs;

		return result;
	}

	$scope.filterForSearch = function( query ){
		var lowercaseQuery = angular.lowercase(query);

    return function filterFn(item) {
      return (item.value.indexOf(lowercaseQuery) !== -1);
    };
	}

	$scope.searchTextChange = function(text) {
		console.log( 'searchTextChange: ' + text );
  }

	$scope.selectedItemChange = function( item )
	{
		if ( item && typeof item !== 'undefined' && typeof item.ID !== 'undefined') {
			var checkin = $scope.docs_inserted_ids.indexOf( parseInt(item.ID) );
			if ( checkin === -1 ) $scope.docs_inserted_ids.push(parseInt(item.ID));
		}

		if (typeof item !== 'undefined') {
			if ( checkin === -1 ) $scope.docs.push(item);
		}

		$scope.syncDocuments(function(){

		});
	}

	$scope.loadDocsList = function( callback )
	{
		$http({
      method: 'POST',
      url: '/ajax/get',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      data: $.param({
        type: "Documents",
        key: 'DocsList'
      })
    }).success(function( r ){
			if (typeof callback !== 'undefined') {
				callback( r.data.map(function(doc){
					doc.value = doc.cim.toLowerCase();
					return doc;
				}) );
			}
    });
	}

	$scope.removeDocument = function(docid){
		$scope.docs_in_sync = true;
		$http({
      method: 'POST',
      url: '/ajax/get',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      data: $.param({
        type: "Documents",
        key: 'RemoveItemFromList',
				id: $scope.termid,
				docid: docid
      })
    }).success(function( r ){
			$scope.docs_in_sync = false;
			$scope.toast('Dokumentum eltávolítva. Lista mentve.', 'success', 5000);
			$scope.loadList();
    });
	}

	$scope.syncDocuments = function( callback )
	{
		$scope.docs_in_sync = true;
		$http({
      method: 'POST',
      url: '/ajax/get',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      data: $.param({
        type: "Documents",
        key: 'SaveList',
				id: $scope.termid,
				list: $scope.docs
      })
    }).success(function( r ){
			console.log(r);
			$scope.docs_in_sync = false;
			if ( r.synced == 0 ) {
				$scope.toast('Dokumentum lista mentve. Nem történt új dokumentumfelvétel.', 'warning', 5000);
			} else {
				$scope.toast(r.synced + 'db új dokumentum hozzáadva a termékhez.', 'success', 8000);
			}
			if (typeof callback !== 'undefined') {
				callback();
			}
    });
	}

	$scope.loadList = function()
	{
		$scope.loading = true;
		$http({
      method: 'POST',
      url: '/ajax/get',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      data: $.param({
        type: "Documents",
        key: 'List',
				id: $scope.termid
      })
    }).success(function(r){
			$scope.loading = false;
			if (r.error == 0) {
				$scope.error = false;
				if ( r.data.length != 0) {
					$scope.docs = r.data;
					angular.forEach( $scope.docs, function(v,k) {
						$scope.docs_inserted_ids.push(parseInt(v.doc_id));
					});
				}
			} else {
				$scope.error = r.msg;
			}
    });
	}

	$scope.toast = function( text, mode, delay ){
		mode = (typeof mode === 'undefined') ? 'simple' : mode;
		delay = (typeof delay === 'undefined') ? 5000 : delay;

		if (typeof text !== 'undefined') {
			$mdToast.show(
				$mdToast.simple()
				.textContent(text)
				.position('top')
				.toastClass('alert-toast mode-'+mode)
				.hideDelay(delay)
			);
		}
	}

}]);
