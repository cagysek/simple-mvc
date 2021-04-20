document.addEventListener("DOMContentLoaded", function(event) {
    createGraph();
});

function createGraph()
{
    let graphData = document.getElementById("graph-data");

    if (!graphData)
    {
        return;
    }

    graphData = JSON.parse(graphData.innerText);

    var ctx = document.getElementById("graf_hodin_odevzdani");
    var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: [{
                    label: 'Počet odevzdání',
                    data: graphData,
                    borderWidth: 1
                }]
            }
        });
}