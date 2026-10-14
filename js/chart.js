document.addEventListener("DOMContentLoaded", function () {
  fetch("chart.php")
  .then(response => response.json())
  .then(data => {
    console.log("DATA:", data);
    let chart = new ej.charts.Chart({
      
      title: "Data Jumlah Kunjungan 4 Hari Terakhir",
      
      tooltip: { enable: true },
      
      primaryXAxis: {
        valueType: "Category",
        title: "Hari"
      },
      
      primaryYAxis: {
        title: "Jumlah Masuk"
      },
      
      series: [{
        type: "Column",
        dataSource: data,
        xName: "hari",
        yName: "pengunjung",
        
        marker: {
          visible: true,
          dataLabel: { visible: true }
        }
      }]
    });
    
    chart.appendTo("#container");
  });
});