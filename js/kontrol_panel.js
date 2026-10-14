$(document).ready(function () {
  $("#startBtn").click(function () {
    $.ajax({
      type: "GET",
      url: "control.php?action=start",
      success: function (data) {
        alert("Program DiJalankan: " + data);
      },
    });
  });

  $("#stopBtn").click(function () {
    $.ajax({
      type: "GET",
      url: "control.php?action=stop",
      success: function (data) {
        alert("Program Diberhentikan: " + data);
      },
    });
  });
});
