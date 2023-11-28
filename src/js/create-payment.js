$(document).ready(function () {
  $("form").submit(function (event) {
    event.preventDefault();
    var formValues = $(this).serialize();
    $thiss = $(this).find("[type=submit]");
    $thiss.text("Wait...");
    $thiss.addClass("disabled");

    $.post("../../src/code/create-payment.php", formValues, function (data) {
      if (data === "successful") {
        $thiss.text("Redirecting...");
        window.location.replace(
          "https://spacebank.thalajaatdatabase.online/main/payment-portal"
        );
      } else {
          $thiss.text("Try Again");
        $thiss.removeClass("disabled");

        // Display the response in the modal
        $("#modalContent").html(data);

        // Open the modal
        $("#warning-alert-modal").modal("show");
      }
    });
  });
  
  // Close the modal when the close button is clicked
    $(".close").click(function() {
    var modal = document.getElementById("warning-alert-modal");
    modal.style.display = "none";
  });
});
