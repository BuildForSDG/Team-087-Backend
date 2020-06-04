<?php

namespace App\Http\Controllers;

use App\Appointment;
use App\Specialist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Appointment Controller
 * 
 * @author Emma NWAMAIFE <emadimabua@gmail.com>
 */
class AppointmentController extends Controller
{
    public function book(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $isPatient = $user->is_patient;

            $data = $this->validate($request, [
                'patient_id' => Rule::requiredIf(!$isPatient),
                'purpose' => 'required|string|max:160',
                'starts_at' => 'required|date_format:Y-m-d H:i:s',
                'ends_at' => 'required|date_format:Y-m-d H:i:s'
            ]);

            $specialistId = $isPatient ? $id : $user->id;
            $patientId = $isPatient ? $user->id : $data['patient_id'];

            Specialist::findOrFail($specialistId);

            $appointment = new Appointment($data);
            $appointment->specialist_id = $specialistId;
            $appointment->patient_id = $patientId;
            $appointment->save();

            return response()->json([
                'status' => true, 'message' => 'Appointment saved successfully', 'data' => $appointment
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()
            ], 400);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'Appointment could not be saved', 'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }
}
