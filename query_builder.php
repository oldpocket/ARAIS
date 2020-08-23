<?php

// ORIGINAL https://phpzm.rocks/php-like-a-boss-2-crie-seu-querybuilder-af430b4125f9
// REFLECTION https://catchmetech.com/en/post/94/how-to-create-an-orm-framework-in-pure-php-orm-creation-tutorial
// DYNAMIC OBJ https://stackoverflow.com/questions/14395631/php-create-object-without-class

require 'query_db_abstraction.php';

/**
* Class QueryBuilder
* 
* Provides to the code a clean way to builds SQL querys.
* The code was based on:
* https://phpzm.rocks/php-like-a-boss-2-crie-seu-querybuilder-af430b4125f9
* 
* @author      Fabio Andreozzi Godoy <fabio.godoy@oldpocket.com>
* @copyright   2019-2020 Fabio Godoy
* @link        https://github.com/oldpocket/ARAIS
* @license     https://github.com/oldpocket/ARAIS/blob/master/LICENSE
* @version     0.5.0
* @package     ARAIS
* 
* @method QueryBuilder table (string $table)
* @method QueryBuilder join (string $join)
* @method QueryBuilder fields (array $fields)
* @method QueryBuilder where (array $where)
* @method QueryBuilder order (array $order)
* @method QueryBuilder group (array $group)
* @method QueryBuilder having (array $having)
* @method QueryBuilder limit (array $join)
*/
class QueryBuilder extends QueryDBAbstraction
{
    /** @var array The list of clausules to be used in the query */
    private $clausules = [];

    /**
    * @param $name
    * @param $arguments
    * @return $this
    */
    public function __call($name, $arguments)
    {
        $clausule = $arguments[0];
        if (count($arguments) > 1) {
            $clausule = $arguments;
        }
        $this->clausules[strtolower($name)] = $clausule;

        return $this;
    }

    /**
    * @param array $values
    * @return string
    */
    public function insert($values)
    {
        // recupera o nome da tabela
        // ou deixa uma marcação para mostrar que faltou informar esse campo
        $table = isset($this->clausules['table']) ? $this->clausules['table'] : '<table>';

        // recupera o array dos campos
        // ou deixa uma marcação para mostrar que faltou informar esse campo
        $_fields = isset($this->clausules['fields']) ? $this->clausules['fields'] : '<fields>';
        $fields = implode(', ', $_fields);

        // cria uma lista de rótulos para usar "prepared statement"
        $_placeholders = array_map(function($v) {
            return '?';
        }, $_fields);
        $placeholders = implode(', ', $_placeholders);

        $command = [];
        $command[] = 'INSERT INTO';
        $command[] = $table;
        $command[] = '(' . $fields . ')';
        $command[] = 'VALUES';
        $command[] = '(' . $placeholders . ')';

        // INSERT INTO {table} ({fields}) VALUES ({values});
        // junta o comando
        $sql = implode(' ', $command);

        return $this->executeInsert($sql, $values);
    }

    /**
    * @param $values
    * @return string
    */
    public function select($values = [])
    {
        // recupera o nome da tabela
        // ou deixa uma marcação para mostrar que faltou informar esse campo
        $table = isset($this->clausules['table']) ? $this->clausules['table'] : '<table>';

        // recupera o array dos campos
        // ou deixa uma marcação para mostrar que faltou informar esse campo
        $_fields = isset($this->clausules['fields']) ? $this->clausules['fields'] : '*';
        $fields = implode(', ', $_fields);

        $join = isset($this->clausules['join']) ? $this->clausules['join'] : '';

        $command = [];
        $command[] = 'SELECT';
        $command[] = $fields;
        $command[] = 'FROM';
        $command[] = $table;
        if ($join) {
            $command[] = $join;
        }

        $clausules = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' ',
            ],
            'group' => [
                'instruction' => 'GROUP BY',
                'separator' => ', ',
            ],
            'order' => [
                'instruction' => 'ORDER BY',
                'separator' => ', ',
            ],
            'having' => [
                'instruction' => 'HAVING',
                'separator' => ' AND ',
            ],
            'limit' => [
                'instruction' => 'LIMIT',
                'separator' => ',',
            ],
        ];
        foreach($clausules as $key => $clausule) {
            if (isset($this->clausules[$key])) {
                $value = $this->clausules[$key];
                if (is_array($value)) {
                    $value = implode($clausule['separator'], $this->clausules[$key]);
                }
                $command[] = $clausule['instruction'] . ' ' . $value;
            }
        }

        // SELECT {fields} FROM <JOIN> {table} <WHERE> <GROUP> <ORDER> <HAVING> <LIMIT>;
        // junta o comando
        $sql = implode(' ', $command);

        return $this->executeSelect($sql, $values);
    }

    /**
    * @param $values
    * @param $filters
    * @return int
    */
    public function update($values, $filters = [])
    {
        // recupera o nome da tabela
        // ou deixa uma marcação para mostrar que faltou informar esse campo
        $table = isset($this->clausules['table']) ? $this->clausules['table'] : '<table>';

        $join = isset($this->clausules['join']) ? $this->clausules['join'] : '';

        // recupera o array dos campos
        // ou deixa uma marcação para mostrar que faltou informar esse campo
        $_fields = isset($this->clausules['fields']) ? $this->clausules['fields'] : '<fields>';

        $sets = $_fields;
        if (is_array($_fields)) {
            $sets = implode(', ', array_map(function($value) {
                return $value . ' = ?';
            }, $_fields));
        }

        $command = [];
        $command[] = 'UPDATE';
        $command[] = $table;
        if ($join) {
            $command[] = $join;
        }
        $command[] = 'SET';
        $command[] = $sets;

        $clausules = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' ',
            ]
        ];
        foreach($clausules as $key => $clausule) {
            if (isset($this->clausules[$key])) {
                $value = $this->clausules[$key];
                if (is_array($value)) {
                    $value = implode($clausule['separator'], $this->clausules[$key]);
                }
                $command[] = $clausule['instruction'] . ' ' . $value;
            }
        }

        // UPDATE {table} SET {set} <WHERE>
        // junta o comando
        $sql = implode(' ', $command);

        return $this->executeUpdate($sql, array_merge($values, $filters));
    }

    /**
    * @param $filters
    * @return int
    */
    public function delete($filters)
    {
        // recupera o nome da tabela
        // ou deixa uma marcação para mostrar que faltou informar esse campo
        $table = isset($this->clausules['table']) ? $this->clausules['table'] : '<table>';

        $join = isset($this->clausules['join']) ? $this->clausules['join'] : '';

        $command = [];
        $command[] = 'DELETE FROM';
        $command[] = $table;
        if ($join) {
            $command[] = $join;
        }

        $clausules = [
            'where' => [
                'instruction' => 'WHERE',
                'separator' => ' ',
            ]
        ];
        foreach($clausules as $key => $clausule) {
            if (isset($this->clausules[$key])) {
                $value = $this->clausules[$key];
                if (is_array($value)) {
                    $value = implode($clausule['separator'], $this->clausules[$key]);
                }
                $command[] = $clausule['instruction'] . ' ' . $value;
            }
        }

        // DELETE FROM {table} <JOIN> <USING> <WHERE>
        // merge the parts of the command
        $sql = implode(' ', $command);

        return $this->executeDelete($sql, $filters);
    }
} 

