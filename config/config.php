<?php

/**
 *  Archivo de configuracion de sistema
 */
interface Config {
    /**
     * Configuracion de la base de datos
     */
    const dbmg = 'mysql';//postgres - oracle - sqlserver
    const dbnm = '70454_qstad';
    const host = 'mysql2.alwaysdata.com';
    const port = '3306';
    const user = 'q-stad';
    const pass = 'seguro#12';
    
    /**
     *  Configuracion de idioma
     */
    const lang = 'es';
    
    /**
     *  Configuracion del proyecto
     */
    const template = '/template';
    const debug = true;
    
}

?>
