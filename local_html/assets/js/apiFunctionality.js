function getJobs(){
    markers.clearLayers();
    Plotly.purge("myDiv");
    $( "#loading" ).toggle();
    let city = document.getElementById("citySearch").value;
    let strict = document.getElementById("strict").checked;
    let keywords = [];
    var x = document.getElementsByClassName("skillSearch");
    for (var i = 0; i < x.length; i++) {
        if(x[i].value.length > 0){
            keywords.push(x[i].value)
        }
    }


    $.ajax({
        type: "GET",
        url: "api/getJobs",
        data: {"city": city, "keywords": keywords.join(), "strict" : strict},
        success: function(data)
        {
            $( "#loading" ).toggle();
            let json = JSON.parse(data);

            populateLeaflet(json[0]);
            populateTable(json[0]);
            populateSunburst(json[1], keywords.length);
        }
    });
}

function populateLeaflet(data){
    if(data.length == 0){
        alert("No Jobs found with all of the keywords listed");
        return;
    }

    map.flyTo([data[0]['lat'],data[0]['lng']], 12);
    var marker;
    data.forEach(element =>{
        marker = L.marker([element['lat'], element['lng']]).bindPopup("<h4>"+element['title']+"</h4><h6>"+element['company']+"<br><a href='"+element['url']+"'>Job Link</a></h6>")
        marker.id = element['id']
        markers.addLayer(marker);
    })
    map.addLayer(markers);
}

function populateTable(data){
    $('#tableListing').dataTable().fnClearTable();
    $('#tableListing').dataTable().fnDestroy();
    data.forEach(element =>{
        let newRow = "<tr><td>"+ element['title'] +"</td><td>"+ element['company'] +"</td><td>"+ element['posted'] +"</td><td>"+ element['url'] +"</td></tr>"
        $("#tableListing tbody").append(newRow);
    })
    $('#tableListing').DataTable();
}

function populateSunburst(data, numSkills){
    let count = 1;

    Object.keys(data).forEach(item => count += data[item].length + 1);

    values1 = [0];
    labels1 = ["Click keywords<br>to expand"];
    parents1 = [""];
    maxdepth = 3;
    if(numSkills > 3){
        maxdepth = 2;
    }
    Object.keys(data).forEach(item => {
            labels1.push(item);
            parents1.push(0);
            let sum = 0;
            data[item].forEach(element => {
                sum += element[1];
            })
            values1.push(sum / data[item].length);

        }
    );
    Object.keys(data).forEach(item => {
            data[item].forEach(element => {
                labels1.push(element[0]);
                parents1.push(labels1.indexOf(item));
                values1.push(element[1]);
            })

        }
    );



    var data = [{
        maxdepth: maxdepth,
        type: "sunburst",
        ids: [...Array(count).keys()],
        labels: labels1,
        parents: parents1,
        values: values1,
        outsidetextfont: {size: 20, color: "#377eb8"},
        leaf: {opacity: 0.4},
        marker: {line: {width: 2}},
    }];

    var layout = {
        margin: {l: 0, r: 0, b: 0, t: 0},
        sunburstcolorway:["#636efa","#ef553b","#00cc96", "#ed63fa","#3182bd", "#e6550d", "#9ecae1", "#fdae6b", "#9ecae1",
            "#fdae6b", "#deebf7", "#deebf7", "#deebf7", "#fee6ce"],
    };
    var config = {responsive: true}


    Plotly.newPlot('myDiv', data, layout, config);

}
