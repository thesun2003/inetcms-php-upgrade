function initMap(id, long, lat, title) {
  var myMap = new ymaps.Map(document.getElementById(id), {
    center: [lat, long],
    zoom: 16,
    type: "yandex#map"
  });

  myMap.controls.add("mapTools")
    .add("zoomControl")
    .add("scaleLine")
    .add("typeSelector");

  myMap.balloon.open([lat, long], {
    contentHeader: '<img src="/images/map_logo.jpg" />',
    contentBody: title
  }, {
      closeButton: false
  });
}

function getMap() {
  var elem_id = 'YMapsID';

  if (typeof($('#'+elem_id)[0]) != "undefined") {
      $('#'+elem_id).html('');
      $('#'+elem_id).addClass('YandexMap-user');
      initMap(elem_id, js_config['map_lng'], js_config['map_lat'], js_config['map_address']);
  }
}
