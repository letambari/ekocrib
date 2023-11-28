$(document).ready(function () {
  $("form").submit(function (event) {
    event.preventDefault();
    var formValues = $(this).serialize();
    $thiss = $(this).find("[type=submit]");
    $thiss.text("Wait...");
    $thiss.addClass("disabled");

    $.post("../../src/code/login.php", formValues, function (data) {
      if (data == "successful") {
        $thiss.text("Redirecting...");
        window.location.replace(
          "https://spacebank.thalajaatdatabase.online/main/dashboard"
        );
      } else {
        $thiss.text("Try Again");
        $thiss.removeClass("disabled");
        $("#result").html(data);
        
               // $("#result").html(data);
        
                // Display the response in the modal
        $("#modalContent").html(data);

        // Open the modal
        $("#warning-alert-modal").modal("show");
      }
    });
  });
  
      $(".close").click(function() {
    var modal = document.getElementById("warning-alert-modal");
    modal.style.display = "none";
  });
});
