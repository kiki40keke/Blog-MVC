<?php

namespace App\Core;

class Routes
{
    public static function register($router, $basePath)
    {
        // Définir le chemin de base
        $router->setBasePath($basePath);

        // Déclaration des routes
        $router
            // Public (clients)
            ->get('/', 'PostController@index', 'home')
            ->get('/blog/category/[*:slug]-[i:id]', 'CategoryController@show', 'category')
            ->get('/blog/[*:slug]-[i:id]',          'PostController@show',    'post')

            // Auth (admin)
            ->match('/login',  'Admins\\AuthController@login',   'login')
            ->get('/logout',   'Admins\\AuthController@logout',  'logout')

            // Admin - Posts
            ->get('/admin',                     'Admins\\PostController@index',  'admin_posts')
            ->get('/admin/post/show/[i:id]',    'Admins\\PostController@show',   'admin_post')
            ->match('/admin/post/edit/[i:id]',  'Admins\\PostController@edit',   'admin_post_edit')
            ->post('/admin/post/delete/[i:id]', 'Admins\\PostController@delete', 'admin_post_delete')
            ->match('/admin/post/new',          'Admins\\PostController@new',    'admin_post_new')

            // Admin - Categories
            ->get('/admin/category',                   'Admins\\CategoryController@index',  'admin_categories')
            ->match('/admin/category/edit/[i:id]',     'Admins\\CategoryController@edit',  'admin_category_edit')
            ->post('/admin/category/delete/[i:id]',    'Admins\\CategoryController@delete', 'admin_category_delete')
            ->match('/admin/category/new',             'Admins\\CategoryController@new',   'admin_category_new')

            ->run();
    }
}
