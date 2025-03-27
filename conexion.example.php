<?php
class BD
{
    private static $instancia = NULL;

    public static function crearInstancia()
    {
        if (!isset(self::$instancia)) {
            // Establecer zona horaria para PHP
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            
            $opcionesPDO[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

            // Replace with your database credentials
            self::$instancia = new PDO('mysql:host=your_host; dbname=your_database', 'your_username', 'your_password', $opcionesPDO);
            self::$instancia->exec("SET CHARACTER SET utf8");
            
            // Establecer zona horaria para MySQL
            self::$instancia->exec("SET time_zone = '-03:00'");
        }
        return self::$instancia;
    }
}
