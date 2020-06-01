<?php

namespace App\Http\Controllers;

use App\Review;
use App\Specialist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Review Controller
 * 
 * @author Emma NWAMAIFE <emadimabua@gmail.com>
 */
class ReviewController extends Controller
{
    public function add(Request $request, $id)
    {
        try {
            $user = auth()->user();
            if (!$user->is_patient) {
                abort(400, 'You cannot give reviews');
            }

            $data = $this->validate($request, [
                //'patient_id' => 'required|numeric|exists:App\Patient,user_id',
                'remark' => 'required|string|max:160',
                'rating' => 'required|numeric|between:0.5,5.0'
            ], ['patient_id.exists' => 'You are not a recognized patient']);


            $specialist = Specialist::where('user_id', $id)->first();
            if (empty($specialist)) {
                throw new ModelNotFoundException("Specialist could not be found");
            }


            $review = new Review($data);
            $review->specialist_id = $id;
            $review->patient_id = $user->id;
            $review->save();

            return response()->json([
                'status' => true, 'message' => 'Review saved successfully', 'data' => $review
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()
            ], 400);
        } catch (\Exception | ModelNotFoundException | BadRequestHttpException $e) {
            return response()->json([
                'status' => false, 'message' => 'Review could not be saved', 'errors' => ['error' => $e->getMessage()]
            ], 400);
        }
    }
}
