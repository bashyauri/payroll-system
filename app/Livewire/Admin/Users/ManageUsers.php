<?php

namespace App\Livewire\Admin\Users;

use Error;
use Flux\Flux;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use App\Models\Department;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Livewire\WithPagination;

class ManageUsers extends Component
{
    use WithPagination;
    public $roles = [];
    public $departments = [];

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';
    public string $department_id = '';
    public string $role_id = '';
    public string $phone = '';
    public string $status = '';
    public ?User $editingUser = null;
    public ?User $userToDelete = null;
    public $search = '';




    public function mount(): void
    {
        $this->roles = Role::all();
        $this->departments = Department::all();
    }
    public string $sortBy = 'name'; // default sorting field
    public string $sortDirection = 'asc'; // default sorting direction
    public function updatingSearch()
    {

        $this->resetPage();
    }
    #[\Livewire\Attributes\Computed]
    public function users()
    {

        return User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('roles', function ($roleQuery) {
                            $roleQuery->where('name', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('department', function ($deptQuery) {
                            $deptQuery->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->with(['roles', 'department'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }
    public function sort($field)
    {
        if ($this->sortBy === $field) {
            // Toggle the direction
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Set new sort field
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function saveUser(): void
    {


        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone'     => ['required', 'min:11', 'numeric'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'status' => ['required', 'string'],
            'department_id' => ['required'],
            'role_id' => ['required'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);


        try {
            $todayDate = Carbon::now()->toDayDateTimeString();
            $user = User::create([
                'name' => $validated['name'],
                'phone_number' => $validated['phone'],
                'email' => $validated['email'],
                'status' => $validated['status'],
                'join_date' => $todayDate,
                'last_login' => $todayDate,
                'department_id' => $validated['department_id'], // If department is a string, otherwise adjust
                'password' => bcrypt($validated['password']),

            ]);

            // Attach role via pivot table
            $user->roles()->attach($validated['role_id']);

            $this->dispatch('user-added', name: $validated['name']);
            $this->reset();
            $this->modal('user-added')->close();
        } catch (Error $e) {
            Log::info("Error: " . $e->getMessage());
        }
    }
    public function getUsersProperty()
    {
        return User::orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }
    public function editUser(User $user): void
    {
        $this->editingUser = $user;
        $this->name = $user?->name ?? '';
        $this->email = $user?->email ?? '';
        $this->phone = $user?->phone_number ?? '';
        $this->status = $user?->status ?? '';
        $this->department_id = $user?->department_id ?? '';
        $this->role_id = $user->roles->first()->id ?? '';


        // Open the modal
        // $this->dispatch('open-modal', name: 'edit-profile-' . $user->id);
    }
    public function updateUser(): void
    {
        // Validation rules (similar to create but with unique email exception)
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'min:11', 'numeric'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email,' . $this->editingUser->id
            ],
            'status' => ['required', 'string'],
            'department_id' => ['required'],
            'role_id' => ['required'],

        ]);

        try {
            // Update basic user info
            $this->editingUser->update([
                'name' => $validated['name'],
                'phone_number' => $validated['phone'],
                'email' => $validated['email'],
                'status' => $validated['status'],
                'department_id' => $validated['department_id'],
            ]);


            // Sync roles (assuming single role per user)
            $this->editingUser->roles()->sync([$validated['role_id']]);

            // Update last login time
            $this->editingUser->update([
                'last_login' => Carbon::now()->toDayDateTimeString()
            ]);

            $this->dispatch('user-updated', name: $validated['name']);
            $this->resetExcept(['roles', 'departments']);
            $this->modal('edit-user-' . $this->editingUser->id)->close();
        } catch (\Exception $e) {
            Log::error("User update failed: " . $e->getMessage());
            $this->addError('update', 'Failed to update user. Please try again.');
        }
    }
    public function closeForm()
    {
        Flux::modals()->close();
    }
    public function resetForm(): void
    {
        $this->reset([
            'name',
            'email',
            'phone',
            'status',
            'department_id',
            'role_id',



        ]);
        $this->resetErrorBag();
    }
    public function confirmDelete(User $user): void
    {
        $this->userToDelete = $user;
        $this->modal('edit-user-' . $user->id)->close();
    }
    public function deleteUser(): void
    {
        try {
            $userName = $this->userToDelete->name;
            $this->userToDelete->delete();

            $this->dispatch('user-deleted', name: $userName);
            $this->reset('userToDelete');
            $this->dispatch('close-modal', name: 'delete-user-' . $this->userToDelete->id);
        } catch (\Exception $e) {
            Log::error("User deletion failed: " . $e->getMessage());
            $this->addError('delete', 'Failed to delete user. Please try again.');
        }
    }



    public function render()
    {
        return view('livewire.admin.users.manage-users', [
            'roles' => $this->roles,
            'departments' => $this->departments
        ]);
    }
}
