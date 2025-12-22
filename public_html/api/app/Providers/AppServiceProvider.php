<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mail\MailchimpTransport;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;
use App\Repositories\OrganizationRepository;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\Interfaces\TenderRepositoryInterface;
use App\Repositories\SettingRepository;
use App\Repositories\TenderRepository;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\RoleRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\ContractorRepositoryInterface;
use App\Repositories\ContractorRepository;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Repositories\ContactRepository;
use App\Repositories\ContractorTenderRepository;
use App\Repositories\ContractorTenderWorkOrderRepository;
use App\Repositories\DashboardRepository;
use App\Repositories\Interfaces\ContractorTenderRepositoryInterface;
use App\Repositories\Interfaces\ContractorTenderWorkOrderRepositoryInterface;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use App\Repositories\Interfaces\NoteRepositoryInterface;
use App\Repositories\Interfaces\ProfileUserRepositoryInterface;
use App\Repositories\ProfileUserRepository;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Repositories\Interfaces\SiteManagerRepositoryInterface;
use App\Repositories\NoteRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ReportRepository;
use App\Repositories\SiteManagerRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
        $this->app->bind(OrganizationRepositoryInterface::class, OrganizationRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);
        $this->app->bind(TenderRepositoryInterface::class, TenderRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ContractorRepositoryInterface::class, ContractorRepository::class);
        $this->app->bind(ContactRepositoryInterface::class, ContactRepository::class);
        $this->app->bind(ProfileUserRepositoryInterface::class, ProfileUserRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(ContractorTenderRepositoryInterface::class, ContractorTenderRepository::class);
        $this->app->bind(SiteManagerRepositoryInterface::class, SiteManagerRepository::class);
        $this->app->bind(NoteRepositoryInterface::class, NoteRepository::class);
        $this->app->bind(ContractorTenderWorkOrderRepositoryInterface::class, ContractorTenderWorkOrderRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mail::extend('mailchimp', function (array $config = []) {
        //     return new MailchimpTransport(/* ... */);
        // });
    }
}
