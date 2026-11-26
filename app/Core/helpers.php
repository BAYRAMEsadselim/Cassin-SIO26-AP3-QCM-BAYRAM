<?php
declare(strict_types=1);

/** Échappe une valeur pour l'affichage HTML (protection XSS). */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
