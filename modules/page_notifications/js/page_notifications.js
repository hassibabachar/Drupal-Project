(function($){

  var checkbox = document.getElementById("edit-pagenotifycheckall");
  var checkboxs = document.getElementsByClassName("form-checkbox");

  if (checkbox) {
    checkbox.addEventListener("click", checkUncheck);
  }

  function checkUncheck() {
    if (checkbox.checked == false){
      for (i = 0; i < checkboxs.length; i++) {
        var target = document.getElementById('edit-page-notifications-list-'+i+'-field-page-notify-node-id');
        if (target !== null){
          target.checked = false;
        }
      }
    } else {
      for (i = 0; i < checkboxs.length; i++) {
        var target = document.getElementById('edit-page-notifications-list-'+i+'-field-page-notify-node-id');
        if (target !== null){
          target.checked = true;
        }
      }
    }
  }

})(jQuery);
