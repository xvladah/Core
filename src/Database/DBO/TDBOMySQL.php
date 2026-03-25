<?php

class TDBOMySQL extends TSQLBase
{
    const string TABLE_NAME = '';
    const array TABLE_FUNCTIONS  = [];
    const array TABLE_CONDITIONS = [];
    const array TABLE_KEYS       = [];

    protected MySQLi $mysql;

    final public static function getInstance(MySQLi $mysql)
    {
        static $instances = [];

        $calledClass = get_called_class();

        if (!isset($instances[$calledClass]))
            $instances[$calledClass] = new $calledClass($mysql);
        else
            $instances[$calledClass]->mysql = $mysql;

        return $instances[$calledClass];
    }

    public function __construct(MySQLi $mysql)
    {
        $this->mysql = $mysql;

        return $this;
    }

    /**
     * @throws ESQLBase
     */
    public function count(array $where_params = [], $column = null, ?string $from = null): mixed
    {
        $params = [];
        $turn = 1;
        $where = parent::parse_where($where_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, static::TABLE_CONDITIONS, $this->LOGIC, $turn);

        if($column === null)
            $column = static::TABLE_KEYS[0];

        if($from === null)
            $from = static::TABLE_NAME;

        $sql = parent::_count($column, $where, $from, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        foreach($params as $sql_param => $value)
            $stmt->bindValue($sql_param, $value);

        $query 	= $stmt->execute();
        $row 	= $query->fetch();

        return $row[0];
    }

    public function delete(array $where_params, ?string $from = null): bool
    {
        $params = [];
        $turn = 1;
        $where = parent::parse_where($where_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, static::TABLE_CONDITIONS, $this->LOGIC, $turn);

        if($from === null)
            $from = static::TABLE_NAME;

        $sql = parent::_delete($where, $from);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        foreach($params as $sql_param => $value)
            $stmt->bindValue($sql_param, $value);

        return $stmt->execute();
    }

    /**
     * @throws ESQLBase
     * @throws EDBOMySQL
     */
    public function select(mixed $columns, array $where_params = [], array $order = [], int $offset = 0, int $count = 0, ?string $from = null): bool
    {
        $params = [];
        $turn = 1;
        $where  = parent::parse_where($where_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, static::TABLE_CONDITIONS, $this->LOGIC, $turn);

        if($from === null)
            $from = static::TABLE_NAME;

        $sql = parent::_select($columns, $where, $order, $offset, $count, $from, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        if($stmt)
        {
            foreach($params as $sql_param => $value)
                $stmt->bindValue($sql_param, $value);

            return $stmt->execute();
        } else
            throw new EDBOMySQL('Exception in SQL query', -230);
    }

    public function group($columns, $group, array $where_params = [], array $order = [], int $offset = 0, int $count = 0, ?string $from = null): bool
    {
        $params = [];
        $turn = 1;
        $where  = parent::parse_where($where_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, static::TABLE_CONDITIONS, $this->LOGIC, $turn);

        if($from === null)
            $from = static::TABLE_NAME;

        $sql = parent::_group($columns, $group, $where, $order, $offset, $count, $from, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        if($stmt)
        {
            foreach($params as $sql_param => $value)
                $stmt->bindValue($sql_param, $value);

            return $stmt->execute();
        } else
            throw new EDBOMySQL('SQL SELECT GROUP BY: Bad SQL query!');
    }

    /**
     * @throws ESQLBase
     */
    public function column(string $column, array $where_params = [], array $order = [], ?string $from = null): mixed
    {
        $params = [];
        $turn = 1;
        $where  = parent::parse_where($where_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, static::TABLE_CONDITIONS, $this->LOGIC, $turn);

        if($from === null)
            $from = static::TABLE_NAME;

        $sql = parent::_column($column, $where, $order, $from, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        foreach($params as $sql_param => $value)
            $stmt->bindValue($sql_param, $value);

        $query 	= $stmt->execute();
        $row 	= $query->fetch_assoc();

        return $row[0];
    }

    /**
     * @throws ESQLBase
     */
    public function record($columns, array $where_params, ?string $from = null)
    {
        $params = [];
        $turn = 1;
        $where  = parent::parse_where($where_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, static::TABLE_CONDITIONS, $this->LOGIC, $turn);

        if($from === null)
            $from = static::TABLE_NAME;

        $sql = parent::_select($columns, $where, [], 0, 1, $from, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        foreach($params as $sql_param => $value)
            $stmt->bindValue($sql_param, $value);

        $query = $stmt->execute();

        return $query->fetch_assoc();
    }

    /**
     * @throws ESQLBase
     */
    public function insert(array $insert_params): bool
    {
        $columns = '';
        $params = [];
        $values = parent::parse_insert($insert_params, $columns, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS);

        $sql = parent::_insert($columns, $values, static::TABLE_NAME);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        foreach($params as $sql_param => $value)
            $stmt->bindValue($sql_param, $value);

        return $stmt->execute();
    }

    /**
     * @throws ESQLBase
     */
    public function insertMulti(array $multi_insert_params): bool
    {
        $columns 	= '';
        $params 	= [];
        $sql 		= '';

        foreach($multi_insert_params as $m => $insert_params)
        {
            $values = parent::parse_insert($insert_params, $columns, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, $m);
            if($sql == '')
                $sql = 'INSERT INTO '.static::TABLE_NAME.'('.$columns.')VALUES';
            else
                $sql .= ',';

            $sql .= '('.$values.')';
        }
        $sql .= ';';

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        if($stmt !== false)
        {
            foreach($params as $sql_param => $value)
                $stmt->bindValue($sql_param, $value);
        } else
            throw new EDBOSQLiteException('Exception during insert values!'.$sql.print_r($params, true).$this->sqlite->lastErrorMsg());

        return $stmt->execute();
    }

    /**
     * @throws ESQLBase
     */
    public function update(array $update_params, array $where_params): bool
    {
        $params  = [];
        $turn = 1;
        $where	 = parent::parse_where($where_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, static::TABLE_CONDITIONS, $this->LOGIC, $turn);
        $set 	 = parent::parse_update($update_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS);

        $sql = parent::_update($set, $where, static::TABLE_NAME);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        foreach($params as $sql_param => $value)
            $stmt->bindValue($sql_param, $value);

        return $stmt->execute();
    }

    /**
     * @throws ESQLBase
     */
    public function options(array $columns, array $options = [], array $where_params = [], array $order = [], int $offset = 0, int $count = 0, ?string $from = null): array
    {
        if(count($options) > 0)
        {
            $where_zal = [];
            foreach($options as $column => $values)
            {
                if((string)$column != '')
                {
                    if((string)$values != '')
                    {
                        if(is_array($values) || is_numeric($values))
                        {
                            unset($options[$column]);
                            $where_zal[$column] = $values;
                        }
                    } else
                        unset($options[$column]);
                }
            }

            if(count($where_zal) > 0)
                if(count($where_params) > 0)
                    $where_params = ['ORx'=>['ANDx'=>$where_params, 'ORx'=>$where_zal]];
        }

        $params  = [];
        $turn = 1;
        $where	 = parent::parse_where($where_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, static::TABLE_CONDITIONS, $this->LOGIC, $turn);

        $column_id = key($columns);
        $column_name = current($columns);

        if($from === null)
            $from = static::TABLE_NAME;

        $sql = parent::_options($column_id, $column_name, $where, $order, $offset, $count, $from, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        foreach($params as $sql_param => $value)
            $stmt->bindValue($sql_param, $value);

        $query = $stmt->execute();

        if(is_array($column_name))
        {
            while($zaznam = $query->fetch_assoc())
            {
                $name = '';
                foreach($column_name as $col)
                {
                    if($name != '')
                        $name .= ' ';

                    $name .= $zaznam[$col];
                }

                $options[$zaznam[$column_id]] = $name;
            }
        } else
            while($zaznam = $query->fetch_assoc())
                $options[$zaznam[$column_id]] = $zaznam[$column_name];

        return $options;
    }

    /**
     * @throws ESQLBase
     */
    public function hash(array $columns, array $where_params = [], array $order = [], int $offset = 0, int $count = 0, ?string $from = null): array
    {
        $params  = [];
        $turn = 1;
        $where	 = parent::parse_where($where_params, $params, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS, static::TABLE_CONDITIONS, $this->LOGIC, $turn);

        $column_id = key($columns);
        $column_values = current($columns);

        if($from === null)
            $from = static::TABLE_NAME;

        $sql = parent::_hash($column_id, $column_values, $where, $order, $offset, $count, $from, static::TABLE_COLUMNS, static::TABLE_FUNCTIONS);

        parent::logger($sql, $params);

        $stmt = $this->mysql->prepare($sql);
        foreach($params as $sql_param => $value)
            $stmt->bindValue($sql_param, $value);

        $query = $stmt->execute();

        $result = [];
        while($zaznam = $query->fetch_assoc())
            $result[$zaznam[$column_id]] = $zaznam;

        return $result;
    }
}
