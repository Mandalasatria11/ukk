<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Approval;
use App\Models\Department;
use App\Models\Position;
use App\Models\Reimbursement;
use App\Models\ReimbursementItem;
use App\Models\TravelPolicy;
use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate tables for clean seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ActivityLog::truncate();
        ReimbursementItem::truncate();
        Reimbursement::truncate();
        Approval::truncate();
        TravelRequest::truncate();
        TravelPolicy::truncate();
        User::truncate();
        Position::truncate();
        Department::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Departments
        $deptIds = [];
        foreach (['IT', 'Finance', 'HR', 'Sales', 'Operations'] as $name) {
            $deptIds[$name] = Department::create(['name' => $name])->id;
        }

        // Positions
        $posIds = [];
        foreach (['Staff', 'Supervisor', 'Manager'] as $name) {
            $posIds[$name] = Position::create(['name' => $name])->id;
        }

        // Users
        $make = function ($name, $email, $role, $dept, $pos, $pw = 'password123') {
            return User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($pw),
                'department_id' => $dept,
                'position_id' => $pos,
                'role' => $role,
                'is_active' => true,
            ]);
        };

        $super = $make('Super Admin', 'superadmin@andrea.co.id', 'SUPER_ADMIN', $deptIds['IT'], $posIds['Manager']);
        $admin = $make('Admin Travel', 'admin@andrea.co.id', 'ADMIN', $deptIds['Operations'], $posIds['Manager']);
        $finance = $make('Finance User', 'finance@andrea.co.id', 'FINANCE', $deptIds['Finance'], $posIds['Manager']);
        $manager = $make('Manager Sales', 'manager@andrea.co.id', 'MANAGER', $deptIds['Sales'], $posIds['Manager']);
        $hrd = $make('HRD User', 'hrd@andrea.co.id', 'HRD', $deptIds['HR'], $posIds['Manager']);
        $employee = $make('Hans Employee', 'hans@andrea.co.id', 'EMPLOYEE', $deptIds['Sales'], $posIds['Staff']);

        // Policies
        $polStaff = TravelPolicy::create([
            'name' => 'Domestik - Staff',
            'position_id' => $posIds['Staff'],
            'destination_tier' => 'DOMESTIC',
            'max_budget' => 5000000,
            'is_active' => true,
        ]);

        $polLocal = TravelPolicy::create([
            'name' => 'Lokal Jabodetabek - Staff',
            'position_id' => $posIds['Staff'],
            'destination_tier' => 'LOCAL',
            'max_budget' => 2000000,
            'is_active' => true,
        ]);

        $polManager = TravelPolicy::create([
            'name' => 'Internasional - Manager',
            'position_id' => $posIds['Manager'],
            'destination_tier' => 'INTERNATIONAL',
            'max_budget' => 15000000,
            'is_active' => true,
        ]);

        // Sample Travel 1: Pending Approval for Manager Sales to Review!
        $travel1 = TravelRequest::create([
            'user_id' => $employee->id,
            'department_id' => $employee->department_id,
            'destination' => 'Surabaya, Jawa Timur',
            'purpose' => 'Menghadiri negosiasi kontrak tender penjualan produk korporat dan kunjungan distributor resmi wilayah Jawa Timur.',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(5),
            'estimated_cost' => 3850000,
            'policy_id' => $polStaff->id,
            'status' => 'PENDING_APPROVAL',
        ]);

        // Level 1 Approval assigned to Manager Sales
        Approval::create([
            'travel_request_id' => $travel1->id,
            'approver_id' => $manager->id,
            'level' => 1,
            'status' => 'PENDING',
        ]);

        // Sample Travel 2: Approved Travel + Reimbursement in Verification
        $travel2 = TravelRequest::create([
            'user_id' => $employee->id,
            'department_id' => $employee->department_id,
            'destination' => 'Bandung, Jawa Barat',
            'purpose' => 'Kunjungan kerja instalasi sistem perangkat korporat pada klien PT Mitra Bandung.',
            'start_date' => now()->subDays(6),
            'end_date' => now()->subDays(4),
            'estimated_cost' => 1950000,
            'policy_id' => $polLocal->id,
            'status' => 'APPROVED',
        ]);

        Approval::create([
            'travel_request_id' => $travel2->id,
            'approver_id' => $manager->id,
            'level' => 1,
            'status' => 'APPROVED',
            'note' => 'Disetujui. Tugas dinas sesuai program penjualan Q4.',
        ]);

        Approval::create([
            'travel_request_id' => $travel2->id,
            'approver_id' => $hrd->id,
            'level' => 2,
            'status' => 'APPROVED',
            'note' => 'Surat tugas dinas telah disahkan HRD.',
        ]);

        // Reimbursement for Travel 2
        $reimb = Reimbursement::create([
            'travel_request_id' => $travel2->id,
            'user_id' => $employee->id,
            'advance_amount' => 500000,
            'status' => 'PENDING_VERIFICATION',
        ]);

        ReimbursementItem::create([
            'reimbursement_id' => $reimb->id,
            'category' => 'TRANSPORT',
            'description' => 'Tiket Kereta Cepat Whoosh Jakarta-Bandung PP',
            'amount' => 600000,
            'transaction_date' => now()->subDays(6),
        ]);

        ReimbursementItem::create([
            'reimbursement_id' => $reimb->id,
            'category' => 'HOTEL',
            'description' => 'Hotel Ibis Bandung Pasteur (2 Malam)',
            'amount' => 1250000,
            'transaction_date' => now()->subDays(5),
        ]);

        // Sample Activity Logs
        ActivityLog::create([
            'user_id' => $employee->id,
            'action' => 'SUBMIT_TRAVEL',
            'entity_type' => 'TravelRequest',
            'entity_id' => $travel1->id,
            'description' => 'Hans Employee mengajukan perjalanan dinas ke Surabaya, Jawa Timur (Status: Pending Approval).',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $manager->id,
            'action' => 'MANAGER_APPROVE_TRAVEL',
            'entity_type' => 'TravelRequest',
            'entity_id' => $travel2->id,
            'description' => 'Manager Sales menyetujui pengajuan perjalanan dinas Bandung #TRV-2 milik Hans Employee.',
            'ip_address' => '127.0.0.1',
        ]);

        ActivityLog::create([
            'user_id' => $employee->id,
            'action' => 'SUBMIT_REIMBURSEMENT',
            'entity_type' => 'Reimbursement',
            'entity_id' => $reimb->id,
            'description' => 'Hans Employee mengajukan klaim reimbursement #RMB-1 sebesar Rp 1.850.000 ke Finance.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
