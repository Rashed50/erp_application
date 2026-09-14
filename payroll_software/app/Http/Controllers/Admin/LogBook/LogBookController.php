<?php

namespace App\Http\Controllers\Admin\LogBook;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Vehicle\VehicleController;
use App\Http\Controllers\DataServices\TransportationDataService;
use App\Http\Controllers\Admin\CompanyProfileController;
use Illuminate\Http\Request;
use App\Models\LogBook;
use Carbon\Carbon;
use Session;
use Auth;
use Image;

class LogBookController extends Controller{
  /*
  |--------------------------------------------------------------------------
  |  DATABASE OPERATION
  |--------------------------------------------------------------------------
  */
  public function getAll(){
    return $all = LogBook::where('status',1)->orderBy('lgb_id','DESC')->get();
  }
  public function findLogBook($id){
    return $edit = LogBook::where('status',1)->where('lgb_id',$id)->first();
  }

  public function delete($id){
      $delete = LogBook::where('status',1)->where('lgb_id',$id)->update([
        'status' => 0,
        'updated_at' => Carbon::now(),
      ]);
      /* redirect back */
      if($delete){
          Session::flash('delete','value');
          return redirect()->back();
      }else{
          Session::flash('error','value');
          return redirect()->back();
      }
  }

  public function insert(Request $request){
      $crator = Auth::user()->id;
      $lastRecord = LogBook::latest('veh_id')->where('veh_id',$request->veh_id)->first();

      if($lastRecord){
        $prevMiles = $lastRecord->present_miles;
        $presentMiles = $request->per_milimeter;
        $fouel = $request->fouel_amount;

        $calculateMiles = ($presentMiles - $prevMiles);
        $averageMiles = ( $calculateMiles / $fouel);


        /* compare two value */
        if($presentMiles > $prevMiles){


          $update = LogBook::where('lgb_id',$lastRecord->lgb_id)->update([
            'average_miles' => $averageMiles,
            'updated_at' => Carbon::now(),
          ]);



          if($request->file('voucharPhoto') != "" ){
            /* making image */
            $voucharPhoto = $request->file('voucharPhoto');
            $name_gen = 'logbook-vouchar-'.time().'.'.$voucharPhoto->getClientOriginalExtension();
            Image::make($voucharPhoto)->resize(300,300)->save('uploads/vehicle/logbook/'.$name_gen);
            $uplodPath = 'uploads/vehicle/logbook/'.$name_gen;

            // insert data in database
            $insert = LogBook::insert([
                'veh_id' => $request->veh_id,
                'present_miles' => $request->per_milimeter,
                'fouel_amount' => $request->fouel_amount,
                'total_cost' => $request->total_cost,
                'date' => $request->date,
                'vouchar_photo' => $uplodPath,
                'create_by_id' => $crator,
                'created_at' => Carbon::now(),
            ]);
            /* redirect back */
            if($insert){
                Session::flash('success','value');
                return redirect()->back();
            }else{
                Session::flash('error','value');
                return redirect()->back();
            }

          }else{
            // insert data in database
            $insert = LogBook::insert([
                'veh_id' => $request->veh_id,
                'present_miles' => $request->per_milimeter,
                'fouel_amount' => $request->fouel_amount,
                'total_cost' => $request->total_cost,
                'date' => $request->date,
                'create_by_id' => $crator,
                'created_at' => Carbon::now(),
            ]);
            /* redirect back */
            if($insert){
                Session::flash('success','value');
                return redirect()->back();
            }else{
                Session::flash('error','value');
                return redirect()->back();
            }


          }



        }else{


          Session::flash('error','value');
          return redirect()->back();

        }






      }else{


        if($request->file('voucharPhoto') != "" ){
          /* making image */
          $voucharPhoto = $request->file('voucharPhoto');
          $name_gen = 'logbook-vouchar-'.time().'.'.$voucharPhoto->getClientOriginalExtension();
          Image::make($voucharPhoto)->resize(300,300)->save('uploads/vehicle/logbook/'.$name_gen);
          $uplodPath = 'uploads/vehicle/logbook/'.$name_gen;

          // insert data in database
          $insert = LogBook::insert([
              'veh_id' => $request->veh_id,
              'present_miles' => $request->per_milimeter,
              'fouel_amount' => $request->fouel_amount,
              'total_cost' => $request->total_cost,
              'date' => $request->date,
              'vouchar_photo' => $uplodPath,
              'create_by_id' => $crator,
              'created_at' => Carbon::now(),
          ]);
          /* redirect back */
          if($insert){
              Session::flash('success','value');
              return redirect()->back();
          }else{
              Session::flash('error','value');
              return redirect()->back();
          }

        }else{
          // insert data in database
          $insert = LogBook::insert([
              'veh_id' => $request->veh_id,
              'present_miles' => $request->per_milimeter,
              'fouel_amount' => $request->fouel_amount,
              'total_cost' => $request->total_cost,
              'date' => $request->date,
              'create_by_id' => $crator,
              'created_at' => Carbon::now(),
          ]);
          /* redirect back */
          if($insert){
              Session::flash('success','value');
              return redirect()->back();
          }else{
              Session::flash('error','value');
              return redirect()->back();
          }


        }



      }













  }

