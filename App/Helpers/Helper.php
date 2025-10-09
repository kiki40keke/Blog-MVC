<?php


function e(?string $content): string
{
    return htmlentities($content) ?? '';
}

function dateMysql(?string $value): ?string
{
    if (empty($value)) {
        return null;
    }

    // Remplacer le T par un espace
    $value = str_replace('T', ' ', $value);

    // Si pas de secondes, on ajoute ":00"
    if (strlen($value) === 16) { // ex: 2025-08-22 16:10
        $value .= ':00';
    }

    return $value; // ex: 2025-08-22 16:10:43
}



function generateMenu($menuItems, $currentPage, $baseClass = 'nav-link', $activeClass = 'active')
{
    $html = '';

    foreach ($menuItems as $page => $item) {
        // Détermine si c'est la page active
        $isActive = ($currentPage === $page);

        // Construction de la classe CSS
        $cssClass = $baseClass;
        if ($isActive) {
            $cssClass .= ' ' . $activeClass;
        }

        // Support pour différents formats d'items
        if (is_array($item)) {
            $url = $item['url'] ?? '#';
            $label = $item['label'] ?? ucfirst($page);
        } else {
            $url = '#'; // URL par défaut
            $label = $item;
        }

        $html .= '<a class="' . $cssClass . '" href="' . htmlspecialchars($url) . '">' . htmlspecialchars($label) . '</a>' . "\n";
    }

    return $html;
}

function isAdminUrl(): bool
{
    // Récupère le chemin de la requête, ex: /admin/posts/12
    $uri = $_SERVER['REQUEST_URI'] ?? '/';

    // On peut ignorer les query string (?page=2 etc.)
    $path = parse_url($uri, PHP_URL_PATH);

    return str_starts_with($path, '/admin');
}
