<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Masuk Realtime</title>
</head>
<body onload ="table();">
    <script type="text/javascript">
        function table(){
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function(){
                document.getElementById("table").innerHTML = this.responseText;
            }
            xhttp.open("GET", "log_masuk.php");
            xhttp.send();
        }

        setInterval(function () {
            table();
        }, 10000);
    </script>
    <div id="table"></div>
</body>
</html>