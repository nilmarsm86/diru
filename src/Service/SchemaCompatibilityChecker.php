<?php

namespace App\Service;

final class SchemaCompatibilityChecker
{
    /**
     * @throws DatabaseBackupException si los esquemas no coinciden
     */
    public function assertCompatible(string $currentDbPath, string $candidateDbPath): void
    {
        //        // pseudocódigo dentro de assertCompatible
        //        $currentVersion   = $this->maxMigrationVersion($currentPdo);
        //        $candidateVersion = $this->maxMigrationVersion($candidatePdo);
        //
        //        if ($currentVersion !== null && $currentVersion === $candidateVersion) {
        //            return; // mismas migraciones aplicadas → esquema idéntico
        //        }

        $current = $this->fingerprint($currentDbPath);
        $candidate = $this->fingerprint($candidateDbPath);

        $differences = $this->diff($current, $candidate);

        if ([] === $differences) {
            return;
        }

        //        throw new DatabaseBackupException("El esquema del archivo importado no coincide con el actual:\n • ".implode("\n • ", $differences));
        throw new DatabaseBackupException('La salva importada esta corrupta.');
    }

    /**
     * @return array<string, array<string, array{type: string, notnull: int, pk: int}>>
     *                                                                                  Estructura: [tabla => [columna => {type, notnull, pk}]]
     */
    private function fingerprint(string $dbPath): array
    {
        $pdo = new \PDO('sqlite:'.$dbPath, options: [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        $tables = $this->listUserTables($pdo);
        $schema = [];

        foreach ($tables as $table) {
            $schema[$table] = $this->describeColumns($pdo, $table);
        }

        ksort($schema);

        return $schema;
    }

    /** @return array<string> */
    private function listUserTables(\PDO $pdo): array
    {
        $sql = "
            SELECT name
              FROM sqlite_master
             WHERE type = 'table'
               AND name NOT LIKE 'sqlite_%'
               AND name NOT LIKE '%_seq'
          ORDER BY name
        ";

        $stmt = $pdo->query($sql);
        if (false === $stmt) {
            return [];
        }

        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        return array_map(function ($item) {
            /* @var string $item */
            return $item;
        }, $result);
    }

    /** @return array<string, array{type: string, notnull: int, pk: int}> */
    private function describeColumns(\PDO $pdo, string $table): array
    {
        $stmt = $pdo->prepare('SELECT name, type, "notnull", pk FROM pragma_table_info(?)');
        $stmt->execute([$table]);

        $columns = [];
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            /* @var array<string> $row */
            $columns[$row['name']] = [
                'type' => $this->toTypeAffinity($row['type']),
                'notnull' => (int) $row['notnull'],
                'pk' => (int) $row['pk'],
            ];
        }

        ksort($columns);

        return $columns;
    }

    /**
     * Reduce cualquier declaración de tipo SQLite a su affinity canónica.
     * Regla oficial: https://www.sqlite.org/datatype3.html#determination_of_column_affinity.
     */
    private function toTypeAffinity(string $declaredType): string
    {
        $type = strtoupper($declaredType);

        if ('' === $type) {
            return 'BLOB';
        }
        if (str_contains($type, 'INT')) {
            return 'INTEGER';
        }
        if (false !== preg_match('/CHAR|CLOB|TEXT/', $type)) {
            return 'TEXT';
        }
        if (str_contains($type, 'BLOB')) {
            return 'BLOB';
        }
        if (false !== preg_match('/REAL|FLOA|DOUB/', $type)) {
            return 'REAL';
        }

        return 'NUMERIC';
    }

    /**
     * @param array<string, array<string, array{type: string, notnull: int, pk: int}>> $current
     * @param array<string, array<string, array{type: string, notnull: int, pk: int}>> $candidate
     *
     * @return list<string>
     */
    private function diff(array $current, array $candidate): array
    {
        $diffs = [];

        $missingTables = array_diff(array_keys($current), array_keys($candidate));
        foreach ($missingTables as $table) {
            $diffs[] = sprintf('Falta la tabla "%s" en el archivo importado.', $table);
        }

        foreach ($current as $table => $expectedColumns) {
            if (!isset($candidate[$table])) {
                continue; // ya reportada arriba
            }

            $diffs = [...$diffs, ...$this->diffColumns($table, $expectedColumns, $candidate[$table])];
        }

        return $diffs;
    }

    /**
     * @param array<string, array{type: string, notnull: int, pk: int}> $expected
     * @param array<string, array{type: string, notnull: int, pk: int}> $actual
     *
     * @return list<string>
     */
    private function diffColumns(string $table, array $expected, array $actual): array
    {
        $diffs = [];

        foreach ($expected as $column => $expectedSpec) {
            if (!isset($actual[$column])) {
                $diffs[] = sprintf('Tabla "%s": falta la columna "%s".', $table, $column);
                continue;
            }

            $actualSpec = $actual[$column];

            if ($expectedSpec['type'] !== $actualSpec['type']) {
                $diffs[] = sprintf(
                    'Tabla "%s", columna "%s": tipo esperado %s, encontrado %s.',
                    $table, $column, $expectedSpec['type'], $actualSpec['type'],
                );
            }

            if ($expectedSpec['pk'] !== $actualSpec['pk']) {
                $diffs[] = sprintf(
                    'Tabla "%s", columna "%s": diferencia en clave primaria.',
                    $table, $column,
                );
            }
        }

        return $diffs;
    }
}
