<?php
/**
 * @file practica-pastor.php
 * @package  practica-pastor
 * @author Adrian Pastor
 * @version 1.0.0
 *
 * Script utilizado en la tarea de otra asignatura que usaremos como ejemplo para la documentación con phpDocumentor.
 *
 */
namespace App\Http\Controllers\ApiAPO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\MascotaAPO;

/**
 * Class APOMascotasControllerAPI
 *
 * Controlador API para gestionar las mascotas del usuario autenticado.
 *
 */
class APOMascotasControllerAPI extends Controller
{
    /**
     * Este método obtiene el usuario autenticado y lista todas sus mascotas.
     *
     * @param  Request      $request  La solicitud HTTP que contiene el usuario autenticado.
     * @return JsonResponse           Arreglo de mascotas en formato JSON.
     */
    public function listarMascotasAPO(Request $request): JsonResponse
    {
        $usuario = $request->user();
        $mascotas = MascotaAPO::where('user_id', $usuario->id)
            ->get(['id', 'nombre', 'descripcion', 'tipo', 'megusta']);
        return response()->json($mascotas);
    }

    /**
     * Crea una nueva mascota asociada al usuario autenticado.
     *
     * @param  Request      $request  La solicitud HTTP con datos de la mascota.
     * @return JsonResponse           Mensaje de éxito y datos de la nueva mascota.
     */
    public function crearMascotaAPO(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->only(['nombre', 'descripcion', 'tipo', 'publica']),
            [
                'nombre'      => 'required|string|max:50',
                'descripcion' => 'required|string|max:250',
                'tipo'        => 'required|string|in:Perro,Gato,Pájaro,Dragón,Conejo,Hamster,Tortuga,Pez,Serpiente',
                'publica'     => 'required|string|in:Si,No',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Datos no válidos',
                'errores' => $validator->errors()
            ], 400);
        }
        $datos = $validator->validated();
        $usuario = $request->user();
        $datos['user_id'] = $usuario->id;
        $datos['megusta'] = 0;
        $mascota = MascotaAPO::create($datos);
        return response()->json([
            'mensaje' => 'Mascota creada con éxito',
            'mascota' => ['id' => $mascota->id],
            'usuario' => [
                'nombre'    => $usuario->name,
                'apellidos' => $usuario->apellidos ?? ''
            ]
        ], 200);
    }

    /**
     * Modifica la descripción y visibilidad de una mascota existente.
     * @internal Este bloque y cualquier anotación marcada con @internal aparecen únicamente en la documentación para desarrolladores.
     * @param  MascotaAPO   $mascota  Instancia de la mascota (route-model binding).
     * @param  Request      $request  La solicitud HTTP con nuevos datos en JSON.
     * @return JsonResponse           Resultado de la operación con datos actualizados.
     */
    public function cambiarMascotaAPO(MascotaAPO $mascota, Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->json()->all(),
            [
                'descripcion' => 'required|string|max:250',
                'publica'     => 'required|string|in:Si,No',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Datos no válidos',
                'errores' => $validator->errors()
            ], 400);
        }
        $usuario = $request->user();
        if ($mascota->user_id !== $usuario->id) {
            return response()->json(['mensaje' => 'No tienes permiso para modificar esta mascota.'], 403);
        }
        $mascota->descripcion = $request->json('descripcion');
        $mascota->publica     = $request->json('publica');
        $mascota->save();
        return response()->json([
            'mensaje' => 'Mascota modificada con éxito',
            'mascota' => [
                'id'          => $mascota->id,
                'descripcion' => $mascota->descripcion,
                'publica'     => $mascota->publica,
            ],
            'usuario' => [
                'nombre'    => $usuario->name,
                'apellidos' => $usuario->apellidos ?? ''
            ]
        ], 200);
    }

    /**
     * Elimina una mascota específica del usuario autenticado.
     *
     * @param  mixed        $mascota  Identificador numérico de la mascota.
     * @return JsonResponse           Mensaje de confirmación o error.
     */
    public function borrarMascotaAPO($mascota): JsonResponse
    {
        if (!is_numeric($mascota)) {
            return response()->json(['mensaje' => 'El identificador debe ser numérico.'], 400);
        }
        $usuario = auth()->user();
        $mascotaModel = MascotaAPO::find($mascota);
        if (! $mascotaModel) {
            return response()->json(['mensaje' => 'La mascota no existe.'], 200);
        }
        if ($mascotaModel->user_id !== $usuario->id) {
            return response()->json(['mensaje' => 'La mascota no pertenece al usuario.'], 200);
        }
        $mascotaModel->delete();
        return response()->json(['mensaje' => 'Mascota eliminada exitosamente.'], 200);
    }
}
