<?php

namespace App\Http\Controllers;

use App\Review;
use App\Specialist;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
                abort(400, 'You cannot give reviews as a non-patient');
            }

            $data = $this->validate($request, [
                //'patient_id' => 'required|numeric|exists:App\Patient,user_id',
                'remark' => 'required|string|max:160',
                'rating' => 'required|numeric|between:0.5,5.0'
            ]);


            $specialist = Specialist::findOrFail($id);
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
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'Review could not be saved', 'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }

    public function edit(Request $request, $id, $reviewId)
    {
        try {
            $user = auth()->user();
            if (!$user->is_patient) {
                abort(400, 'You cannot edit reviews as a non-patient');
            }

            $data = $this->validate($request, [
                'remark' => 'required|string|max:160', 'rating' => 'required|numeric|between:0.5,5.0'
            ]);

            $review = Review::with(['patient.user'])->where(['specialist_id' => $id])->findOrFail($reviewId);
            // if (empty($review)) {
            //     abort(404, 'Review does not exist');
            // }

            if ($review->patient->user->id !== $user->id) {
                abort(400, 'Review cannot be edited by proxy');
            }

            $review->remark = $data['remark'];
            $review->rating = $data['rating'];
            $review->save();

            return response()->json([
                'status' => true, 'message' => 'Review updated successfully', 'data' => $review
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()
            ], 400);
        } catch (\Exception | ModelNotFoundException $e) {
            return response()->json([
                'status' => false, 'message' => 'Review could not be updated', 'errors' => ['error' => $e->getMessage()]
            ], ($e instanceof ModelNotFoundException ? 404 : 400));
        }
    }
}
