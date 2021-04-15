demo = {
  initPickColor: function() {
    $('.pick-class-label').click(function() {
      var new_class = $(this).attr('new-class');
      var old_class = $('#display-buttons').attr('data-class');
      var display_div = $('#display-buttons');
      if (display_div.length) {
        var display_buttons = display_div.find('.btn');
        display_buttons.removeClass(old_class);
        display_buttons.addClass(new_class);
        display_div.attr('data-class', new_class);
      }
    });
  },

  showNotification: function(color_,from, align, message) {
    color = color_;

    $.notify({
      icon: "nc-icon nc-bell-55",
      message: message

    }, {
      type: color,
      timer: 500,
      placement: {
        from: from,
        align: align
      }
    });
  }

};