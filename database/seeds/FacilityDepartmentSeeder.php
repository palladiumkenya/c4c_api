<?php

use Illuminate\Database\Seeder;

class FacilityDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (\App\Facility::all() as $facility) {
            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Outpatient department (OPD)";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();

            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Inpatient Service (IP)";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();



            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Medical Department";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();


            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "New Born Unit (NBU)";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();


            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Renal Unit";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();


            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Mother and Child (MCH)";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();



            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Paramedical Department";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();



            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Physical Medicine and Rehabilitation Department";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();


            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Operation Theatre Complex (OT)";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();


            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Pharmacy Department";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();


            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Radiology Department (X-ray)";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();



            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Dietary Department";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();


            $facilityDepartment = new \App\FacilityDepartment();
            $facilityDepartment->facility_id = $facility->id;
            $facilityDepartment->department_name = "Medical Record Department (MRD)";
            $facilityDepartment->created_at = \Carbon\Carbon::now();
            $facilityDepartment->updated_at = \Carbon\Carbon::now();
            $facilityDepartment->saveOrFail();

        }
    }
}
