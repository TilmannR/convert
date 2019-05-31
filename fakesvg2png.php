<?php
$tool_user_name = 'svgworkaroundbot';
include_once ( 'shared/common.php' ) ;
error_reporting( E_ALL & ~E_NOTICE ); # Don't clutter the directory with unhelpful stuff
$url = getProtocol() . "://tools.wmflabs.org/$tool_user_name/";
if ( !array_key_exists( 'file', $_FILES ) ) {
  header( "Location: $url" );
  die();
}
file_put_contents('foobar', "$(date) phpZeile 10\n", FILE_APPEND);
$uploadName = $_FILES['file']['tmp_name'];
$fileName = $uploadName . '.svg';
$targetName = $fileName . '.png';
file_put_contents('foobar', "$(date) phpZeile 13\n", FILE_APPEND);
if ( $_FILES['file']['size'] > 6*0x100000 ) {
  unlink( $uploadName );
  header( "Location: $url#tooBig" );
  die();
}
file_put_contents('foobar', time(), FILE_APPEND);
if ( !move_uploaded_file( $uploadName, $fileName ) ) {
  unlink( $uploadName );
  header( "Location: $url#cantmove" );
  echo( 'cant move uploaded file' );
  die();
}
file_put_contents('foobar', "$(date) phpZeile 25\n", FILE_APPEND);
exec( './svg2base.sh ' . escapeshellarg( $fileName ) . ' ' . escapeshellarg( $targetName ) );
file_put_contents('foobar', '$(date) phpZeile 27\n', FILE_APPEND);
unlink( $fileName );
$file = 'tmp.svg';
// Öffnet die Datei, um den vorhandenen Inhalt zu laden
$current = file_get_contents($file);
// Fügt eine neue Person zur Datei hinzu
$current .= "John Smith\n";
// Schreibt den Inhalt in die Datei zurück
file_put_contents($file, $current);
$handle = fopen( $targetName, 'r' );
if ( $handle === false ) {
  header( "Location: $url#conversionError" );
  echo( 'error converting the file' );
  die();
}
if ( filesize( $targetName ) > 10*0x100000 ) {
  fclose( $handle );
  unlink( $targetName );
  header( "Location: $url#outputTooHuge" );
  echo( 'output unexpectedly huge' );
  die();
}
$content = file_get_contents( $targetName );
fclose( $handle );
unlink( $targetName );
if ( strlen( $content ) > 10*0x100000 ) {
  header( "Location: $url#outputTooHuge2" );
  echo( 'output unexpectedly huge 2' );
  die();
}
header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
header( 'Content-type: image/png' );
header( 'Content-Disposition: attachment; filename="' . addslashes( $_FILES['file']['name'] ) . '.png"' );
echo $content;
die();
