<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\IspClient;
use App\Models\Plan;
use App\Models\Nas;
use App\Models\FatNode;
use App\Models\AdminPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientImportExportController extends Controller {
    private function check(string $perm) {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $uid = session('admin_user_id');
        if ($uid && session('admin_role') !== 'admin' && !AdminPermission::userHas($uid,$perm)) {
            return back()->with('error','You do not have the "'.AdminPermission::allPermissions()[$perm].'" permission.');
        }
        return null;
    }

    public function export(Request $req) {
        if ($r = $this->check('clients.export')) return $r;
        $clients = IspClient::with(['plan','nas','fat'])->get();
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="clients_'.date('Y-m-d').'.csv"',
        ];
        $callback = function() use ($clients) {
            $file = fopen('php://output','w');
            fputcsv($file,['Username','First Name','Last Name','Phone','Email','Connection Type','Plan','NAS','FAT Node','Static IP','MAC','Status','Expiry Date','Created At']);
            foreach ($clients as $c) {
                fputcsv($file,[
                    $c->username,$c->first_name,$c->last_name,$c->phone,$c->email,
                    $c->connection_type,$c->plan?->name,$c->nas?->shortname,
                    $c->fat?->code,$c->static_ip,$c->mac_address,$c->status,
                    $c->expiry_date?->format('Y-m-d'),$c->created_at->format('Y-m-d'),
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback,200,$headers);
    }

    public function showImport() {
        if ($r = $this->check('clients.import')) return $r;
        return view('admin.clients.import');
    }

    public function import(Request $req) {
        if ($r = $this->check('clients.import')) return $r;
        $req->validate(['file'=>'required|file|mimes:csv,txt|max:5120']);

        $file     = $req->file('file');
        $handle   = fopen($file->getRealPath(),'r');
        $header   = fgetcsv($handle); // skip header row
        $imported = 0;
        $errors   = [];
        $row      = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            if (count($data) < 5) { $errors[] = "Row {$row}: Too few columns"; continue; }

            [$username,$first,$last,$phone,$email,$type,$planName,$nasName,$fatCode,$ip,$mac,$status,$expiry] = array_pad($data,13,'');

            $v = Validator::make([
                'username' => $username,
                'first_name'=> $first,
                'last_name' => $last,
            ],['username'=>'required','first_name'=>'required','last_name'=>'required']);

            if ($v->fails()) { $errors[] = "Row {$row}: ".implode(', ',$v->errors()->all()); continue; }

            $plan = Plan::where('name',$planName)->first();
            $nas  = Nas::where('shortname',$nasName)->first();
            $fat  = FatNode::where('code',$fatCode)->first();

            $existing = IspClient::where('username',$username)->first();
            if ($existing) {
                $existing->update([
                    'first_name'=>$first,'last_name'=>$last,'phone'=>$phone,'email'=>$email,
                    'plan_id'=>$plan?->id,'nas_id'=>$nas?->id,'fat_node_id'=>$fat?->id,
                    'status'=>$status?:'active',
                    'expiry_date'=>$expiry?:null,
                ]);
            } else {
                IspClient::create([
                    'username'       =>$username,
                    'password'       =>bcrypt($username), // default password = username
                    'first_name'     =>$first,'last_name'=>$last,
                    'phone'          =>$phone,'email'=>$email,
                    'connection_type'=>$type?:'pppoe',
                    'plan_id'        =>$plan?->id,'nas_id'=>$nas?->id,'fat_node_id'=>$fat?->id,
                    'static_ip'      =>$ip?:null,'mac_address'=>$mac?:null,
                    'status'         =>$status?:'pending',
                    'expiry_date'    =>$expiry?:null,
                ]);
            }
            $imported++;
        }
        fclose($handle);
        $msg = "Import complete: {$imported} clients imported/updated.";
        if ($errors) $msg .= ' '.count($errors).' errors: '.implode(' | ',array_slice($errors,0,5));
        return back()->with($errors && $imported === 0 ? 'error' : 'success', $msg);
    }

    public function downloadTemplate() {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="import_template.csv"',
        ];
        $callback = function() {
            $f = fopen('php://output','w');
            fputcsv($f,['username','first_name','last_name','phone','email','connection_type','plan_name','nas_shortname','fat_code','static_ip','mac_address','status','expiry_date']);
            fputcsv($f,['john_doe','John','Doe','+254712345678','john@email.com','pppoe','Home 10Mbps','NAS-001','FAT001','','','active','2025-12-31']);
            fclose($f);
        };
        return response()->stream($callback,200,$headers);
    }
}