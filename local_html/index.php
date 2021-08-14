<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>455</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">

    <link rel="stylesheet" href="assets/css/Add-Another-Button.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link rel="stylesheet" href="assets/css/Multi-step-form.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.0/jquery.typeahead.min.css">

</head>

<body style="background: var(--gray-dark);">
    <h1 class="text-center" style="color: var(--light);">Skill Finder</h1>
    <section>
        <!-- Defines the progress bar at the top that is clickable to jump to different frames, can be though of as a header, its in a separate div to the body as its persistent throughout all the frames -->
        <div id="multple-step-form-n" style="margin-top: 0px;margin-bottom: 10px;padding-top: 57px;">
            <div id="progress-bar-button" class="multisteps-form">
                <div class="row">
                    <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
                        <div class="multisteps-form__progress"><a class="btn multisteps-form__progress-btn js-active" role="button" title="User Info">City</a><a class="btn multisteps-form__progress-btn" role="button" title="User Info">Skills</a><a class="btn multisteps-form__progress-btn" role="button" title="User Info">Results</a></div>
                    </div>
                </div>
            </div>
            <!-- The body, which describes each one of the frames, which I will move to their own files for code readability -->
            <div id="multistep-start-row" class="row">
                <div id="multistep-start-column" class="col-12 col-lg-8 m-auto">
                    <!-- Frames wrapped in a form so the data can be sent to the api for processing -->
                    <form id="main-form" class="multisteps-form__form">
                        <div id="single-form-next" class="multisteps-form__panel shadow p-4 rounded bg-white js-active" data-animation="scaleIn">
                            <h3 class="text-center multisteps-form__title">Enter your City</h3>

                            <div id="form-content" class="multisteps-form__content">
                                <div id="input-grp-single" class="form-row mt-4">
                                    <div class="col-12">
                                    	<input class="form-control multisteps-form__input typeahead" id="citySearch" type="text" placeholder="City">
                                    </div>
                                </div>
                                <div id="next-button" class="button-row d-flex mt-4">
                                	<button class="btn btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next</button>
                                </div>
                            </div>
                        </div>
                        <div id="single-form-next-prev" class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleIn">
                            <h3 class="text-center multisteps-form__title">Skills</h3>
                            <div id="form-content-1" class="multisteps-form__content">
                                <div id="input-grp-single-1" class="form-row mt-4">
                                    <div class="col-12"><input class="form-control multisteps-form__input skillSearch" type="text" placeholder="Skill 1"></div>
                                </div>
                                <br>

                                <button id='addButton' class="btn btn-outline-primary text-truncate float-none float-sm-none add-another-btn" data-bss-hover-animate="pulse" type="button">Add Skill<i class="fas fa-plus-circle edit-icon"></i></button>
                            
                                <div class="form-check">
								    <input type="checkbox" class="form-check-input" id="strict">
								    <label class="form-check-label" for="exampleCheck1">Include Only Jobs that match ALL keywords</label>
								  </div>
                                <div id="next-prev-buttons" class="button-row d-flex mt-4">
                                	<button class="btn btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>                            
                                	<button class="btn btn btn-primary ml-auto js-btn-next" id='getResults' onclick="getJobs()" type="button" title="Next">Find Results</button>
                                </div>
                            </div>
                        </div>
                        <div id="single-form-next-prev-1" class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleIn">
                            <h3 class="text-center multisteps-form__title">Results</h3>
                            	<img class="centered" id="loading" style="display:none" src="load.gif">
                            	<div id='myDiv'><!-- Plotly chart will be drawn inside this DIV --></div>
				          		<br>
                                <div id="mapid"></div>
                                <div id="next-prev-buttons-1" class="button-row d-flex mt-4"><button class="btn btn btn-primary js-btn-prev" type="button" id="jump" title="Prev">Prev</button></div>
                                <h4 class="text-center">Click Marker to view job description</h4>
                                <div id="description">
    							</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/typeahead.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/Multi-step-form.js"></script>
    <script src="assets/js/leaflet2.js"></script>
    <script src="assets/js/leaflet.js"></script>
    <script src='https://cdn.plot.ly/plotly-latest.min.js'></script>



    <script>
    	var layerGroup = L.featureGroup().addTo(map).on("click", groupClick);
    	$( "#jump" ).click(function() {
    		document.getElementById("description").innerHTML = "";
  $('html,body').scrollTop(0);
});

    	function getJobs(){
    		layerGroup.clearLayers();
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
    			marker = L.marker([element['lat'], element['lng']]).addTo(layerGroup).bindPopup("<h4>"+element['title']+"</h4><h6>"+element['company']+"<br><a href='"+element['url']+"'>Job Link</a></h6>")
    			marker.id = element['id']
    			})
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

    	function groupClick(event) {
    		$.ajax({
	           type: "GET",
	           url: "api/getJobDesc",
	           data: {"id": event.layer.id},
	           success: function(data)
	           {
	           	let json = JSON.parse(data);

	           	document.getElementById("description").innerHTML = json['description'];

	           }
	         });
		}





    </script>
</body>
</html>