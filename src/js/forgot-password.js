$(document).ready(function () {
  $("form").submit(function (event) {
    event.preventDefault();
    var formValues = $(this).serialize();
    $thiss = $(this).find("[type=submit]");
    $thiss.text("Wait...");
    $thiss.addClass("disabled");

    $.post("../../src/code/forgot-password.php", formValues, function (data) {
      if (data == "OTP validation successful.") {
        $thiss.text("Redirecting...");
        window.location.replace(
          "https://spacebank.thalajaatdatabase.online/main/dashboard"
        );
      } else {
        $thiss.text("Try Again");
        $thiss.removeClass("disabled");
        $("#result").html(data);
      }
    });
  });
});
