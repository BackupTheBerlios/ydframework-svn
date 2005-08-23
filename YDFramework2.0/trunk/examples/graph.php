<?php

    // Standard include
    include_once( dirname( __FILE__ ) . '/../YDFramework2/YDF2_init.php' );

    // Includes
    YDInclude( 'YDRequest.php' );
    YDInclude( 'YDGraph.php' );
    YDInclude( 'YDGraphPie.php' );

    // Class definition
    class graph extends YDRequest {

        // Class constructor
        function graph() {

            // Initialize the parent
            $this->YDRequest();

            $this->vCht4 = array(60,40,20,34,26,52,41,20,34,43,64,40);
            $this->vCht5 = array(12,21,12,27,14,23,21,5,29,23,12,29);
            $this->vCht6 = array(5,7,3,15,7,8,2,2,2,11,22,3);
            $this->vCht7 = array(5,7,3,15);
            $this->vCht8 = array(1500, 2000, 4555, 5000, 200, 1514);
            $this->vColors8 = array( '#990000', '#AAAAFF', '#F4A460', '#70DB93', '#B2DFEE' );
            $this->vLabels = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
            $this->vLabels8 = array( 'Brazil', 'Belgium', 'Portugal', 'Holland', 'France', 'Germany' );

        }

        // Default action
        function actionDefault() {

            // The labels and the values for the graph
            $values = array( 60, 40, 20, 34, 26, 52, 41, 20, 34, 43, 64, 40 );
            $labels = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );

            // Create a new graph
            $g1 = new YDGraph( 550, 250 );

            // Set the limits
            $g1->setLimits( 0, 20 );

            // Set the offsets
            $g1->setOffset( 5 );

            // Set the format
            $g1->setFormat( 1, ',', '.' );

            // Add the series
            $g1->addSeries($values, 'bar', 'Series1', SOLID, '#444444', '#4682B4');

            // Set the Y axis
            $g1->setYAxis( '#4682B4', SOLID, 5, 'example' );

            // Set the labels
            $g1->setLabels( $labels, '#000000', 1, HORIZONTAL );

            // Plot the graph
            $g1->plot();

        }

        // AREA chart
        function actionDemo1() {
            $ochart = new YDGraph(300,130,7, '#eeeeee');
            $ochart->setTitle("Area","#000000",3);
            $ochart->setPlotArea(SOLID,"#000000", '#ddddee');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht4,'area','Series1', SOLID,'#000000', '#8888ff');
            $ochart->setXAxis('#000000', SOLID, 1, "", '%s');
            $ochart->setYAxis('#000000', SOLID, 1, "", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, HORIZONTAL);
            $ochart->setGrid("#bbbbbb", DOTTED, "#bbbbbb", DOTTED);
            $ochart->plot();
        }

        // AREA and LINE chart
        function actionDemo2() {
            $ochart = new YDGraph(300,130,7, '#eeeeee');
            $ochart->setTitle("Area & line","#000000",2);
            $ochart->setPlotArea(SOLID,"#000000", '#ddeedd');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht5,'area','Series1', SOLID,'#000000', '#ffffaa');
            $ochart->addSeries($this->vCht6,'area','Series2', SOLID,'#000000', '#ffaaaa');
            $ochart->addSeries($this->vCht4,'line','Series3', DASHED,'#000000', '#0000ff');
            $ochart->addSeries($this->vCht4,'dot','Series4', SOLID,'#000000', '#0000ff');
            $ochart->setXAxis('#000000', SOLID, 1, "", '%s');
            $ochart->setYAxis('#000000', SOLID, 1, "", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, HORIZONTAL);
            $ochart->setGrid("#bbbbbb", DOTTED, "#bbbbbb", DOTTED);
            $ochart->plot();
        }

        // BAR chart
        function actionDemo3() {
            $ochart = new YDGraph(300,130,7, '#eeeeee');
            $ochart->setTitle("Bar plot","#000000",2);
            $ochart->setPlotArea(SOLID,"#aaaaaa", '#ffffff');
            $ochart->setLimits(0,30,10);
            $ochart->addSeries($this->vCht5,'bar','Series1', SOLID,'#444444', '#aa4444');
            $ochart->setXAxis('#000000', SOLID, 1, "", '%s');
            $ochart->setYAxis('#000000', SOLID, 1, "", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, HORIZONTAL);
            $ochart->setGrid("#cccccc", DASHED, "", DOTTED);
            $ochart->plot();
        }

        // Multiple BAR chart
        function actionDemo4() {
            $ochart = new YDGraph(300,130,7, '#ccdddd');
            $ochart->setTitle("Multiple bar plot","#000000",2);
            $ochart->setPlotArea(SOLID,"#aaaaaa", '#eeffff');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht4,'bar','Series1', SOLID,'#444444', '#4444dd');
            $ochart->addSeries($this->vCht5,'bar','Series2', SOLID,'#444444', '#cc4444');
            $ochart->addSeries($this->vCht6,'bar','Series3', SOLID,'#444444', '#44dd44');
            $ochart->setXAxis('#000000', SOLID, 1, "", '%s');
            $ochart->setYAxis('#000000', SOLID, 1, "", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, HORIZONTAL);
            $ochart->setGrid("#cccccc", SOLID, "", DOTTED);
            $ochart->plot();
        }

        // BAR and LINE
        function actionDemo5() {
            $ochart = new YDGraph(300,130,7, '#000088');
            $ochart->setTitle("Bar & line","#ffffff",3);
            $ochart->setPlotArea(SOLID,"", '#000088');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht5,'bar','Series1', SOLID,'#000000', '#bbbb00');
            $ochart->addSeries($this->vCht4,'line','Series2', LARGE_SOLID,'#ffffff', '#bbbb00');
            $ochart->setXAxis('#ffffff', SOLID, 1, "", '%s');
            $ochart->setYAxis('#ffffff', SOLID, 1, "", '%d');
            $ochart->setLabels($this->vLabels, '#ffffff', 1, HORIZONTAL);
            $ochart->setGrid("#7777bb", SOLID, "7777bb", DOTTED);
            $ochart->plot();
        }

        // STEP and DOT
        function actionDemo6() {
            $ochart = new YDGraph(300,130,7, '#eeeeee');
            $ochart->setTitle("Step & dot","#444444",3);
            $ochart->setPlotArea(SOLID,"444444", '#bbeeff');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht4,'step','Series1', SOLID,'#000000', '#99ccff');
            $ochart->addSeries($this->vCht4,'dot','Series2', SOLID,'#0000ff', '#ffffff');
            $ochart->addSeries($this->vCht5,'step','Series3', DASHED,'#000000', '#66aadd');
            $ochart->setXAxis('#444444', MEDIUM_SOLID, 1, "", '%s');
            $ochart->setYAxis('#444444', SOLID, 1, "", '%d');
            $ochart->setLabels($this->vLabels, '#444444', 1, HORIZONTAL);
            $ochart->setGrid("#7777bb", DOTTED, "#7777bb", DOTTED);
            $ochart->plot();
        }

        // LINE
        function actionDemo7() {
            $ochart = new YDGraph(300,130,5, '#eeeeee');
            $ochart->setTitle("Lines plot","#000000",2);
            $ochart->setPlotArea(SOLID,"#444444", '#dddddd');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht4,'line','Series1', SOLID,'#ff0000', '#ffcccc');
            $ochart->setXAxis('#000000', SOLID, 2, "", '%s');
            $ochart->setYAxis('#000000', SOLID, 2, "", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, VERTICAL);
            $ochart->setGrid("#bbbbbb", DASHED, "#bbbbbb", DOTTED);
            $ochart->setLabels($this->vLabels, '#000000',1, 0);
            $ochart->plot();
        }

        // LINE and DOTS
        function actionDemo8() {
            $ochart = new YDGraph(300,130,5, '#ffffdd');
            $ochart->setTitle("Lines & dots","#000000",2);
            $ochart->setPlotArea(SOLID,"#444444", '#eeeedd');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht4,'line','Series1', SOLID,'#00aa00', '#ccffcc');
            $ochart->addSeries($this->vCht4,'dot','Series2', SOLID,'#00aa00', '#ccffcc');
            $ochart->setXAxis('#000000', SOLID, 1, "", '%s');
            $ochart->setYAxis('#000000', SOLID, 1, "Y axis", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, VERTICAL);
            $ochart->setGrid("#bbbbbb", 0, "", DOTTED);
            $ochart->plot();
        }

        // MULTITPLE LINES
        function actionDemo9() {
            $ochart = new YDGraph(300,130,5, '#ffdddd');
            $ochart->setTitle("Multiple line series","#000000",2);
            $ochart->setPlotArea(SOLID,"#444444", '#ddeeee');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht4,'line','Series1', SOLID,'#00aa00', '#ccffcc');
            $ochart->addSeries($this->vCht4,'dot','Series2', SOLID,'#00aa00', '#ccffcc');
            $ochart->addSeries($this->vCht5,'line','Series3', SOLID,'#0000aa', '#ccccff');
            $ochart->setXAxis('#000000', SOLID, 2, "", '%s');
            $ochart->setYAxis('#000000', SOLID, 2, "", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, VERTICAL);
            $ochart->setGrid("#bbbbbb", DASHED, "#bbbbbb", DOTTED);
            $ochart->setLabels($this->vLabels, '#000000',1, 0);
            $ochart->plot();
        }

        // IMPULS
        function actionDemo10() {
            $ochart = new YDGraph(300,130,5, '#eeeeee');
            $ochart->setTitle("Impuls plot","#000000",2);
            $ochart->setPlotArea(SOLID,"#444444", '#dddddd');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht4,'impuls','Series1', SOLID,'#000000', '#0000ff');
            $ochart->setXAxis('#000000', SOLID, 1, "X Axis", '%s');
            $ochart->setYAxis('#000000', SOLID, 1, "", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, VERTICAL);
            $ochart->setGrid("#bbbbbb", DASHED, "#bbbbbb", DOTTED);
            $ochart->plot();
        }

        // IMPULS and DOTS
        function actionDemo11() {
            $ochart = new YDGraph(300,130,5, '#eeeeee');
            $ochart->setTitle("Impuls & dots plot","#0000ff",2);
            $ochart->setPlotArea(SOLID,"#444444", '#dddddd');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht4,'impuls','Series1', SOLID,'#000000', '#0000ff');
            $ochart->addSeries($this->vCht4,'dot','Series2', SOLID,'#000000', '#0000ff');
            $ochart->setXAxis('#000000', SOLID, 1, "X Axis", '%s');
            $ochart->setYAxis('#000000', SOLID, 1, "", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, VERTICAL);
            $ochart->setGrid("#bbbbbb", DOTTED, "", DOTTED);
            $ochart->plot();
        }

        // IMPULS LINES and DOTS
        function actionDemo12() {
            $ochart = new YDGraph(300,130,7, '#bbeebb');
            $ochart->setTitle("Impuls, line & dot","#004400",2);
            $ochart->setPlotArea(SOLID,"#888888", '#bbbbbb');
            $ochart->setLimits(0,70,10);
            $ochart->addSeries($this->vCht4,'impuls','Series1', SOLID,'#000000', '#ffff00');
            $ochart->addSeries($this->vCht4,'line','Series2', SOLID,'#ff0000', '#ffff00');
            $ochart->addSeries($this->vCht4,'dot','Series3', SOLID,'#ff0000', '#ffff00');
            $ochart->setXAxis('#006600', MEDIUM_SOLID, 1, "", '%s');
            $ochart->setYAxis('#006600', SOLID, 5, "Y Axis", '%d');
            $ochart->setLabels($this->vLabels, '#000000', 1, HORIZONTAL);
            $ochart->plot();
        }
        
        // PIE CHART
        function actionDemo13() {
            
            $ochart = new YDGraphPie(300,200,7 );
            $ochart->setTitle("Countries","#000000",2);
            $ochart->setValues($this->vCht8, SOLID );
            $ochart->setLabels($this->vLabels8, '#000000', 2 );
            $ochart->setColors($this->vColors8 );
            $ochart->plot();
        }

    }

    // Process the request
    YDInclude( 'YDF2_process.php' );

?>
