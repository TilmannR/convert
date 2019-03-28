<?PHP
/**
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along
* with this program; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
* http://www.gnu.org/copyleft/gpl.html
*
* @file
* @defgroup API API
*/

 
header("Connection: close");

function getProtocol()
{
	if (isset($_SERVER['HTTPS']) &&
		($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
		isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
		$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			return 'https';
	} else {
		return 'http';
	}
}
  
// http://noc.wikimedia.org/conf/highlight.php?file=CommonSettings.php
$wgCrossSiteAJAXdomains = array(
	'*.wikipedia.org',
	'*.wikimedia.org',
	'*.wikinews.org',
	'*.wiktionary.org',
	'*.wikibooks.org',
	'*.wikiversity.org',
	'*.wikisource.org',
	'wikisource.org',
	'*.wikiquote.org',
	'*.wikidata.org',
	'*.wikivoyage.org',
	'www.mediawiki.org',
	'm.mediawiki.org',
	'*.wmflabs.org',
	'ricordisamoa.tk'
);
  
  // From https://github.com/wikimedia/mediawiki-core/blob/d746d589cac7f4fc02107295aa47e2f4c6415cfa/includes/api/ApiMain.php
  /**
    * Attempt to match an Origin header against a set of rules and a set of exceptions
    * @param string $value Origin header
    * @param array $rules Set of wildcard rules
    * @param array $exceptions Set of wildcard rules
    * @return bool True if $value matches a rule in $rules and doesn't match
    * any rules in $exceptions, false otherwise
    */
function matchOrigin( $value ) {
	global $wgCrossSiteAJAXdomains;
	
	foreach ( $wgCrossSiteAJAXdomains as $rule ) {
		if ( preg_match( wildcardToRegex( $rule ), $value ) ) {
			return true;
		}
	}
	return false;
}

/**
* Helper function to convert wildcard string into a regex
* '*' => '.*?'
* '?' => '.'
*
* @param string $wildcard String with wildcards
* @return string Regular expression
*/
function wildcardToRegex( $wildcard ) {
	$wildcard = preg_quote( $wildcard, '/' );
	$wildcard = str_replace(
		array( '\*', '\?' ),
		array( '.*?', '.' ),
		$wildcard
	);

	return "/https?:\/\/$wildcard/";
}

/**
 * Convert an arbitrarily-long digit string from one numeric base
 * to another, optionally zero-padding to a minimum column width.
 *
 * Supports base 2 through 36; digit values 10-36 are represented
 * as lowercase letters a-z. Input is case-insensitive.
 *
 * @param string $input Input number
 * @param int $sourceBase Base of the input number
 * @param int $destBase Desired base of the output
 * @param int $pad Minimum number of digits in the output (pad with zeroes)
 * @param bool $lowercase Whether to output in lowercase or uppercase
 * @param string $engine Either "gmp", "bcmath", or "php"
 * @return string|bool The output number as a string, or false on error
 */
function wfBaseConvert( $input, $sourceBase, $destBase, $pad = 1, $lowercase = true, $engine = 'auto' ) {
	$input = (string)$input;
	if (
		$sourceBase < 2 ||
		$sourceBase > 36 ||
		$destBase < 2 ||
		$destBase > 36 ||
		$sourceBase != (int)$sourceBase ||
		$destBase != (int)$destBase ||
		$pad != (int)$pad ||
		!preg_match(
			"/^[" . substr( '0123456789abcdefghijklmnopqrstuvwxyz', 0, $sourceBase ) . "]+$/i",
			$input
		)
	) {
		return false;
	}

	static $baseChars = array(
		10 => 'a', 11 => 'b', 12 => 'c', 13 => 'd', 14 => 'e', 15 => 'f',
		16 => 'g', 17 => 'h', 18 => 'i', 19 => 'j', 20 => 'k', 21 => 'l',
		22 => 'm', 23 => 'n', 24 => 'o', 25 => 'p', 26 => 'q', 27 => 'r',
		28 => 's', 29 => 't', 30 => 'u', 31 => 'v', 32 => 'w', 33 => 'x',
		34 => 'y', 35 => 'z',

		'0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5,
		'6' => 6, '7' => 7, '8' => 8, '9' => 9, 'a' => 10, 'b' => 11,
		'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17,
		'i' => 18, 'j' => 19, 'k' => 20, 'l' => 21, 'm' => 22, 'n' => 23,
		'o' => 24, 'p' => 25, 'q' => 26, 'r' => 27, 's' => 28, 't' => 29,
		'u' => 30, 'v' => 31, 'w' => 32, 'x' => 33, 'y' => 34, 'z' => 35
	);

	if ( extension_loaded( 'gmp' ) && ( $engine == 'auto' || $engine == 'gmp' ) ) {
		$result = gmp_strval( gmp_init( $input, $sourceBase ), $destBase );
	} elseif ( extension_loaded( 'bcmath' ) && ( $engine == 'auto' || $engine == 'bcmath' ) ) {
		$decimal = '0';
		foreach ( str_split( strtolower( $input ) ) as $char ) {
			$decimal = bcmul( $decimal, $sourceBase );
			$decimal = bcadd( $decimal, $baseChars[$char] );
		}

		for ( $result = ''; bccomp( $decimal, 0 ); $decimal = bcdiv( $decimal, $destBase, 0 ) ) {
			$result .= $baseChars[bcmod( $decimal, $destBase )];
		}

		$result = strrev( $result );
	} else {
		$inDigits = array();
		foreach ( str_split( strtolower( $input ) ) as $char ) {
			$inDigits[] = $baseChars[$char];
		}

		// Iterate over the input, modulo-ing out an output digit
		// at a time until input is gone.
		$result = '';
		while ( $inDigits ) {
			$work = 0;
			$workDigits = array();

			// Long division...
			foreach ( $inDigits as $digit ) {
				$work *= $sourceBase;
				$work += $digit;

				if ( $workDigits || $work >= $destBase ) {
					$workDigits[] = (int)( $work / $destBase );
				}
				$work %= $destBase;
			}

			// All that division leaves us with a remainder,
			// which is conveniently our next output digit.
			$result .= $baseChars[$work];

			// And we continue!
			$inDigits = $workDigits;
		}

		$result = strrev( $result );
	}

	if ( !$lowercase ) {
		$result = strtoupper( $result );
	}

	return str_pad( $result, $pad, '0', STR_PAD_LEFT );
}
  
  
// --------------------------------------
// THE DATABASE
// --------------------------------------
/**
 * @author Magnus Manske
 *
 */
$use_db_cache = false ;
$common_db_cache = array() ;

function myurlencode ( $t ) {
	$t = str_replace ( " " , "_" , $t ) ;
	$t = urlencode ( $t ) ;
	return $t ;
}

function getDBpassword () {
	global $mysql_user , $mysql_password , $tool_user_name ;
	if ( isset ( $tool_user_name ) ) $user = $tool_user_name ;
	else $user = str_replace ( 'local-' , '' , get_current_user() ) ;
	$passwordfile = '/data/project/' . $user . '/replica.my.cnf' ;
	if ( $user == 'magnus' ) $passwordfile = '/home/' . $user . '/replica.my.cnf' ; // Command-line usage
	$t = file_get_contents ( $passwordfile ) ;
	$lines = explode ( "\n" , $t ) ;
	foreach ( $lines AS $l ) {
		$l = explode ( '=' , trim ( str_replace ( "'" , '' , $l  ) ) , 2 ) ;
		if ( $l[0] == 'user' ) $mysql_user = $l[1] ;
		if ( $l[0] == 'password' ) $mysql_password = $l[1] ;
	}
}

function getDBname ( $language , $project ) {
	$ret = $language ;
	if ( $language == 'commons' ) $ret = 'commonswiki_p' ;
	else if ( $language == 'wikidata' || $project == 'wikidata' ) $ret = 'wikidatawiki_p' ;
	else if ( $project == 'wikipedia' ) $ret .= 'wiki_p' ;
	else if ( $project == 'wikisource' ) $ret .= 'wikisource_p' ;
	else if ( $project == 'wiktionary' ) $ret .= 'wiktionary_p' ;
	else if ( $project == 'wikibooks' ) $ret .= 'wikibooks_p' ;
	else if ( $project == 'wikinews' ) $ret .= 'wikinews_p' ;
	else if ( $project == 'wikiversity' ) $ret .= 'wikiversity_p' ;
	else if ( $project == 'wikivoyage' ) $ret .= 'wikivoyage_p' ;
	else if ( $project == 'wikiquote' ) $ret .= 'wikiquote_p' ;
	else die ( "Cannot construct database name for $language.$project - aborting." ) ;
	return $ret ;
}

function openToolDB ( $dbname = '' , $server = '' ) {
	global $o , $mysql_user , $mysql_password;

	getDBpassword() ;
	if ( $dbname == '' ) $dbname = '_main' ;
	else $dbname = "__$dbname" ;

	$dbname = $mysql_user.$dbname; #"_main" ;

	if ( $server == '' ) $server = "tools-db" ;
	$db = new mysqli($server, $mysql_user, $mysql_password, $dbname);
	if($db->connect_errno > 0) {
		$o['msg'] = 'Unable to connect to database [' . $db->connect_error . ']';
		$o['status'] = 'ERROR' ;
		return false ;
	}
	return $db ;
}

function openDB ( $language , $project ) {
	global $mysql_user , $mysql_password , $o , $common_db_cache , $use_db_cache ;
	
	$db_key = "$language.$project" ;
	if ( isset ( $common_db_cache[$db_key] ) ) return $common_db_cache[$db_key] ;
	
	getDBpassword() ;
	$dbname = getDBname ( $language , $project ) ;

	$p = $project ;
	if ( $p == "wikipedia" ) $p = "wiki" ;
	
	$l = str_replace ( 'classic' , 'classical' , $language ) ;
	if ( $l == 'commons' ) $p = 'wiki' ;
	else if ( $l == 'wikidata' or $project == 'wikidata' ) $p = 'wiki' ;
	$server = "$l$p.labsdb" ;

	$db = new mysqli($server, $mysql_user, $mysql_password, $dbname);
	if($db->connect_errno > 0) {
		$o['msg'] = 'Unable to connect to database [' . $db->connect_error . ']';
		$o['status'] = 'ERROR' ;
		return false ;
	}
	if ( $use_db_cache ) $common_db_cache[$db_key] = $db ;
	return $db ;
}

function get_db_safe ( $s , $fixup = false ) {
	global $db ;
	if ( $fixup ) $s = str_replace ( ' ' , '_' , trim ( ucfirst ( $s ) ) ) ;
	return $db->real_escape_string ( str_replace ( ' ' , '_' , $s ) ) ;
}

function make_db_safe ( &$s , $fixup = false ) {
	$s = get_db_safe ( $s , $fixup ) ;
}
// --------------------------------------
// END: THE DATABASE
// --------------------------------------

?>