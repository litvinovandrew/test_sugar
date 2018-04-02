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


    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8,
            center: {lat: 51.612037, lng: 9.722828}
        });

        var geocoder = new google.maps.Geocoder();

        //get addresses
        var options = document.getElementById('map-data').getAttribute("options");
        var obj = JSON.parse(options);
        console.log(obj);

        for (var key in obj) {
            var item = obj[key];
            console.log(obj[key].account_address);

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

//        obj.forEach(function (element) {
//            geocodeAddress(geocoder, element.account_address, map);
//        });

    }


    function geocodeAddress(geocoder, address, infoWindow, resultsMap) {

        geocoder.geocode({'address': address}, function (results, status) {
            if (status === 'OK') {
                resultsMap.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map: resultsMap,
                    position: results[0].geometry.location,
                    title: 'Account'
                });

                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });

                var bounds = new google.maps.LatLngBounds();
                bounds.extend(marker.position);


            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    }


</script>

<script>

</script>