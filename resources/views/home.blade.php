@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div id="map"></div>
                        <div id="map-data" options="{{$accountsGeoNames}}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>

    /**
     * this function is called after google js is initialized on the page
     */
    function initMap() {
        /**
         * new Map object, cet center to Germany
         * @type {google.maps.Map}
         */
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 6,
            center: {lat: 51.612037, lng: 9.722828}
        });

        var geocoder = new google.maps.Geocoder();

        //get addresses that are kept as "options" attributes of the 'map-data' element
        var options = document.getElementById('map-data').getAttribute("options");
        var obj = JSON.parse(options);
        console.log(obj);

        for (var key in obj) {
            var item = obj[key];

            /**
             * prepare content for every marker
             * @type {string}
             */
            var contentStr = '<div id="content">'+
                '<div id="siteNotice">'+
                '</div>'+
                '<h1 id="firstHeading" class="firstHeading">'+item.account_name+'</h1>'+
                '<div id="bodyContent">'+
                '<p><b>'+item.account_address+'</b> </p> ' +
                '<br><b>Opportunity name </b>: '+item.opportunity_name+'<br>'+
                '<b>Amount </b>:'+ item.opportunity_amount+
                '<br><b>Sales stage </b>:'+ item.opportunity_sales_stage +
                '</div>'+
                '</div>';

            var infowindow = new google.maps.InfoWindow({
                content: contentStr
            });

            geocodeAddress(geocoder, item.account_address, infowindow, map);
        }

    }

    /**
     * Finds the coordinates by text, places markers to the map
     * @param geocoder
     * @param address
     * @param infoWindow
     * @param resultsMap
     */
    function geocodeAddress(geocoder, address, infoWindow, resultsMap) {

        geocoder.geocode({'address': address}, function (results, status) {
            if (status === 'OK') {

                //add random delta to show points with the same coordinates
                var randomLatDelta = Math.floor((Math.random() * 5) + 1)/100;
                var randomLngDelta = Math.floor((Math.random() * 5) + 1)/100;

                var location = results[0].geometry.location;
                resultsMap.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map: resultsMap,
                    position: {lat:location.lat()+ randomLatDelta, lng:location.lng()+randomLngDelta},
                    title: 'Account'
                });

                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });

                var bounds = new google.maps.LatLngBounds();
                bounds.extend(marker.position);

            } else {
                console.log('Geocode was not successful for the following reason: ' + status);
            }
        });
    }


</script>
