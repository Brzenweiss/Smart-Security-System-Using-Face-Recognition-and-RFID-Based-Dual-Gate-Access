function writeToCard() {
  var data = document.getElementById("dataInput").value;
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "write.php?data=" + data, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      alert(xhr.responseText);
    }
  };
  xhr.send();
}
