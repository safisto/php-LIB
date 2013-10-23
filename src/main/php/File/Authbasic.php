<?

include_once( 'PEAR.php' );
include_once( 'File/Passwd.php' );

if( !defined( 'AUTHBASIC_CONSTANTS' ) )
{
	define( 'AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLetters', 1 );
	define( 'AUTHBASIC_CHAR_RULE_LettersAndNumbers', 2 );
	define( 'AUTHBASIC_CHAR_RULE_ContainsNonAlphanumericCharacters', 3 );
	define( 'AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLettersOrLettersAndNumbers', 4 );
	define( 'AUTHBASIC_CHAR_RULE_MixedUpperAndLowerCaseLettersAndNumbers', 5 );
	define( 'AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLettersOrLettersAndNumbersAndContainsNonAlphanumericCharacters', 6 );
	define( 'AUTHBASIC_CHAR_RULE_MixedUpperAndLowerCaseLettersAndNumbersAndMixedUpperAndLowerCaseLettersAndNumbers', 7 );

	define( 'AUTHBASIC_CONSTANTS', true );
}

class Authbasic
{
	private $passfile;
	private $groupfile;

	private $passwd;
	private $groups;

	private $usernameRule = AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLetters;
	private $passwordRule = AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLetters;

	private $usernameMinLen = 5;
	private $passwordMinLen = 5;

	function Authbasic( $passfile = '.htpasswd', $groupfile = NULL )
	{
		Authbasic::__construct( $passfile, $groupfile );
	}

	function __construct( $passfile = '.htpasswd', $groupfile = NULL )
	{
		$this->passfile = $passfile;
		$this->groupfile = $groupfile;

		$this->passwd = &File_Passwd::factory( 'Authbasic' );
		$this->passwd->setFile( $passfile );
		$this->passwd->load();

		if( !is_null( $groupfile ) )
		{
			$this->loadGroups();
		}
	}

	public function setUsernameRule( $rule )
	{
		$this->usernameRule = $rule;
	}

	public function setPasswordRule( $rule )
	{
		$this->passwordRule = $rule;
	}

	public function setUsernameMinLen( $len )
	{
		$this->usernameMinLen = $len;
	}

	public function setPasswordMinLen( $len )
	{
		$this->passwordMinLen = $len;
	}

	private function loadGroups()
	{
		if ( !$fp = @fopen( $this->groupfile, 'r' ) )
		{
			throw new Exception( 'Couldn\'t open \'' . $groupfile . '!' );
		}
		$content = '';
		while( !feof( $fp ) )
		{
			$content .= fgets( $fp, 1024 );
		}
		fclose( $fp );

		$this->groups = array();
		foreach( explode( "\n", $content ) as $line )
		{
			$data = explode( ':', $line );
			if( count( $data ) < 2 )
				continue;
			list( $group, $users ) = $data;
			if( strlen ($group) )
			{
				$this->groups[$group] = explode( ' ', $users );
			}
		}
	}

	private function saveGroups()
	{
		$content = '';
		while( list( $user, $group ) = each( $this->groups ) )
		{
			foreach( $this->groups as $group => $users )
			{
				$content .= $group . ':' . implode( ' ', $users ) . "\n";
			}
		}

		if ( !$fp = @fopen( $this->groupfile, 'w' ) )
		{
			throw new Exception( 'Couldn\'t open \'' . $groupfile . '!' );
		}
		$err = NULL;
		if ( flock( $fp, LOCK_EX ) )
		{
			fputs( $fp, $content );
			flock( $fp, LOCK_UN );
		}
		else
		{
			$err = 'Couldn\'t lock \'' . $groupfile . '!';
		}
		fclose($fp);

		if( !is_null( $err ) )
		{
			throw new Exception( $err );
		}
	}

