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
            $access = AccessNotification::where('notification_id', $this->id)
                ->first();

            $this->roleStatus[$role->id] = $access ? $access->is_active : false;
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
        // Cek role dari checkbox
        $role = Role::find($roleId);

        if (!$role) {
            return;
        }

        // Cari user dengan role yang dipilih
        $users = User::where('role_id', $roleId)->get();

        foreach ($users as $user) {
            $access = AccessNotification::where('notification_id', $this->id)
                ->where('data_warga_id', $user->data_warga_id)
                ->first();

            if ($access) {
                // Jika data sudah ada, toggle is_active
                $access->is_active = !$access->is_active;
                $access->save();
            } else {
                // Jika data belum ada, tambahkan
                AccessNotification::create([
                    'notification_id' => $this->id,
                    'data_warga_id' => $user->data_warga_id,
                    'is_active' => true,
                ]);
            }

            // Perbarui status di frontend
            $this->roleStatus[$roleId] = !$this->roleStatus[$roleId];
        }
    }

    public function render()
    {
        return view('livewire.access-notif.kas-payment', [
            'roles' => $this->roles,
        ]);
    }
}
