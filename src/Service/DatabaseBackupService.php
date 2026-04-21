<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class DatabaseBackupService
{
    private const SQLITE_MAGIC_HEADER = "SQLite format 3\x00";
    private const SQLITE_AUX_SUFFIXES = ['-wal', '-shm', '-journal'];
    private const BACKUP_SUFFIX = '.backup';

    public function __construct(
        private readonly Connection $connection,
        private readonly Filesystem $filesystem,
        private readonly SchemaCompatibilityChecker $schemaChecker,
    ) {
    }

    /**
     * Crea un snapshot consistente en un archivo temporal y devuelve su ruta.
     * El llamador es responsable de enviarlo al cliente y borrarlo.
     *
     * @throws DatabaseBackupException|Exception
     */
    public function exportToTempFile(): string
    {
        $snapshotPath = $this->buildSnapshotPath();
        /** @var string $snapshotPathQuote */
        $snapshotPathQuote = $this->connection->quote($snapshotPath);

        // VACUUM INTO produce una copia consistente sin bloquear lectores.
        $this->connection->executeStatement(
            sprintf('VACUUM INTO %s', $snapshotPathQuote)
        );

        if (!$this->filesystem->exists($snapshotPath)) {
            throw new DatabaseBackupException('No se pudo generar el snapshot de la base de datos.');
        }

        return $snapshotPath;
    }

    /**
     * Reemplaza la base de datos actual con la subida por el usuario.
     * Ante cualquier fallo, restaura automáticamente el backup.
     */
    public function replaceWithUpload(UploadedFile $upload, bool $force = false): void
    {
        $this->assertUploadIsValidSqlite($upload);
        $this->assertUploadIntegrity($upload->getRealPath());

        if (!$force) {
            $this->schemaChecker->assertCompatible(
                currentDbPath: $this->getDatabasePath(),
                candidateDbPath: $upload->getRealPath(),
            );
        }

        $targetPath = $this->getDatabasePath();
        $backupPath = $targetPath.self::BACKUP_SUFFIX;

        // Cerramos conexión para liberar el archivo (SQLite bloquea en Windows).
        $this->connection->close();

        // Backup del actual ANTES de tocar nada.
        $this->filesystem->copy($targetPath, $backupPath, overwriteNewerFiles: true);

        try {
            $this->removeAuxiliaryFiles($targetPath);
            $upload->move(\dirname($targetPath), \basename($targetPath));
        } catch (\Throwable $e) {
            $this->rollback($backupPath, $targetPath);
            throw new DatabaseBackupException('Error al importar la base de datos: '.$e->getMessage(), previous: $e);
        }

        // Opcional: borrar el backup tras éxito. Recomiendo conservarlo por seguridad.
    }

    private function getDatabasePath(): string
    {
        /**
         * @var array<string> $params
         */
        $params = $this->connection->getParams();

        if ('' === $params['path']) {
            throw new DatabaseBackupException('La conexión activa no es SQLite basada en archivo.');
        }

        return $params['path'];
    }

    private function buildSnapshotPath(): string
    {
        return sprintf(
            '%s%sdb-snapshot-%s-%s.sqlite',
            sys_get_temp_dir(),
            \DIRECTORY_SEPARATOR,
            date('Ymd-His'),
            bin2hex(random_bytes(4)),
        );
    }

    private function assertUploadIsValidSqlite(UploadedFile $upload): void
    {
        if (!$upload->isValid()) {
            throw new DatabaseBackupException('El archivo no se subió correctamente.');
        }

        $handle = @fopen($upload->getRealPath(), 'rb');
        if (false === $handle) {
            throw new DatabaseBackupException('No se pudo leer el archivo subido.');
        }

        $header = fread($handle, 16);
        fclose($handle);

        if (self::SQLITE_MAGIC_HEADER !== $header) {
            throw new DatabaseBackupException('El archivo no es una base de datos SQLite válida.');
        }
    }

    private function assertUploadIntegrity(string $path): void
    {
        try {
            $pdo = new \PDO('sqlite:'.$path, options: [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            $stmt = $pdo->query('PRAGMA integrity_check');
            $result = (false !== $stmt) ? $stmt->fetchColumn() : '';
        } catch (\PDOException $e) {
            throw new DatabaseBackupException('El archivo no se puede abrir como SQLite: '.$e->getMessage());
        }

        if ('ok' !== $result) {
            throw new DatabaseBackupException('La base de datos subida está corrupta: '.$result);
        }
    }

    private function removeAuxiliaryFiles(string $targetPath): void
    {
        foreach (self::SQLITE_AUX_SUFFIXES as $suffix) {
            $auxFile = $targetPath.$suffix;
            if ($this->filesystem->exists($auxFile)) {
                $this->filesystem->remove($auxFile);
            }
        }
    }

    private function rollback(string $backupPath, string $targetPath): void
    {
        if ($this->filesystem->exists($backupPath)) {
            $this->filesystem->copy($backupPath, $targetPath, overwriteNewerFiles: true);
        }
    }
}
