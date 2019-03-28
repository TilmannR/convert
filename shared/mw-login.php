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


// --------------------------------------
// THE DATABASE
// --------------------------------------
/**
 * @author Magnus Manske
 * @author Rillke
 *
 */
function mwDBLogInAndSecretKey () {
	global $wgDBuser , $wgDBpassword , $tool_user_name,  $wgSecretKey, $wgUpgradeKey;
	if ( isset ( $tool_user_name ) ) $user = $tool_user_name ;
	else $user = str_replace ( 'local-' , '' , get_current_user() ) ;
	$passwordfile = '/data/project/' . $user . '/replica.my.cnf' ;
	if ( $user == 'magnus' ) $passwordfile = '/home/' . $user . '/replica.my.cnf' ; // Command-line usage
	$t = file_get_contents ( $passwordfile ) ;
	$lines = explode ( "\n" , $t ) ;
	foreach ( $lines AS $l ) {
		$l = explode ( '=' , trim ( str_replace ( "'" , '' , $l  ) ) , 2 ) ;
		if ( $l[0] == 'user' ) $wgDBuser = $l[1] ;
		if ( $l[0] == 'password' ) $wgDBpassword = $l[1] ;
	}
	
	$keyfile = '/data/project/' . $user . '/mw-secretkey' ;
	$wgSecretKeys = file_get_contents ( $keyfile ) ;
	$lines = explode ( "\n" , $wgSecretKeys ) ;
	$wgSecretKey =  trim( $lines[0] );
	$wgUpgradeKey = trim( $lines[1] );
}