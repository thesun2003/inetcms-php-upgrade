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
    contentHeader: '<img src="/img/rsz_new_logo.png" style="width:70%;" />',
    contentBody: title
  }, {
      closeButton: false
  });
}

function getMap() {
  elem_id = 'YMapsID';
  if($(elem_id)) {
    $('#'+elem_id).html('');
    $('#'+elem_id).addClass('YandexMap-user');
    initMap(elem_id, '84.99287849999997', '56.46383377433857', 'ул. Елизаровых, 49, офис 28');
  }
}