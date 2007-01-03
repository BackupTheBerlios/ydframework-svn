<html>

<head>

    <title>Adobe Spry + Yellow Duck Framework</title>

    <script type="text/javascript" src="xpath.js"></script>
    <script type="text/javascript" src="SpryData.js"></script>

    {literal}<style>
        .even {
            background-color: #eee;
        }
        .odd {
            background-color: #fff;
        }
        th {
            text-align: left;
            background-color: #ccc;
        }
    </style>

    <script type="text/javascript">

        var dsData = new Spry.Data.XMLDataSet( "?do=GetRecords", "root/row" );

        function FilterData() {
            var tf = document.getElementById( "filterTF" );
            if ( ! tf.value ) {
                dsData.filter(null);
                return;
            }
            var regExpStr = tf.value;
            if ( ! document.getElementById( "containsCB" ).checked ) {
                regExpStr = "^" + regExpStr;
            }
            var regExp = new RegExp( regExpStr, "i" );
            var filterFunc = function( ds, row, rowNumber ) {
                var str = row["variable_name"];
                if ( str && str.search( regExp ) != -1 ) {
                    return row;
                }
                return null;
            };
            dsData.filter( filterFunc );
        }

        function StartFilterTimer() {
            if ( StartFilterTimer.timerID ) {
                clearTimeout( StartFilterTimer.timerID );
            }
            StartFilterTimer.timerID = setTimeout(
                function() { StartFilterTimer.timerID = null; FilterData(); }, 100
            );
        }

    </script>{/literal}

</head>

<body>

    <h1>Adobe Spry + Yellow Duck Framework</h1>

    <p>Enter the name of a variable: 
        <input type="text" id="filterTF" onkeyup="StartFilterTimer();" /> 
        Contains: <input type="checkbox" id="containsCB" />
    </p>

    {literal}<div spry:region="dsData">
        <table border="0" width="100%" cellspacing="0" cellpadding="4">
            <tr>
                <th scope="col" onclick="dsData.sort('variable_name');">Variable</th>
                <th scope="col" onclick="dsData.sort('value');">Value</th>
            </tr>
            <tr spry:repeat="dsData" class="{ds_EvenOddRow}">
                <td>{variable_name}</td>
                <td>{value}</td>
            </tr>
        </table>
    </div>{/literal}

</body>

</html>