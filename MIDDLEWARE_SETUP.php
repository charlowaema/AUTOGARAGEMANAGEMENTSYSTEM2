<?php

/**
 * ============================================================
 * AGMS — Middleware Registration
 * ============================================================
 *
 * Add the 'admin' middleware alias to your HTTP Kernel.
 *
 * File: app/Http/Kernel.php
 * Find the $middlewareAliases (or $routeMiddleware) array
 * and add this line:
 *
 *   'admin' => \App\Http\Middleware\AdminMiddleware::class,
 *
 * Example:
 * -------------------------------------------------------
 *
 * protected $middlewareAliases = [
 *     'auth'       => \App\Http\Middleware\Authenticate::class,
 *     'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,
 *     // ... other middleware ...
 *
 *     // ✅ Add this line:
 *     'admin'      => \App\Http\Middleware\AdminMiddleware::class,
 * ];
 *
 * -------------------------------------------------------
 *
 * For Laravel 11+ (bootstrap/app.php style):
 *
 * ->withMiddleware(function (Middleware $middleware) {
 *     $middleware->alias([
 *         'admin' => \App\Http\Middleware\AdminMiddleware::class,
 *     ]);
 * })
 *
 * ============================================================
 * DEFAULT LOGIN CREDENTIALS (after php artisan db:seed)
 * ============================================================
 *
 * Email:    admin@agms.local
 * Password: password
 *
 * ⚠ Change the password immediately after first login!
 *
 * ============================================================
 */
