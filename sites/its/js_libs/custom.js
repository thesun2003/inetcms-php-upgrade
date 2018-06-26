function recount_sum() {
  var total_sum = 0;
  var computer_sum = $('computer_cost').get('html') * parseInt($('computer_count').get('value'));
  var laptop_sum = $('laptop_cost').get('html') * parseInt($('laptop_count').get('value'));
  var server_sum = $('server_cost').get('html') * parseInt($('server_count').get('value'));
  total_sum = computer_sum + laptop_sum + server_sum;
  /*
  $('computer_sum').set('html', computer_sum);
  $('laptop_sum').set('html', laptop_sum);
  $('server_sum').set('html', server_sum);
  */
  $('total_sum').set('html', total_sum);
}