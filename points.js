$.ajaxSetup({async: false});

var map, infowindow = new google.maps.InfoWindow();

$.getJSON('points.json', function (data) {
    points = data;
});

function initialize() {

    /*map setting*/
    $('#map-canvas').height(window.outerHeight / 2.2);

    map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 10,
        center: {lat: 23.00, lng: 120.30}
    });

    var markers = [];
    for (p in points) {
        var latLng = new google.maps.LatLng(points[p]['latitude'], points[p]['longitude']);
        var markerTitle = points[p].title;
        if (null !== points[p].year) {
            markerTitle = '[' + points[p].year + ']' + points[p].title;
        }
        var marker = new MarkerWithLabel({
            position: latLng,
            clickable: true,
            labelContent: markerTitle,
            labelClass: 'labels',
            labelAnchor: new google.maps.Point(100, 5),
            labelStyle: {opacity: 0.75}
        });
        marker.info = points[p];
        google.maps.event.addListener(marker, 'click', (function (marker) {
            return function () {
                var pageContent = '';
                if (marker.info.year !== null) {
                    pageContent += '<br /><b>年度：</b> ' + marker.info.year;
                }
                pageContent += '<br /><b>位置：</b> [' + marker.info.area + ']' + marker.info.location;
                pageContent += '<br /><b>案件：</b><p>' + marker.info.description + '</p>';
                var info = '<b>[' + marker.info.year + ']' + marker.info.title + '</b>';
                info += '<br /><b>位置：</b> [' + marker.info.area + ']' + marker.info.location;
                infowindow.setContent(info);
                infowindow.open(map, marker);
                $('#title').html(marker.info.title);
                $('#content').html(pageContent);
            }
        })(marker));
        markers.push(marker);
    }
    var markerCluster = new MarkerClusterer(map, markers, {maxZoom: 16});
}

google.maps.event.addDomListener(window, 'load', initialize);