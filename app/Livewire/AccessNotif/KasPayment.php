<?php

namespace App\Livewire\AccessNotif;

use App\Models\AccessNotification;
use Livewire\Component;
use App\Models\DataNotification;
use App\Models\Role;
use App\Models\User;

class KasPayment extends Component
{

    public $id; // notification_id
    public $waNotification;
    public $emailNotification;
    public $pengurus;
    public $anggota;
    public $program;
    public $roles = []; // Role list selain Anggota & Warga
    public $roleStatus = []; // Status setiap role checkbox
    public $usersByRole = []; // Menyimpan pengguna berdasarkan role


    public function mount($id)
    {
        $this->id = $id;
        $data = DataNotification::find($id);

        if ($data) {
            $this->waNotification = $data->wa_notification;
            $this->emailNotification = $data->email_notification;
            $this->pengurus = $data->pengurus;
            $this->anggota = $data->anggota;
            $this->program = $data->program;
        }

        // Ambil role selain Anggota & Warga
        $this->roles = Role::whereNotIn('name', ['Anggota', 'Warga'])->get();

        // Inisialisasi status role
        foreach ($this->roles as $role) {
            $users = User::where('role_id', $role->id)->get();

            // Simpan daftar pengguna untuk role ini
            $this->usersByRole[$role->id] = $users;

            // Periksa status setiap pengguna
            $this->roleStatus[$role->id] = $users->every(function ($user) {
                $access = AccessNotification::where('notification_id', $this->id)
                    ->where('data_warga_id', $user->id)
                    ->first();
                return $access ? $access->is_active : false;
            });
        }
    }

    public function toggle($field)
    {
        $data = DataNotification::find($this->id);

        if ($data && in_array($field, ['waNotification', 'emailNotification', 'pengurus', 'anggota', 'program'])) {
            $this->{$field} = !$this->{$field};
            $data->{$this->mapField($field)} = $this->{$field};
            $data->save();
        }
    }

    private function mapField($field)
    {
        return match ($field) {
            'waNotification' => 'wa_notification',
            'emailNotification' => 'email_notification',
            'pengurus' => 'pengurus',
            'anggota' => 'anggota',
            'program' => 'program',
        };
    }

    public function toggleRole($roleId)
    {
        $users = $this->usersByRole[$roleId] ?? [];

        foreach ($users as $user) {
            $access = AccessNotification::where('notification_id', $this->id)
                ->where('data_warga_id', $user->id)
                ->first();

            if ($access) {
                // Toggle status jika data ada
                $access->is_active = !$access->is_active;
                $access->save();
            } else {
                // Buat data baru jika tidak ada
                AccessNotification::create([
                    'notification_id' => $this->id,
                    'data_warga_id' => $user->id,
                    'is_active' => true,
                ]);
            }
        }

        // Perbarui status di frontend
        $this->roleStatus[$roleId] = !$this->roleStatus[$roleId];
    }

    public function render()
    {
        return view('livewire.access-notif.kas-payment', [
            'roles' => $this->roles,
        ]);
    }
}
