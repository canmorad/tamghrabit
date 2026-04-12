<?php
namespace App\Traits;

trait DatabaseTransaction
{
    public function beginTransaction()
    {
        if (!$this->conn->inTransaction()) {
            $this->conn->beginTransaction();
        }
    }

    public function commit()
    {
        if ($this->conn->inTransaction()) {
            $this->conn->commit();
        }
    }

    public function rollBack()
    {
        if ($this->conn->inTransaction()) {
            $this->conn->rollBack();
        }
    }
}