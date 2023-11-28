$(document).ready(function () {
  $("form").submit(function (event) {
    event.preventDefault();
    var formValues = $(this).serialize();
    $thiss = $(this).find("[type=submit]");
    $thiss.text("Wait...");
    $thiss.addClass("disabled");

    $.post("../../src/code/withdraw.php", formValues, function (data) {
           if (data == "success") {
        //$thiss.text("Try Again");
        $thiss.removeClass("disabled");
 var successMessage = 'Your withdrawal is successful';
        // Display the response in the modal
        $("#modalContent").html(successMessage);

        // Open the modal
        $("#success-alert-modal").modal("show");
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
    var modal = document.getElementById("success-alert-modal");
    modal.style.display = "none";
  });
  
    $(".close").click(function() {
    var modal = document.getElementById("warning-alert-modal");
    modal.style.display = "none";
  });
});
