<html>


<head>

    {literal}<script>
    
        var req;

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
    
        function SendQuery() {
            Initialize();
            var url = "{/literal}{$YD_SELF_SCRIPT}{literal}?do=server";
            if ( req != null ) {
                req.onreadystatechange = Process;
                req.open( "GET", url, true );
                req.send( null );
            }
        }
    
        function Process() {
            if ( req.readyState == 4 ) {
                if ( req.status == 200 ) {
                    if( req.responseText == "" ) {
                        HideDiv( "autocomplete" );
                    } else {
                        ShowDiv( "autocomplete" );
                        document.getElementById( "autocomplete" ).innerHTML = req.responseText;
                    }
                } else {
                    document.getElementById("autocomplete").innerHTML = "There was a problem retrieving data:<br/>" + req.statusText;
                }
            }
        }
    
        function ShowDiv( divid ) {
            if ( document.layers ) {
                document.layers[divid].visibility = "show";
            } else {
                document.getElementById(divid).style.visibility = "visible";
            }
        }
    
        function HideDiv( divid ) {
            if ( document.layers ) {
                document.layers[divid].visibility = "hide";
            } else {
                document.getElementById(divid).style.visibility = "hidden";
            }
        }
    
        function BodyLoad() {
            HideDiv( "autocomplete" );
        }
        
    </script>{/literal}

</head>

<body onload="BodyLoad();">

    <form name="form1">
        <input type="submit" onClick="SendQuery(); return false;" value="Get version info" />
        <br/>&nbsp;<br/>
        <div align="left" class="box" id="autocomplete" style="WIDTH:500px;BACKGROUND-COLOR:#ccccff"></div>
    </form>

</body>

</html>
