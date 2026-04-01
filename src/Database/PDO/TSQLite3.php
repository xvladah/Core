<?php

/**
 * @version 2.0
 * @name TSQLite3
 * @author Vladimir Horky
 * @copyright TEDOM, Vladimir Horky
 *
 * changes
 * -- added function getVersion()
 */

namespace Core\Database\PDO;

use SQLite3;

class TSQLite3 extends SQLite3
{
    public function beginTransaction(): bool
    {
        return $this->exec('BEGIN TRANSACTION;');
    }

    public function commit(): bool
    {
        return $this->exec('COMMIT TRANSACTION;');
    }

    public function rollBack(): false|SQLite3Result
    {
        return $this->query('ROLLBACK TRANSACTION;');
    }

    public function lastInsertId(): int
    {
        return $this->lastInsertRowID();
    }

    public function getVersion(): ?int
    {
        $result = null;

        $query = $this->query('SELECT sys_hodnota FROM sys WHERE sys_pk_id=\'verze\'');
        while($zaznam = $query->fetchArray(SQLITE3_ASSOC))
        {
            $pos = strpos($zaznam['sys_hodnota'], '.');
            if($pos !== false)
                $result = intval(substr($zaznam['sys_hodnota'], 0, $pos-1));
            else
                $result = intval($zaznam['sys_hodnota']);
        }

        return $result;
    }
}
