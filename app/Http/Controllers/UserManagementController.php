<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserManagementController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $users = User::with('role')->paginate(10);
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $this->authorize('updateRole', User::class);
        
        Log::info('Entrando en UserManagementController@updateRole', [
            'user_id' => $user->id,
            'request_data' => $request->all(),
            'input_receives_notifications' => $request->input('receives_notifications', null),
            'has_receives_notifications' => $request->has('receives_notifications')
        ]);
        
        try {
            $request->validate([
                'role_id' => 'required|exists:roles,id',
            ]);

            $role = Role::findOrFail($request->role_id);
            
            // Obtener el valor del checkbox - estará presente solo si está marcado
            $receives_notifications = $request->has('receives_notifications') ? true : false;
            
            Log::info('Valores a actualizar', [
                'role_id' => $role->id,
                'receives_notifications' => $receives_notifications,
                'current_values' => [
                    'current_role_id' => $user->role_id,
                    'current_receives_notifications' => $user->receives_notifications
                ]
            ]);

            // Actualizar solo los campos específicos
            $user->role_id = $role->id;
            $user->receives_notifications = $receives_notifications;
            $user->save();

            Log::info('Usuario actualizado correctamente', [
                'user_id' => $user->id,
                'new_role_id' => $user->role_id,
                'new_role_name' => $role->name,
                'receives_notifications' => $user->receives_notifications
            ]);

            return redirect()->back()->with('success', 'Información de usuario actualizada correctamente');
        } catch (\Exception $e) {
            Log::error('Error en UserManagementController@updateRole', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error al actualizar la información del usuario: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', User::class);

        try {
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
            }

            $user->delete();
            return redirect()->back()->with('success', 'Usuario eliminado con éxito');
        } catch (\Exception $e) {
            Log::error('Error al eliminar usuario', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error al eliminar el usuario');
        }
    }
}