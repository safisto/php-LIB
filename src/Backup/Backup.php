<?php

class Backup
{
    public $DIRECTORY_ROOT;
    public $BACKUP_DIR;
    public $MYSQL_USERNAME;
    public $MYSQL_PASSWORD;
    public $MYSQL_DATABASES;
    public $FILES;

    private $now;
    private $domain;
    private $logFile;

    function Backup()
    {
	$this->now = date('Y_m_d_H_i_s');
	$this->domain = $this->resolveDomain();
	$this->logFile = $this->now . '_backup-' . $this->domain . '.log';
    }

    private function resolveDomain()
    {
	ereg( '\.([^\.]*)\.[^\.]*$', getenv( 'HTTP_HOST' ), $args );
	return $args[1];
    }

    private function println( $msg )
    {
	echo $msg . "\n";
	ob_flush();
	flush();
	system( "echo '$msg' >> " . $this->BACKUP_DIR . $this->logFile );
    }

    private function backupMySQL()
    {
	foreach( split( ' ', $this->MYSQL_DATABASES ) as $database )
	{
	    $this->println( 'MySQL backup: ' . $database );

	    $sqlfile = $this->BACKUP_DIR . $this->now . '_mysql_' . $database . '-' . $this->domain . '.sql';

	    system( '/usr/bin/mysqldump -u' . $this->MYSQL_USERNAME . ' -p' . $this->MYSQL_PASSWORD . ' -h localhost ' . $database . ' > ' . $sqlfile , $retval );

	    if( $retval == 0 )
	    {
		$this->println( 'File: ' . $sqlfile );
		$this->println( 'Size: ' . filesize( $sqlfile ) . ' bytes'  );
		$this->println( 'Success.' );
	    }
	    else
	    {
		$this->println( 'Error!' );
	    }
	}
    }

    private function backupFiles()
    {
	$this->println( 'File backup: ' . $this->FILES );

	$archivefile = $this->BACKUP_DIR . $this->now . '_files-' . $this->domain . '.tgz';

	chdir( $this->DIRECTORY_ROOT );

	system( 'tar cvfz ' . $archivefile . ' ' . $this->FILES . ' >> ' . $this->BACKUP_DIR . $this->logFile , $retval );

	if( $retval == 0 )
	{
	    $this->println( 'File: ' . $archivefile );
	    $this->println( 'Size: ' . filesize( $archivefile ) . ' bytes'  );
	    $this->println( 'Success.' );
	}
	else
	{
	    $this->println( 'Error!' );
	}
    }

    public function execute( $action = 'ALL' )
    {
	$action = strtoupper( $action );

	ob_start();
	echo '<pre>';

	$this->println( 'Starting backup of ' . $this->domain . ' at ' . date( 'd.m.Y H:i:s' ) . ' ...' );
	$this->println( 'Action: ' . $action );

	if( $action == 'ALL' || $action == 'MYSQL' )
	{
	    $this->backupMySQL();
	}
	if( $action == 'ALL' || $action == 'FILES' )
	{
	    $this->backupFiles();
	}

	$this->println( 'Backup of ' . $this->domain . ' complete.' );
	
	echo '</pre>';
	ob_end_flush();
    }

}

?>