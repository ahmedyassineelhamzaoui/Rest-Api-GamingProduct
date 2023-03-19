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
 *      title="Blog Management System with REST API",
 *      description="a scalable and flexible system for managing blog content with the ability to support multiple users.",
 *      @OA\Contact(
 *          email="ahmed.yassin.elhamzaoui2019@gmail.com"
 *      ),
 *     @OA\License(
 *         name="lazone m7koma",
 *         url="https://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
