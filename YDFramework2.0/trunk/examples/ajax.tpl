<html>


<head>

    {literal}<script>

        // The variable holding the request object
        var req;

        // Initialize the request object
        function Initialize() {
            try {
                req = new ActiveXObject( "Msxml2.XMLHTTP" );
            } catch( e ) {
                try {
                    req=new ActiveXObject( "Microsoft.XMLHTTP" );
                } catch( oc ) {
                    req=null;
                }
            }
            if ( ! req && typeof XMLHttpRequest != "undefined" ) {
                req= new XMLHttpRequest();
            }
        }

        // Send the query
        function SendQuery() {
            Initialize();
            var url = "{/literal}{$YD_SELF_SCRIPT}{literal}?do=server";
            if ( req != null ) {
                req.onreadystatechange = Process;
                req.open( "GET", url, true );
                req.send( null );
            }
        }

        // Process the response
        function Process() {
            if ( req.readyState == 4 ) {
                if ( req.status == 200 ) {
                    if( req.responseText == "" ) {
                        HideDiv( "version_display" );
                    } else {
                        ShowDiv( "version_display" );
                        document.getElementById( "version_display" ).innerHTML = req.responseText;
                    }
                } else {
                    document.getElementById("version_display").innerHTML = "There was a problem retrieving data:<br/>" + req.statusText;
                }
            }
        }

        // Show the div
        function ShowDiv( divid ) {
            if ( document.layers ) {
                document.layers[divid].visibility = "show";
            } else {
                document.getElementById(divid).style.visibility = "visible";
            }
        }

        // Hide the div
        function HideDiv( divid ) {
            if ( document.layers ) {
                document.layers[divid].visibility = "hide";
            } else {
                document.getElementById(divid).style.visibility = "hidden";
            }
        }

        // Executed when the body loads
        function BodyLoad() {
            HideDiv( "version_display" );
        }
        
    </script>{/literal}

</head>

<body onload="BodyLoad();">

    <form name="form1">
        <input type="submit" onClick="SendQuery(); return false;" value="Get version info" />
        <br/>&nbsp;<br/>
        <div align="left" class="box" id="version_display" style="WIDTH:500px;BACKGROUND-COLOR:#ccccff"></div>
    </form>

</body>

</html>
