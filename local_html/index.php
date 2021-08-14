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
    <link rel="stylesheet" href="assets/css/rangeslider.css" />
    <link rel="stylesheet" href="assets/css/MarkerCluster.css" />
    <link rel="stylesheet" href="assets/css/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.0/jquery.typeahead.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">



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
                        <?php require('frames/cityFrame.html') ?>
                        <div id="single-form-next-prev" class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleIn">
                            <?php require('frames/skillsFrame.html') ?>
                        </div>
                        <div id="single-form-next-prev-1" class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleIn">
                            <?php require('frames/resultsFrame.html') ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script src="assets/js/third-party/jquery.min.js"></script>
                <script src="assets/js/third-party/rangeslider.min.js"></script>
    <?php require('frames/advancedOptions.html') ?>
    

            <script src="assets/js/third-party/typeahead.js"></script>
            <script src="assets/bootstrap/js/bootstrap.min.js"></script>
            <script src="assets/js/script.js"></script>
            <script src="assets/js/third-party/Multi-step-form.js"></script>
            <script src="assets/js/third-party/leaflet2.js"></script>
            <script src="assets/js/third-party/leaflet.markercluster.js"></script>
            <script src="assets/js/leaflet.js"></script>
            <script src="assets/js/apiFunctionality.js"></script>
            <script src='https://cdn.plot.ly/plotly-latest.min.js'></script>
            <script src='https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js'></script>

</body>

</html>