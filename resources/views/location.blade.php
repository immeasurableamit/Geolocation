<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script
        src='http://maps.googleapis.com/maps/api/js?sensor=true&amp;libraries=places&key=AIzaSyDr2kuGpu02LslF4N2Xbg5aopkWbE0Qtkg'>
    </script>

    <title>ChooseYour Location</title>
</head>

<body>


    <div class="container col-sm-6" style="margin-top: 100px;">
        <form accept="#" accept-charset="utf-8">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">ChooseYour Location</h5>
                </div>

                <div class="modal-body">
                    <div class="col">
                        <div class="text-bold-600 font-medium-2 mb-1">
                            Search for your area/street name
                        </div>
                        <fieldset class="form-group position-relative has-icon-left input-divider-left">
                            <input class="form-control" type="text" name="location" id="location"
                                placeholder="Search for your area/street name" required="" />
                            <input type="hidden" name="txtLatLong" id="txtLatLong" />

                            <input type="hidden" class="form-control" id="lat" name="lat" value="">

                            <input type="hidden" class="form-control" id="lng" name="lng" value="">

                            <div id="map"></div>

                            <div class="form-control-position">
                                <i class="feather icon-map-pin"></i>
                            </div>
                        </fieldset>

                        <div class="mt-4">
                            <button type="button" onClick="return getlocation()" class="btn btn-info"
                                style="color: white"><i class="feather icon-crosshair"></i>
                                Current Location</button>

                            <button type="submit" class="btn btn-primary" style="color: white">
                                Proceed</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script>
        window.onload = function() {
            var startPos;

            var geoSuccess = function(position) {
                startPos = position;
                $latLong = startPos.coords.latitude + "-" + startPos.coords.longitude;
                $latitude = startPos.coords.latitude;
                $longitude = startPos.coords.longitude;

                document.getElementById('txtLatLong').value = $latLong;

                initializeSearch();
            };
            var geoError = function(error) {
                console.log('Error occurred. Error code: ' + error.code);
                // error.code can be:
                //   0: unknown error
                //   1: permission denied
                //   2: position unavailable (error response from location provider)
                //   3: timed out
            };
            navigator.geolocation.getCurrentPosition(geoSuccess, geoError);
        };

        function initializeSearch() {
            var input = document.getElementById('txtLatLong').value;

            var latlngStr = input.split('-');
            var latlng = new google.maps.LatLng(parseFloat(latlngStr[0]), parseFloat(latlngStr[1]));

            var input = document.getElementById('location');

            var geocoder = new google.maps.Geocoder();
            var autocomplete = new google.maps.places.Autocomplete(input);

            //press enter key
            google.maps.event.addDomListener(input, 'keydown', function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                }
            });

            autocomplete.addListener('place_changed', function() {
                $city = "";
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    // $("#location-model").modal('show');
                    return;
                }
                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];

                    // for the country, get the country code (the "short name") also

                    if (addressType == "country") {
                        $countryCode = place.address_components[i].short_name;
                    }

                    if (addressType == "bus_station") {
                        $city = place.address_components[i].long_name;
                    }

                    if ($city == "") {
                        if (addressType == "establishment") {
                            $city = place.address_components[i].long_name;
                        }
                    }

                    if ($city == "") {
                        if (addressType == "route") {
                            $city = place.address_components[i].long_name;
                        }
                    }

                    if ($city == "") {
                        if (addressType == "political") {
                            $city = place.address_components[i].long_name;
                        }
                    }

                    if ($city == "") {
                        if (addressType == "locality") {
                            $city = place.address_components[i].long_name;
                        }
                    }

                    if ($city == "") {
                        if (addressType == "administrative_area_level_2") {
                            $city = place.address_components[i].long_name;


                        }
                    }
                }
                bindDataToForm(place.name, place.formatted_address, place.geometry.location.lat(), place.geometry
                    .location.lng(), $city, $countryCode);
            });
        }

        function bindDataToForm(address, formatted_address, lat, lng, city, countryCode) {

            document.getElementById('location').value = address;
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
        }


        google.maps.event.addDomListener(window, 'load', initializeSearch);

        function getlocation() {

            if (navigator.geolocation) {
                /* if(document.getElementById('location').value == '') { */
                navigator.geolocation.getCurrentPosition(visitorLocation, showReError);
                /* }  */
            } else {
                $('#location').html('This browser does not support Geolocation Service.');
            }
        }

        function showReError(error) {
            console.log(error);
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("Location access is disabled in your browser.please give permission to access your location.");
                    $("#location-model").modal('show');
                    break;
                case error.POSITION_UNAVAILABLE:
                    $("#location-model").modal('show');
                    break;
                case error.TIMEOUT:
                    $("#location-model").modal('show');
                    break;
                case error.UNKNOWN_ERROR:
                    $("#location-model").modal('show');
                    break;
            }
        }

        function visitorLocation(position) {
            var currentLat = position.coords.latitude;
            var currentLong = position.coords.longitude;

            console.log(position);


            document.getElementById('lat').value = currentLat;
            document.getElementById('lng').value = currentLong;

            var geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(currentLat, currentLong);

            geocoder.geocode({
                'latLng': latlng
            }, function(result, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var address = result[0].formatted_address;
                    document.getElementById('location').value = address;
                } else {
                    alert("Could not get the geolocation");
                }
            });

        }
    </script>

</body>

</html>
