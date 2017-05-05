todolistContainer.addEventListener('click', function(event) {
  var targetElement = event.toElement;

  while (!targetElement.classList.contains("task")) {
    targetElement = targetElement.parentElement;
  }

  var checkbox = targetElement.querySelector(".checkbox");

  if (checkbox.checked) {
    targetElement.classList.add("completed");
  } else {
    targetElement.classList.remove("completed");
  }
});
$(document).ready(function () {
            $('.results > li').hide();

            $('div.tags').find('input:checkbox').live('click', function () {
                $('.results > li').hide();
                $('div.tags').find('input:checked').each(function () {
                    $('.results > li.' + $(this).attr('rel')).show();
                });
            });
        });      