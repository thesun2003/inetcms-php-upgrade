function initMap(id, long, lat, title) {
    var map;
    map = new YMaps.Map(document.getElementById(id));
    map.setCenter(new YMaps.GeoPoint(long, lat), 16);

    map.addControl(new YMaps.TypeControl());
    map.addControl(new YMaps.ToolBar());
    map.addControl(new YMaps.Zoom());
    map.addControl(new YMaps.ScaleLine());

    var content = document.createElement('span');
    content.innerHTML = '<img src="/images/map_logo.jpg" /><br />'+title;
    map.openBalloon(new YMaps.GeoPoint(long, lat), content, {hasCloseButton: false, mapAutoPan: 0});
};

function getMap() {
  elem_id = 'YMapsID';
  if($(elem_id)) {
    $(elem_id).addClass('YandexMap-user');
    initMap(elem_id, '82.923475', '54.962411', 'ул. Мира, 54а');
  }
}