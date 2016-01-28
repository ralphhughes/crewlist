function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition,showError);
    } else { 
        alert('Geolocation is not supported by this browser.');
    }
}

function showPosition(position) {
    // Users location
    var lat1 = position.coords.latitude;
    var lon1 = position.coords.longitude;
    
    // Boathouse location
    var lat2 = 53.322016;
    var lon2 = -3.834049;
    
    // Show users straight-line distance from boathouse in km
    var distanceFromBoathouse = distance(lat1, lon1, lat2, lon2);
    var positionAccuracy = position.coords.accuracy;
    
    if (positionAccuracy < 1000) {
        alert('You are ' + distanceFromBoathouse.toFixed(2) + ' km from boathouse');
    }
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
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert('Geolocation needs to be enabled for this site');
            break;
        case error.POSITION_UNAVAILABLE:
            alert('Location information is unavailable.');
            break;
        case error.TIMEOUT:
            alert('The request to get user location timed out.');
            break;
        case error.UNKNOWN_ERROR:
            alert('An unknown error occurred.');
            break;
    }
}
