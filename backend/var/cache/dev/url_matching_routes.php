<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/admin/hotels' => [[['_route' => 'admin_hotels_list', '_controller' => 'App\\Controller\\Api\\Admin\\HotelController::list'], null, ['GET' => 0], null, false, false, null]],
        '/api/admin/maintenance' => [[['_route' => 'admin_maintenance_create', '_controller' => 'App\\Controller\\Api\\Admin\\MaintenanceController::create'], null, ['POST' => 0], null, false, false, null]],
        '/api/admin/services' => [[['_route' => 'admin_services_list', '_controller' => 'App\\Controller\\Api\\Admin\\ServiceController::list'], null, ['GET' => 0], null, false, false, null]],
        '/api/client/reservations' => [[['_route' => 'client_reservation_create', '_controller' => 'App\\Controller\\Api\\Client\\ReservationController::create'], null, ['POST' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'api_login', '_controller' => 'App\\Controller\\Api\\LoginController::login'], null, ['POST' => 0], null, false, false, null]],
        '/api/register' => [[['_route' => 'api_register', '_controller' => 'App\\Controller\\Api\\RegisterController::register'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/api/(?'
                    .'|admin/(?'
                        .'|hotels/([^/]++)/rooms(*:45)'
                        .'|maintenance/(?'
                            .'|rooms/([^/]++)(*:81)'
                            .'|([^/]++)(*:96)'
                        .')'
                        .'|rooms/([^/]++)(*:118)'
                    .')'
                    .'|client/reservations/([^/]++)(*:155)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        45 => [[['_route' => 'admin_hotels_rooms', '_controller' => 'App\\Controller\\Api\\Admin\\HotelController::rooms'], ['id'], ['GET' => 0], null, false, false, null]],
        81 => [[['_route' => 'admin_maintenance_list_by_room', '_controller' => 'App\\Controller\\Api\\Admin\\MaintenanceController::listByRoom'], ['id'], ['GET' => 0], null, false, true, null]],
        96 => [[['_route' => 'admin_maintenance_update', '_controller' => 'App\\Controller\\Api\\Admin\\MaintenanceController::update'], ['id'], ['PATCH' => 0], null, false, true, null]],
        118 => [[['_route' => 'admin_rooms_show', '_controller' => 'App\\Controller\\Api\\Admin\\RoomController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        155 => [
            [['_route' => 'client_reservation_delete', '_controller' => 'App\\Controller\\Api\\Client\\ReservationController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
