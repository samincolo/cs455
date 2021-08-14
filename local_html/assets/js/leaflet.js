var map = L.map('mapid').setView([39.7392, -104.9903], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var markers = L.markerClusterGroup({
    showCoverageOnHover: false});

markers.on('click', groupClick)
$( "#jump" ).click(function() {
    document.getElementById("description").innerHTML = "";
    $('html,body').scrollTop(0);
});

function groupClick(event) {
    let id = null;
    if(!event.layer){
        id = event;
    }
    else{
        id = event.layer.id;
    }
    $.ajax({
        type: "GET",
        url: "api/getJobDesc",
        data: {"id": id},
        success: function(data)
        {
            let json = JSON.parse(data);
            document.getElementById("description").innerHTML = json['description'];
        }
    });
}