var new_city_x = 0;
var new_city_y = 0;

function start_dilermap() {
  $('map_russia').addEvents({
    mousemove: dilermap_mousemove,
    click: function(){
      $('map_russia').removeEvent('mousemove', dilermap_mousemove);
      $('new_city_x').set('value', new_city_x);
      $('new_city_y').set('value', new_city_y);
    }
  });  
}

function dilermap_mousemove(event){
  new_city_x = event.client.x;
  new_city_y = event.client.y;
  new_city_x = new_city_x - $('map_russia').getPosition().x + 10;
  new_city_y = new_city_y - $('map_russia').getPosition().y + 5;

  var new_city_obj = $('new_city');
  if(new_city_obj) {
    new_city_obj.setStyles({'left': new_city_x, 'top': new_city_y, 'position': 'absolute', 'display': 'block'});
  }
}
