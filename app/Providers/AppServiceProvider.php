<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\AllRouteUrl;
use App\Models\Anggaran;
use App\Models\AnggaranSaldo;
use App\Models\AnggaranSetting;
use App\Models\CashExpenditures;
use App\Models\CompanyInformation;
use App\Models\DataWarga;
use App\Models\KasPayment;
use App\Models\Menu;
use App\Models\Program;
use App\Models\ProgramSetting;
use App\Models\Role;
use App\Models\Saldo;
use App\Models\StatusPekerjaan;
use App\Models\SubMenu;
use App\Models\User;

use App\Observers\CompanyInfoAdmin;
use App\Observers\DataAdminObserver;
use App\Observers\DataAnggaranAdmin;
use App\Observers\DataCashExAdmin;
use App\Observers\DataKasPaymentAdmin;
use App\Observers\DataMenuAdmin;
use App\Observers\DataPekerjaanAdmin;
use App\Observers\DataProgramAdmin;
use App\Observers\DataRoleAdmin;
use App\Observers\DataRouteUrlAdmin;
use App\Observers\DataSaldoAdmin;
use App\Observers\DataSaldoAnggaran;
use App\Observers\DataSettAnggaranAdmin;
use App\Observers\DataSettProgramAdmin;
use App\Observers\DataSubMenuAdmin;
use App\Observers\DataUserAdmin;
use App\Observers\DataWargaAdmin;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Admin::observe(DataAdminObserver::class);
        CompanyInformation::observe(CompanyInfoAdmin::class);
        AllRouteUrl::observe(DataRouteUrlAdmin::class);
        Menu::observe(DataMenuAdmin::class);
        SubMenu::observe(DataSubMenuAdmin::class);
        Program::observe(DataProgramAdmin::class);
        ProgramSetting::observe(DataSettProgramAdmin::class);
        Anggaran::observe(DataAnggaranAdmin::class);
        AnggaranSetting::observe(DataSettAnggaranAdmin::class);
        Role::observe(DataRoleAdmin::class);
        DataWarga::observe(DataWargaAdmin::class);
        StatusPekerjaan::observe(DataPekerjaanAdmin::class);
        Admin::observe(DataAdminObserver::class);
        KasPayment::observe(DataKasPaymentAdmin::class);
        CashExpenditures::observe(DataCashExAdmin::class);
        Saldo::observe(DataSaldoAdmin::class);
        AnggaranSaldo::observe(DataSaldoAnggaran::class);
    }
}