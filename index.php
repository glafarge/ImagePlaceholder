<?php

/**
 * Image placeholder
 * @copyright     Copyright 2012, Guillaume Lafarge http://guillaumelafarge.fr
 */

    /**
     * Required classes  
     */
        require('class/PlaceholderRequest.php');
        require('class/PlaceholderImage.php');


    /**
     * Here's where all the magic happens :)
     */

        // A good query makes me happy
        if(PlaceholderRequest::parseParameters()) {		
                $placeholder = new PlaceholderImage( PlaceholderRequest::$config );
                
                if(isset($_GET['forceDownload']))
                    $placeholder->forceDownload();
                else
                    $placeholder->output();
        }


    /**
     * Home page 
     */
        
        include('home.php');

?>