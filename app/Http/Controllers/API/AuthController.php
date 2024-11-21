<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::paginate(50);
        return $this->success($users, "{$users->total()} usuários encontrados", 200);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            return $this->success($user, "Usuário encontrado com sucesso.", 200);
        } catch (ModelNotFoundException $e) {
            return $this->error(null, "Usuário com ID {$id} não encontrado.", 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Erro ao buscar usuário.", 500);
        }
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|required|max:191',
            'username' => 'string|required|unique:users|max:30',
            'email' => 'string|email|required|max:254',
            'password' => 'string|confirmed|required|max:254',
            'avatar' => 'sometimes|nullable|image|max:2048',
        ]);

        try{
            // Create the user (without the avatar)
            $user = User::create([
                'name' => $validated['name'],
                'username' => Str::kebab($validated['username']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Upload the avatar and save the model
            if ($request->hasFile('avatar')) {
                $user->uploadAvatar($request->file('avatar'));
            }

            return $this->success($user, 'Usuário criado com sucesso', 201);
        }catch(\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'string|required|max:191',
                'username' => "string|required|unique:users,username,{$user->id}|max:30",
                'email' => "string|email|required|unique:users,email,{$user->id}|max:254",
            ]);

            $user->update($validated);

            return $this->success($user, "Usuário com ID {$id} atualizado com sucesso.", 200);
        } catch (ModelNotFoundException $e) {
            return $this->error(null, "Usuário com ID {$id} não encontrado.", 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Erro ao atualizar usuário.", 500);
        }
    }

    public function updatePassword(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $validated = $request->validate([
                'password' => 'string|required|confirmed|min:6|max:254',
            ]);
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
            return $this->success(null, "Senha do usuário com ID {$id} atualizada com sucesso.", 200);
        } catch (ModelNotFoundException $e) {
            return $this->error(null, "Usuário com ID {$id} não encontrado.", 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Erro ao atualizar senha.", 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'email|string|required',
            'password' => 'string|required|min:6',
        ]);

        if(!Auth::attempt($credentials)){

            // Add a delay for wrong credentials
            sleep(2);

            return $this->error(null, 'Credenciais inválidas', 401);
        }

        $user = Auth::user();
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API_Token')->plainTextToken,
        ], 'Login realizado com sucesso.', 200);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();

        if(!$user instanceof User){
            return $this->error(null, 'Você não está logado', 401);
        }

        $user->tokens()->delete();
        return $this->success(null, 'Logout efetuado com sucesso', 200);
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return $this->success(null, "Usuário com ID {$id} excluído com sucesso.", 200);
        } catch (ModelNotFoundException $e) {
            return $this->error(null, "Usuário com ID {$id} não encontrado.", 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Erro ao excluir usuário.", 500);
        }
    }

    public function restore(int $id): JsonResponse
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();
            return $this->success(null, "Usuário com ID {$id} restaurado com sucesso.", 200);
        } catch (ModelNotFoundException $e) {
            return $this->error(null, "Usuário com ID {$id} não encontrado na lixeira.", 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Erro ao restaurar usuário.", 500);
        }
    }

    public function me(): JsonResponse
    {
        $user = Auth::user();

        if(!$user instanceof User){
            return $this->error(null, 'Usuário inexistente', 404);
        }

        return $this->success($user, 'Usuário encontrado', 200);
    }

    public function updateAvatar(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $validated = $request->validate([
                'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);
            $user->uploadAvatar($validated['avatar']);
            return $this->success($user->getAvatarPath(), "Avatar do usuário com ID {$id} atualizado com sucesso.", 200);
        } catch (ModelNotFoundException $e) {
            return $this->error(null, "Usuário com ID {$id} não encontrado.", 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Erro ao atualizar avatar.", 500);
        }
    }

    public function deleteAvatar(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            if (!$user->avatar) {
                return $this->error(null, "Usuário {$user->name} não possui um avatar para deletar.", 400);
            }
            $user->deleteAvatar();
            return $this->success(null, "Avatar de {$user->name} deletado com sucesso.", 200);
        } catch (ModelNotFoundException $e) {
            return $this->error(null, "Usuário com ID {$id} não encontrado.", 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Erro ao deletar avatar.", 500);
        }
    }

    public function annihilate(int $id): JsonResponse
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->tokens()->delete();
            $user->forceDelete();
            return $this->success(null, "Usuário com ID {$id} deletado permanentemente.", 200);
        } catch (ModelNotFoundException $e) {
            return $this->error(null, "Usuário com ID {$id} não encontrado.", 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), "Erro ao deletar usuário permanentemente.", 500);
        }
    }

}
