<?php
    class wdgt13d228d3bbeee7d2657183a568688e3d extends widget{

        var $uuid ="wdgt:13d228d3-bbee-e7d2-6571-83a568688e3d";
        var $type = "big";
        var $title = "Events";
        var $jsIncludes = array('modules/base/widgets/events/events.js');
        var $cssIncludes = array('widgets/base/events.css');

        function displayMiddle(){

            ?><div id="eventsBox"></div><?php

        }//end function displayMiddle

    }//end class events
?>