  public function update(Request $request){
      $crator = Auth::user()->id;
      $id = $request->id;
      $old_image = $request->old_image;
      // insert data in database





      if($request->file('voucharPhoto') != "" ){
        if($old_image != ""){
          unlink($old_image);
        }
        /* making image */
        $voucharPhoto = $request->file('voucharPhoto');
        $name_gen = 'logbook-vouchar-'.time().'.'.$voucharPhoto->getClientOriginalExtension();
        Image::make($voucharPhoto)->resize(300,300)->save('uploads/vehicle/logbook/'.$name_gen);
        $uplodPath = 'uploads/vehicle/logbook/'.$name_gen;

        // insert data in database
        $update = LogBook::where('lgb_id',$id)->update([
            'veh_id' => $request->veh_id,
            'present_miles' => $request->per_milimeter,
            'fouel_amount' => $request->fouel_amount,
            'total_cost' => $request->total_cost,
            'date' => $request->date,
            'vouchar_photo' => $uplodPath,
            'create_by_id' => $crator,
            'created_at' => Carbon::now(),
        ]);
        /* redirect back */
        if($update){
            Session::flash('success_update','value');
            return redirect()->route('add-new.LogBook');
        }else{
            Session::flash('error','value');
            return redirect()->back();
        }

      }else{
        // update data in database
        $update = LogBook::where('lgb_id',$id)->update([
            'veh_id' => $request->veh_id,
            'present_miles' => $request->per_milimeter,
            'fouel_amount' => $request->fouel_amount,
            'total_cost' => $request->total_cost,
            'date' => $request->date,
            'create_by_id' => $crator,
            'created_at' => Carbon::now(),
        ]);
        /* redirect back */
        if($update){
            Session::flash('success','value');
            return redirect()->route('add-new.LogBook');
        }else{
            Session::flash('error','value');
            return redirect()->back();
        }
      }

  }


  public function reportGenerate(Request $request){
    $vehId = $request->veh_id;
    $start_date = $request->start_date;
    $end_date = $request->end_date;

    $companyOBJ = new CompanyProfileController();
    $company = $companyOBJ->findCompanry();


    $LogBook = LogBook::where('veh_id',$vehId)->whereBetween('date',[$start_date,$end_date])->get();

    return view('admin.log-book.report-generat',compact('company','LogBook'));
  }










  /*
  |--------------------------------------------------------------------------
  |  BLADE OPERATION
  |--------------------------------------------------------------------------
  */
  public function index(){
    $all = $this->getAll();
    
    $vehicle = (new TransportationDataService())->getAllActiveVehicaleInfos();
    return view('admin.log-book.all',compact('all','vehicle'));
  }

  public function edit($id){
    $edit = $this->findLogBook($id);
     
    $vehicle = (new TransportationDataService())->getAllActiveVehicaleInfos();
    return view('admin.log-book.edit',compact('edit','vehicle'));
  }

  public function report(){
     
    $vehicle = (new TransportationDataService())->getAllActiveVehicaleInfos();
    return view('admin.log-book.report',compact('vehicle') );
  }













  /*
  |--------------------------------------------------------------------------
  |  API OPERATION
  |--------------------------------------------------------------------------
  */



  /* _____________________________ === _____________________________ */
}
