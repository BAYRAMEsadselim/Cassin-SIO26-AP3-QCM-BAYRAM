<?php
declare(strict_types=1);

/**
 * Lanceur de tests minimal, sans dépendance : php tests/run.php
 * Les tests ne touchent pas à la base de données.
 */

spl_autoload_register(function (string $class): void {
    if (strncmp($class, 'App\\', 4) === 0) {
        $file = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

$GLOBALS['resultats'] = ['ok' => 0, 'ko' => 0];

function test(string $nom, callable $fn): void
{
    try {
        $fn();
        $GLOBALS['resultats']['ok']++;
        echo "  OK  $nom\n";
    } catch (Throwable $e) {
        $GLOBALS['resultats']['ko']++;
        echo "  KO  $nom : " . $e->getMessage() . "\n";
    }
}

function verifier(bool $condition, string $message = 'Assertion fausse'): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function verifierEgal(mixed $attendu, mixed $obtenu): void
{
    verifier($attendu === $obtenu, 'attendu ' . var_export($attendu, true) . ', obtenu ' . var_export($obtenu, true));
}

foreach (glob(__DIR__ . '/*Test.php') as $fichier) {
    echo basename($fichier) . "\n";
    require $fichier;
}

echo "\n{$GLOBALS['resultats']['ok']} réussi(s), {$GLOBALS['resultats']['ko']} échec(s)\n";
exit($GLOBALS['resultats']['ko'] > 0 ? 1 : 0);
