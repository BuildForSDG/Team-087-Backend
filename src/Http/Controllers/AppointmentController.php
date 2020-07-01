<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Specialist;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Appointment Controller
 * 
 * @author Emma NWAMAIFE <emadimabua@gmail.com>
 */
class AppointmentController extends Controller
{
    public function book(Request $request, $id)
    {
        $user = auth()->user();
        $isPatient = $user->is_patient;

        $data = $this->validate($request, [
            // 'patient_id' => Rule::requiredIf(!$isPatient),
            'purpose' => 'required|string|max:160',
            'starts_at' => 'required',// date_format:Y-m-d H:i:s
            'ends_at' => 'required'// date_format:Y-m-d H:i:s
        ]);

        try {
            $specialistId = $isPatient ? $id : $user->id;
            $patientId = $isPatient ? $user->id : $id;// $data['patient_id']

            Specialist::findOrFail($specialistId);

            $appointment = new Appointment($data);
            $appointment->specialist_id = $specialistId;
            $appointment->patient_id = $patientId;
            $appointment->save();

            return response()->json([
                'status' => true, 'message' => 'Appointment saved successfully', 'data' => $appointment
            ], 201);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'Appointment could not be saved', 'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }

    public function fetch(Request $request, $id = 0)
    {
        try {
            $userId = empty($id) ? auth()->user()->id : $id;
            $user = User::findOrFail($userId);

            $appointments = empty($id) ? collect([]) : Appointment::where(['specialist_id' => $id])->get();
            $appointments = $user->is_patient ? $user->patient->appointments : ($user->is_specialist ? $user->specialist->appointments : $appointments);

            return response()->json(['status' => true, 'data' => $appointments->paginate($request->input('chunk', 10))], 200);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'Appointment(s) could not be fetched', 'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }
}
