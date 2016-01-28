<?php 
include 'html_header.php';
?>

        <script src="js/geolocation.js"></script>
        <script>
            var x = document.getElementById("demo");
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition, showError);
                } else {
                    x.innerHTML = "Geolocation is not supported by this browser.";
                }
            }

            function showPosition(position) {
                x.innerHTML = "Latitude: " + position.coords.latitude +
                        "<br>Longitude: " + position.coords.longitude;

                locationMaths(position);
            }

            function locationMaths(position) {
                // Users location
                var lat1 = position.coords.latitude;
                var lon1 = position.coords.longitude;

                // Boathouse location
                var lat2 = 53.322016;
                var lon2 = -3.834049;

                alert(distance(lat1, lon1, lat2, lon2));
            }

            function distance(lat1, lon1, lat2, lon2) {
                var deg2rad = 0.017453292519943295; // === Math.PI / 180
                var cos = Math.cos;
                lat1 *= deg2rad;
                lon1 *= deg2rad;
                lat2 *= deg2rad;
                lon2 *= deg2rad;
                var a = (
                        (1 - cos(lat2 - lat1)) +
                        (1 - cos(lon2 - lon1)) * cos(lat1) * cos(lat2)
                        ) / 2;

                return 12742 * Math.asin(Math.sqrt(a)); // Diameter of the earth in km (2 * 6371)
            }



            function showError(error) {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        x.innerHTML = "User denied the request for Geolocation.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        x.innerHTML = "Location information is unavailable.";
                        break;
                    case error.TIMEOUT:
                        x.innerHTML = "The request to get user location timed out.";
                        break;
                    case error.UNKNOWN_ERROR:
                        x.innerHTML = "An unknown error occurred.";
                        break;
                }
            }
        </script>

    </head>
    <body class="ui-mobile-viewport ui-overlay-a">
        <div data-role="page" id="login">

            <div data-role="header">
                <h1>RNLI Crewlist</h1>
            </div>

            <div data-role="content">

                <p id="demo">Click the button to get your coordinates:</p>
                <span class="ui-icon-gear ui-btn-icon-left " style="position:relative;">Mechanics</span><br/>
                <span class="ui-icon-star ui-btn-icon-left " style="position:relative;">Coxswains\Helms</span><br/>
                <span class="ui-icon-user ui-btn-icon-left " style="position:relative;">Crew</span><br/>
                <span class="ui-icon-location ui-btn-icon-left " onclick="getLocation()" style="position:relative;">Location</span><br/>
                <span class="ui-icon-action ui-btn-icon-left " style="position:relative;">Logout</span><br/>

            </div>
        </div>
    </body>
</html>