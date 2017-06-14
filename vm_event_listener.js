jQuery('.vm-link').click(function(e) {
  var vmvid = jQuery(this).data('video');
  console.log(vmvid);
  _vm.navigate(vmvid);
});

