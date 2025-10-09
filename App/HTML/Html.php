<?php

declare(strict_types=1);

namespace App\HTML;

final class Html
{
    public static function generateMenu(
        array $menuItems,
        string $currentPage,
        string $baseClass = 'nav-link',
        string $activeClass = 'active'
    ): string {
        $html = '';

        foreach ($menuItems as $page => $item) {
            $isActive = ($currentPage === $page);
            $cssClass = $baseClass . ($isActive ? " $activeClass" : '');

            if (is_array($item)) {
                $url   = $item['url']   ?? '#';
                $label = $item['label'] ?? ucfirst($page);
            } else {
                $url = '#';
                $label = (string) $item;
            }

            $html .= sprintf(
                '<a class="%s" href="%s">%s</a>' . PHP_EOL,
                htmlspecialchars($cssClass, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
            );
        }

        return $html;
    }
}
