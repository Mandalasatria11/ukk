<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // departments
        $deptIds = [];
        foreach (['IT','Finance','HR','Sales','Operations'] as $name) {
            $deptIds[$name] = DB::table('departments')->insertGetId(['name'=>$name,'created_at'=>now(),'updated_at'=>now()]);
        }
        // positions
        $posIds = [];
        foreach (['Staff','Supervisor','Manager'] as $name) {
            $posIds[$name] = DB::table('positions')->insertGetId(['name'=>$name,'created_at'=>now(),'updated_at'=>now()]);
        }

        $make = function ($name,$email,$role,$dept,$pos,$pw='password123') {
            return User::create([
                'name'=>$name,'email'=>$email,'password'=>$pw,
                'department_id'=>$dept,'position_id'=>$pos,'role'=>$role,'is_active'=>true,
            ]);
        };

        $super = $make('Super Admin','superadmin@andrea.co.id','SUPER_ADMIN',$deptIds['IT'],$posIds['Manager']);
        $admin = $make('Admin Travel','admin@andrea.co.id','ADMIN',$deptIds['Operations'],$posIds['Manager']);
        $finance = $make('Finance User','finance@andrea.co.id','FINANCE',$deptIds['Finance'],$posIds['Manager']);
        $manager = $make('Manager Sales','manager@andrea.co.id','MANAGER',$deptIds['Sales'],$posIds['Manager']);
        $hrd = $make('HRD User','hrd@andrea.co.id','HRD',$deptIds['HR'],$posIds['Manager']);
        $employee = $make('Azka Employee','azka@andrea.co.id','EMPLOYEE',$deptIds['Sales'],$posIds['Staff']);

        // policies
        foreach ([
            ['Local - Staff', $posIds['Staff'], 'LOCAL', 2000000],
            ['Domestic - Staff', $posIds['Staff'], 'DOMESTIC', 5000000],
            ['International - Manager', $posIds['Manager'], 'INTERNATIONAL', 15000000],
        ] as [$n,$p,$t,$b]) {
            DB::table('travel_policies')->insert(['name'=>$n,'position_id'=>$p,'destination_tier'=>$t,'max_budget'=>$b,'is_active'=>true,'created_at'=>now(),'updated_at'=>now()]);
        }

        // sample notification
        DB::table('notifications')->insert(['user_id'=>$employee->id,'title'=>'Selamat datang','message'=>'Akun Anda aktif. Selamat menggunakan sistem.','is_read'=>false,'created_at'=>now(),'updated_at'=>now()]);
    }
}
