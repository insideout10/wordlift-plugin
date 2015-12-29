<?php
/**
 * User: david
 * Date: 02/08/12 15:57
 */

class WordPress_ProxyService {

    public $logger;

    public $urlPrefix;
    public $expiryModifier;
    public $dateFormat;
    public $cacheSubdir;
    public $hashAlgo;

    public function cacheURL( $url ) {

        // check that the url starts with the right prefix.
        if ( 0 !== strpos( $url, $this->urlPrefix ) )
            throw new Exception( "The requested URL does not respect the configuration constraints [$url]." );

        // prepare the request.
        $options = array( "http" => array(
            "method"  => "GET"
        ));

        $context = stream_context_create( $options );

        // execute the request.
        $stream = fopen($url, 'r', false, $context);
        $contents = stream_get_contents($stream);
        fclose($stream);

        // get the local filename.
        $filename = $this->getLocalFilename( $contents, $url );

        $uploadDirectory = wp_upload_dir();
        $localDirectory = $uploadDirectory[ "basedir" ] . $filename[ "subdir" ];
        $localFile = $uploadDirectory[ "basedir" ] . $filename[ "subdir" ] . "/" . $filename[ "filename" ];
        $localURL = $uploadDirectory[ "baseurl" ] . $filename[ "subdir" ] . "/" . $filename[ "filename" ];

        // create the cache subdirectory if it doesn't exist.
        if ( !file_exists( $localDirectory ) || is_file( $localDirectory ))
            mkdir( $localDirectory, 0775, true );

        if ( file_exists( $localFile ) || is_file( $localFile ) )
            return $localURL;

        $bytes = file_put_contents(
            $localFile,
            $contents,
            LOCK_EX
        );

        if ( false === $bytes ) {
            $this->logger->warn( "Could not save a local copy of URL [$url] to file [$localFile]." );
            return $url;
        }

        return $localURL;

//        // Thu, 09 Aug 2012 13:30:01 GMT
//        $date = date("Y-m-d H:i:s"); // current date
//        $date = strtotime(date("Y-m-d H:i:s", strtotime($date)) . " $this->expiryModifier");
//
////        header( "Expires: " . date( $this->dateFormat, $date ) );
//

//        if ( NULL !== $contents ) {
//            foreach ( $http_response_header as $header)
//                header( $header );
//
//            header( "X-Served-From: remote" );
//            echo $contents;
//            exit;
//        }
//
//        if ( file_exists( $path ) && is_file( $path ) ) {
//            header( "Location: $cacheURL" );
////            echo file_get_contents( $path );
//            exit;
//        }
//
//        echo "error";

    }

    private function getLocalFilename( $contents, $url ) {
        // $filename = preg_replace( "/[^\\w\\d\\_\\.]/i", "-", $url );

        $pathInfo = pathinfo( $url );
        $extension = $pathInfo[ "extension" ];
        $hashCode = hash( $this->hashAlgo, $contents );
        $filename = strlen( $contents ) . "-$hashCode.$extension";

        $subdir = "/$this->cacheSubdir/" . substr( $hashCode, 0, 2 );

        return array(
            "filename" => $filename,
            "subdir" => $subdir
        );
    }

}

?>