	private function verifyCharRule( $value, $rule, $len )
	{
		if ($len < 1 || strlen ($value) < $len) return false;
		if ( !$rule ) return true;

		switch( $rule )
		{
			case AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLetters :
				if (!preg_match ("#^[A-Za-z]+$#", $value) || !preg_match ("#[A-Z]+#", $value) || !preg_match ("#[a-z]+#", $value)) return false;
				break;

			case AUTHBASIC_CHAR_RULE_LettersAndNumbers :
				if (!preg_match ("#^[A-Za-z0-9]+$#", $value) || !preg_match ("#[A-Za-z]+#", $value) || !preg_match ("#[0-9]+#", $value)) return false;
				break;

			case AUTHBASIC_CHAR_RULE_ContainsNonAlphanumericCharacters :
				if (!preg_match ("#[<>\.,;_+*\#@|!\"'�$%&/=?]#", $value)) return false;
				break;

			case AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLettersOrLettersAndNumbers :
				if (!$this->verifyCharRule ($value, AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLetters, $len) && !$this->verifyCharRule ($value, AUTHBASIC_CHAR_RULE_LettersAndNumbers, $len)) return false;
				break;

			case AUTHBASIC_CHAR_RULE_MixedUpperAndLowerCaseLettersAndNumbers :
				if (!preg_match ("#^[A-Za-z0-9]+$#", $value) || !preg_match ("#[A-Z]+#", $value) || !preg_match ("#[a-z]+#", $value) || !preg_match ("#[0-9]+#", $value)) return false;
				break;

			case AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLettersOrLettersAndNumbersAndContainsNonAlphanumericCharacters :
				if (!$this->verifyCharRule ($value, AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLetters, $len) || (!$this->verifyCharRule ($value, AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLetters, $len) && !$this->verifyCharRule ($value, AUTHBASIC_CHAR_RULE_LettersAndNumbers, $len))) return false;
				break;

			case AUTHBASIC_CHAR_RULE_MixedUpperAndLowerCaseLettersAndNumbersAndMixedUpperAndLowerCaseLettersAndNumbers :
				if (!$this->verifyCharRule ($value, AUTHBASIC_CHAR_RULE_MixedUpperAndLowerCaseLettersAndNumbers, $len) || !$this->verifyCharRule ($value, AUTHBASIC_CHAR_RULE_UpperAndLowerCaseLetters, $len)) return false;
				break;

			default :
				return false;
				break;
		}
		return true;
	}

	public function addUserToGroups( $user, $groups )
	{
		if( !is_array( $groups ) )
		{
			throw new Exception( 'Groups is not an array!' );
		}
		foreach( $groups as $group )
		{
			$this->addUserToGroup( $user, $group );
		}
	}

	public function addUserToGroup( $user, $group )
	{
		if( !$this->passwd->userExists( $user ) )
		{
			throw new Exception( 'User \'' . $user . '\' does not exist!' );
		}
		elseif ( is_null( $this->groupfile ) )
		{
			throw new Exception( 'No groupfile given!' );
		}
		if( is_null( $this->groups ) )
		{
			$this->groups = array();
		}
		if( !array_key_exists( $group, $this->groups ) )
		{
			$this->groups[$group] = array();
		}
		if( !in_array( $user, $this->groups[$group] ) )
		{
			array_push( $this->groups[$group], $user );
		}
		return true;
	}

	public function delUserFromGroups( $user, $groups )
	{
		if( !is_array( $groups ) )
		{
			throw new Exception( 'Groups is not an array!' );
		}
		foreach( $groups as $group )
		{
			$this->delUserFromGroup( $user, $group );
		}
	}

	public function delUserFromAllGroups( $user )
	{
		foreach( array_keys( $this->groups ) as $group )
		{
			$this->delUserFromGroup( $user, $group );
		}
	}

	public function delUserFromGroup( $user, $group )
	{
		if ( is_null( $this->groupfile ) )
		{
			throw new Exception( 'No groupfile given!' );
		}
		if( !is_null( $this->groups ) && array_key_exists( $group, $this->groups ) )
		{
			$index = array_search( $user, $this->groups[$group] );
			if( $index !== FALSE )
			{
				array_splice( $this->groups[$group], $index, 1 );
			}
		}
		return true;
	}

	public function addUser( $user, $password, $groups = NULL )
	{
		if( !$this->verifyUsername( $user ) )
		{
			return false;
		}
		elseif ( !$this->verifyPassword( $password ) )
		{
			return false;
		}
		elseif( !is_null( $groups ) && is_null( $this->groupfile ) )
		{
			throw new Exception( 'No groupfile given!' );
		}

		$this->passwd->addUser( $user, $password );

		if( !is_null( $groups ) )
		{
			foreach( $groups as $group )
			{
				$this->addUserToGroup( $user, $group );
			}
		}
		return true;
	}

	public function verifyUsername( $user )
	{
		return $this->verifyCharRule( $user, $this->usernameRule, $this->usernameMinLen );
	}

	public function verifyPassword( $password )
	{
		return $this->verifyCharRule( $password, $this->passwordRule, $this->passwordMinLen );
	}

	public function userExists( $user )
	{
		return $this->passwd->userExists( $user );
	}

	public function setEncryptedPassword( $user, $password )
	{
		if( !$this->passwd->userExists( $user ) )
		{
			throw new Exception( 'User \'' . $user . '\' does not exist!' );
		}
		$this->passwd->_users[$user] = $password;
	}

	public function getEncryptedPassword( $password )
	{
		return $this->passwd->_genPass( $password );
	}

	public function delUser( $user )
	{
		if( !is_null( $this->groupfile ) )
		{
			$this->delUserFromAllGroups( $user );
		}
		$this->passwd->delUser( $user );
		return true;
	}

	public function changePasswd( $user, $password )
	{
		if ( !$this->verifyPassword( $password ) )
		{
			return false;
		}
		$this->passwd->changePasswd( $user, $password );
		return true;
	}

	public function save()
	{
		if( !is_null( $this->groupfile ) )
		{
			$this->saveGroups();
		}
		$this->passwd->save();
		return true;
	}

}

?>