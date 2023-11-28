$(document).ready(function () {
  $("form").submit(function (event) {
    event.preventDefault();
    var formValues = $(this).serialize();
    $thiss = $(this).find("[type=submit]");
    $thiss.text("Wait...");
    $thiss.addClass("disabled");

    $.post("../../src/code/reset-password.php", formValues, function (data) {
      if (data == "successful") {
        $thiss.text("Redirecting...");
        window.location.replace(
          "https://spacebank.thalajaatdatabase.online/main/login"
        );
      } else {
        $thiss.text("Try Again");
        $thiss.removeClass("disabled");
        $("#result").html(data);
      }
    });
  });
});
