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
        <div id="multple-step-form-n" class="container" style="margin-top: 0px;margin-bottom: 10px;padding-top: 57px;">
            <div id="progress-bar-button" class="multisteps-form">
                <div class="row">
                    <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
                        <div class="multisteps-form__progress"><a class="btn multisteps-form__progress-btn js-active" role="button" title="User Info">City</a><a class="btn multisteps-form__progress-btn" role="button" title="User Info">Skills</a><a class="btn multisteps-form__progress-btn" role="button" title="User Info">Results</a></div>
                    </div>
                </div>
            </div>
            <div id="multistep-start-row" class="row">
                <div id="multistep-start-column" class="col-12 col-lg-8 m-auto">
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
                                <div id="next-prev-buttons" class="button-row d-flex mt-4">
                                	<button class="btn btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>                            
                                	<button class="btn btn btn-primary ml-auto js-btn-next" id='getResults' onclick="getJobs()" type="button" title="Next">Find Results</button>
                                </div>
                            </div>
                        </div>
                        <div id="single-form-next-prev-1" class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleIn">
                            <h3 class="text-center multisteps-form__title">Map</h3>
                            <div id="form-content-2" class="multisteps-form__content">
                                <div id="mapid"></div>
                                <div id="next-prev-buttons-1" class="button-row d-flex mt-4"><button class="btn btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button><button class="btn btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next</button></div>
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

    <script>
    	L.marker([51.5, -0.09]).addTo(map)
    .bindPopup('A pretty CSS3 popup.<br> Easily customizable.');
    </script>

    <script>
    	function getJobs(){
    		let city = document.getElementById("citySearch").value;
    		let keywords = [];
    		var x = document.getElementsByClassName("skillSearch");
    		for (var i = 0; i < x.length; i++) {
    			if(x[i].value.length > 0){
 				 keywords.push(x[0].value)
    			}
			}

    	$.ajax({
           type: "GET",
           url: "api/getJobs",
           data: {"city": city, "keywords": keywords.join()},
           success: function(data)
           {
           	populateLeaflet(data);
           }
         });
    	}

    	function populateLeaflet(data){
    		console.log(data);
    		let json = JSON.parse(data);
    		map.flyTo([json[0]['lat'],json[0]['lng']], 12);
    		json.forEach(element =>
    			L.marker([element['lat'], element['lng']]).addTo(map).bindPopup(element['title'])
    			)
    	}
    </script>
</body>

</html>