<?php

declare(strict_types=1);

namespace AdvancedDatabaseReplacer\Replacer\Builder;

use AdvancedDatabaseReplacer\Replacer\Logger\Logger;

class QueryProcessor
{
    public $lastError = '';
    private $isDry;

    private $whereRelation;

    private $mustWhere = [];

    private $where = [];

    private $set = '';

    private $tables = [];

    public function __construct(bool $isDry, string $whereRelation = 'AND')
    {
        $this->isDry = $isDry;
        $this->whereRelation = \strtoupper($whereRelation);
    }

    public function execute(): array
    {
        global $wpdb;
        $wpdb->show_errors = false;

        $queryDry = $wpdb->prepare($this->prepareDryQuery());

        if ($this->lastError) {
            $errorMessage = \sprintf(
                \__('Dry run executed with errors. Processor query errors: %s', 'adr'),
                $this->lastError
            );

            Logger::Instance()->error(
                $errorMessage,
                ['user' => \get_current_user_id(), 'isDry' => (int) $this->isDry, 'query' => $queryDry]
            );

            return [
                'status'  => 'error',
                'isDry'   => (int) $this->isDry,
                'query'   => $queryDry,
                'message' => $errorMessage,
            ];
        }

        $resultDry = $wpdb->get_var($queryDry);

        if ($wpdb->last_error) {
            $errorMessage = \sprintf(
                \__('Dry run executed with errors. Database query errors: %s', 'adr'),
                $wpdb->last_error
            );

            Logger::Instance()->error(
                $errorMessage,
                ['user' => \get_current_user_id(), 'isDry' => (int) $this->isDry, 'query' => $queryDry]
            );

            return [
                'status'  => 'error',
                'isDry'   => (int) $this->isDry,
                'query'   => $queryDry,
                'message' => $wpdb->last_error,
            ];
        }

        if ($this->isDry) {
            $message = \sprintf(
                \__('Dry run executed successfully. The real execution will replace %d rows in the database.', 'adr'),
                $resultDry
            );

            Logger::Instance()->info(
                $message,
                ['user' => \get_current_user_id(), 'isDry' => (int) $this->isDry, 'query' => $queryDry]
            );

            return [
                'status'  => 'success',
                'isDry'   => (int) $this->isDry,
                'query'   => $queryDry,
                'message' => $message,
            ];
        }

        $query = $wpdb->prepare($this->prepareQuery());

        if ($this->lastError) {
            $errorMessage = \sprintf(
                \__('Dry run executed with errors. Processor query errors: %s', 'adr'),
                $this->lastError
            );

            Logger::Instance()->error(
                $errorMessage,
                ['user' => \get_current_user_id(), 'isDry' => (int) $this->isDry, 'query' => $query]
            );

            return [
                'status'  => 'error',
                'isDry'   => (int) $this->isDry,
                'query'   => $query,
                'message' => $errorMessage,
            ];
        }

        $wpdb->get_var($query);

        if ($wpdb->last_error) {
            $errorMessage = \sprintf(
                \__('Dry run executed with errors. Database query errors: %s', 'adr'),
                $wpdb->last_error
            );

            Logger::Instance()->error(
                $errorMessage,
                ['user' => \get_current_user_id(), 'isDry' => (int) $this->isDry, 'query' => $query]
            );

            return [
                'status'  => 'error',
                'isDry'   => (int) $this->isDry,
                'query'   => $query,
                'message' => $wpdb->last_error,
            ];
        }

        $message = \sprintf(
            \__('Run executed successfully. The execution replaced %d rows in the database.', 'adr'),
            $resultDry
        );

        Logger::Instance()->info(
            $message,
            ['user' => \get_current_user_id(), 'isDry' => (int) $this->isDry, 'query' => $queryDry]
        );

        return [
            'status'  => 'success',
            'isDry'   => (int) $this->isDry,
            'query'   => $query,
            'message' => $message,
        ];
    }

    public function setMustWhere(string $field, string $condition, string $value): void
    {
        $where = "({$field} {$condition} {$value})";
        $this->mustWhere[\md5(\serialize($where))] = $where;
    }

    public function setWhere(string $field, string $condition, $value): void
    {
        $where = "({$field} {$condition} {$value})";
        $this->where[\md5(\serialize($where))] = $where;
    }

    public function setRawWhere(string $where): void
    {
        $this->where[\md5(\serialize($where))] = $where;
    }

    public function setSet(string $field, string $value): void
    {
        $this->set = "{$field} = {$value}";
    }

    public function setTables(string $table, string $leftJoin = null, string $rightJoin = null): void
    {
        if (null !== $leftJoin && null !== $rightJoin) {
            $join = " \n\t\tON {$leftJoin} = {$rightJoin}";
        }

        $this->tables[\md5(\serialize($table))] = $table . ($join ?? '');
    }

    private function prepareQuery(): string
    {
        $tables = \implode(" \n\tJOIN ", $this->tables);
        $mustWhere = \implode(" \n\tAND ", $this->mustWhere) ?: 'true';
        $where = \implode(" \n\t\t{$this->whereRelation} ", $this->where) ?: 'true';

        return "UPDATE \n\t{$tables} \nSET \n\t{$this->set} \nWHERE \n\t{$mustWhere} \n\tAND (\n\t\t{$where}\n\t)";
    }

    private function prepareDryQuery(): string
    {
        $tables = \implode(" \n\tJOIN ", $this->tables);
        $mustWhere = \implode(" \n\tAND ", $this->mustWhere) ?: 'true';
        $where = \implode(" \n\t\t{$this->whereRelation} ", $this->where) ?: 'true';

        return "SELECT \n\tcount(*) \nFROM \n\t{$tables} \nWHERE \n\t{$mustWhere} \n\tAND (\n\t\t{$where}\n\t)";
    }
}
