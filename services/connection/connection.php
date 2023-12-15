<?php
    require 'config.php';
    
    class Connection extends PDO {
        public function __construct() {
            try {			
                $host		= HOST;
                $database	= DATABASE;
                $user		= USER;
                $password	= PASSWORD;
                $port       = PORT;
                
                parent::__construct('pgsql:host='.$host.';port='.$port.';dbname='.$database, $user, $password);
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (PDOException $e) {
                error_log("Error al conectarse a la base de datos: ".$e->getMessage());
                exit;
            }
        }
    }
?>