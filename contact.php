 <!-- Map Column -->
            <div class="col-md-8">
 School Location
                <!-- Embedded Google Map -->
                <!-- <iframe width="100%" height="400px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed/v1/place?q=green%20valley%20%20General%20Paulino%20Santos%20Drive%2C%20Koronadal%20City%2C%209506%20South%20Cotabato%2C%20Philippines"></iframe> -->
           <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
          
           <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
           <div style="overflow:hidden;height:400px;width:100%;"><div id="gmap_canvas" style="height:100%;width:400px;">
           <style>#gmap_canvas img{max-width:none!important;background:none!important}</style>
           <a class="google-map-code" href="http://www.map-embed.com" id="get-map-data">embed google map</a>
           </div>
           <script type="text/javascript">
    function init_map() {
        var myOptions = {
            zoom: 16, // Adjusted zoom for better neighborhood visibility
            center: new google.maps.LatLng(13.5211508, 123.4915954),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        
        var map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
        
        var marker = new google.maps.Marker({
            map: map,
            position: new google.maps.LatLng(13.5211508, 123.4915954)
        });
        
        var infowindow = new google.maps.InfoWindow({
            content: "<b>San Pedro</b><br/>Iriga City, Camarines Sur<br/>Philippines"
        });
        
        google.maps.event.addListener(marker, "click", function() {
            infowindow.open(map, marker);
        });
        
        infowindow.open(map, marker);
    }
    
    google.maps.event.addDomListener(window, 'load', init_map);
</script>
            </div>
            <!-- Contact Details Column -->
            <div class="col-md-4">
                <h3>Contact Details</h3>
                <p>
                    Green Valley College 
                <br>General Paulino Santos Drive, Koronadal City, 9506 South Cotabato, Philippines<br>
                </p>
                <p><i class="fa fa-phone"></i> 
                    <abbr title="Phone">P</abbr>: (083) 228-9722</p>
                <p><i class="fa fa-envelope-o"></i> 
                    <abbr title="Email">Email</abbr>: <a href="mailto:name@example.com">Admission@greenvalleyph.com</a>
                </p>
                <p><i class="fa fa-clock-o"></i> 
                    <abbr title="Hours">H</abbr>: Monday - Friday: 9:00 AM to 5:00 PM</p>
                <ul class="list-unstyled list-inline list-social-icons">
                    <li>
                        <a href="#"><i class="fa fa-facebook-square fa-2x"></i></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-linkedin-square fa-2x"></i></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-twitter-square fa-2x"></i></a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-google-plus-square fa-2x"></i></a>
                    </li>
                </ul>
            </div>