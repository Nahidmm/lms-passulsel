<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class KelolAksesController extends Controller
{
    // ==========================================
    // Dashboard Kelola Akses
    // ==========================================

    public function index()
    {
        $roles       = Role::withCount(['permissions', 'users'])->orderBy('is_default', 'desc')->orderBy('nama')->get();
        $permissions = Permission::orderBy('grup')->orderBy('urutan')->get()->groupBy('grup');
        $stats       = [
            'total_roles'       => Role::count(),
            'total_permissions' => Permission::count(),
            'users_with_custom' => User::has('customRoles')->count(),
            'total_users'       => User::where('role', '!=', 'superadmin')->count(),
        ];

        return view('admin.kelola-akses.index', compact('roles', 'permissions', 'stats'));
    }

    // ==========================================
    // CRUD Role
    // ==========================================

    public function createRole()
    {
        $permissions = Permission::orderBy('grup')->orderBy('urutan')->get()->groupBy('grup');
        return view('admin.kelola-akses.roles.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:100|unique:roles,nama',
            'deskripsi'   => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'nama'       => $request->nama,
            'deskripsi'  => $request->deskripsi,
            'is_default' => false,
            'base_role'  => null,
        ]);

        if ($request->permissions) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.kelola-akses.index')
            ->with('success', "Role \"{$role->nama}\" berhasil dibuat.");
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);

        if ($role->isProtected()) {
            return redirect()->route('admin.kelola-akses.index')
                ->with('error', 'Role Superadmin dilindungi dan tidak dapat diedit.');
        }

        $permissions       = Permission::orderBy('grup')->orderBy('urutan')->get()->groupBy('grup');
        $rolePermissionIds = $role->permissions()->pluck('permissions.id')->toArray();

        return view('admin.kelola-akses.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->isProtected()) {
            return redirect()->route('admin.kelola-akses.index')
                ->with('error', 'Role Superadmin dilindungi dan tidak dapat diedit.');
        }

        $request->validate([
            'nama'          => 'required|string|max:100|unique:roles,nama,' . $role->id,
            'deskripsi'     => 'nullable|string|max:500',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('admin.kelola-akses.index')
            ->with('success', "Role \"{$role->nama}\" berhasil diperbarui.");
    }

    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_default) {
            return redirect()->route('admin.kelola-akses.index')
                ->with('error', 'Role default sistem tidak dapat dihapus.');
        }

        $roleName = $role->nama;
        $role->delete();

        return redirect()->route('admin.kelola-akses.index')
            ->with('success', "Role \"{$roleName}\" berhasil dihapus.");
    }

    // ==========================================
    // Assign Role ke User
    // ==========================================

    public function users(Request $request)
    {
        $query = User::with(['jabatan', 'customRoles'])
            ->where('role', '!=', 'superadmin');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('base_role')) {
            $query->where('role', $request->base_role);
        }

        $users = $query->orderBy('nama')->paginate(15)->withQueryString();
        $customRoles = Role::where('is_default', false)->orderBy('nama')->get();

        return view('admin.kelola-akses.users', compact('users', 'customRoles'));
    }

    public function assignRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->isSuperadmin()) {
            return back()->with('error', 'Tidak dapat mengubah akses superadmin.');
        }

        $request->validate([
            'role_ids'   => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        // Prevent assigning the superadmin protected role
        $safeRoleIds = collect($request->role_ids ?? [])->filter(function ($roleId) {
            $role = Role::find($roleId);
            return $role && !$role->isProtected();
        })->values()->toArray();

        $user->customRoles()->sync($safeRoleIds);

        return back()->with('success', "Akses role untuk {$user->nama} berhasil diperbarui.");
    }
}
