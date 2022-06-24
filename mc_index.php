<?php
//Connection to Database
require_once("mc_db.php");
$arr = connectToDB::getLitterList();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Littermap - Chillin Chameleons</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- map -->
    <script src="js/jquery.min.js"></script>
    <link rel="stylesheet" href="css/leaflet.css" />
    <script src="js/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>

    <!-- loading circle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.0.1/spin.min.js"></script>
    <script src="./js/leaflet.spin.min.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <!-- bootsstrap + fontawesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v5.0.8/js/all.js"></script>

    <!-- css styles -->
    <link href="./css/schoeneWebsitePLS.css" rel="stylesheet" />
</head>

<body>

    <!-- navigationbar -->
    <nav class="navbar navbar-expand-md navbar-light bg-light sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="img/CC.png" alt="ChillinChameleons Symbol" width="90px" height="90px"></a>
            <button class="navabr-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.chillinchameleons.com/#mission" target="_blank">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#upload">Upload Litter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#edit">Edit Litter</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#delete">Delete Litter</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>



    <!-- map section -->
    <div class="Map">
        <div id="map" style="width: 100%; height: 877px"></div>
    </div>


    <script>
        // = MappingNS = MappingNS ||
        //start marker = Munderkingen (hometown)
        var draggableMarker;
        var MappingNS = {
            latitude: 48.237829,
            longitude: 9.644874,
            map: null,
            osm: null,
            url: null
        };

        MappingNS.map = L.map('map').setView([MappingNS.latitude, MappingNS.longitude], 13);
        MappingNS.map.spin(true);

        //loading circle - stops when data is fully loaded
        setTimeout(function() {
            MappingNS.map.spin(false);
        }, 500);

        //integration of the different card styles
        //geocoding
        L.Control.geocoder().addTo(MappingNS.map);
        let osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        let osmAttrib = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors';
        let osm = new L.TileLayer(osmUrl, {
            minZoom: 1,
            maxZoom: 21,
            attribution: osmAttrib
        }).addTo(MappingNS.map);

        var wmsTopoLayer = L.tileLayer.wms('http://ows.mundialis.de/services/service?', {
            layers: 'TOPO-OSM-WMS'
        }).addTo(MappingNS.map);

        var wmsHillshadeLayer = L.tileLayer.wms('http://ows.mundialis.de/services/service?', {
            layers: 'SRTM30-Colored-Hillshade'
        }).addTo(MappingNS.map);

        var baseMaps = {
            "WMS-Layer": wmsTopoLayer,
            "Hillshade": wmsHillshadeLayer,
            "OpenStreetMaps": osm,
        };

        var overlayMaps = {};

        L.control.layers(baseMaps, overlayMaps).addTo(MappingNS.map);

        //function, to position the map:
        function centerMapTo(lat, lng) {
            MappingNS.map.setView([lat, lng], 13);
        }

        let pointArray = new Array();
        var string = "Your route:<br><br>";

        //getting the coordinates by clicking in the map
        function onMapClick(e) {
            console.log("You clicked on " + e.latlng);
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            $("#lat").val(lat);
            $("#lng").val(lng);
            draggableMarker = L.marker([lat, lng], {
                draggable: true,
                zIndexOffset: 900
            }).addTo(MappingNS.map);
            draggableMarker.setLatLng(lat, lng);
        }

        //create a draggable marker in the center of the map
        function putDraggable() {
            draggableMarker = L.marker([MappingNS.map.getCenter().lat, MappingNS.map.getCenter().lng], {
                draggable: true,
                zIndexOffset: 900
            }).addTo(MappingNS.map);

            //collect Lat,Lng values
            draggableMarker.on('dragend', function(e) {
                $("#lat").val(this.getLatLng().lat);
                $("#lng").val(this.getLatLng().lng);
            });
        }

        $(document).ready(function() {

            putDraggable();
            MappingNS.map.on('click ', onMapClick);

            //by klicking on the button --> start this function which writes the data into the database
            $("form#insert").submit(function(event) {
                console.log("AJAX Call");
                event.preventDefault(); //prevent the actual sending

                //identify the submitting form and metadata
                var form = $(this);
                var action = form.attr("action"),
                    method = form.attr("method"),
                    data = form.serialize();
                $.ajax({
                    url: action,
                    type: method,
                    data: data
                }).done(function(data) {
                    //transfer successfull
                    $('#message').html(data);
                    console.log("received: " + data);

                }).fail(function() {
                    //transfer failed
                    alert("error - the litter was not recorded into the database");
                }).always(function() {
                    //independent from transferstatus
                    console.log("AJAX 1 accomplished!");
                });
            });


        });

        //updatelitterID
        $(document).ready(function() {
            putDraggable();
            //by choosing the LitterID the data is received from the database and filled into the empty columns
            $("#litterID").change(function() {
                for (var i = 0; i < arr.length; i++) {
                    if (arr[i]['id'] == $('#litterID').val()) {
                        $('#littertype').val(arr[i]['littertype']);
                        $('#description').val(arr[i]['description']);
                        $('#latitude').val(arr[i]['latitude']);
                        $('#longitude').val(arr[i]['longitude']);
                        $('#discordname').val(arr[i]['discordname']);

                        //the litter will be indicated with a marker on the map
                        MappingNS.map.panTo([arr[i]['latitude'], arr[i]['longitude']]);
                        draggableMarker.setLatLng([arr[i]['latitude'], arr[i]['longitude']]);
                        draggableMarker.bindPopup("<b>" + arr[i]['litterID'] + " </b><br />" + arr[i]['littertype'] + " </b><br />" + arr[i]['description'] + " </b><br />" + arr[i]['discordname']).openPopup();
                        break;
                    }
                }
            });
        });
        //data is being transferred to the frontend --> convert into json-format
        var arr = JSON.parse('<?php echo json_encode($arr) ?>');
        console.log(arr);

        var fjb_action = "mc_updatelitter_db.php";
        $('#loading').html("");

        //by clicking the button "delete" --> method delete runs
        $('#delete').click(function() {
            console.log('You clicked on #delete!');
            fjb_action = "mc_deletelitter_db.php";
        });

        //formular from litter is being edited and updated in the database  
        $("form#update").submit(function(event) {
            console.log("AJAX Call");
            event.preventDefault(); //prevent the actual sending
            //identify the submitting form and metadata
            var form = $(this);
            var action = fjb_action;
            method = form.attr("method"),
                data = form.serialize();

            //start the AJAX Call
            $.ajax({
                url: action,
                type: method,
                data: data
            }).done(function(data) {

                //transfer successfull
                $('#message').html(data);
                if (fjb_action == "mc_deletelitter_db.php") {
                    var option = '';
                    var numbers = [1, 2, 3, 4, 5];
                    $('#litterID').append(option);
                    updateSelection();
                }
            }).fail(function() {

                //transfer failed --> error message
                alert("error");
            }).always(function() {

                //independent from transferstatus
                alert("Your entry has been updated successfully!");
            });
        });

        //edited info in the frontend will be written into the database 
        function updateSelection() {
            console.log('updateSelection()');
            var action = "getJsonEncodedLitterList.php",
                method = "post",
                data = "";
            $.ajax({
                url: action,
                type: method,
                data: data
            }).done(function(data) {
                //transfer successfull
                $('#message').html(data);
                $('#litterID').empty();

                //the selected LitterID will be deleted from the database
                if (fjb_action == "mc_deletelitter_db.php") {
                    var option = '';
                    data = eval(data);
                    $('#message').html(data.length + ":" + data);
                    for (var i = 0; i < data.length; i++) {
                        option += '<option value="' + data[i].id + '">' + data[i].litterID + '</option>';
                    }
                    $('#litterID').append(option);
                }
            }).fail(function() {
                //transfer failed --> error message
                alert("Fehler");
            }).always(function() {
                //independent from transferstatus
                alert("Your entry has been deleted successfully!");
            });
        }

        //for every Litter entry their location will be shown on the map 
        let markers = {};

        function createMarker(lat, lng, s1, s2) {
            let newMarker = new L.marker([lat, lng], {
                draggable: true,
                zIndexOffset: 900,
                title: s1
            }).addTo(MappingNS.map);
            newMarker.bindPopup("<b>" + s1 + " </b><br />" + s2);
        }

        function createMarkerWithId(id, lat, lng, s1, s2) {
            let newMarker = new L.marker([lat, lng], {
                draggable: true,
                zIndexOffset: 900,
                title: s1
            }).addTo(MappingNS.map);
            newMarker.bindPopup("<b>" + s1 + " </b><br />" + s2);
            markers[id] = newMarker;
            markers[id]._icon.id = id;
        }
    </script>

    </div>

    <!-- jumbotron -->
    <div class="container-fluid padding">
        <div class="row jumbotron text-center">
            <div class="col-12">
                <p class="lead">The overall mission of the Chillin Chameleons is to create a self sustainable future for
                    our planet,
                    through the implementation of innovative technologies that can take harmful waste from the ocean,
                    such as plastic,
                    and turn it into renewable energy. A portion of both our primary and secondary sales have been
                    allocated towards our partnered
                    non-profit ‘Pro2tect’ with the goal of helping fund our ocean conservation efforts and future
                    community cleanup events and initiatives.</p>
            </div>
            <div class="col-12">
                <a href="https://www.chillinchameleons.com/" target="_blank"><button type="button" class="btn btn-outline-secondary btn-lg">CC-Website</button></a>
            </div>
        </div>
    </div>

    <!-- welcome section -->
    <div class="container-fluid padding">
        <div class="row welcome text-center">
            <div class="col-12">
                <h1 class="display-4">Be the change.</h1>
            </div>
            <hr>
            <div class="col-12">
                <p class="lead">Welcome to the official Chillin Chameleons ChlitterMap!
                    This is a community driven LitterMap! You and everyone else in the community
                    can distribute to this map. With the help of all of you we can achieve
                    great things and generate a map with our progress!
                    Be a part of the change and do something good for mother nature.
                </p>
            </div>
        </div>
    </div>

    <!-- three column section -->
    <div class="container-fluid padding">
        <div class="row text-center padding">
            <div class="col-xs-12 col-sm-6 col-md-4">
                <i class="fas fa-upload"></i>
                <h3>UPLOAD</h3>
                <p>Upload the litter you found with all the Informations needed.</p>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-4">
                <i class="fas fa-edit"></i>
                <h3>EDIT</h3>
                <p>Edit the litter you uploaded by searching for the LitterID.</p>
            </div>
            <div class="col-sm-12 col-md-4">
                <i class="fas fa-eraser"></i>
                <h3>DELETE</h3>
                <p>Delete the litter you uploaded by searching for the LitterID.</p>
            </div>
        </div>
    </div>

    <!-- image slider -->
    <!--<div id="slides" class="carousel slide" data-ride="carousel">
        <ul class="carousel-indicators">
            <li data-target="#slides" data-slide-to="0" class="active"></li>
            <li data-target="#slides" data-slide-to="1"></li>
            <li data-target="#slides" data-slide-to="2"></li>
        </ul>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/grouppic.jpg" style="width:100%; height:400px">
            </div>
            <div class="carousel-item">
                <img src="img/trash.jpg" style="width:100%; height:400px">
            </div>
            <div class="carousel-item">
                <img src="img/grouppic2.jpg" style="width:100%; height:400px">
            </div>
        </div>
    </div> -->

    <!-- two column section || insert litter -->
    <div class="container-fluid padding" id="upload">
        <div class="row welcome text-center">
            <div class="col-12">
                <h2>You found litter/rubbish? Add it into our Map!</h2>
                <p>All the small things matter - thats why even the smallest things
                    you pick up are necessary and sea-& wildlife changing!
                </p>
                <p>We face climate change, water-& air pollution and a decline of our sea-& wildlife!
                    It is our mission to counteract these devastating events! Be a part of the change and
                    clean up your evironment and add it into our ChlitterMap!
                </p><br>
                <div class="col-12">
                    <!-- Methode Post passt -->
                    <form class="img-fluid" id="insert" action="mc_addlitter_db.php" method="post" style="display: active;">
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tbody>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Give your Litter a Name!</td>
                                    <td align="left" valign="top"><textarea name="litterID">Give your Litter a unique name! (e.g WeaponizedSteel1, WeaponizedSteel2,...)</textarea>
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Type of Litter:</td>
                                    <td align="left" valign="top"><input type="text" name="littertype" /></td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Description:</td>
                                    <td align="left" valign="top"><textarea name="description"></textarea></td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Latitude:</td>
                                    <td align="left" valign="top"><input id="lat" type="text" name="latitude" /></td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Longitude:</td>
                                    <td align="left" valign="top"><input id="lng" type="text" name="longitude" /></td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Discordname:</td>
                                    <td align="left" valign="top"><input type="text" name="discordname" /></td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top"></td>
                                    <td align="left" valign="top"><input type="submit" value="Save"></td>
                                </tr>
                            </tbody>
                            <div id="message" align="left" valign="top"></div>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- two column section || edit litter -->
    <div class="container-fluid padding" id="edit">
        <div class="row welcome text-center">
            <div class="col-12">
                <h2>You want to change some details? Or add some necessary informations? Just edit them right here!</h2>
                <div class="col-lg-6">
                    <form id="update" action="mc_updatelitter_db.php" method="POST">
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tbody>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Your LitterID:</td>
                                    <td align="left" valign="top">
                                        <select id="litterID" name="litterID" class="form-select">
                                            <option value="0">Please choose your LitterID</option>

                                            <?php for ($i = 0; $i < count($arr); $i++) {
                                                print '<option value="' . $arr[$i]['id'] . '">' . $arr[$i]['litterID'] . '</option>';
                                            } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Type of Litter:</td>
                                    <td align="left" valign="top"><input type="text" name="littertype" /></td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Description:</td>
                                    <td align="left" valign="top"><textarea id="description" name="description"></textarea>
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Latitude:</td>
                                    <td align="left" valign="top"><input id="latitude" type="text" name="latitude" />
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Longitude:</td>
                                    <td align="left" valign="top"><input id="longitude" type="text" name="longitude" />
                                    </td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top">Discordname:</td>
                                    <td align="left" valign="top"><input type="text" name="discordname" /></td>
                                </tr>
                                <tr align="left" valign="top">
                                    <td align="left" valign="top"></td>
                                    <td align="left" valign="top"><input id='update' type="submit" value="Update"><input id='delete' type="submit" value="Delete"></td>
                                </tr>
                            </tbody>
                            <div id="message" align="left" valign="top"></div>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- two column section || delete litter -->
    <div class="container-fluid padding" id="delete">
        <div class="row welcome text-center">
            <div class="col-12">
                <form action="mc_deletelitter_db.php" method="POST">
                    <h2>Delete a Litter you found!</h2>
                    <table cellpadding="5" cellspacing="0" border="0">
                        <tbody>
                            <tr align="left" valign="top">
                                <td align="left" valign="top">Your LitterID:</td>
                                <td align="left" valign="top">
                                    <select id="litterID" name="litterID" class="form-select">
                                        <option value="0">Please choose your LitterID</option>

                                        <?php for ($i = 0; $i < count($arr); $i++) {
                                            print '<option value="' . $arr[$i]['id'] . '">' . $arr[$i]['litterID'] . '</option>';
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr align="left" valign="top">
                                <td align="left" valign="top"></td>
                                <td align="left" valign="top"><input type="submit" value="Delete"></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <hr class="my-4">
    </div>

    <div class="table-responsive text-center" >
        <h2>Table with all entrys!</h2>
        <table class="table table-striped table-hover table-bordered table-sm table-light border-dark">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">LitterID</th>
                    <th scope="col">Littertype</th>
                    <th scope="col">Description</th>
                    <th scope="col">Discordname</th>
                    <th scope="col">Latitude</th>
                    <th scope="col">Longitude</th>
                </tr>
            </thead>
            <tbody>
                <script>
                    var arr = JSON.parse('<?php echo json_encode($arr) ?>');
                    for (i = 0; i < arr.length; i++) {
                        document.write("<tr>");
                        document.write("<td>" + arr[i]['id'] + "</td>");
                        document.write("<td>" + arr[i]['litterID'] + "</td>");
                        document.write("<td>" + arr[i]['littertype'] + "</td>");
                        document.write("<td>" + arr[i]['description'] + "</td>");
                        document.write("<td>" + arr[i]['discordname'] + "</td>");
                        document.write("<td>" + arr[i]['latitude'] + "</td>");
                        document.write("<td>" + arr[i]['longitude'] + "</td>");
                        document.write("</tr>");
                    }
                </script>
            </tbody>
        </table>
        <hr class="my-4">
    </div>


    <!-- connect -->
    <div class="container-fluid padding">
        <div class="row text-center padding">
            <div class="col-12">
                <h2>Connect</h2>
            </div>
            <div class="col-12 social padding">
                <a href="https://twitter.com/Chillnchameleon" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/chillinchameleons/" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://discord.gg/vgz6SxAV" target="_blank"><i class="fab fa-discord"></i></a>
            </div>
        </div>
    </div>

    <!-- footer -->
    <footer>
        <div class="container-fluid padding">
            <div class="row text-center">
                <div class="col-lg-6">
                    <img src="img/CC.png" alt="ChillinChameleons Symbol" width="57px" height="57px">
                    <hr class="light">
                    <p>Mert Cumert - WeaponizedSteel</p>
                    <p>cumert1999@gmail.com</p>
                    <p>70173 Stuttgart</p>
                </div>
                <div class="col-lg-6">
                    <hr class="light">
                    <h5>Our partners</h5>
                    <hr class="light">
                    <p>pro2tect</p>
                </div>
                <div class="col-12">
                    <hr class="light-100">
                    <h5>&copy; chillinchameleons.com</h5>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
