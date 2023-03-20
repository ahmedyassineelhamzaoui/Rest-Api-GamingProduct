<?php
namespace Tests\storage\annotations\OpenApi;
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      x={
 *          "logo": {
 *              "url": "https://via.placeholder.com/190x90.png?text=L5-Swagger"
 *          }
 *      },
 *      title="gaming product management with REST API",
 *      description="a scalable and flexible system for managing a store of gaming product .",
 *      @OA\Contact(
 *          email="ahmed.yassin.elhamzaoui2019@gmail.com"
 *      ),
 *     @OA\License(
 *         name="made by Ahmed Yassine El Hamzaoui",
 *         url="https://github.com/ahmedyassineelhamzaoui"
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
